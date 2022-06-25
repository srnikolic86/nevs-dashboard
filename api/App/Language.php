<?php

namespace App;

use App\Models\User;
use Nevs\Config;

class Language
{
    static function Get(string $key): string
    {
        $locale = User::Current() !== null ? User::Current()->locale : Config::Get('default_locale');
        $locale_file = Config::Get('app_root') . 'Languages/' . $locale . '.json';
        if (!file_exists($locale_file)) return $key;

        $key_array = explode('.', $key);
        $iterator = json_decode(file_get_contents($locale_file), true);

        foreach ($key_array as $sub_key) {
            if (!isset($iterator[$sub_key])) return $key;
            $iterator = $iterator[$sub_key];
        }

        return $iterator;
    }
}