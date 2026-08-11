<?php

namespace App\Models;

use Nevs\Model;

class UserData extends Model
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'user_data';
    }

    /**
     * Everything stored for one user, as a key => data map. Sent along with the session and the login response
     * so the UI can paint its stored state right away instead of fetching it once it is already on screen.
     */
    public static function ForUser(int $user_id): array
    {
        $out = [];
        foreach (self::Select('`user_id` = ?', [$user_id]) as $record) {
            $out[$record->key] = $record->data;
        }
        return $out;
    }
}
