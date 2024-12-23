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

        $parts = explode(' ', $number);

        $parts[0] = str_replace('۰', '0', $parts[0]);

        // If there's a time portion (i.e., there are more than 1 parts after the explode)
        if (count($parts) > 1) {
            // The first part is the day, remove leading zeros and replace with Persian zero
            $day = str_replace('0', '۰', ltrim($parts[0], '0'));

            // The second part is the time, keep it as it is
            $time = $parts[1];

            // Combine day and time in the desired format: "day ساعت time"
            return ['day' => $day, 'time' => $time];
        } else {
            // If there is no time portion, just remove leading zeros and replace with Persian zero
            return str_replace('0', '۰', ltrim($parts[0], '0'));
        }
    }


    public function DateformatToHumanReadbleJalali($date)
    {
        // Check if the date string contains time
        $dateTimeParts = explode(' ', $date);
        $datePart = $dateTimeParts[0];
        $timePart = isset($dateTimeParts[1]) ? $dateTimeParts[1] : null;

        // Split the date part by '/'
        $parts = explode('/', $datePart);
        $monthNumber = $parts[1]; // Get the second part as the month number
        $day = $parts[2];

        // For Month
        $eng = $this->persianNumbersToEng($monthNumber);
        $monthName = $this->humanReadableDate($eng);

        // For Day
        $daywithoutZero = $this->removeLeftZero($day);
        // Message text for date
        $humanReadableDate = "$daywithoutZero $monthName $parts[0]";

        // Append time part if it exists
        if ($timePart) {
            $humanReadableDate .= " ساعت $timePart";
        }

        return $humanReadableDate;
    }

    public function convertGregorianToJalali(string $gregorianDate)
    {
        // Convert the Gregorian date to a Jalali date
        $jalaliDate = \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($gregorianDate));

        // Convert numbers to Persian characters
        $persianCharJalaliDate = \Morilog\Jalali\CalendarUtils::convertNumbers($jalaliDate, false);

        return $persianCharJalaliDate;
    }

}
