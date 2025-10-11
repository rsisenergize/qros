<?php

namespace App\Helper;

class Common
{

    public static function safeString($string)
    {
        if (empty($string) || $string == null) {
            return '';
        }

        if (!is_string($string) || strlen($string) > 255) {
            abort(400, 'Invalid search term.');
        }
        return $string;
    }
}
