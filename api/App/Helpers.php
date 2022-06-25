<?php

namespace App;

class Helpers
{
    static function CheckEmail(string $email): bool
    {
        return (filter_var($email, FILTER_VALIDATE_EMAIL)) ;
    }
}