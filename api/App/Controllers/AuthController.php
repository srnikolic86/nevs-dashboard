<?php

namespace App\Controllers;

use App\Models\AccessToken;
use Nevs\Config;
use Nevs\Controller;
use Nevs\Response;
use App\Models\User;

class AuthController extends Controller
{
    public function Login(): Response
    {
        $validation = $this->request->Validate([
            'email' => 'string',
            'password' => 'string',
            'remember' => 'bool'
        ]);
        if ($validation !== true) {
            return new Response(json_encode($validation), ['HTTP/1.1 400 Bad Request']);
        }

        $logged_in = null;
        $users = User::Select('email=?', [$this->request->data['email']]);
        foreach ($users as $user) {
            if (password_verify($this->request->data['password'], $user->password)) {
                $logged_in = $user;
            }
        }

        if ($logged_in === null) {
            return new Response(json_encode(['error' => 'login failed']), ['HTTP/1.1 401 Bad Request']);
        }

        $token = AccessToken::New($logged_in, $this->request->data['remember']);
        setcookie(Config::Get('cookies.prefix') . 'token', $token, [
            'expires' => time() + 365 * 24 * 60 * 60,
            'path' => Config::Get('cookies.path'),
            'Secure' => Config::Get('cookies.secure'),
            'httponly' => true,
            'samesite' => 'None'
        ]);

        return new Response(json_encode([
            'user' => $logged_in
        ]));
    }

    public function Logout(): Response
    {
        $token = $_COOKIE[Config::Get('cookies.prefix') . 'token'] ?? '';

        setcookie(Config::Get('cookies.prefix') . 'token', $token, [
            'expires' => time() - 60 * 60,
            'path' => Config::Get('cookies.path'),
            'Secure' => Config::Get('cookies.secure'),
            'httponly' => true,
            'samesite' => 'None'
        ]);
        AccessToken::Destroy($token);
        return new Response(json_encode([
            'success' => true
        ]));
    }

    public function Session(): Response
    {
        return new Response(json_encode([
            'user' => User::Current()
        ]));
    }
}