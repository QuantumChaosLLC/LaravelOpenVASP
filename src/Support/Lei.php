<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Support;

final class Lei
{
    private function __construct() {}

    public static function isValid(string $lei): bool
    {
        if (! preg_match('/^[A-Z0-9]{18}[0-9]{2}$/', $lei)) {
            return false;
        }

        $expanded = '';
        foreach (str_split($lei) as $character) {
            $expanded .= ctype_alpha($character)
                ? (string) (ord($character) - 55)
                : $character;
        }

        $remainder = 0;
        foreach (str_split($expanded) as $digit) {
            $remainder = (($remainder * 10) + (int) $digit) % 97;
        }

        return $remainder === 1;
    }
}
