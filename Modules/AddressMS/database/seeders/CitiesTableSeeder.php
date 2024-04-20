<?php

namespace Modules\AddressMS\database\seeders;

use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('cities')->delete();

        \DB::table('cities')->insert(array (
            0 =>
            array (
                'id' => '1',
                'name' => 'آبادان',
                'amar_code' => '601',
                'state_id' => '13',
            ),
            1 =>
            array (
                'id' => '2',
                'name' => 'آباده',
                'amar_code' => '701',
                'state_id' => '17',
            ),
            2 =>
            array (
                'id' => '3',
                'name' => 'آبدانان',
                'amar_code' => '1606',
                'state_id' => '6',
            ),
            3 =>
            array (
                'id' => '4',
                'name' => 'آبیک',
                'amar_code' => '2604',
                'state_id' => '18',
            ),
            4 =>
            array (
                'id' => '5',
                'name' => 'آذرشهر',
                'amar_code' => '321',
                'state_id' => '1',
            ),
            5 =>
            array (
                'id' => '6',
                'name' => 'آرادان',
                'amar_code' => '2006',
                'state_id' => '15',
            ),
            6 =>
            array (
                'id' => '7',
                'name' => 'آران وبیدگل',
                'amar_code' => '1018',
                'state_id' => '4',
            ),
            7 =>
            array (
                'id' => '8',
                'name' => 'آزادشهر',
                'amar_code' => '2710',
                'state_id' => '24',
            ),
            8 =>
            array (
                'id' => '9',
                'name' => 'آستارا',
                'amar_code' => '101',
                'state_id' => '25',
            ),
            9 =>
            array (
                'id' => '10',
                'name' => 'آستانه اشرفیه',
                'amar_code' => '102',
                'state_id' => '25',
            ),
            10 =>
            array (
                'id' => '11',
                'name' => 'آشتیان',
                'amar_code' => '2',
                'state_id' => '28',
            ),
            11 =>
            array (
                'id' => '12',
                'name' => 'آغاجاری',
                'amar_code' => '626',
                'state_id' => '13',
            ),
            12 =>
            array (
                'id' => '13',
                'name' => 'آق قلا',
                'amar_code' => '2708',
                'state_id' => '24',
            ),
            13 =>
            array (
                'id' => '14',
                'name' => 'آمل',
                'amar_code' => '201',
                'state_id' => '27',
            ),
            14 =>
            array (
                'id' => '15',
                'name' => 'آوج',
                'amar_code' => '2606',
                'state_id' => '18',
            ),
            15 =>
            array (
                'id' => '16',
                'name' => 'ابرکوه',
                'amar_code' => '2107',
                'state_id' => '31',
            ),
            16 =>
            array (
                'id' => '17',
                'name' => 'ابوموسی',
                'amar_code' => '2201',
                'state_id' => '29',
            ),
            17 =>
            array (
                'id' => '18',
                'name' => 'ابهر',
                'amar_code' => '1901',
                'state_id' => '14',
            ),
            18 =>
            array (
                'id' => '19',
                'name' => 'اراک',
                'amar_code' => '1',
                'state_id' => '28',
            ),
            19 =>
            array (
                'id' => '20',
                'name' => 'اردبیل',
                'amar_code' => '2401',
                'state_id' => '3',
            ),
            20 =>
            array (
                'id' => '21',
                'name' => 'اردستان',
                'amar_code' => '1001',
                'state_id' => '4',
            ),
            21 =>
            array (
                'id' => '22',
                'name' => 'اردکان',
                'amar_code' => '2101',
                'state_id' => '31',
            ),
            22 =>
            array (
                'id' => '23',
                'name' => 'اردل',
                'amar_code' => '1405',
                'state_id' => '9',
            ),
            23 =>
            array (
                'id' => '24',
                'name' => 'ارزوییه',
                'amar_code' => '823',
                'state_id' => '21',
            ),
            24 =>
            array (
                'id' => '25',
                'name' => 'ارسنجان',
                'amar_code' => '717',
                'state_id' => '17',
            ),
            25 =>
            array (
                'id' => '26',
                'name' => 'ارومیه',
                'amar_code' => '401',
                'state_id' => '2',
            ),
            26 =>
            array (
                'id' => '27',
                'name' => 'ازنا',
                'amar_code' => '1507',
                'state_id' => '26',
            ),
            27 =>
            array (
                'id' => '28',
                'name' => 'استهبان',
                'amar_code' => '702',
                'state_id' => '17',
            ),
            28 =>
            array (
                'id' => '29',
                'name' => 'اسدآباد',
                'amar_code' => '1306',
                'state_id' => '30',
            ),
            29 =>
            array (
                'id' => '30',
                'name' => 'اسفراین',
                'amar_code' => '2801',
                'state_id' => '12',
            ),
            30 =>
            array (
                'id' => '31',
                'name' => 'اسکو',
                'amar_code' => '322',
                'state_id' => '1',
            ),
            31 =>
            array (
                'id' => '32',
                'name' => 'اسلام آبادغرب',
                'amar_code' => '501',
                'state_id' => '22',
            ),
            32 =>
            array (
                'id' => '33',
                'name' => 'اسلامشهر',
                'amar_code' => '2310',
                'state_id' => '8',
            ),
            33 =>
            array (
                'id' => '34',
                'name' => 'اشتهارد',
                'amar_code' => '3005',
                'state_id' => '5',
            ),
            34 =>
            array (
                'id' => '35',
                'name' => 'اشکذر',
                'amar_code' => '2108',
                'state_id' => '31',
            ),
            35 =>
            array (
                'id' => '36',
                'name' => 'اشنویه',
                'amar_code' => '413',
                'state_id' => '2',
            ),
            36 =>
            array (
                'id' => '37',
                'name' => 'اصفهان',
                'amar_code' => '1002',
                'state_id' => '4',
            ),
            37 =>
            array (
                'id' => '38',
                'name' => 'اصلاندوز',
                'amar_code' => '2411',
                'state_id' => '3',
            ),
            38 =>
            array (
                'id' => '39',
                'name' => 'اقلید',
                'amar_code' => '703',
                'state_id' => '17',
            ),
            39 =>
            array (
                'id' => '40',
                'name' => 'البرز',
                'amar_code' => '2605',
                'state_id' => '18',
            ),
            40 =>
            array (
                'id' => '41',
                'name' => 'الیگودرز',
                'amar_code' => '1501',
                'state_id' => '26',
            ),
            41 =>
            array (
                'id' => '42',
                'name' => 'املش',
                'amar_code' => '113',
                'state_id' => '25',
            ),
            42 =>
            array (
                'id' => '43',
                'name' => 'امیدیه',
                'amar_code' => '616',
                'state_id' => '13',
            ),
            43 =>
            array (
                'id' => '44',
                'name' => 'انار',
                'amar_code' => '820',
                'state_id' => '21',
            ),
            44 =>
            array (
                'id' => '45',
                'name' => 'اندیکا',
                'amar_code' => '621',
                'state_id' => '13',
            ),
            45 =>
            array (
                'id' => '46',
                'name' => 'اندیمشک',
                'amar_code' => '602',
                'state_id' => '13',
            ),
            46 =>
            array (
                'id' => '47',
                'name' => 'اوز',
                'amar_code' => '736',
                'state_id' => '17',
            ),
            47 =>
            array (
                'id' => '48',
                'name' => 'اهر',
                'amar_code' => '302',
                'state_id' => '1',
            ),
            48 =>
            array (
                'id' => '49',
                'name' => 'اهواز',
                'amar_code' => '603',
                'state_id' => '13',
            ),
            49 =>
            array (
                'id' => '50',
                'name' => 'ایجرود',
                'amar_code' => '1906',
                'state_id' => '14',
            ),
            50 =>
            array (
                'id' => '51',
                'name' => 'ایذه',
                'amar_code' => '604',
                'state_id' => '13',
            ),
            51 =>
            array (
                'id' => '52',
                'name' => 'ایرانشهر',
                'amar_code' => '1101',
                'state_id' => '16',
            ),
            52 =>
            array (
                'id' => '53',
                'name' => 'ایلام',
                'amar_code' => '1601',
                'state_id' => '6',
            ),
            53 =>
            array (
                'id' => '54',
                'name' => 'ایوان',
                'amar_code' => '1607',
                'state_id' => '6',
            ),
            54 =>
            array (
                'id' => '55',
                'name' => 'بابل',
                'amar_code' => '202',
                'state_id' => '27',
            ),
            55 =>
            array (
                'id' => '56',
                'name' => 'بابلسر',
                'amar_code' => '216',
                'state_id' => '27',
            ),
            56 =>
            array (
                'id' => '57',
                'name' => 'باخرز',
                'amar_code' => '937',
                'state_id' => '11',
            ),
            57 =>
            array (
                'id' => '58',
                'name' => 'باشت',
                'amar_code' => '1707',
                'state_id' => '23',
            ),
            58 =>
            array (
                'id' => '59',
                'name' => 'باغ ملک',
                'amar_code' => '615',
                'state_id' => '13',
            ),
            59 =>
            array (
                'id' => '60',
                'name' => 'بافت',
                'amar_code' => '801',
                'state_id' => '21',
            ),
            60 =>
            array (
                'id' => '61',
                'name' => 'بافق',
                'amar_code' => '2102',
                'state_id' => '31',
            ),
            61 =>
            array (
                'id' => '62',
                'name' => 'بانه',
                'amar_code' => '1201',
                'state_id' => '20',
            ),
            62 =>
            array (
                'id' => '63',
                'name' => 'باوی',
                'amar_code' => '624',
                'state_id' => '13',
            ),
            63 =>
            array (
                'id' => '64',
                'name' => 'بجستان',
                'amar_code' => '931',
                'state_id' => '11',
            ),
            64 =>
            array (
                'id' => '65',
                'name' => 'بجنورد',
                'amar_code' => '2802',
                'state_id' => '12',
            ),
            65 =>
            array (
                'id' => '66',
                'name' => 'بختگان',
                'amar_code' => '735',
                'state_id' => '17',
            ),
            66 =>
            array (
                'id' => '67',
                'name' => 'بدره',
                'amar_code' => '1610',
                'state_id' => '6',
            ),
            67 =>
            array (
                'id' => '68',
                'name' => 'برخوار',
                'amar_code' => '1022',
                'state_id' => '4',
            ),
            68 =>
            array (
                'id' => '69',
                'name' => 'بردسکن',
                'amar_code' => '923',
                'state_id' => '11',
            ),
            69 =>
            array (
                'id' => '70',
                'name' => 'بردسیر',
                'amar_code' => '810',
                'state_id' => '21',
            ),
            70 =>
            array (
                'id' => '71',
                'name' => 'بروجرد',
                'amar_code' => '1502',
                'state_id' => '26',
            ),
            71 =>
            array (
                'id' => '72',
                'name' => 'بروجن',
                'amar_code' => '1401',
                'state_id' => '9',
            ),
            72 =>
            array (
                'id' => '73',
                'name' => 'بستان آباد',
                'amar_code' => '313',
                'state_id' => '1',
            ),
            73 =>
            array (
                'id' => '74',
                'name' => 'بستک',
                'amar_code' => '2209',
                'state_id' => '29',
            ),
            74 =>
            array (
                'id' => '75',
                'name' => 'بشاگرد',
                'amar_code' => '2213',
                'state_id' => '29',
            ),
            75 =>
            array (
                'id' => '76',
                'name' => 'بشرویه',
                'amar_code' => '2908',
                'state_id' => '10',
            ),
            76 =>
            array (
                'id' => '77',
                'name' => 'بم',
                'amar_code' => '802',
                'state_id' => '21',
            ),
            77 =>
            array (
                'id' => '78',
                'name' => 'بمپور',
                'amar_code' => '1120',
                'state_id' => '16',
            ),
            78 =>
            array (
                'id' => '79',
                'name' => 'بن',
                'amar_code' => '1409',
                'state_id' => '9',
            ),
            79 =>
            array (
                'id' => '80',
                'name' => 'بناب',
                'amar_code' => '312',
                'state_id' => '1',
            ),
            80 =>
            array (
                'id' => '81',
                'name' => 'بندرانزلی',
                'amar_code' => '103',
                'state_id' => '25',
            ),
            81 =>
            array (
                'id' => '82',
                'name' => 'بندرعباس',
                'amar_code' => '2202',
                'state_id' => '29',
            ),
            82 =>
            array (
                'id' => '83',
                'name' => 'بندرگز',
                'amar_code' => '2701',
                'state_id' => '24',
            ),
            83 =>
            array (
                'id' => '84',
                'name' => 'بندرلنگه',
                'amar_code' => '2203',
                'state_id' => '29',
            ),
            84 =>
            array (
                'id' => '85',
                'name' => 'بندرماهشهر',
                'amar_code' => '605',
                'state_id' => '13',
            ),
            85 =>
            array (
                'id' => '86',
                'name' => 'بو یین و میاندشت',
                'amar_code' => '1024',
                'state_id' => '4',
            ),
            86 =>
            array (
                'id' => '87',
                'name' => 'بوانات',
                'amar_code' => '716',
                'state_id' => '17',
            ),
            87 =>
            array (
                'id' => '88',
                'name' => 'بوشهر',
                'amar_code' => '1801',
                'state_id' => '7',
            ),
            88 =>
            array (
                'id' => '89',
                'name' => 'بوکان',
                'amar_code' => '410',
                'state_id' => '2',
            ),
            89 =>
            array (
                'id' => '90',
                'name' => 'بویراحمد',
                'amar_code' => '1701',
                'state_id' => '23',
            ),
            90 =>
            array (
                'id' => '91',
                'name' => 'بویین زهرا',
                'amar_code' => '2601',
                'state_id' => '18',
            ),
            91 =>
            array (
                'id' => '92',
                'name' => 'بهاباد',
                'amar_code' => '2111',
                'state_id' => '31',
            ),
            92 =>
            array (
                'id' => '93',
                'name' => 'بهار',
                'amar_code' => '1307',
                'state_id' => '30',
            ),
            93 =>
            array (
                'id' => '94',
                'name' => 'بهارستان',
                'amar_code' => '2319',
                'state_id' => '8',
            ),
            94 =>
            array (
                'id' => '95',
                'name' => 'بهبهان',
                'amar_code' => '606',
                'state_id' => '13',
            ),
            95 =>
            array (
                'id' => '96',
                'name' => 'بهشهر',
                'amar_code' => '204',
                'state_id' => '27',
            ),
            96 =>
            array (
                'id' => '97',
                'name' => 'بهمیی',
                'amar_code' => '1705',
                'state_id' => '23',
            ),
            97 =>
            array (
                'id' => '98',
                'name' => 'بیجار',
                'amar_code' => '1202',
                'state_id' => '20',
            ),
            98 =>
            array (
                'id' => '99',
                'name' => 'بیرجند',
                'amar_code' => '2901',
                'state_id' => '10',
            ),
            99 =>
            array (
                'id' => '100',
                'name' => 'بیضا',
                'amar_code' => '731',
                'state_id' => '17',
            ),
            100 =>
            array (
                'id' => '101',
                'name' => 'بیله سوار',
                'amar_code' => '2402',
                'state_id' => '3',
            ),
            101 =>
            array (
                'id' => '102',
                'name' => 'بینالود',
                'amar_code' => '932',
                'state_id' => '11',
            ),
            102 =>
            array (
                'id' => '103',
                'name' => 'پارس آباد',
                'amar_code' => '2406',
                'state_id' => '3',
            ),
            103 =>
            array (
                'id' => '104',
                'name' => 'پارسیان',
                'amar_code' => '2211',
                'state_id' => '29',
            ),
            104 =>
            array (
                'id' => '105',
                'name' => 'پاسارگاد',
                'amar_code' => '723',
                'state_id' => '17',
            ),
            105 =>
            array (
                'id' => '106',
                'name' => 'پاکدشت',
                'amar_code' => '2313',
                'state_id' => '8',
            ),
            106 =>
            array (
                'id' => '107',
                'name' => 'پاوه',
                'amar_code' => '503',
                'state_id' => '22',
            ),
            107 =>
            array (
                'id' => '108',
                'name' => 'پردیس',
                'amar_code' => '2320',
                'state_id' => '8',
            ),
            108 =>
            array (
                'id' => '109',
                'name' => 'پلدختر',
                'amar_code' => '1508',
                'state_id' => '26',
            ),
            109 =>
            array (
                'id' => '110',
                'name' => 'پلدشت',
                'amar_code' => '415',
                'state_id' => '2',
            ),
            110 =>
            array (
                'id' => '111',
                'name' => 'پیرانشهر',
                'amar_code' => '402',
                'state_id' => '2',
            ),
            111 =>
            array (
                'id' => '112',
                'name' => 'پیشوا',
                'amar_code' => '2318',
                'state_id' => '8',
            ),
            112 =>
            array (
                'id' => '113',
                'name' => 'تاکستان',
                'amar_code' => '2602',
                'state_id' => '18',
            ),
            113 =>
            array (
                'id' => '114',
                'name' => 'تایباد',
                'amar_code' => '904',
                'state_id' => '11',
            ),
            114 =>
            array (
                'id' => '115',
                'name' => 'تبریز',
                'amar_code' => '303',
                'state_id' => '1',
            ),
            115 =>
            array (
                'id' => '116',
                'name' => 'تربت جام',
                'amar_code' => '906',
                'state_id' => '11',
            ),
            116 =>
            array (
                'id' => '117',
                'name' => 'تربت حیدریه',
                'amar_code' => '905',
                'state_id' => '11',
            ),
            117 =>
            array (
                'id' => '118',
                'name' => 'ترکمن',
                'amar_code' => '2702',
                'state_id' => '24',
            ),
            118 =>
            array (
                'id' => '119',
                'name' => 'تفت',
                'amar_code' => '2103',
                'state_id' => '31',
            ),
            119 =>
            array (
                'id' => '120',
                'name' => 'تفتان',
                'amar_code' => '1121',
                'state_id' => '16',
            ),
            120 =>
            array (
                'id' => '121',
                'name' => 'تفرش',
                'amar_code' => '3',
                'state_id' => '28',
            ),
            121 =>
            array (
                'id' => '122',
                'name' => 'تکاب',
                'amar_code' => '412',
                'state_id' => '2',
            ),
            122 =>
            array (
                'id' => '123',
                'name' => 'تنکابن',
                'amar_code' => '205',
                'state_id' => '27',
            ),
            123 =>
            array (
                'id' => '124',
                'name' => 'تنگستان',
                'amar_code' => '1802',
                'state_id' => '7',
            ),
            124 =>
            array (
                'id' => '125',
                'name' => 'تویسرکان',
                'amar_code' => '1301',
                'state_id' => '30',
            ),
            125 =>
            array (
                'id' => '126',
                'name' => 'تهران',
                'amar_code' => '2301',
                'state_id' => '8',
            ),
            126 =>
            array (
                'id' => '127',
                'name' => 'تیران وکرون',
                'amar_code' => '1019',
                'state_id' => '4',
            ),
            127 =>
            array (
                'id' => '128',
                'name' => 'ثلاث باباجانی',
                'amar_code' => '512',
                'state_id' => '22',
            ),
            128 =>
            array (
                'id' => '129',
                'name' => 'جاجرم',
                'amar_code' => '2803',
                'state_id' => '12',
            ),
            129 =>
            array (
                'id' => '130',
                'name' => 'جاسک',
                'amar_code' => '2206',
                'state_id' => '29',
            ),
            130 =>
            array (
                'id' => '131',
                'name' => 'جغتای',
                'amar_code' => '934',
                'state_id' => '11',
            ),
            131 =>
            array (
                'id' => '132',
                'name' => 'جلفا',
                'amar_code' => '319',
                'state_id' => '1',
            ),
            132 =>
            array (
                'id' => '133',
                'name' => 'جم',
                'amar_code' => '1809',
                'state_id' => '7',
            ),
            133 =>
            array (
                'id' => '134',
                'name' => 'جوانرود',
                'amar_code' => '509',
                'state_id' => '22',
            ),
            134 =>
            array (
                'id' => '135',
                'name' => 'جویبار',
                'amar_code' => '221',
                'state_id' => '27',
            ),
            135 =>
            array (
                'id' => '136',
                'name' => 'جوین',
                'amar_code' => '936',
                'state_id' => '11',
            ),
            136 =>
            array (
                'id' => '137',
                'name' => 'جهرم',
                'amar_code' => '704',
                'state_id' => '17',
            ),
            137 =>
            array (
                'id' => '138',
                'name' => 'جیرفت',
                'amar_code' => '803',
                'state_id' => '21',
            ),
            138 =>
            array (
                'id' => '139',
                'name' => 'چادگان',
                'amar_code' => '1020',
                'state_id' => '4',
            ),
            139 =>
            array (
                'id' => '140',
                'name' => 'چاراویماق',
                'amar_code' => '323',
                'state_id' => '1',
            ),
            140 =>
            array (
                'id' => '141',
                'name' => 'چالدران',
                'amar_code' => '414',
                'state_id' => '2',
            ),
            141 =>
            array (
                'id' => '142',
                'name' => 'چالوس',
                'amar_code' => '220',
                'state_id' => '27',
            ),
            142 =>
            array (
                'id' => '143',
                'name' => 'چاه بهار',
                'amar_code' => '1102',
                'state_id' => '16',
            ),
            143 =>
            array (
                'id' => '144',
                'name' => 'چایپاره',
                'amar_code' => '416',
                'state_id' => '2',
            ),
            144 =>
            array (
                'id' => '145',
                'name' => 'چرام',
                'amar_code' => '1706',
                'state_id' => '23',
            ),
            145 =>
            array (
                'id' => '146',
                'name' => 'چرداول',
                'amar_code' => '1604',
                'state_id' => '6',
            ),
            146 =>
            array (
                'id' => '147',
                'name' => 'چگنی',
                'amar_code' => '1510',
                'state_id' => '26',
            ),
            147 =>
            array (
                'id' => '148',
                'name' => 'چناران',
                'amar_code' => '918',
                'state_id' => '11',
            ),
            148 =>
            array (
                'id' => '149',
                'name' => 'حاجی اباد',
                'amar_code' => '2208',
                'state_id' => '29',
            ),
            149 =>
            array (
                'id' => '150',
                'name' => 'حمیدیه',
                'amar_code' => '625',
                'state_id' => '13',
            ),
            150 =>
            array (
                'id' => '151',
                'name' => 'خاتم',
                'amar_code' => '2109',
                'state_id' => '31',
            ),
            151 =>
            array (
                'id' => '152',
                'name' => 'خاش',
                'amar_code' => '1103',
                'state_id' => '16',
            ),
            152 =>
            array (
                'id' => '153',
                'name' => 'خانمیرزا',
                'amar_code' => '1410',
                'state_id' => '9',
            ),
            153 =>
            array (
                'id' => '154',
                'name' => 'خداآفرین',
                'amar_code' => '326',
                'state_id' => '1',
            ),
            154 =>
            array (
                'id' => '155',
                'name' => 'خدابنده',
                'amar_code' => '1903',
                'state_id' => '14',
            ),
            155 =>
            array (
                'id' => '156',
                'name' => 'خرامه',
                'amar_code' => '729',
                'state_id' => '17',
            ),
            156 =>
            array (
                'id' => '157',
                'name' => 'خرم آباد',
                'amar_code' => '1503',
                'state_id' => '26',
            ),
            157 =>
            array (
                'id' => '158',
                'name' => 'خرم بید',
                'amar_code' => '718',
                'state_id' => '17',
            ),
            158 =>
            array (
                'id' => '159',
                'name' => 'خرمدره',
                'amar_code' => '1907',
                'state_id' => '14',
            ),
            159 =>
            array (
                'id' => '160',
                'name' => 'خرمشهر',
                'amar_code' => '607',
                'state_id' => '13',
            ),
            160 =>
            array (
                'id' => '161',
                'name' => 'خفر',
                'amar_code' => '734',
                'state_id' => '17',
            ),
            161 =>
            array (
                'id' => '162',
                'name' => 'خلخال',
                'amar_code' => '2403',
                'state_id' => '3',
            ),
            162 =>
            array (
                'id' => '163',
                'name' => 'خلیل آباد',
                'amar_code' => '929',
                'state_id' => '11',
            ),
            163 =>
            array (
                'id' => '164',
                'name' => 'خمیر',
                'amar_code' => '2210',
                'state_id' => '29',
            ),
            164 =>
            array (
                'id' => '165',
                'name' => 'خمین',
                'amar_code' => '4',
                'state_id' => '28',
            ),
            165 =>
            array (
                'id' => '166',
                'name' => 'خمینی شهر',
                'amar_code' => '1003',
                'state_id' => '4',
            ),
            166 =>
            array (
                'id' => '167',
                'name' => 'خنج',
                'amar_code' => '724',
                'state_id' => '17',
            ),
            167 =>
            array (
                'id' => '168',
                'name' => 'خنداب',
                'amar_code' => '12',
                'state_id' => '28',
            ),
            168 =>
            array (
                'id' => '169',
                'name' => 'خواف',
                'amar_code' => '919',
                'state_id' => '11',
            ),
            169 =>
            array (
                'id' => '170',
                'name' => 'خوانسار',
                'amar_code' => '1004',
                'state_id' => '4',
            ),
            170 =>
            array (
                'id' => '171',
                'name' => 'خور و بیابانک',
                'amar_code' => '1023',
                'state_id' => '4',
            ),
            171 =>
            array (
                'id' => '172',
                'name' => 'خوسف',
                'amar_code' => '2910',
                'state_id' => '10',
            ),
            172 =>
            array (
                'id' => '173',
                'name' => 'خوشاب',
                'amar_code' => '938',
                'state_id' => '11',
            ),
            173 =>
            array (
                'id' => '174',
                'name' => 'خوی',
                'amar_code' => '403',
                'state_id' => '2',
            ),
            174 =>
            array (
                'id' => '175',
                'name' => 'داراب',
                'amar_code' => '705',
                'state_id' => '17',
            ),
            175 =>
            array (
                'id' => '176',
                'name' => 'دالاهو',
                'amar_code' => '513',
                'state_id' => '22',
            ),
            176 =>
            array (
                'id' => '177',
                'name' => 'دامغان',
                'amar_code' => '2001',
                'state_id' => '15',
            ),
            177 =>
            array (
                'id' => '178',
                'name' => 'داورزن',
                'amar_code' => '939',
                'state_id' => '11',
            ),
            178 =>
            array (
                'id' => '179',
                'name' => 'درگز',
                'amar_code' => '907',
                'state_id' => '11',
            ),
            179 =>
            array (
                'id' => '180',
                'name' => 'درگزین',
                'amar_code' => '1310',
                'state_id' => '30',
            ),
            180 =>
            array (
                'id' => '181',
                'name' => 'درمیان',
                'amar_code' => '2902',
                'state_id' => '10',
            ),
            181 =>
            array (
                'id' => '182',
                'name' => 'دره شهر',
                'amar_code' => '1602',
                'state_id' => '6',
            ),
            182 =>
            array (
                'id' => '183',
                'name' => 'دزفول',
                'amar_code' => '608',
                'state_id' => '13',
            ),
            183 =>
            array (
                'id' => '184',
                'name' => 'دشت آزادگان',
                'amar_code' => '609',
                'state_id' => '13',
            ),
            184 =>
            array (
                'id' => '185',
                'name' => 'دشتستان',
                'amar_code' => '1803',
                'state_id' => '7',
            ),
            185 =>
            array (
                'id' => '186',
                'name' => 'دشتی',
                'amar_code' => '1804',
                'state_id' => '7',
            ),
            186 =>
            array (
                'id' => '187',
                'name' => 'دشتیاری',
                'amar_code' => '1122',
                'state_id' => '16',
            ),
            187 =>
            array (
                'id' => '188',
                'name' => 'دلفان',
                'amar_code' => '1504',
                'state_id' => '26',
            ),
            188 =>
            array (
                'id' => '189',
                'name' => 'دلگان',
                'amar_code' => '1112',
                'state_id' => '16',
            ),
            189 =>
            array (
                'id' => '190',
                'name' => 'دلیجان',
                'amar_code' => '5',
                'state_id' => '28',
            ),
            190 =>
            array (
                'id' => '191',
                'name' => 'دماوند',
                'amar_code' => '2302',
                'state_id' => '8',
            ),
            191 =>
            array (
                'id' => '192',
                'name' => 'دنا',
                'amar_code' => '1704',
                'state_id' => '23',
            ),
            192 =>
            array (
                'id' => '193',
                'name' => 'دورود',
                'amar_code' => '1505',
                'state_id' => '26',
            ),
            193 =>
            array (
                'id' => '194',
                'name' => 'دهاقان',
                'amar_code' => '1021',
                'state_id' => '4',
            ),
            194 =>
            array (
                'id' => '195',
                'name' => 'دهگلان',
                'amar_code' => '1210',
                'state_id' => '20',
            ),
            195 =>
            array (
                'id' => '196',
                'name' => 'دهلران',
                'amar_code' => '1603',
                'state_id' => '6',
            ),
            196 =>
            array (
                'id' => '197',
                'name' => 'دیر',
                'amar_code' => '1805',
                'state_id' => '7',
            ),
            197 =>
            array (
                'id' => '198',
                'name' => 'دیلم',
                'amar_code' => '1808',
                'state_id' => '7',
            ),
            198 =>
            array (
                'id' => '199',
                'name' => 'دیواندره',
                'amar_code' => '1207',
                'state_id' => '20',
            ),
            199 =>
            array (
                'id' => '200',
                'name' => 'رابر',
                'amar_code' => '818',
                'state_id' => '21',
            ),
            200 =>
            array (
                'id' => '201',
                'name' => 'راز و جرگلان',
                'amar_code' => '2808',
                'state_id' => '12',
            ),
            201 =>
            array (
                'id' => '202',
                'name' => 'راسک',
                'amar_code' => '1108',
                'state_id' => '16',
            ),
            202 =>
            array (
                'id' => '203',
                'name' => 'رامسر',
                'amar_code' => '206',
                'state_id' => '27',
            ),
            203 =>
            array (
                'id' => '204',
                'name' => 'رامشیر',
                'amar_code' => '619',
                'state_id' => '13',
            ),
            204 =>
            array (
                'id' => '205',
                'name' => 'رامهرمز',
                'amar_code' => '610',
                'state_id' => '13',
            ),
            205 =>
            array (
                'id' => '206',
                'name' => 'رامیان',
                'amar_code' => '2711',
                'state_id' => '24',
            ),
            206 =>
            array (
                'id' => '207',
                'name' => 'راور',
                'amar_code' => '811',
                'state_id' => '21',
            ),
            207 =>
            array (
                'id' => '208',
                'name' => 'رباط کریم',
                'amar_code' => '2312',
                'state_id' => '8',
            ),
            208 =>
            array (
                'id' => '209',
                'name' => 'رزن',
                'amar_code' => '1308',
                'state_id' => '30',
            ),
            209 =>
            array (
                'id' => '210',
                'name' => 'رستم',
                'amar_code' => '726',
                'state_id' => '17',
            ),
            210 =>
            array (
                'id' => '211',
                'name' => 'رشت',
                'amar_code' => '105',
                'state_id' => '25',
            ),
            211 =>
            array (
                'id' => '212',
                'name' => 'رشتخوار',
                'amar_code' => '927',
                'state_id' => '11',
            ),
            212 =>
            array (
                'id' => '213',
                'name' => 'رضوانشهر',
                'amar_code' => '114',
                'state_id' => '25',
            ),
            213 =>
            array (
                'id' => '214',
                'name' => 'رفسنجان',
                'amar_code' => '804',
                'state_id' => '21',
            ),
            214 =>
            array (
                'id' => '215',
                'name' => 'روانسر',
                'amar_code' => '514',
                'state_id' => '22',
            ),
            215 =>
            array (
                'id' => '216',
                'name' => 'رودان',
                'amar_code' => '2207',
                'state_id' => '29',
            ),
            216 =>
            array (
                'id' => '217',
                'name' => 'رودبار',
                'amar_code' => '106',
                'state_id' => '25',
            ),
            217 =>
            array (
                'id' => '218',
                'name' => 'رودبارجنوب',
                'amar_code' => '815',
                'state_id' => '21',
            ),
            218 =>
            array (
                'id' => '219',
                'name' => 'رودسر',
                'amar_code' => '107',
                'state_id' => '25',
            ),
            219 =>
            array (
                'id' => '220',
                'name' => 'رومشکان',
                'amar_code' => '1511',
                'state_id' => '26',
            ),
            220 =>
            array (
                'id' => '221',
                'name' => 'ری',
                'amar_code' => '2303',
                'state_id' => '8',
            ),
            221 =>
            array (
                'id' => '222',
                'name' => 'ریگان',
                'amar_code' => '817',
                'state_id' => '21',
            ),
            222 =>
            array (
                'id' => '223',
                'name' => 'زابل',
                'amar_code' => '1104',
                'state_id' => '16',
            ),
            223 =>
            array (
                'id' => '224',
                'name' => 'زاوه',
                'amar_code' => '935',
                'state_id' => '11',
            ),
            224 =>
            array (
                'id' => '225',
                'name' => 'زاهدان',
                'amar_code' => '1105',
                'state_id' => '16',
            ),
            225 =>
            array (
                'id' => '226',
                'name' => 'زرقان',
                'amar_code' => '730',
                'state_id' => '17',
            ),
            226 =>
            array (
                'id' => '227',
                'name' => 'زرند',
                'amar_code' => '805',
                'state_id' => '21',
            ),
            227 =>
            array (
                'id' => '228',
                'name' => 'زرندیه',
                'amar_code' => '10',
                'state_id' => '28',
            ),
            228 =>
            array (
                'id' => '229',
                'name' => 'زرین دشت',
                'amar_code' => '719',
                'state_id' => '17',
            ),
            229 =>
            array (
                'id' => '230',
                'name' => 'زنجان',
                'amar_code' => '1904',
                'state_id' => '14',
            ),
            230 =>
            array (
                'id' => '231',
                'name' => 'زهک',
                'amar_code' => '1110',
                'state_id' => '16',
            ),
            231 =>
            array (
                'id' => '232',
                'name' => 'زیرکوه',
                'amar_code' => '2909',
                'state_id' => '10',
            ),
            232 =>
            array (
                'id' => '233',
                'name' => 'ساری',
                'amar_code' => '207',
                'state_id' => '27',
            ),
            233 =>
            array (
                'id' => '234',
                'name' => 'سامان',
                'amar_code' => '1408',
                'state_id' => '9',
            ),
            234 =>
            array (
                'id' => '235',
                'name' => 'ساوجبلاغ',
                'amar_code' => '3002',
                'state_id' => '5',
            ),
            235 =>
            array (
                'id' => '236',
                'name' => 'ساوه',
                'amar_code' => '6',
                'state_id' => '28',
            ),
            236 =>
            array (
                'id' => '237',
                'name' => 'سبزوار',
                'amar_code' => '908',
                'state_id' => '11',
            ),
            237 =>
            array (
                'id' => '238',
                'name' => 'سپیدان',
                'amar_code' => '706',
                'state_id' => '17',
            ),
            238 =>
            array (
                'id' => '239',
                'name' => 'سراب',
                'amar_code' => '305',
                'state_id' => '1',
            ),
            239 =>
            array (
                'id' => '240',
                'name' => 'سراوان',
                'amar_code' => '1106',
                'state_id' => '16',
            ),
            240 =>
            array (
                'id' => '241',
                'name' => 'سرایان',
                'amar_code' => '2906',
                'state_id' => '10',
            ),
            241 =>
            array (
                'id' => '242',
                'name' => 'سرباز',
                'amar_code' => '1123',
                'state_id' => '16',
            ),
            242 =>
            array (
                'id' => '243',
                'name' => 'سربیشه',
                'amar_code' => '2903',
                'state_id' => '10',
            ),
            243 =>
            array (
                'id' => '244',
                'name' => 'سرپل ذهاب',
                'amar_code' => '504',
                'state_id' => '22',
            ),
            244 =>
            array (
                'id' => '245',
                'name' => 'سرچهان',
                'amar_code' => '732',
                'state_id' => '17',
            ),
            245 =>
            array (
                'id' => '246',
                'name' => 'سرخس',
                'amar_code' => '920',
                'state_id' => '11',
            ),
            246 =>
            array (
                'id' => '247',
                'name' => 'سرخه',
                'amar_code' => '2008',
                'state_id' => '15',
            ),
            247 =>
            array (
                'id' => '248',
                'name' => 'سردشت',
                'amar_code' => '404',
                'state_id' => '2',
            ),
            248 =>
            array (
                'id' => '249',
                'name' => 'سرعین',
                'amar_code' => '2410',
                'state_id' => '3',
            ),
            249 =>
            array (
                'id' => '250',
                'name' => 'سروآباد',
                'amar_code' => '1209',
                'state_id' => '20',
            ),
            250 =>
            array (
                'id' => '251',
                'name' => 'سروستان',
                'amar_code' => '725',
                'state_id' => '17',
            ),
            251 =>
            array (
                'id' => '252',
                'name' => 'سقز',
                'amar_code' => '1203',
                'state_id' => '20',
            ),
            252 =>
            array (
                'id' => '253',
                'name' => 'سلسله',
                'amar_code' => '1509',
                'state_id' => '26',
            ),
            253 =>
            array (
                'id' => '254',
                'name' => 'سلطانیه',
                'amar_code' => '1910',
                'state_id' => '14',
            ),
            254 =>
            array (
                'id' => '255',
                'name' => 'سلماس',
                'amar_code' => '405',
                'state_id' => '2',
            ),
            255 =>
            array (
                'id' => '256',
                'name' => 'سمنان',
                'amar_code' => '2002',
                'state_id' => '15',
            ),
            256 =>
            array (
                'id' => '257',
                'name' => 'سمیرم',
                'amar_code' => '1005',
                'state_id' => '4',
            ),
            257 =>
            array (
                'id' => '258',
                'name' => 'سنقر',
                'amar_code' => '505',
                'state_id' => '22',
            ),
            258 =>
            array (
                'id' => '259',
                'name' => 'سنندج',
                'amar_code' => '1204',
                'state_id' => '20',
            ),
            259 =>
            array (
                'id' => '260',
                'name' => 'سوادکوه',
                'amar_code' => '208',
                'state_id' => '27',
            ),
            260 =>
            array (
                'id' => '261',
                'name' => 'سوادکوه شمالی',
                'amar_code' => '227',
                'state_id' => '27',
            ),
            261 =>
            array (
                'id' => '262',
                'name' => 'سیاهکل',
                'amar_code' => '115',
                'state_id' => '25',
            ),
            262 =>
            array (
                'id' => '263',
                'name' => 'سیب و سوران',
                'amar_code' => '1114',
                'state_id' => '16',
            ),
            263 =>
            array (
                'id' => '264',
                'name' => 'سیرجان',
                'amar_code' => '806',
                'state_id' => '21',
            ),
            264 =>
            array (
                'id' => '265',
                'name' => 'سیروان',
                'amar_code' => '1609',
                'state_id' => '6',
            ),
            265 =>
            array (
                'id' => '266',
                'name' => 'سیریک',
                'amar_code' => '2212',
                'state_id' => '29',
            ),
            266 =>
            array (
                'id' => '267',
                'name' => 'سیمرغ',
                'amar_code' => '226',
                'state_id' => '27',
            ),
            267 =>
            array (
                'id' => '268',
                'name' => 'شادگان',
                'amar_code' => '611',
                'state_id' => '13',
            ),
            268 =>
            array (
                'id' => '269',
                'name' => 'شازند',
                'amar_code' => '7',
                'state_id' => '28',
            ),
            269 =>
            array (
                'id' => '270',
                'name' => 'شاهرود',
                'amar_code' => '2003',
                'state_id' => '15',
            ),
            270 =>
            array (
                'id' => '271',
                'name' => 'شاهین دژ',
                'amar_code' => '411',
                'state_id' => '2',
            ),
            271 =>
            array (
                'id' => '272',
                'name' => 'شاهین شهرومیمه',
                'amar_code' => '1016',
                'state_id' => '4',
            ),
            272 =>
            array (
                'id' => '273',
                'name' => 'شبستر',
                'amar_code' => '314',
                'state_id' => '1',
            ),
            273 =>
            array (
                'id' => '274',
                'name' => 'شفت',
                'amar_code' => '112',
                'state_id' => '25',
            ),
            274 =>
            array (
                'id' => '275',
                'name' => 'شمیرانات',
                'amar_code' => '2304',
                'state_id' => '8',
            ),
            275 =>
            array (
                'id' => '276',
                'name' => 'شوش',
                'amar_code' => '614',
                'state_id' => '13',
            ),
            276 =>
            array (
                'id' => '277',
                'name' => 'شوشتر',
                'amar_code' => '612',
                'state_id' => '13',
            ),
            277 =>
            array (
                'id' => '278',
                'name' => 'شوط',
                'amar_code' => '417',
                'state_id' => '2',
            ),
            278 =>
            array (
                'id' => '279',
                'name' => 'شهربابک',
                'amar_code' => '807',
                'state_id' => '21',
            ),
            279 =>
            array (
                'id' => '280',
                'name' => 'شهرضا',
                'amar_code' => '1009',
                'state_id' => '4',
            ),
            280 =>
            array (
                'id' => '281',
                'name' => 'شهرکرد',
                'amar_code' => '1402',
                'state_id' => '9',
            ),
            281 =>
            array (
                'id' => '282',
                'name' => 'شهریار',
                'amar_code' => '2309',
                'state_id' => '8',
            ),
            282 =>
            array (
                'id' => '283',
                'name' => 'شیراز',
                'amar_code' => '707',
                'state_id' => '17',
            ),
            283 =>
            array (
                'id' => '284',
                'name' => 'شیروان',
                'amar_code' => '2804',
                'state_id' => '12',
            ),
            284 =>
            array (
                'id' => '285',
                'name' => 'صالح آباد',
                'amar_code' => '940',
                'state_id' => '11',
            ),
            285 =>
            array (
                'id' => '286',
                'name' => 'صحنه',
                'amar_code' => '510',
                'state_id' => '22',
            ),
            286 =>
            array (
                'id' => '287',
                'name' => 'صومعه سرا',
                'amar_code' => '108',
                'state_id' => '25',
            ),
            287 =>
            array (
                'id' => '288',
                'name' => 'طارم',
                'amar_code' => '1908',
                'state_id' => '14',
            ),
            288 =>
            array (
                'id' => '289',
                'name' => 'طالقان',
                'amar_code' => '3004',
                'state_id' => '5',
            ),
            289 =>
            array (
                'id' => '290',
                'name' => 'طبس',
                'amar_code' => '2911',
                'state_id' => '10',
            ),
            290 =>
            array (
                'id' => '291',
                'name' => 'طوالش',
                'amar_code' => '104',
                'state_id' => '25',
            ),
            291 =>
            array (
                'id' => '292',
                'name' => 'عباس آباد',
                'amar_code' => '224',
                'state_id' => '27',
            ),
            292 =>
            array (
                'id' => '293',
                'name' => 'عجب شیر',
                'amar_code' => '325',
                'state_id' => '1',
            ),
            293 =>
            array (
                'id' => '294',
                'name' => 'عسلویه',
                'amar_code' => '1810',
                'state_id' => '7',
            ),
            294 =>
            array (
                'id' => '295',
                'name' => 'علی آباد کتول',
                'amar_code' => '2703',
                'state_id' => '24',
            ),
            295 =>
            array (
                'id' => '296',
                'name' => 'عنبرآباد',
                'amar_code' => '812',
                'state_id' => '21',
            ),
            296 =>
            array (
                'id' => '297',
                'name' => 'فارسان',
                'amar_code' => '1403',
                'state_id' => '9',
            ),
            297 =>
            array (
                'id' => '298',
                'name' => 'فاروج',
                'amar_code' => '2805',
                'state_id' => '12',
            ),
            298 =>
            array (
                'id' => '299',
                'name' => 'فاریاب',
                'amar_code' => '822',
                'state_id' => '21',
            ),
            299 =>
            array (
                'id' => '300',
                'name' => 'فامنین',
                'amar_code' => '1309',
                'state_id' => '30',
            ),
            300 =>
            array (
                'id' => '301',
                'name' => 'فراشبند',
                'amar_code' => '722',
                'state_id' => '17',
            ),
            301 =>
            array (
                'id' => '302',
                'name' => 'فراهان',
                'amar_code' => '13',
                'state_id' => '28',
            ),
            302 =>
            array (
                'id' => '303',
                'name' => 'فردوس',
                'amar_code' => '2907',
                'state_id' => '10',
            ),
            303 =>
            array (
                'id' => '304',
                'name' => 'فردیس',
                'amar_code' => '3006',
                'state_id' => '5',
            ),
            304 =>
            array (
                'id' => '305',
                'name' => 'فریدن',
                'amar_code' => '1006',
                'state_id' => '4',
            ),
            305 =>
            array (
                'id' => '306',
                'name' => 'فریدونشهر',
                'amar_code' => '1007',
                'state_id' => '4',
            ),
            306 =>
            array (
                'id' => '307',
                'name' => 'فریدونکنار',
                'amar_code' => '223',
                'state_id' => '27',
            ),
            307 =>
            array (
                'id' => '308',
                'name' => 'فریمان',
                'amar_code' => '922',
                'state_id' => '11',
            ),
            308 =>
            array (
                'id' => '309',
                'name' => 'فسا',
                'amar_code' => '708',
                'state_id' => '17',
            ),
            309 =>
            array (
                'id' => '310',
                'name' => 'فلاورجان',
                'amar_code' => '1008',
                'state_id' => '4',
            ),
            310 =>
            array (
                'id' => '311',
                'name' => 'فنوج',
                'amar_code' => '1119',
                'state_id' => '16',
            ),
            311 =>
            array (
                'id' => '312',
                'name' => 'فومن',
                'amar_code' => '109',
                'state_id' => '25',
            ),
            312 =>
            array (
                'id' => '313',
                'name' => 'فهرج',
                'amar_code' => '819',
                'state_id' => '21',
            ),
            313 =>
            array (
                'id' => '314',
                'name' => 'فیروزآباد',
                'amar_code' => '709',
                'state_id' => '17',
            ),
            314 =>
            array (
                'id' => '315',
                'name' => 'فیروزکوه',
                'amar_code' => '2314',
                'state_id' => '8',
            ),
            315 =>
            array (
                'id' => '316',
                'name' => 'فیروزه',
                'amar_code' => '933',
                'state_id' => '11',
            ),
            316 =>
            array (
                'id' => '317',
                'name' => 'قایم شهر',
                'amar_code' => '210',
                'state_id' => '27',
            ),
            317 =>
            array (
                'id' => '318',
                'name' => 'قاینات',
                'amar_code' => '2904',
                'state_id' => '10',
            ),
            318 =>
            array (
                'id' => '319',
                'name' => 'قدس',
                'amar_code' => '2316',
                'state_id' => '8',
            ),
            319 =>
            array (
                'id' => '320',
                'name' => 'قرچک',
                'amar_code' => '2321',
                'state_id' => '8',
            ),
            320 =>
            array (
                'id' => '321',
                'name' => 'قروه',
                'amar_code' => '1205',
                'state_id' => '20',
            ),
            321 =>
            array (
                'id' => '322',
                'name' => 'قزوین',
                'amar_code' => '2603',
                'state_id' => '18',
            ),
            322 =>
            array (
                'id' => '323',
                'name' => 'قشم',
                'amar_code' => '2204',
                'state_id' => '29',
            ),
            323 =>
            array (
                'id' => '324',
                'name' => 'قصرشیرین',
                'amar_code' => '506',
                'state_id' => '22',
            ),
            324 =>
            array (
                'id' => '325',
                'name' => 'قصرقند',
                'amar_code' => '1118',
                'state_id' => '16',
            ),
            325 =>
            array (
                'id' => '326',
                'name' => 'قلعه گنج',
                'amar_code' => '816',
                'state_id' => '21',
            ),
            326 =>
            array (
                'id' => '327',
                'name' => 'قم',
                'amar_code' => '2501',
                'state_id' => '19',
            ),
            327 =>
            array (
                'id' => '328',
                'name' => 'قوچان',
                'amar_code' => '913',
                'state_id' => '11',
            ),
            328 =>
            array (
                'id' => '329',
                'name' => 'قیروکارزین',
                'amar_code' => '720',
                'state_id' => '17',
            ),
            329 =>
            array (
                'id' => '330',
                'name' => 'کارون',
                'amar_code' => '627',
                'state_id' => '13',
            ),
            330 =>
            array (
                'id' => '331',
                'name' => 'کازرون',
                'amar_code' => '710',
                'state_id' => '17',
            ),
            331 =>
            array (
                'id' => '332',
                'name' => 'کاشان',
                'amar_code' => '1010',
                'state_id' => '4',
            ),
            332 =>
            array (
                'id' => '333',
                'name' => 'کاشمر',
                'amar_code' => '914',
                'state_id' => '11',
            ),
            333 =>
            array (
                'id' => '334',
                'name' => 'کامیاران',
                'amar_code' => '1208',
                'state_id' => '20',
            ),
            334 =>
            array (
                'id' => '335',
                'name' => 'کبودرآهنگ',
                'amar_code' => '1305',
                'state_id' => '30',
            ),
            335 =>
            array (
                'id' => '336',
                'name' => 'کرج',
                'amar_code' => '3001',
                'state_id' => '5',
            ),
            336 =>
            array (
                'id' => '337',
                'name' => 'کردکوی',
                'amar_code' => '2704',
                'state_id' => '24',
            ),
            337 =>
            array (
                'id' => '338',
                'name' => 'کرمان',
                'amar_code' => '808',
                'state_id' => '21',
            ),
            338 =>
            array (
                'id' => '339',
                'name' => 'کرمانشاه',
                'amar_code' => '502',
                'state_id' => '22',
            ),
            339 =>
            array (
                'id' => '340',
                'name' => 'کلات',
                'amar_code' => '928',
                'state_id' => '11',
            ),
            340 =>
            array (
                'id' => '341',
                'name' => 'کلاردشت',
                'amar_code' => '228',
                'state_id' => '27',
            ),
            341 =>
            array (
                'id' => '342',
                'name' => 'کلاله',
                'amar_code' => '2709',
                'state_id' => '24',
            ),
            342 =>
            array (
                'id' => '343',
                'name' => 'کلیبر',
                'amar_code' => '315',
                'state_id' => '1',
            ),
            343 =>
            array (
                'id' => '344',
                'name' => 'کمیجان',
                'amar_code' => '11',
                'state_id' => '28',
            ),
            344 =>
            array (
                'id' => '345',
                'name' => 'کنارک',
                'amar_code' => '1109',
                'state_id' => '16',
            ),
            345 =>
            array (
                'id' => '346',
                'name' => 'کنگان',
                'amar_code' => '1806',
                'state_id' => '7',
            ),
            346 =>
            array (
                'id' => '347',
                'name' => 'کنگاور',
                'amar_code' => '507',
                'state_id' => '22',
            ),
            347 =>
            array (
                'id' => '348',
                'name' => 'کوار',
                'amar_code' => '728',
                'state_id' => '17',
            ),
            348 =>
            array (
                'id' => '349',
                'name' => 'کوثر',
                'amar_code' => '2407',
                'state_id' => '3',
            ),
            349 =>
            array (
                'id' => '350',
                'name' => 'کوه چنار',
                'amar_code' => '733',
                'state_id' => '17',
            ),
            350 =>
            array (
                'id' => '351',
                'name' => 'کوهبنان',
                'amar_code' => '814',
                'state_id' => '21',
            ),
            351 =>
            array (
                'id' => '352',
                'name' => 'کوهدشت',
                'amar_code' => '1506',
                'state_id' => '26',
            ),
            352 =>
            array (
                'id' => '353',
                'name' => 'کوهرنگ',
                'amar_code' => '1406',
                'state_id' => '9',
            ),
            353 =>
            array (
                'id' => '354',
                'name' => 'کوهسرخ',
                'amar_code' => '941',
                'state_id' => '11',
            ),
            354 =>
            array (
                'id' => '355',
                'name' => 'کهگیلویه',
                'amar_code' => '1702',
                'state_id' => '23',
            ),
            355 =>
            array (
                'id' => '356',
                'name' => 'کهنوج',
                'amar_code' => '809',
                'state_id' => '21',
            ),
            356 =>
            array (
                'id' => '357',
                'name' => 'کیار',
                'amar_code' => '1407',
                'state_id' => '9',
            ),
            357 =>
            array (
                'id' => '358',
                'name' => 'گالیکش',
                'amar_code' => '2714',
                'state_id' => '24',
            ),
            358 =>
            array (
                'id' => '359',
                'name' => 'گتوند',
                'amar_code' => '620',
                'state_id' => '13',
            ),
            359 =>
            array (
                'id' => '360',
                'name' => 'گچساران',
                'amar_code' => '1703',
                'state_id' => '23',
            ),
            360 =>
            array (
                'id' => '361',
                'name' => 'گراش',
                'amar_code' => '727',
                'state_id' => '17',
            ),
            361 =>
            array (
                'id' => '362',
                'name' => 'گرگان',
                'amar_code' => '2705',
                'state_id' => '24',
            ),
            362 =>
            array (
                'id' => '363',
                'name' => 'گرمسار',
                'amar_code' => '2004',
                'state_id' => '15',
            ),
            363 =>
            array (
                'id' => '364',
                'name' => 'گرمه',
                'amar_code' => '2807',
                'state_id' => '12',
            ),
            364 =>
            array (
                'id' => '365',
                'name' => 'گرمی',
                'amar_code' => '2405',
                'state_id' => '3',
            ),
            365 =>
            array (
                'id' => '366',
                'name' => 'گلپایگان',
                'amar_code' => '1011',
                'state_id' => '4',
            ),
            366 =>
            array (
                'id' => '367',
                'name' => 'گلوگاه',
                'amar_code' => '222',
                'state_id' => '27',
            ),
            367 =>
            array (
                'id' => '368',
                'name' => 'گمیشان',
                'amar_code' => '2713',
                'state_id' => '24',
            ),
            368 =>
            array (
                'id' => '369',
                'name' => 'گناباد',
                'amar_code' => '915',
                'state_id' => '11',
            ),
            369 =>
            array (
                'id' => '370',
                'name' => 'گناوه',
                'amar_code' => '1807',
                'state_id' => '7',
            ),
            370 =>
            array (
                'id' => '371',
                'name' => 'گنبدکاووس',
                'amar_code' => '2706',
                'state_id' => '24',
            ),
            371 =>
            array (
                'id' => '372',
                'name' => 'گیلانغرب',
                'amar_code' => '508',
                'state_id' => '22',
            ),
            372 =>
            array (
                'id' => '373',
                'name' => 'لارستان',
                'amar_code' => '711',
                'state_id' => '17',
            ),
            373 =>
            array (
                'id' => '374',
                'name' => 'لالی',
                'amar_code' => '617',
                'state_id' => '13',
            ),
            374 =>
            array (
                'id' => '375',
                'name' => 'لامرد',
                'amar_code' => '715',
                'state_id' => '17',
            ),
            375 =>
            array (
                'id' => '376',
                'name' => 'لاهیجان',
                'amar_code' => '111',
                'state_id' => '25',
            ),
            376 =>
            array (
                'id' => '377',
                'name' => 'لردگان',
                'amar_code' => '1404',
                'state_id' => '9',
            ),
            377 =>
            array (
                'id' => '378',
                'name' => 'لنجان',
                'amar_code' => '1012',
                'state_id' => '4',
            ),
            378 =>
            array (
                'id' => '379',
                'name' => 'لنده',
                'amar_code' => '1708',
                'state_id' => '23',
            ),
            379 =>
            array (
                'id' => '380',
                'name' => 'لنگرود',
                'amar_code' => '110',
                'state_id' => '25',
            ),
            380 =>
            array (
                'id' => '381',
                'name' => 'مارگون',
                'amar_code' => '1709',
                'state_id' => '23',
            ),
            381 =>
            array (
                'id' => '382',
                'name' => 'ماسال',
                'amar_code' => '116',
                'state_id' => '25',
            ),
            382 =>
            array (
                'id' => '383',
                'name' => 'ماکو',
                'amar_code' => '406',
                'state_id' => '2',
            ),
            383 =>
            array (
                'id' => '384',
                'name' => 'مانه وسملقان',
                'amar_code' => '2806',
                'state_id' => '12',
            ),
            384 =>
            array (
                'id' => '385',
                'name' => 'ماهنشان',
                'amar_code' => '1909',
                'state_id' => '14',
            ),
            385 =>
            array (
                'id' => '386',
                'name' => 'مبارکه',
                'amar_code' => '1017',
                'state_id' => '4',
            ),
            386 =>
            array (
                'id' => '387',
                'name' => 'محلات',
                'amar_code' => '9',
                'state_id' => '28',
            ),
            387 =>
            array (
                'id' => '388',
                'name' => 'محمودآباد',
                'amar_code' => '218',
                'state_id' => '27',
            ),
            388 =>
            array (
                'id' => '389',
                'name' => 'مراغه',
                'amar_code' => '306',
                'state_id' => '1',
            ),
            389 =>
            array (
                'id' => '390',
                'name' => 'مراوه تپه',
                'amar_code' => '2712',
                'state_id' => '24',
            ),
            390 =>
            array (
                'id' => '391',
                'name' => 'مرند',
                'amar_code' => '307',
                'state_id' => '1',
            ),
            391 =>
            array (
                'id' => '392',
                'name' => 'مرودشت',
                'amar_code' => '712',
                'state_id' => '17',
            ),
            392 =>
            array (
                'id' => '393',
                'name' => 'مریوان',
                'amar_code' => '1206',
                'state_id' => '20',
            ),
            393 =>
            array (
                'id' => '394',
                'name' => 'مسجدسلیمان',
                'amar_code' => '613',
                'state_id' => '13',
            ),
            394 =>
            array (
                'id' => '395',
                'name' => 'مشگین شهر',
                'amar_code' => '2404',
                'state_id' => '3',
            ),
            395 =>
            array (
                'id' => '396',
                'name' => 'مشهد',
                'amar_code' => '916',
                'state_id' => '11',
            ),
            396 =>
            array (
                'id' => '397',
                'name' => 'ملارد',
                'amar_code' => '2317',
                'state_id' => '8',
            ),
            397 =>
            array (
                'id' => '398',
                'name' => 'ملایر',
                'amar_code' => '1302',
                'state_id' => '30',
            ),
            398 =>
            array (
                'id' => '399',
                'name' => 'ملکان',
                'amar_code' => '320',
                'state_id' => '1',
            ),
            399 =>
            array (
                'id' => '400',
                'name' => 'ملکشاهی',
                'amar_code' => '1608',
                'state_id' => '6',
            ),
            400 =>
            array (
                'id' => '401',
                'name' => 'ممسنی',
                'amar_code' => '713',
                'state_id' => '17',
            ),
            401 =>
            array (
                'id' => '402',
                'name' => 'منوجان',
                'amar_code' => '813',
                'state_id' => '21',
            ),
            402 =>
            array (
                'id' => '403',
                'name' => 'مه ولات',
                'amar_code' => '930',
                'state_id' => '11',
            ),
            403 =>
            array (
                'id' => '404',
                'name' => 'مهاباد',
                'amar_code' => '407',
                'state_id' => '2',
            ),
            404 =>
            array (
                'id' => '405',
                'name' => 'مهدی شهر',
                'amar_code' => '2005',
                'state_id' => '15',
            ),
            405 =>
            array (
                'id' => '406',
                'name' => 'مهر',
                'amar_code' => '721',
                'state_id' => '17',
            ),
            406 =>
            array (
                'id' => '407',
                'name' => 'مهران',
                'amar_code' => '1605',
                'state_id' => '6',
            ),
            407 =>
            array (
                'id' => '408',
                'name' => 'مهرستان',
                'amar_code' => '1113',
                'state_id' => '16',
            ),
            408 =>
            array (
                'id' => '409',
                'name' => 'مهریز',
                'amar_code' => '2104',
                'state_id' => '31',
            ),
            409 =>
            array (
                'id' => '410',
                'name' => 'میامی',
                'amar_code' => '2007',
                'state_id' => '15',
            ),
            410 =>
            array (
                'id' => '411',
                'name' => 'میاندوآب',
                'amar_code' => '408',
                'state_id' => '2',
            ),
            411 =>
            array (
                'id' => '412',
                'name' => 'میاندورود',
                'amar_code' => '225',
                'state_id' => '27',
            ),
            412 =>
            array (
                'id' => '413',
                'name' => 'میانه',
                'amar_code' => '310',
                'state_id' => '1',
            ),
            413 =>
            array (
                'id' => '414',
                'name' => 'میبد',
                'amar_code' => '2106',
                'state_id' => '31',
            ),
            414 =>
            array (
                'id' => '415',
                'name' => 'میرجاوه',
                'amar_code' => '1117',
                'state_id' => '16',
            ),
            415 =>
            array (
                'id' => '416',
                'name' => 'میناب',
                'amar_code' => '2205',
                'state_id' => '29',
            ),
            416 =>
            array (
                'id' => '417',
                'name' => 'مینودشت',
                'amar_code' => '2707',
                'state_id' => '24',
            ),
            417 =>
            array (
                'id' => '418',
                'name' => 'نایین',
                'amar_code' => '1013',
                'state_id' => '4',
            ),
            418 =>
            array (
                'id' => '419',
                'name' => 'نجف آباد',
                'amar_code' => '1014',
                'state_id' => '4',
            ),
            419 =>
            array (
                'id' => '420',
                'name' => 'نرماشیر',
                'amar_code' => '821',
                'state_id' => '21',
            ),
            420 =>
            array (
                'id' => '421',
                'name' => 'نطنز',
                'amar_code' => '1015',
                'state_id' => '4',
            ),
            421 =>
            array (
                'id' => '422',
                'name' => 'نظرآباد',
                'amar_code' => '3003',
                'state_id' => '5',
            ),
            422 =>
            array (
                'id' => '423',
                'name' => 'نقده',
                'amar_code' => '409',
                'state_id' => '2',
            ),
            423 =>
            array (
                'id' => '424',
                'name' => 'نکا',
                'amar_code' => '219',
                'state_id' => '27',
            ),
            424 =>
            array (
                'id' => '425',
                'name' => 'نمین',
                'amar_code' => '2408',
                'state_id' => '3',
            ),
            425 =>
            array (
                'id' => '426',
                'name' => 'نور',
                'amar_code' => '214',
                'state_id' => '27',
            ),
            426 =>
            array (
                'id' => '427',
                'name' => 'نوشهر',
                'amar_code' => '215',
                'state_id' => '27',
            ),
            427 =>
            array (
                'id' => '428',
                'name' => 'نهاوند',
                'amar_code' => '1303',
                'state_id' => '30',
            ),
            428 =>
            array (
                'id' => '429',
                'name' => 'نهبندان',
                'amar_code' => '2905',
                'state_id' => '10',
            ),
            429 =>
            array (
                'id' => '430',
                'name' => 'نی ریز',
                'amar_code' => '714',
                'state_id' => '17',
            ),
            430 =>
            array (
                'id' => '431',
                'name' => 'نیر',
                'amar_code' => '2409',
                'state_id' => '3',
            ),
            431 =>
            array (
                'id' => '432',
                'name' => 'نیشابور',
                'amar_code' => '917',
                'state_id' => '11',
            ),
            432 =>
            array (
                'id' => '433',
                'name' => 'نیک شهر',
                'amar_code' => '1107',
                'state_id' => '16',
            ),
            433 =>
            array (
                'id' => '434',
                'name' => 'نیمروز',
                'amar_code' => '1115',
                'state_id' => '16',
            ),
            434 =>
            array (
                'id' => '435',
                'name' => 'ورامین',
                'amar_code' => '2306',
                'state_id' => '8',
            ),
            435 =>
            array (
                'id' => '436',
                'name' => 'ورزقان',
                'amar_code' => '324',
                'state_id' => '1',
            ),
            436 =>
            array (
                'id' => '437',
                'name' => 'هامون',
                'amar_code' => '1116',
                'state_id' => '16',
            ),
            437 =>
            array (
                'id' => '438',
                'name' => 'هرسین',
                'amar_code' => '511',
                'state_id' => '22',
            ),
            438 =>
            array (
                'id' => '439',
                'name' => 'هریس',
                'amar_code' => '316',
                'state_id' => '1',
            ),
            439 =>
            array (
                'id' => '440',
                'name' => 'هشترود',
                'amar_code' => '311',
                'state_id' => '1',
            ),
            440 =>
            array (
                'id' => '441',
                'name' => 'هفتکل',
                'amar_code' => '622',
                'state_id' => '13',
            ),
            441 =>
            array (
                'id' => '442',
                'name' => 'هلیلان',
                'amar_code' => '1611',
                'state_id' => '6',
            ),
            442 =>
            array (
                'id' => '443',
                'name' => 'همدان',
                'amar_code' => '1304',
                'state_id' => '30',
            ),
            443 =>
            array (
                'id' => '444',
                'name' => 'هندیجان',
                'amar_code' => '618',
                'state_id' => '13',
            ),
            444 =>
            array (
                'id' => '445',
                'name' => 'هوراند',
                'amar_code' => '327',
                'state_id' => '1',
            ),
            445 =>
            array (
                'id' => '446',
                'name' => 'هویزه',
                'amar_code' => '623',
                'state_id' => '13',
            ),
            446 =>
            array (
                'id' => '447',
                'name' => 'هیرمند',
                'amar_code' => '1111',
                'state_id' => '16',
            ),
            447 =>
            array (
                'id' => '448',
                'name' => 'یزد',
                'amar_code' => '2105',
                'state_id' => '31',
            ),
        ));


    }
}
