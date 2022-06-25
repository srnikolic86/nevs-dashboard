<?php

namespace App;

require_once('vendor/autoload.php');
require_once('App/config.php');
require_once('App/router.php');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        if (in_array($_SERVER['HTTP_ORIGIN'],NEVS_CONFIG['allowed_origins'])) {
            $headers = [
                'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Credentials: true',
                'Access-Control-Max-Age: 86400 ',
                'Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept',
                'Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']
            ];
            foreach ($headers as $header) {
                header($header);
            }
        }
    }
} else {
    \Nevs\Main::Run();
}