<?php

namespace Modules\EMS\app\Http\Traits;

trait DateTrait
{
    // Define the Persian months as a constant
    protected static $persianMonths = [
        "فروردین",
        "اردیبهشت",
        "خرداد",
        "تیر",
        "مرداد",
        "شهریور",
        "مهر",
        "آبان",
        "آذر",
        "دی",
        "بهمن",
        "اسفند",
    ];

    // Convert month number to human-readable month name
    public function humanReadableDate($month)
    {
        // Validate input
        if ($month < 1 || $month > 12) {
            return "Invalid month number";
        }

        // Get the month name
        return self::$persianMonths[$month - 1]; // Subtract 1 to match array index
    }

    public function persianNumbersToEng(string $persianNumber)
    {
        $persianDigits = ['۰', '۰۱', '۰۲', '۰۳', '۰۴', '۰۵', '۰۶', '۰۷', '۰۸', '۰۹', '۱۰', '۱۱', '۱۲'];
        $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];

        $index = array_search($persianNumber, $persianDigits);
        return $englishDigits[$index];
    }


    public function removeLeftZero($number)
    {
        // Replace Persian ۰ with 0, then remove leading zeros
        $number = str_replace('۰', '0', $number);  // Convert Persian digits to Western digits
        return ltrim($number, '0');  // Remove leading zeros
    }
}
