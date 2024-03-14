<?php

namespace App\Libraries;

class CurrencyFormatter
{
    public static function formatToIDR($number)
    {
        $formatted_number = number_format($number, 0, ',', '.');
        return $formatted_number;
    }
}