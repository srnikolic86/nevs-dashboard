<?php


namespace App\Controllers;

use Nevs\Config;
use Nevs\Controller;
use Nevs\Response;

class VersionController extends Controller
{
    public function GetVersion(): Response
    {
        return new Response(file_get_contents(Config::Get('app_root') . 'App/version.json'));
    }
}