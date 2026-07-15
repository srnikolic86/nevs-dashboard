<?php

namespace App\Classes;

use GuzzleHttp\Client;
use Nevs\Config;

/**
 * Minimal S3-compatible object storage client using AWS Signature Version 4, built on Guzzle. It implements
 * only the object operations the app needs (put, get, exists, delete) and works against any S3-compatible
 * endpoint such as Linode Object Storage. Configuration is read from 'uploads.s3'.
 */
class S3Client
{
    private string $endpoint;
    private string $region;
    private string $bucket;
    private string $key;
    private string $secret;
    private bool $path_style;
    private Client $http;

    public function __construct()
    {
        $this->endpoint = rtrim((string)Config::Get('uploads.s3.endpoint'), '/');
        $this->region = (string)Config::Get('uploads.s3.region');
        $this->bucket = (string)Config::Get('uploads.s3.bucket');
        $this->key = (string)Config::Get('uploads.s3.key');
        $this->secret = (string)Config::Get('uploads.s3.secret');
        $this->path_style = (bool)Config::Get('uploads.s3.use_path_style');
        $this->http = new Client(['http_errors' => false]);
    }

    public function PutObject(string $key, string $body, ?string $content_type = null): void
    {
        $headers = $content_type !== null ? ['Content-Type' => $content_type] : [];
        $response = $this->Request('PUT', $key, $body, $headers);
        $status = $response->getStatusCode();
        if ($status < 200 || $status >= 300) {
            throw new \RuntimeException('S3 PutObject failed (' . $status . '): ' . $response->getBody());
        }
    }

    public function GetObject(string $key): ?string
    {
        $response = $this->Request('GET', $key);
        $status = $response->getStatusCode();
        if ($status === 404) {
            return null;
        }
        if ($status < 200 || $status >= 300) {
            throw new \RuntimeException('S3 GetObject failed (' . $status . '): ' . $response->getBody());
        }
        return (string)$response->getBody();
    }

    public function ObjectExists(string $key): bool
    {
        $status = $this->Request('HEAD', $key)->getStatusCode();
        return $status >= 200 && $status < 300;
    }

    public function DeleteObject(string $key): void
    {
        $status = $this->Request('DELETE', $key)->getStatusCode();
        // 204 is the success status; a missing object (404) is treated as already deleted.
        if ($status !== 204 && $status !== 200 && $status !== 404) {
            throw new \RuntimeException('S3 DeleteObject failed (' . $status . ')');
        }
    }

    /**
     * Signs (SigV4) and sends a single request for the given object key.
     */
    private function Request(string $method, string $key, string $body = '', array $extra_headers = [])
    {
        [$host, $url, $canonical_uri] = $this->Resolve($key);

        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $amz_date = $now->format('Ymd\THis\Z');
        $date_stamp = $now->format('Ymd');
        $payload_hash = hash('sha256', $body);

        // Only host and the two x-amz headers are signed; anything else (e.g. Content-Type) is sent unsigned.
        $signed = [
            'host' => $host,
            'x-amz-content-sha256' => $payload_hash,
            'x-amz-date' => $amz_date
        ];
        ksort($signed);
        $canonical_headers = '';
        foreach ($signed as $name => $value) {
            $canonical_headers .= $name . ':' . trim($value) . "\n";
        }
        $signed_headers = implode(';', array_keys($signed));

        $canonical_request = $method . "\n"
            . $canonical_uri . "\n"
            . "\n"                       // empty canonical query string
            . $canonical_headers . "\n"
            . $signed_headers . "\n"
            . $payload_hash;

        $scope = $date_stamp . '/' . $this->region . '/s3/aws4_request';
        $string_to_sign = "AWS4-HMAC-SHA256\n"
            . $amz_date . "\n"
            . $scope . "\n"
            . hash('sha256', $canonical_request);

        $signature = hash_hmac('sha256', $string_to_sign, $this->SigningKey($date_stamp));

        $authorization = 'AWS4-HMAC-SHA256 '
            . 'Credential=' . $this->key . '/' . $scope . ', '
            . 'SignedHeaders=' . $signed_headers . ', '
            . 'Signature=' . $signature;

        // Host is derived by Guzzle from the URL, so it is not passed explicitly.
        $request_headers = array_merge([
            'x-amz-content-sha256' => $payload_hash,
            'x-amz-date' => $amz_date,
            'Authorization' => $authorization
        ], $extra_headers);

        return $this->http->request($method, $url, [
            'headers' => $request_headers,
            'body' => $body
        ]);
    }

    /**
     * Resolves [host, url, canonical uri] for a key, honouring path-style vs virtual-hosted addressing.
     */
    private function Resolve(string $key): array
    {
        $encoded_key = $this->EncodeKey($key);
        $parsed = parse_url($this->endpoint);
        // parse_url() only recognises a host when the endpoint carries a scheme (or a leading "//"). A
        // scheme-less value like "de-fra-1.linodeobjects.com" is parsed entirely as a path, leaving the host
        // empty and producing a malformed "https:///bucket/key" URL. Re-parse it as an authority in that case.
        if (empty($parsed['host'])) {
            $parsed = parse_url('//' . ltrim($this->endpoint, '/'));
        }
        $scheme = $parsed['scheme'] ?? 'https';
        $endpoint_host = $parsed['host'] ?? '';
        if (isset($parsed['port'])) {
            $endpoint_host .= ':' . $parsed['port'];
        }

        if ($this->path_style) {
            $host = $endpoint_host;
            $canonical_uri = '/' . $this->bucket . '/' . $encoded_key;
            $url = $scheme . '://' . $endpoint_host . '/' . $this->bucket . '/' . $encoded_key;
        } else {
            $host = $this->bucket . '.' . $endpoint_host;
            $canonical_uri = '/' . $encoded_key;
            $url = $scheme . '://' . $host . '/' . $encoded_key;
        }
        return [$host, $url, $canonical_uri];
    }

    /** URI-encodes an object key per RFC 3986, preserving '/' path separators. */
    private function EncodeKey(string $key): string
    {
        return implode('/', array_map('rawurlencode', explode('/', $key)));
    }

    private function SigningKey(string $date_stamp): string
    {
        $k_date = hash_hmac('sha256', $date_stamp, 'AWS4' . $this->secret, true);
        $k_region = hash_hmac('sha256', $this->region, $k_date, true);
        $k_service = hash_hmac('sha256', 's3', $k_region, true);
        return hash_hmac('sha256', 'aws4_request', $k_service, true);
    }
}
