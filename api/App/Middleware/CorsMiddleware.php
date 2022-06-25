<?php

namespace App\Middleware;

use Nevs\Middleware;
use Nevs\Request;
use Nevs\Response;
use Nevs\Config;

class CorsMiddleware extends Middleware
{
    public function Before(Request &$request): null|Response
    {
        return null;
    }

    public function After(Request &$request, Response &$response): void
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            if (in_array($_SERVER['HTTP_ORIGIN'], Config::get('allowed_origins'))) {
                $response->headers = array_merge($response->headers, [
                    'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE',
                    'Access-Control-Allow-Credentials: true',
                    'Access-Control-Max-Age: 86400 ',
                    'Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept',
                    'Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']
                ]);
            }
        }
    }
}