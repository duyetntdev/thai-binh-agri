<?php

use Illuminate\Support\Number;

if (! function_exists('format_currency')) {
    /**
     * Format a number as Vietnamese Dong (VND).
     * e.g. 35000 → "35.000 ₫"
     */
    function format_currency(float|int|string $amount): string
    {
        return number_format((float) $amount, 0, ',', '.') . ' ₫';
    }
}

if (! function_exists('format_weight')) {
    /**
     * Format weight with unit.
     * e.g. format_weight(1.5, 'kg') → "1,5 kg"
     */
    function format_weight(float $value, string $unit = 'kg'): string
    {
        return number_format($value, 1, ',', '.') . ' ' . $unit;
    }
}

if (! function_exists('short_number')) {
    /**
     * Shorten large numbers for display.
     * e.g. 1500000 → "1,5 tr"
     */
    function short_number(float|int $number): string
    {
        if ($number >= 1_000_000) {
            return number_format($number / 1_000_000, 1, ',', '.') . ' tr';
        }

        if ($number >= 1_000) {
            return number_format($number / 1_000, 0, ',', '.') . ' nghìn';
        }

        return (string) $number;
    }
}
