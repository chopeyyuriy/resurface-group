<?php


namespace App\Helpers;


class DateFormat
{
    public static function getter(string $value): string
    {
        return date('m/d/Y', strtotime($value));
    }

    public static function setter(string $value): string
    {
        return date('Y-m-d', strtotime($value));
    }
}
