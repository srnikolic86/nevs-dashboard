<?php

namespace App\Models;

use Nevs\Model;

class Upload extends Model
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'uploads';
    }
}