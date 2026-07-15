<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\UserData;
use Nevs\Controller;
use Nevs\Response;

/**
 * Stores arbitrary JSON data tied to a string key for the currently logged-in user. Used to persist
 * per-user UI state (e.g. the selected calendar trainer filter). Each (user, key) pair holds one row.
 */
class UserDataController extends Controller
{
    public function Get(): Response
    {
        $user = User::Current();
        if ($user === null) {
            return new Response(json_encode(['error' => 'unauthorized']), ['HTTP/1.1 401 Unauthorized']);
        }

        if (!isset($this->request->data['key']) || (string)$this->request->data['key'] === '') {
            return new Response(json_encode(['error' => 'invalid_key']), ['HTTP/1.1 400 Bad Request']);
        }

        $record = $this->FindRecord($user->id, (string)$this->request->data['key']);

        return new Response(json_encode(['data' => $record !== null ? $record->data : null]));
    }

    public function Set(): Response
    {
        $user = User::Current();
        if ($user === null) {
            return new Response(json_encode(['error' => 'unauthorized']), ['HTTP/1.1 401 Unauthorized']);
        }

        if (!isset($this->request->data['key']) || (string)$this->request->data['key'] === '') {
            return new Response(json_encode(['error' => 'invalid_key']), ['HTTP/1.1 400 Bad Request']);
        }

        $key = (string)$this->request->data['key'];
        $data = $this->request->data['data'] ?? null;

        $record = $this->FindRecord($user->id, $key);
        if ($record !== null) {
            $record->Update(['data' => $data]);
        } else {
            UserData::Create([
                'user_id' => $user->id,
                'key' => $key,
                'data' => $data
            ]);
        }

        return new Response(json_encode(['success' => true]));
    }

    private function FindRecord(int $userId, string $key): ?UserData
    {
        $records = UserData::Select('`user_id` = ? AND `key` = ?', [$userId, $key]);
        return count($records) > 0 ? $records[0] : null;
    }
}
