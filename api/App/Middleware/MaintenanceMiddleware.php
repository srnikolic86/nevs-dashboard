<?php

namespace App\Middleware;

use Nevs\Config;
use Nevs\Middleware;
use Nevs\Request;
use Nevs\Response;

class MaintenanceMiddleware extends Middleware
{
    public function Before(Request &$request): null|Response
    {
        if (Config::Get('maintenance')) {
            return new Response(json_encode(['error' => 'maintenance in progress']), ['HTTP/1.1 400 Bad Request']);
        }
        return null;
    }

    public function After(Request &$request, null|Response &$response): void
    {

    }
}
