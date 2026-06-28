<?php

declare(strict_types=1);

namespace terpz710\kdrpe\utils;

class Random {

    /**
     *
     * Sonion...
     *
    **/
    public static function generateRandomNumber(?int $number1 = null, ?int $number2 = null) : int{
        if ($number1 === null || $number2 === null) {
            return rand();
        }

        return rand($number1, $number2);
    }
}