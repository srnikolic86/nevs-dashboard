<?php

namespace App\Models;

use Carbon\Carbon;
use Nevs\Model;

class AccessToken extends Model
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'access_tokens';
    }

    static function New(User $user, bool $remember_me): string
    {
        $term = true;
        $token = '';
        while ($term) {
            $token = md5(round(microtime(true) * 1000) . rand(0, 999999));
            $duplicates = AccessToken::Select('token=?', [$token]);
            $term = (count($duplicates) !== 0);
        }
        $duration = 8;
        if ($remember_me) {
            $duration = 30 * 24;
        }
        AccessToken::create([
            'token' => $token,
            'user_id' => $user->id,
            'expires' => Carbon::now()->addHours($duration),
            'duration' => $duration
        ]);
        return $token;
    }

    static function Check(string $token): User|null
    {
        $access_tokens = AccessToken::Select('token=? AND expires>?', [$token, Carbon::now()]);
        $access_token = (count($access_tokens) > 0) ? $access_tokens[0] : null;
        if ($access_token == null) {
            return null;
        }
        $user = User::Find($access_token->user_id);
        return (fn($item): User => $item)($user);
    }

    static function Reset($token): bool
    {
        $access_tokens = AccessToken::Select('token=?', [$token]);
        $access_token = (count($access_tokens) > 0) ? $access_tokens[0] : null;
        if ($access_token == null) {
            return false;
        }
        $access_token->update([
            'expires' => Carbon::now()->addHours($access_token->duration)
        ]);
        return true;
    }

    static function Destroy($token): void {
        global $DB;
        error_log($token);
        $DB->Execute('DELETE FROM `access_tokens` WHERE `token`=?', [$token]);
    }
}