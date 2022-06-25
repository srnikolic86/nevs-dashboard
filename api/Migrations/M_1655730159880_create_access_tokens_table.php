<?php
use Nevs\Database;
class M_1655730159880_create_access_tokens_table 
{
    function migrate() : void
    {
        $DB = new Database();
        $DB->CreateTable(['name'=>'access_tokens', 'fields'=>[
            [
                'name' => 'id',
                'type' => 'int',
                'primary_key' => true,
                'auto_increment' => true
            ],
            [
                'name' => 'token',
                'type' => 'string'
            ],
            [
                'name' => 'expires',
                'type' => 'datetime'
            ],
            [
                'name' => 'duration',
                'type' => 'int'
            ],
            [
                'name' => 'user_id',
                'type' => 'int',
                'foreign_key' => 'users'
            ],
        ]]);
    }
}