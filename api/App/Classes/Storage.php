<?php

namespace App\Classes;

use Nevs\Config;

/**
 * Storage abstraction for uploaded files. Depending on the 'uploads.driver' config it stores and reads files
 * either on the local filesystem (Storage/Uploads, the historical behaviour) or in an S3-compatible object
 * storage bucket. Files are addressed by their unique upload name (the `real_name` column), which is used as
 * the object key. Regardless of driver, files are only ever served through the API, never linked to directly.
 */
class Storage
{
    private const UPLOADS_SUBPATH = 'Storage/Uploads/';
    private const TEMP_SUBPATH = 'Storage/Temp/';

    private const CONTENT_TYPES = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'pdf' => 'application/pdf'
    ];

    public static function UsesObjectStorage(): bool
    {
        return Config::Get('uploads.driver') === 's3';
    }

    /** Whether a file with the given name exists in the active storage. */
    public static function Exists(string $name): bool
    {
        if (self::UsesObjectStorage()) {
            return (new S3Client())->ObjectExists($name);
        }
        return file_exists(self::LocalUploadPath($name));
    }

    /**
     * Stores a freshly uploaded file (a $_FILES 'tmp_name') under the given name. On the filesystem this moves
     * the uploaded temp file into place; in object storage it uploads its contents. Returns success.
     */
    public static function StoreUploadedFile(string $name, string $tmp_path): bool
    {
        if (self::UsesObjectStorage()) {
            $contents = file_get_contents($tmp_path);
            if ($contents === false) {
                return false;
            }
            (new S3Client())->PutObject($name, $contents, self::ContentType($name));
            return true;
        }
        return move_uploaded_file($tmp_path, self::LocalUploadPath($name));
    }

    /** Stores arbitrary bytes under the given name. */
    public static function Put(string $name, string $contents): bool
    {
        if (self::UsesObjectStorage()) {
            (new S3Client())->PutObject($name, $contents, self::ContentType($name));
            return true;
        }
        return file_put_contents(self::LocalUploadPath($name), $contents) !== false;
    }

    /** Returns a file's contents, or null when it does not exist. */
    public static function Get(string $name): ?string
    {
        if (self::UsesObjectStorage()) {
            return (new S3Client())->GetObject($name);
        }
        $path = self::LocalUploadPath($name);
        if (!file_exists($path)) {
            return null;
        }
        $contents = file_get_contents($path);
        return $contents === false ? null : $contents;
    }

    /** Deletes a file. A missing file is not an error. */
    public static function Delete(string $name): void
    {
        if (self::UsesObjectStorage()) {
            (new S3Client())->DeleteObject($name);
            return;
        }
        $path = self::LocalUploadPath($name);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * Returns a local filesystem path to the file, for callers that genuinely need one (PDF generation, e-mail
     * attachments, certificate loading). On the filesystem this is the stored file itself; in object storage
     * the file is first downloaded to a temporary path (which the temp-cleanup cron later removes). Returns
     * null when the file does not exist.
     */
    public static function LocalPath(string $name): ?string
    {
        if (!self::UsesObjectStorage()) {
            $path = self::LocalUploadPath($name);
            return file_exists($path) ? $path : null;
        }

        $contents = (new S3Client())->GetObject($name);
        if ($contents === null) {
            return null;
        }
        // Keep the original file name (and extension) so callers that infer the type from it still work.
        $temp_path = Config::Get('app_root') . self::TEMP_SUBPATH . uniqid('obj_', true) . '_' . basename($name);
        if (file_put_contents($temp_path, $contents) === false) {
            return null;
        }
        return $temp_path;
    }

    /** The absolute local path for a name under Storage/Uploads. */
    private static function LocalUploadPath(string $name): string
    {
        return Config::Get('app_root') . self::UPLOADS_SUBPATH . $name;
    }

    /** Best-effort content type from the file extension, defaulting to application/octet-stream. */
    private static function ContentType(string $name): string
    {
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        return self::CONTENT_TYPES[$extension] ?? 'application/octet-stream';
    }
}
