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
}
