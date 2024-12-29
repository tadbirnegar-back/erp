<?php
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
    $englishJalaliDateString = \Morilog\Jalali\CalendarUtils::convertNumbers($perisanCharDate, true);

    $dateTimeString = \Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d', $englishJalaliDateString)
        ->toDateTimeString();

    return $dateTimeString;
}

function convertEnglishNumbersDate(string $englishNumbersDate)
{
    $dateTimeString = \Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d', $englishNumbersDate)
        ->toDateTimeString();
    return $dateTimeString;
}

function convertDateTimeJalaliPersianCharactersToGregorian(string $persianCharDateTime)
{
    // Convert Persian numbers to English numbers
    $englishJalaliDateTimeString = \Morilog\Jalali\CalendarUtils::convertNumbers($persianCharDateTime, true);

    // Parse the date and time from the Jalali format and convert to Gregorian
    $dateTimeString = \Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d H:i:s', $englishJalaliDateTimeString)
        ->toDateTimeString();

    return $dateTimeString;
}


function convertDateTimeGregorianToJalaliDateTime(string $value)
{
    $jalali = \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($value)); // 1395-02-19
    $jalaliPersianNumbers = \Morilog\Jalali\CalendarUtils::convertNumbers($jalali); // ۱۳۹۵-۰۲-۱۹
    return $jalaliPersianNumbers;
}

function convertDateTimeHaveDashJalaliPersianCharactersToGregorian(string $persianCharDateTime)
{
    // Convert Persian numbers to English numbers
    $englishJalaliDateTimeString = \Morilog\Jalali\CalendarUtils::convertNumbers($persianCharDateTime, true);

//    dd($englishJalaliDateTimeString);
    // Parse the date and time from the Jalali format and convert to Gregorian
    $dateTimeString = \Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d H:i:s', $englishJalaliDateTimeString)
        ->toDateTimeString();

    return $dateTimeString;
}

function convertGregorianToJalali(string $gregorianDate)
{
    // Convert the Gregorian date to a Jalali date
    $jalaliDate = \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($gregorianDate));

    // Convert numbers to Persian characters
    $persianCharJalaliDate = \Morilog\Jalali\CalendarUtils::convertNumbers($jalaliDate, false);

    return $persianCharJalaliDate;
}

function convertSecondToMinute($second)
{
    $minutes = floor($second / 60);
    $remainingSeconds = $second % 60;

    return "{$minutes}:{$remainingSeconds}";
}
