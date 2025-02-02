<?php

use Morilog\Jalali\CalendarUtils;

function convertToDbFriendly($string)
{
    $replacements = [
        // Arabic to Persian (default)
        'ي' => 'ی',
        'ك' => 'ک',
        'ة' => 'ه',

        // Persian to Arabic
//        'ی' => 'ي',
//        'ک' => 'ك',
//        'ه' => 'ة',

        // Persian numbers to English numbers
        '۰' => '0',
        '۱' => '1',
        '۲' => '2',
        '۳' => '3',
        '۴' => '4', // Both '۴' and '٤' are replaced with '4'
        '٥' => '5',
        '٦' => '6',
        '۷' => '7',
        '۸' => '8',
        '۹' => '9',

        // English numbers to Persian numbers
//        '0' => '۰',
//        '1' => '۱',
//        '2' => '۲',
//        '3' => '۳',
//        '4' => '۴',
//        '5' => '٥',
//        '6' => '٦',
//        '7' => '۷',
//        '8' => '۸',
//        '9' => '۹',
    ];


    $combinedReplacements = array_merge($replacements, $replacements);

    return str_replace(array_keys($combinedReplacements), array_values($combinedReplacements), $string);
}

function convertJalaliPersianCharactersToGregorian(string $perisanCharDate)
{
    $englishJalaliDateString = CalendarUtils::convertNumbers($perisanCharDate, true);

    $dateTimeString = CalendarUtils::createCarbonFromFormat('Y/m/d', $englishJalaliDateString)
        ->toDateTimeString();

    return $dateTimeString;
}

function convertEnglishNumbersDate(string $englishNumbersDate)
{
    $dateTimeString = CalendarUtils::createCarbonFromFormat('Y/m/d', $englishNumbersDate)
        ->toDateTimeString();
    return $dateTimeString;
}

function convertDateTimeJalaliPersianCharactersToGregorian(string $persianCharDateTime)
{
    // Convert Persian numbers to English numbers
    $englishJalaliDateTimeString = CalendarUtils::convertNumbers($persianCharDateTime, true);

    // Parse the date and time from the Jalali format and convert to Gregorian
    $dateTimeString = CalendarUtils::createCarbonFromFormat('Y/m/d H:i:s', $englishJalaliDateTimeString)
        ->toDateTimeString();

    return $dateTimeString;
}


function convertDateTimeGregorianToJalaliDateTime(string $value)
{
    $jalali = CalendarUtils::strftime('Y/m/d H:i:s', strtotime($value)); // 1395-02-19
    $jalaliPersianNumbers = CalendarUtils::convertNumbers($jalali); // ۱۳۹۵-۰۲-۱۹
    return $jalaliPersianNumbers;
}

function convertDateTimeHaveDashJalaliPersianCharactersToGregorian(string $persianCharDateTime)
{
    // Convert Persian numbers to English numbers
    $englishJalaliDateTimeString = CalendarUtils::convertNumbers($persianCharDateTime, true);

//    dd($englishJalaliDateTimeString);
    // Parse the date and time from the Jalali format and convert to Gregorian
    $dateTimeString = CalendarUtils::createCarbonFromFormat('Y/m/d H:i:s', $englishJalaliDateTimeString)
        ->toDateTimeString();

    return $dateTimeString;
}

function convertGregorianToJalali(string $gregorianDate)
{
    // Convert the Gregorian date to a Jalali date
    $jalaliDate = CalendarUtils::strftime('Y/m/d', strtotime($gregorianDate));

    // Convert numbers to Persian characters
    $persianCharJalaliDate = CalendarUtils::convertNumbers($jalaliDate, false);

    return $persianCharJalaliDate;
}

function convertSecondToMinute($second)
{
    $minutes = floor($second / 60);
    $remainingSeconds = $second % 60;

    return "{$minutes}:{$remainingSeconds}";
}

function convertMinuteToSecondFormatted($time)
{
    list($minutes, $seconds) = explode(':', $time);

    $totalSeconds = ($minutes * 60) + $seconds;

    return $totalSeconds;
}

function getPersianMonths()
{
    return [
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
}

// Convert month number to human-readable month name
function humanReadableDate($month)
{
    $persianMonths = getPersianMonths();

    // Validate input
    if ($month < 1 || $month > 12) {
        return "Invalid month number";
    }

    // Get the month name
    return $persianMonths[$month - 1]; // Subtract 1 to match array index
}

function persianNumbersToEng(string $persianNumber)
{
    $persianDigits = ['۰', '۰۱', '۰۲', '۰۳', '۰۴', '۰۵', '۰۶', '۰۷', '۰۸', '۰۹', '۱۰', '۱۱', '۱۲'];
    $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];

    $index = array_search($persianNumber, $persianDigits);
    return $englishDigits[$index];
}

function removeLeftZero($number)
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

function DateformatToHumanReadableJalali($date, $showClock = true)
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
    $eng = persianNumbersToEng($monthNumber);
    $monthName = humanReadableDate($eng);

    // For Day
    $daywithoutZero = removeLeftZero($day);

    // Message text for date
    $humanReadableDate = "$daywithoutZero $monthName $parts[0]";

    // Append time part if it exists and $showClock is true
    if ($showClock && $timePart) {
        $humanReadableDate .= " ساعت $timePart";
    }

    return $humanReadableDate;
}

function convertPersianToGregorianBothHaveTimeAndDont($persianCharDateTime)
{
    if ($persianCharDateTime == null || empty($persianCharDateTime)) {
        return null;
    }
    // Convert Persian numbers to English numbers
    $englishJalaliDateTimeString = CalendarUtils::convertNumbers($persianCharDateTime, true);

    // Check if the input string contains time information
    $hasTime = strpos($englishJalaliDateTimeString, ':') !== false;

    // Define the format based on the presence of time
    $format = $hasTime ? 'Y/m/d H:i:s' : 'Y/m/d';

    // Parse the date and time from the Jalali format and convert to Gregorian
    $dateTime = CalendarUtils::createCarbonFromFormat($format, $englishJalaliDateTimeString);

    // Return the datetime string, including the time if it was provided
    return $hasTime ? $dateTime->toDateTimeString() : $dateTime->toDateString();
}

function convertDateTimeGregorianToJalaliDateTimeButWithoutTime(string $value)
{
    // Convert to Jalali with time (H:i:s)
    $jalali = CalendarUtils::strftime('Y/m/d H:i:s', strtotime($value)); // 1395-02-19 12:30:45
    $jalaliPersianNumbers = CalendarUtils::convertNumbers($jalali); // ۱۳۹۵-۰۲-۱۹ ۱۲:۳۰:۴۵

    $dateOnly = substr($jalaliPersianNumbers, 0, 10); // ۱۳۹۵-۰۲-۱۹

    return $dateOnly;
}




