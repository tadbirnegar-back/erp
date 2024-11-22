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

