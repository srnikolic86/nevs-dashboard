<?php

namespace App\Middleware;

use App\Models\AccessToken;
use App\Models\User;
use Nevs\Config;
use Nevs\Middleware;
use Nevs\Request;
use Nevs\Response;

class AuthMiddleware extends Middleware
{
    public function Before(Request &$request): null|Response
    {
        $logged_in = User::Current();
        if ($logged_in === null) {
            if (in_array($_SERVER['HTTP_ORIGIN'], Config::get('allowed_origins'))) {
                return new Response(json_encode(['error' => 'unauthorized']), [
                    'HTTP/1.1 401 Bad Request',
                    'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE',
                    'Access-Control-Allow-Credentials: true',
                    'Access-Control-Max-Age: 86400 ',
                    'Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept',
                    'Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']
                ]);
            }
            return new Response(json_encode(['error' => 'unauthorized']), ['HTTP/1.1 401 Bad Request']);
        }
        if (isset($_COOKIE[Config::Get('cookies.prefix') . 'token'])) {
            AccessToken::Reset($_COOKIE[Config::Get('cookies.prefix') . 'token']);
        }
        return null;
    }

    public function After(Request &$request, Response &$response): void
    {

    }
}