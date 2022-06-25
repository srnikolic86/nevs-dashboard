<?php

namespace App\Models;

use Nevs\Model;

class Permission extends Model
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'permissions';
    }
}