<?php


namespace App\Controllers;

use Nevs\Config;
use Nevs\Controller;
use Nevs\Response;

class VersionController extends Controller
{
    public function GetVersion(): Response
    {
        $version = json_decode(file_get_contents(Config::Get('app_root') . 'App/version.json'), true);
        return new Response(json_encode([
            'version' => $version['version'],
            'maintenance_date' => Config::Get('maintenance_date'),
            'maintenance_time' => Config::Get('maintenance_time'),
            'maintenance_hours' => Config::Get('maintenance_hours')
        ]));
    }
}