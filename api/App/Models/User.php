<?php

namespace App\Models;

use Nevs\Config;
use Nevs\Model;

class User extends Model
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'users';
        $this->hidden = ['password'];
    }

    static function Current(): User|null {

        $token = false;

        if (isset($_COOKIE[Config::Get('cookies.prefix') . 'token'])) {
            $token = $_COOKIE[Config::Get('cookies.prefix') . 'token'];
        }

        if (!$token) {
            return null;
        }

        $user = AccessToken::Check($token);
        if ($user != null && !$user->active) {
            $user = null;
        }

        return $user;
    }
}