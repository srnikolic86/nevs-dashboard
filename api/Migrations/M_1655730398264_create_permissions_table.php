<?php
use Nevs\Database;
class M_1655730398264_create_permissions_table 
{
    function migrate() : void
    {
        $DB = new Database();
        $DB->CreateTable(['name'=>'permissions', 'fields'=>[
            [
                'name' => 'id',
                'type' => 'int',
                'primary_key' => true,
                'auto_increment' => true
            ],
            [
                'name' => 'name',
                'type' => 'string'
            ]
        ]]);
        $DB->Execute("INSERT INTO `permissions` (`name`) VALUES ('MANAGE_USERS')");
    }
}