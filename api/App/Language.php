<?php

namespace App;

use App\Models\User;
use Nevs\Config;

class Language
{
    // Per-request caches. Resolving the current user costs a DB round-trip and every lookup used to re-read the
    // language file, which made Language::Get very slow when called once per row in large lists.
    private static ?string $current_locale = null;
    private static array $translations = [];

    static function Get(string $key): string
    {
        if (self::$current_locale === null) {
            self::$current_locale = User::Current() !== null ? User::Current()->locale : Config::Get('default_locale');
        }
        $locale = self::$current_locale;

        if (!array_key_exists($locale, self::$translations)) {
            $locale_file = Config::Get('app_root') . 'Languages/' . $locale . '.json';
            self::$translations[$locale] = file_exists($locale_file) ? json_decode(file_get_contents($locale_file), true) : null;
        }
        if (self::$translations[$locale] === null) return $key;

        $iterator = self::$translations[$locale];
        foreach (explode('.', $key) as $sub_key) {
            if (!isset($iterator[$sub_key])) return $key;
            $iterator = $iterator[$sub_key];
        }

        return $iterator;
    }
}
