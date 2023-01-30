<?php

namespace App\Helper;

class StringHelper
{
    public static function camelCaseToSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    public static function getClassName(string $class): string
    {
        $str = strrchr($class, "\\");
        if (false === $str) {
            return '';
        }
        return substr($str, 1);
    }
}