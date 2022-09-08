<?php

use Nevs\Config;
use Nevs\Database;

class M_1655729946715_create_users_table
{
    function migrate(Database|null $DB = null) : void
    {
        if ($DB == null) $DB = new Database();
        $DB->CreateTable(['name' => 'users', 'fields' => [
            [
                'name' => 'id',
                'type' => 'int',
                'primary_key' => true,
                'auto_increment' => true
            ],
            [
                'name' => 'first_name',
                'type' => 'string'
            ],
            [
                'name' => 'last_name',
                'type' => 'string'
            ],
            [
                'name' => 'email',
                'type' => 'string'
            ],
            [
                'name' => 'password',
                'type' => 'string'
            ],
            [
                'name' => 'permissions',
                'type' => 'json'
            ],
            [
                'name' => 'locale',
                'type' => 'string'
            ],
            [
                'name' => 'active',
                'type' => 'bool',
                'default' => true
            ]
        ]]);
        $DB->Execute("INSERT INTO `users` (`first_name`, `last_name`, `email`, `password`, `permissions`, `locale`) VALUES ('Admin', 'Admin', 'admin@admin.com', '" . password_hash('12345', PASSWORD_BCRYPT) . "', '[\"MANAGE_USERS\"]', '" . Config::Get('default_locale') . "')");
    }
}