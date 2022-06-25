<?php

namespace App\Controllers;

use App\Helpers;
use App\Language;
use App\Models\User;
use Nevs\Controller;
use Nevs\Response;

class PermissionsController extends Controller
{
    public function Select(): Response
    {
        global $DB;

        $options = [];
        $permissions = $DB->ExecuteSelect('SELECT * FROM `permissions`');
        foreach ($permissions as $permission) {
            $options[] = [
                'label' => Language::Get('permissions.' . $permission['name']),
                'value' => $permission['name']
            ];
        }

        return new Response(json_encode($options));
    }
}