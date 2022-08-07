<?php

namespace App\Controllers;

use App\Helpers;
use App\Language;
use App\Models\User;
use Nevs\Controller;
use Nevs\Response;

class UserController extends Controller
{
    public function GetOne(): Response
    {
        $user = User::Find($this->request->parameters['id']);
        return new Response(json_encode([
            'user' => $user
        ]));
    }

    public function GetMultiple(): Response
    {
        global $DB;
        $response = [
            "records" => [],
            "total_records" => 0
        ];

        $sort = json_decode($this->request->data['sort'], true);
        $filters = json_decode($this->request->data['filters'], true);
        $current_page = $this->request->data['currentPage'];
        $rows_per_page = $this->request->data['rowsPerPage'];

        $query = 'SELECT * FROM `users` WHERE 1=1';
        $params = [];

        if (isset($filters['first_name'])) {
            $query .= ' AND `first_name` LIKE ? ';
            $params[] = '%' . $filters['first_name'] . '%';
        }
        if (isset($filters['last_name'])) {
            $query .= ' AND `last_name` LIKE ? ';
            $params[] = '%' . $filters['last_name'] . '%';
        }
        if (isset($filters['email'])) {
            $query .= ' AND `email` LIKE ? ';
            $params[] = '%' . $filters['email'] . '%';
        }
        if (isset($filters['active'])) {
            if ($filters['active'] != -1) {
                $query .= ' AND `active` = ? ';
                $params[] = $filters['active'];
            }
        }

        $response['total_records'] = count($DB->ExecuteSelect($query, $params));

        $query .= ' ORDER BY `' . $sort['field'] . '` ';
        $query .= (!$sort['descending']) ? 'ASC' : 'DESC';

        $start_record = ($current_page - 1) * $rows_per_page;
        $query .= ' LIMIT ' . $start_record . ', ' . $rows_per_page;

        $records = $DB->ExecuteSelect($query, $params);

        foreach ($records as $record) {
            $record['active_display'] = ($record['active'] === 1) ? Language::Get('active.yes') : Language::Get('active.no');
            $response['records'][] = $record;
        }

        return new Response(json_encode($response));
    }

    public function Add(): Response
    {
        if (!in_array('USER_MANAGEMENT', User::Current()->permissions)) {
            return new Response(json_encode(['error' => 'unauthorized']), ['HTTP/1.1 401 Bad Request']);
        }
        $validation = $this->request->Validate([
            'email' => 'string',
            'first_name' => 'string',
            'last_name' => 'string',
            'active' => 'bool',
            'permissions' => 'array',
            'password' => 'string'
        ]);
        if ($validation === true) {
            if ($this->request->data['first_name'] === '') {
                $validation = [
                    'first_name' => 'mandatory'
                ];
            }
            if ($this->request->data['last_name'] === '') {
                $validation = [
                    'last_name' => 'mandatory'
                ];
            }
            if ($this->request->data['password'] === '') {
                $validation = [
                    'password' => 'mandatory'
                ];
            }
            if (!Helpers::CheckEmail($this->request->data['email'])) {
                $validation = [
                    'email' => 'invalid address'
                ];
            }
        }
        if ($validation !== true) {
            return new Response(json_encode($validation), ['HTTP/1.1 400 Bad Request']);
        }

        $data = $this->request->data;
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $user = User::Create($data);

        return new Response(json_encode(['user' => $user]));
    }

    public function Update(): Response
    {
        if (!in_array('MANAGE_USERS', User::Current()->permissions) && User::Current()->id != $this->request->parameters['id']) {
            return new Response(json_encode(['error' => 'unauthorized']), ['HTTP/1.1 401 Bad Request']);
        }

        $validation = $this->request->Validate([
            'email' => 'string',
            'first_name' => 'string',
            'last_name' => 'string',
            'active' => 'bool',
            'permissions' => 'array',
            'password' => 'string'
        ]);
        if ($validation === true) {
            if ($this->request->data['first_name'] === '') {
                $validation = [
                    'first_name' => 'mandatory'
                ];
            }
            if ($this->request->data['last_name'] === '') {
                $validation = [
                    'last_name' => 'mandatory'
                ];
            }
            if (!Helpers::CheckEmail($this->request->data['email'])) {
                $validation = [
                    'email' => 'invalid address'
                ];
            }
        }
        if ($validation !== true) {
            return new Response(json_encode($validation), ['HTTP/1.1 400 Bad Request']);
        }

        $user = User::Find($this->request->parameters['id']);

        $data = $this->request->data;
        if (!in_array('MANAGE_USERS', User::Current()->permissions)) {
            unset($data['permissions']);
            unset($data['active']);
        }

        if (isset($data['password']) && $data['password'] === '') {
            unset($data['password']);
        } else {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $user->update($data);

        return new Response(json_encode(['success' => true]));
    }
}