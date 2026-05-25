<?php

namespace App\Support;

class Money
{
    public static function format(int|float|string|null $amount, bool $withSign = false): string
    {
        $value = (float) ($amount ?? 0);
        $formatted = 'KSh ' . number_format(abs($value), 2);

        if ($withSign) {
            return ($value >= 0 ? '+' : '−') . $formatted;
        }
        return $formatted;
    }

    public static function short(int|float|string|null $amount): string
    {
        $value = (float) ($amount ?? 0);
        return 'KSh ' . number_format($value, 0);
    }
}
