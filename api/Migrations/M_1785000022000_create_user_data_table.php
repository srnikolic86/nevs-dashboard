<?php
use Nevs\Database;
class M_1785000022000_create_user_data_table
{
    function migrate(Database|null $DB = null) : void
    {
        if ($DB == null) $DB = new Database();
        $DB->CreateTable(['name' => 'user_data', 'fields' => [
            ['name' => 'id', 'type' => 'int', 'primary_key' => true, 'auto_increment' => true],
            ['name' => 'user_id', 'type' => 'int', 'foreign_key' => 'users'],
            ['name' => 'key', 'type' => 'string'],
            ['name' => 'data', 'type' => 'json']
        ]]);
    }
}
