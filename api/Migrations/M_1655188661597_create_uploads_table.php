<?php
use Nevs\Database;
class M_1655188661597_create_uploads_table 
{
    function migrate() : void
    {
        $DB = new Database();
        $DB->CreateTable(['name'=>'uploads', 'fields'=>[
            [
                'name' => 'id',
                'type' => 'int',
                'primary_key' => true,
                'auto_increment' => true
            ],
            [
                'name' => 'real_name',
                'type' => 'string'
            ],
            [
                'name' => 'original_name',
                'type' => 'string'
            ],
            [
                'name' => 'hash',
                'type' => 'string'
            ],
            [
                'name' => 'public',
                'type' => 'bool'
            ]
        ]]);
    }
}