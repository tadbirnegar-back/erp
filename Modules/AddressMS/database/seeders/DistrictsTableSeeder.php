<?php

namespace Modules\AddressMS\database\seeders;

use Illuminate\Database\Seeder;

class DistrictsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('districts')->delete();

        \DB::table('districts')->insert(array (
            0 =>
            array (
                'id' => '1',
                'name' => 'آب پخش',
                'state_id' => '7',
                'city_id' => '185',
                'amar_code' => '180306',
            ),
            1 =>
            array (
                'id' => '2',
                'name' => 'آبدان',
                'state_id' => '7',
                'city_id' => '197',
                'amar_code' => '180503',
            ),
            2 =>
            array (
                'id' => '3',
                'name' => 'آبژدان',
                'state_id' => '13',
                'city_id' => '45',
                'amar_code' => '62101',
            ),
            3 =>
            array (
                'id' => '4',
                'name' => 'آبش احمد',
                'state_id' => '1',
                'city_id' => '343',
                'amar_code' => '31503',
            ),
            4 =>
            array (
                'id' => '5',
                'name' => 'آبگرم',
                'state_id' => '18',
                'city_id' => '15',
                'amar_code' => '260602',
            ),
            5 =>
            array (
                'id' => '6',
                'name' => 'آرمرده',
                'state_id' => '20',
                'city_id' => '62',
                'amar_code' => '120101',
            ),
            6 =>
            array (
                'id' => '7',
                'name' => 'آسارا',
                'state_id' => '5',
                'city_id' => '336',
                'amar_code' => '300103',
            ),
            7 =>
            array (
                'id' => '8',
                'name' => 'آسفیج',
                'state_id' => '31',
                'city_id' => '92',
                'amar_code' => '211101',
            ),
            8 =>
            array (
                'id' => '9',
                'name' => 'آسمینون',
                'state_id' => '21',
                'city_id' => '402',
                'amar_code' => '81302',
            ),
            9 =>
            array (
                'id' => '10',
                'name' => 'آشار',
                'state_id' => '16',
                'city_id' => '408',
                'amar_code' => '111302',
            ),
            10 =>
            array (
                'id' => '11',
                'name' => 'آفتاب',
                'state_id' => '8',
                'city_id' => '126',
                'amar_code' => '230103',
            ),
            11 =>
            array (
                'id' => '12',
                'name' => 'آهوران',
                'state_id' => '16',
                'city_id' => '433',
                'amar_code' => '110706',
            ),
            12 =>
            array (
                'id' => '13',
                'name' => 'آیسک',
                'state_id' => '10',
                'city_id' => '241',
                'amar_code' => '290601',
            ),
            13 =>
            array (
                'id' => '14',
                'name' => 'ابولفارس',
                'state_id' => '13',
                'city_id' => '205',
                'amar_code' => '61005',
            ),
            14 =>
            array (
                'id' => '15',
                'name' => 'احمد آباد مستوفی',
                'state_id' => '8',
                'city_id' => '33',
                'amar_code' => '231003',
            ),
            15 =>
            array (
                'id' => '16',
                'name' => 'احمدآباد',
                'state_id' => '11',
                'city_id' => '396',
                'amar_code' => '91601',
            ),
            16 =>
            array (
                'id' => '17',
                'name' => 'احمدسرگوراب',
                'state_id' => '25',
                'city_id' => '274',
                'amar_code' => '11201',
            ),
            17 =>
            array (
                'id' => '18',
                'name' => 'احمدی',
                'state_id' => '29',
                'city_id' => '149',
                'amar_code' => '220803',
            ),
            18 =>
            array (
                'id' => '19',
                'name' => 'ارجمند',
                'state_id' => '8',
                'city_id' => '315',
                'amar_code' => '231401',
            ),
            19 =>
            array (
                'id' => '20',
                'name' => 'ارد',
                'state_id' => '17',
                'city_id' => '361',
                'amar_code' => '72702',
            ),
            20 =>
            array (
                'id' => '21',
                'name' => 'ارژن',
                'state_id' => '17',
                'city_id' => '283',
                'amar_code' => '70706',
            ),
            21 =>
            array (
                'id' => '22',
                'name' => 'ارس',
                'state_id' => '2',
                'city_id' => '110',
                'amar_code' => '41501',
            ),
            22 =>
            array (
                'id' => '23',
                'name' => 'ارسک',
                'state_id' => '10',
                'city_id' => '76',
                'amar_code' => '290802',
            ),
            23 =>
            array (
                'id' => '24',
                'name' => 'ارشق',
                'state_id' => '3',
                'city_id' => '395',
                'amar_code' => '240401',
            ),
            24 =>
            array (
                'id' => '25',
                'name' => 'ارم',
                'state_id' => '7',
                'city_id' => '185',
                'amar_code' => '180304',
            ),
            25 =>
            array (
                'id' => '26',
                'name' => 'ارمند',
                'state_id' => '9',
                'city_id' => '153',
                'amar_code' => '141002',
            ),
            26 =>
            array (
                'id' => '27',
                'name' => 'اروندکنار',
                'state_id' => '13',
                'city_id' => '1',
                'amar_code' => '60101',
            ),
            27 =>
            array (
                'id' => '28',
                'name' => 'ازگله',
                'state_id' => '22',
                'city_id' => '128',
                'amar_code' => '51202',
            ),
            28 =>
            array (
                'id' => '29',
                'name' => 'اسالم',
                'state_id' => '25',
                'city_id' => '291',
                'amar_code' => '10405',
            ),
            29 =>
            array (
                'id' => '30',
                'name' => 'اسفرورین',
                'state_id' => '18',
                'city_id' => '113',
                'amar_code' => '260201',
            ),
            30 =>
            array (
                'id' => '31',
                'name' => 'اسفندقه',
                'state_id' => '21',
                'city_id' => '138',
                'amar_code' => '80306',
            ),
            31 =>
            array (
                'id' => '32',
                'name' => 'اسلام آباد',
                'state_id' => '3',
                'city_id' => '103',
                'amar_code' => '240604',
            ),
            32 =>
            array (
                'id' => '33',
                'name' => 'اسلامیه',
                'state_id' => '10',
                'city_id' => '303',
                'amar_code' => '290703',
            ),
            33 =>
            array (
                'id' => '34',
                'name' => 'اسماعیلی',
                'state_id' => '21',
                'city_id' => '138',
                'amar_code' => '80305',
            ),
            34 =>
            array (
                'id' => '35',
                'name' => 'اسماعیلیه',
                'state_id' => '13',
                'city_id' => '49',
                'amar_code' => '60304',
            ),
            35 =>
            array (
                'id' => '36',
                'name' => 'اسیر',
                'state_id' => '17',
                'city_id' => '406',
                'amar_code' => '72104',
            ),
            36 =>
            array (
                'id' => '37',
                'name' => 'اشترینان',
                'state_id' => '26',
                'city_id' => '71',
                'amar_code' => '150201',
            ),
            37 =>
            array (
                'id' => '38',
                'name' => 'اشکنان',
                'state_id' => '17',
                'city_id' => '375',
                'amar_code' => '71503',
            ),
            38 =>
            array (
                'id' => '39',
                'name' => 'اطاقور',
                'state_id' => '25',
                'city_id' => '380',
                'amar_code' => '11002',
            ),
            39 =>
            array (
                'id' => '40',
                'name' => 'افزر',
                'state_id' => '17',
                'city_id' => '329',
                'amar_code' => '72001',
            ),
            40 =>
            array (
                'id' => '41',
                'name' => 'افشار',
                'state_id' => '14',
                'city_id' => '155',
                'amar_code' => '190301',
            ),
            41 =>
            array (
                'id' => '42',
                'name' => 'الوارگرمسیری',
                'state_id' => '13',
                'city_id' => '46',
                'amar_code' => '60201',
            ),
            42 =>
            array (
                'id' => '43',
                'name' => 'امام',
                'state_id' => '20',
                'city_id' => '252',
                'amar_code' => '120304',
            ),
            43 =>
            array (
                'id' => '44',
                'name' => 'امام حسن',
                'state_id' => '7',
                'city_id' => '198',
                'amar_code' => '180801',
            ),
            44 =>
            array (
                'id' => '45',
                'name' => 'امام زاده',
                'state_id' => '4',
                'city_id' => '421',
                'amar_code' => '101502',
            ),
            45 =>
            array (
                'id' => '46',
                'name' => 'امامزاده عبدالله',
                'state_id' => '27',
                'city_id' => '14',
                'amar_code' => '20104',
            ),
            46 =>
            array (
                'id' => '47',
                'name' => 'امیراباد',
                'state_id' => '15',
                'city_id' => '177',
                'amar_code' => '200101',
            ),
            47 =>
            array (
                'id' => '48',
                'name' => 'انابد',
                'state_id' => '11',
                'city_id' => '69',
                'amar_code' => '92301',
            ),
            48 =>
            array (
                'id' => '49',
                'name' => 'انار',
                'state_id' => '21',
                'city_id' => '44',
                'amar_code' => '82001',
            ),
            49 =>
            array (
                'id' => '50',
                'name' => 'انارک',
                'state_id' => '4',
                'city_id' => '418',
                'amar_code' => '101301',
            ),
            50 =>
            array (
                'id' => '51',
                'name' => 'انزل',
                'state_id' => '2',
                'city_id' => '26',
                'amar_code' => '40101',
            ),
            51 =>
            array (
                'id' => '52',
                'name' => 'انگوت',
                'state_id' => '3',
                'city_id' => '365',
                'amar_code' => '240501',
            ),
            52 =>
            array (
                'id' => '53',
                'name' => 'انگوران',
                'state_id' => '14',
                'city_id' => '385',
                'amar_code' => '190901',
            ),
            53 =>
            array (
                'id' => '54',
                'name' => 'اورامان',
                'state_id' => '20',
                'city_id' => '250',
                'amar_code' => '120901',
            ),
            54 =>
            array (
                'id' => '55',
                'name' => 'ایرندگان',
                'state_id' => '16',
                'city_id' => '152',
                'amar_code' => '110303',
            ),
            55 =>
            array (
                'id' => '56',
                'name' => 'ایزدخواست',
                'state_id' => '17',
                'city_id' => '229',
                'amar_code' => '71901',
            ),
            56 =>
            array (
                'id' => '57',
                'name' => 'ایلخچی',
                'state_id' => '1',
                'city_id' => '31',
                'amar_code' => '32201',
            ),
            57 =>
            array (
                'id' => '58',
                'name' => 'ایوانکی',
                'state_id' => '15',
                'city_id' => '363',
                'amar_code' => '200401',
            ),
            58 =>
            array (
                'id' => '59',
                'name' => 'ایواوغلی',
                'state_id' => '2',
                'city_id' => '174',
                'amar_code' => '40304',
            ),
            59 =>
            array (
                'id' => '60',
                'name' => 'بابا حیدر',
                'state_id' => '9',
                'city_id' => '297',
                'amar_code' => '140303',
            ),
            60 =>
            array (
                'id' => '61',
                'name' => 'بابل کنار',
                'state_id' => '27',
                'city_id' => '55',
                'amar_code' => '20204',
            ),
            61 =>
            array (
                'id' => '62',
                'name' => 'باجگیران',
                'state_id' => '11',
                'city_id' => '328',
                'amar_code' => '91301',
            ),
            62 =>
            array (
                'id' => '63',
                'name' => 'باروق',
                'state_id' => '2',
                'city_id' => '411',
                'amar_code' => '40805',
            ),
            63 =>
            array (
                'id' => '64',
                'name' => 'بازرگان',
                'state_id' => '2',
                'city_id' => '383',
                'amar_code' => '40605',
            ),
            64 =>
            array (
                'id' => '65',
                'name' => 'بازفت',
                'state_id' => '9',
                'city_id' => '353',
                'amar_code' => '140601',
            ),
            65 =>
            array (
                'id' => '66',
                'name' => 'باشتین',
                'state_id' => '11',
                'city_id' => '178',
                'amar_code' => '93902',
            ),
            66 =>
            array (
                'id' => '67',
                'name' => 'باغ بهادران',
                'state_id' => '4',
                'city_id' => '378',
                'amar_code' => '101201',
            ),
            67 =>
            array (
                'id' => '68',
                'name' => 'باغ حلی',
                'state_id' => '14',
                'city_id' => '254',
                'amar_code' => '191002',
            ),
            68 =>
            array (
                'id' => '69',
                'name' => 'باغ صفا',
                'state_id' => '17',
                'city_id' => '245',
                'amar_code' => '73202',
            ),
            69 =>
            array (
                'id' => '70',
                'name' => 'بالا طالقان',
                'state_id' => '5',
                'city_id' => '289',
                'amar_code' => '300402',
            ),
            70 =>
            array (
                'id' => '71',
                'name' => 'بالا ولایت',
                'state_id' => '11',
                'city_id' => '57',
                'amar_code' => '93702',
            ),
            71 =>
            array (
                'id' => '72',
                'name' => 'بام وصفی آباد',
                'state_id' => '12',
                'city_id' => '30',
                'amar_code' => '280101',
            ),
            72 =>
            array (
                'id' => '73',
                'name' => 'بانش',
                'state_id' => '17',
                'city_id' => '100',
                'amar_code' => '73102',
            ),
            73 =>
            array (
                'id' => '74',
                'name' => 'باهوکلات',
                'state_id' => '16',
                'city_id' => '187',
                'amar_code' => '112202',
            ),
            74 =>
            array (
                'id' => '75',
                'name' => 'بایک',
                'state_id' => '11',
                'city_id' => '117',
                'amar_code' => '90501',
            ),
            75 =>
            array (
                'id' => '76',
                'name' => 'باینگان',
                'state_id' => '22',
                'city_id' => '107',
                'amar_code' => '50301',
            ),
            76 =>
            array (
                'id' => '77',
                'name' => 'بران',
                'state_id' => '3',
                'city_id' => '38',
                'amar_code' => '241102',
            ),
            77 =>
            array (
                'id' => '78',
                'name' => 'بربرود شرقی',
                'state_id' => '26',
                'city_id' => '41',
                'amar_code' => '150106',
            ),
            78 =>
            array (
                'id' => '79',
                'name' => 'بربرود غربی',
                'state_id' => '26',
                'city_id' => '41',
                'amar_code' => '150105',
            ),
            79 =>
            array (
                'id' => '80',
                'name' => 'بردخون',
                'state_id' => '7',
                'city_id' => '197',
                'amar_code' => '180501',
            ),
            80 =>
            array (
                'id' => '81',
                'name' => 'بررود',
                'state_id' => '11',
                'city_id' => '354',
                'amar_code' => '94102',
            ),
            81 =>
            array (
                'id' => '82',
                'name' => 'برزک',
                'state_id' => '4',
                'city_id' => '332',
                'amar_code' => '101005',
            ),
            82 =>
            array (
                'id' => '83',
                'name' => 'بروات',
                'state_id' => '21',
                'city_id' => '77',
                'amar_code' => '80207',
            ),
            83 =>
            array (
                'id' => '84',
                'name' => 'بزمان',
                'state_id' => '16',
                'city_id' => '52',
                'amar_code' => '110101',
            ),
            84 =>
            array (
                'id' => '85',
                'name' => 'بزینه رود',
                'state_id' => '14',
                'city_id' => '155',
                'amar_code' => '190302',
            ),
            85 =>
            array (
                'id' => '86',
                'name' => 'بستان',
                'state_id' => '13',
                'city_id' => '184',
                'amar_code' => '60901',
            ),
            86 =>
            array (
                'id' => '87',
                'name' => 'بسطام',
                'state_id' => '15',
                'city_id' => '270',
                'amar_code' => '200301',
            ),
            87 =>
            array (
                'id' => '88',
                'name' => 'بشاریات',
                'state_id' => '18',
                'city_id' => '4',
                'amar_code' => '260401',
            ),
            88 =>
            array (
                'id' => '89',
                'name' => 'بفروییه',
                'state_id' => '31',
                'city_id' => '414',
                'amar_code' => '210603',
            ),
            89 =>
            array (
                'id' => '90',
                'name' => 'بلبان آباد',
                'state_id' => '20',
                'city_id' => '195',
                'amar_code' => '121002',
            ),
            90 =>
            array (
                'id' => '91',
                'name' => 'بلداجی',
                'state_id' => '9',
                'city_id' => '72',
                'amar_code' => '140104',
            ),
            91 =>
            array (
                'id' => '92',
                'name' => 'بلده',
                'state_id' => '27',
                'city_id' => '426',
                'amar_code' => '21401',
            ),
            92 =>
            array (
                'id' => '93',
                'name' => 'بلورد',
                'state_id' => '21',
                'city_id' => '264',
                'amar_code' => '80605',
            ),
            93 =>
            array (
                'id' => '94',
                'name' => 'بم پشت',
                'state_id' => '16',
                'city_id' => '240',
                'amar_code' => '110605',
            ),
            94 =>
            array (
                'id' => '95',
                'name' => 'بمانی',
                'state_id' => '29',
                'city_id' => '266',
                'amar_code' => '221201',
            ),
            95 =>
            array (
                'id' => '96',
                'name' => 'بن رود',
                'state_id' => '4',
                'city_id' => '37',
                'amar_code' => '100205',
            ),
            96 =>
            array (
                'id' => '97',
                'name' => 'بنارویه',
                'state_id' => '17',
                'city_id' => '373',
                'amar_code' => '71107',
            ),
            97 =>
            array (
                'id' => '98',
                'name' => 'بنت',
                'state_id' => '16',
                'city_id' => '433',
                'amar_code' => '110701',
            ),
            98 =>
            array (
                'id' => '99',
                'name' => 'بندپی شرقی',
                'state_id' => '27',
                'city_id' => '55',
                'amar_code' => '20203',
            ),
            99 =>
            array (
                'id' => '100',
                'name' => 'بندپی غربی',
                'state_id' => '27',
                'city_id' => '55',
                'amar_code' => '20201',
            ),
            100 =>
            array (
                'id' => '101',
                'name' => 'بندرامام خمینی',
                'state_id' => '13',
                'city_id' => '85',
                'amar_code' => '60501',
            ),
            101 =>
            array (
                'id' => '102',
                'name' => 'بندزرک',
                'state_id' => '29',
                'city_id' => '416',
                'amar_code' => '220505',
            ),
            102 =>
            array (
                'id' => '103',
                'name' => 'بوژگان',
                'state_id' => '11',
                'city_id' => '116',
                'amar_code' => '90604',
            ),
            103 =>
            array (
                'id' => '104',
                'name' => 'بوستان',
                'state_id' => '8',
                'city_id' => '94',
                'amar_code' => '231902',
            ),
            104 =>
            array (
                'id' => '105',
                'name' => 'بوستان',
                'state_id' => '23',
                'city_id' => '58',
                'amar_code' => '170702',
            ),
            105 =>
            array (
                'id' => '106',
                'name' => 'بوشکان',
                'state_id' => '7',
                'city_id' => '185',
                'amar_code' => '180305',
            ),
            106 =>
            array (
                'id' => '107',
                'name' => 'بومهن',
                'state_id' => '8',
                'city_id' => '108',
                'amar_code' => '232001',
            ),
            107 =>
            array (
                'id' => '108',
                'name' => 'بهاران',
                'state_id' => '24',
                'city_id' => '362',
                'amar_code' => '270503',
            ),
            108 =>
            array (
                'id' => '109',
                'name' => 'بهمن',
                'state_id' => '31',
                'city_id' => '16',
                'amar_code' => '210701',
            ),
            109 =>
            array (
                'id' => '110',
                'name' => 'بهمن صغاد',
                'state_id' => '17',
                'city_id' => '2',
                'amar_code' => '70105',
            ),
            110 =>
            array (
                'id' => '111',
                'name' => 'بهمیی گرمسیری',
                'state_id' => '23',
                'city_id' => '97',
                'amar_code' => '170501',
            ),
            111 =>
            array (
                'id' => '112',
                'name' => 'بهنمیر',
                'state_id' => '27',
                'city_id' => '56',
                'amar_code' => '21603',
            ),
            112 =>
            array (
                'id' => '113',
                'name' => 'بیارجمند',
                'state_id' => '15',
                'city_id' => '270',
                'amar_code' => '200302',
            ),
            113 =>
            array (
                'id' => '114',
                'name' => 'بیدشهر',
                'state_id' => '17',
                'city_id' => '47',
                'amar_code' => '73602',
            ),
            114 =>
            array (
                'id' => '115',
                'name' => 'بیرانوند',
                'state_id' => '26',
                'city_id' => '157',
                'amar_code' => '150303',
            ),
            115 =>
            array (
                'id' => '116',
                'name' => 'بیرم',
                'state_id' => '17',
                'city_id' => '373',
                'amar_code' => '71105',
            ),
            116 =>
            array (
                'id' => '117',
                'name' => 'بیستون',
                'state_id' => '22',
                'city_id' => '438',
                'amar_code' => '51101',
            ),
            117 =>
            array (
                'id' => '118',
                'name' => 'بیکاه',
                'state_id' => '29',
                'city_id' => '216',
                'amar_code' => '220704',
            ),
            118 =>
            array (
                'id' => '119',
                'name' => 'بیلوار',
                'state_id' => '22',
                'city_id' => '339',
                'amar_code' => '50208',
            ),
            119 =>
            array (
                'id' => '120',
                'name' => 'پاپی',
                'state_id' => '26',
                'city_id' => '157',
                'amar_code' => '150302',
            ),
            120 =>
            array (
                'id' => '121',
                'name' => 'پاتاوه',
                'state_id' => '23',
                'city_id' => '192',
                'amar_code' => '170401',
            ),
            121 =>
            array (
                'id' => '122',
                'name' => 'پادنا',
                'state_id' => '4',
                'city_id' => '257',
                'amar_code' => '100501',
            ),
            122 =>
            array (
                'id' => '123',
                'name' => 'پادنا علیا',
                'state_id' => '4',
                'city_id' => '257',
                'amar_code' => '100503',
            ),
            123 =>
            array (
                'id' => '124',
                'name' => 'پارود',
                'state_id' => '16',
                'city_id' => '202',
                'amar_code' => '110805',
            ),
            124 =>
            array (
                'id' => '125',
                'name' => 'پاریز',
                'state_id' => '21',
                'city_id' => '264',
                'amar_code' => '80602',
            ),
            125 =>
            array (
                'id' => '126',
                'name' => 'پاسارگاد',
                'state_id' => '17',
                'city_id' => '105',
                'amar_code' => '72302',
            ),
            126 =>
            array (
                'id' => '127',
                'name' => 'پایین جام',
                'state_id' => '11',
                'city_id' => '116',
                'amar_code' => '90605',
            ),
            127 =>
            array (
                'id' => '128',
                'name' => 'پره سر',
                'state_id' => '25',
                'city_id' => '213',
                'amar_code' => '11401',
            ),
            128 =>
            array (
                'id' => '129',
                'name' => 'پشتکوه',
                'state_id' => '16',
                'city_id' => '152',
                'amar_code' => '110304',
            ),
            129 =>
            array (
                'id' => '130',
                'name' => 'پشتکوه',
                'state_id' => '17',
                'city_id' => '430',
                'amar_code' => '71402',
            ),
            130 =>
            array (
                'id' => '131',
                'name' => 'پلان',
                'state_id' => '16',
                'city_id' => '143',
                'amar_code' => '110204',
            ),
            131 =>
            array (
                'id' => '132',
                'name' => 'پلنگ آباد',
                'state_id' => '5',
                'city_id' => '34',
                'amar_code' => '300502',
            ),
            132 =>
            array (
                'id' => '133',
                'name' => 'پیربکران',
                'state_id' => '4',
                'city_id' => '310',
                'amar_code' => '100801',
            ),
            133 =>
            array (
                'id' => '134',
                'name' => 'پیرسلمان',
                'state_id' => '30',
                'city_id' => '29',
                'amar_code' => '130602',
            ),
            134 =>
            array (
                'id' => '135',
                'name' => 'پیشخور',
                'state_id' => '30',
                'city_id' => '300',
                'amar_code' => '130902',
            ),
            135 =>
            array (
                'id' => '136',
                'name' => 'پیشکمر',
                'state_id' => '24',
                'city_id' => '342',
                'amar_code' => '270903',
            ),
            136 =>
            array (
                'id' => '137',
                'name' => 'پیشین',
                'state_id' => '16',
                'city_id' => '202',
                'amar_code' => '110801',
            ),
            137 =>
            array (
                'id' => '138',
                'name' => 'تازه کند',
                'state_id' => '3',
                'city_id' => '103',
                'amar_code' => '240603',
            ),
            138 =>
            array (
                'id' => '139',
                'name' => 'تالارپی',
                'state_id' => '27',
                'city_id' => '267',
                'amar_code' => '22602',
            ),
            139 =>
            array (
                'id' => '140',
                'name' => 'تخت',
                'state_id' => '29',
                'city_id' => '82',
                'amar_code' => '220205',
            ),
            140 =>
            array (
                'id' => '141',
                'name' => 'تخت سلیمان',
                'state_id' => '2',
                'city_id' => '122',
                'amar_code' => '41202',
            ),
            141 =>
            array (
                'id' => '142',
                'name' => 'ترکمانچای',
                'state_id' => '1',
                'city_id' => '413',
                'amar_code' => '31001',
            ),
            142 =>
            array (
                'id' => '143',
                'name' => 'تسوج',
                'state_id' => '1',
                'city_id' => '273',
                'amar_code' => '31403',
            ),
            143 =>
            array (
                'id' => '144',
                'name' => 'تشان',
                'state_id' => '13',
                'city_id' => '95',
                'amar_code' => '60604',
            ),
            144 =>
            array (
                'id' => '145',
                'name' => 'تلنگ',
                'state_id' => '16',
                'city_id' => '325',
                'amar_code' => '111803',
            ),
            145 =>
            array (
                'id' => '146',
                'name' => 'تنب',
                'state_id' => '29',
                'city_id' => '17',
                'amar_code' => '220101',
            ),
            146 =>
            array (
                'id' => '147',
                'name' => 'تنکمان',
                'state_id' => '5',
                'city_id' => '422',
                'amar_code' => '300302',
            ),
            147 =>
            array (
                'id' => '148',
                'name' => 'توکهور',
                'state_id' => '29',
                'city_id' => '416',
                'amar_code' => '220504',
            ),
            148 =>
            array (
                'id' => '149',
                'name' => 'تولم',
                'state_id' => '25',
                'city_id' => '287',
                'amar_code' => '10801',
            ),
            149 =>
            array (
                'id' => '150',
                'name' => 'تیکمه داش',
                'state_id' => '1',
                'city_id' => '73',
                'amar_code' => '31301',
            ),
            150 =>
            array (
                'id' => '151',
                'name' => 'تیمورآباد',
                'state_id' => '16',
                'city_id' => '437',
                'amar_code' => '111602',
            ),
            151 =>
            array (
                'id' => '152',
                'name' => 'ثمرین',
                'state_id' => '3',
                'city_id' => '20',
                'amar_code' => '240106',
            ),
            152 =>
            array (
                'id' => '153',
                'name' => 'جاپلق',
                'state_id' => '26',
                'city_id' => '27',
                'amar_code' => '150701',
            ),
            153 =>
            array (
                'id' => '154',
                'name' => 'جاجرود',
                'state_id' => '8',
                'city_id' => '108',
                'amar_code' => '232002',
            ),
            154 =>
            array (
                'id' => '155',
                'name' => 'جازموریان',
                'state_id' => '21',
                'city_id' => '218',
                'amar_code' => '81501',
            ),
            155 =>
            array (
                'id' => '156',
                'name' => 'جالق',
                'state_id' => '16',
                'city_id' => '240',
                'amar_code' => '110601',
            ),
            156 =>
            array (
                'id' => '157',
                'name' => 'جایزان',
                'state_id' => '13',
                'city_id' => '43',
                'amar_code' => '61601',
            ),
            157 =>
            array (
                'id' => '158',
                'name' => 'جبالبارز',
                'state_id' => '21',
                'city_id' => '138',
                'amar_code' => '80302',
            ),
            158 =>
            array (
                'id' => '159',
                'name' => 'جبالبارزجنوبی',
                'state_id' => '21',
                'city_id' => '296',
                'amar_code' => '81203',
            ),
            159 =>
            array (
                'id' => '160',
                'name' => 'جرقویه سفلی',
                'state_id' => '4',
                'city_id' => '37',
                'amar_code' => '100201',
            ),
            160 =>
            array (
                'id' => '161',
                'name' => 'جرقویه علیا',
                'state_id' => '4',
                'city_id' => '37',
                'amar_code' => '100204',
            ),
            161 =>
            array (
                'id' => '162',
                'name' => 'جرگلان',
                'state_id' => '12',
                'city_id' => '201',
                'amar_code' => '280803',
            ),
            162 =>
            array (
                'id' => '163',
                'name' => 'جره وبالاده',
                'state_id' => '17',
                'city_id' => '331',
                'amar_code' => '71001',
            ),
            163 =>
            array (
                'id' => '164',
                'name' => 'جزمان',
                'state_id' => '6',
                'city_id' => '442',
                'amar_code' => '161102',
            ),
            164 =>
            array (
                'id' => '165',
                'name' => 'جزینک',
                'state_id' => '16',
                'city_id' => '231',
                'amar_code' => '111001',
            ),
            165 =>
            array (
                'id' => '166',
                'name' => 'جعفراباد',
                'state_id' => '19',
                'city_id' => '327',
                'amar_code' => '250101',
            ),
            166 =>
            array (
                'id' => '167',
                'name' => 'جغین',
                'state_id' => '29',
                'city_id' => '216',
                'amar_code' => '220703',
            ),
            167 =>
            array (
                'id' => '168',
                'name' => 'جلگه',
                'state_id' => '4',
                'city_id' => '37',
                'amar_code' => '100206',
            ),
            168 =>
            array (
                'id' => '169',
                'name' => 'جلگه چاه هاشم',
                'state_id' => '16',
                'city_id' => '189',
                'amar_code' => '111202',
            ),
            169 =>
            array (
                'id' => '170',
                'name' => 'جلگه رخ',
                'state_id' => '11',
                'city_id' => '117',
                'amar_code' => '90507',
            ),
            170 =>
            array (
                'id' => '171',
                'name' => 'جلگه زوزن',
                'state_id' => '11',
                'city_id' => '169',
                'amar_code' => '91903',
            ),
            171 =>
            array (
                'id' => '172',
                'name' => 'جلگه سنخواست',
                'state_id' => '12',
                'city_id' => '129',
                'amar_code' => '280301',
            ),
            172 =>
            array (
                'id' => '173',
                'name' => 'جلگه شوقان',
                'state_id' => '12',
                'city_id' => '129',
                'amar_code' => '280302',
            ),
            173 =>
            array (
                'id' => '174',
                'name' => 'جلگه ماژان',
                'state_id' => '10',
                'city_id' => '172',
                'amar_code' => '291002',
            ),
            174 =>
            array (
                'id' => '175',
                'name' => 'جلیل آباد',
                'state_id' => '8',
                'city_id' => '112',
                'amar_code' => '231802',
            ),
            175 =>
            array (
                'id' => '176',
                'name' => 'جناح',
                'state_id' => '29',
                'city_id' => '74',
                'amar_code' => '220901',
            ),
            176 =>
            array (
                'id' => '177',
                'name' => 'جنت',
                'state_id' => '17',
                'city_id' => '175',
                'amar_code' => '70505',
            ),
            177 =>
            array (
                'id' => '178',
                'name' => 'جنت آباد',
                'state_id' => '11',
                'city_id' => '285',
                'amar_code' => '94002',
            ),
            178 =>
            array (
                'id' => '179',
                'name' => 'جنگل',
                'state_id' => '11',
                'city_id' => '212',
                'amar_code' => '92701',
            ),
            179 =>
            array (
                'id' => '180',
                'name' => 'جوادآباد',
                'state_id' => '8',
                'city_id' => '435',
                'amar_code' => '230602',
            ),
            180 =>
            array (
                'id' => '181',
                'name' => 'جوزار',
                'state_id' => '17',
                'city_id' => '401',
                'amar_code' => '71306',
            ),
            181 =>
            array (
                'id' => '182',
                'name' => 'جوقین',
                'state_id' => '8',
                'city_id' => '282',
                'amar_code' => '230903',
            ),
            182 =>
            array (
                'id' => '183',
                'name' => 'جوکار',
                'state_id' => '30',
                'city_id' => '398',
                'amar_code' => '130201',
            ),
            183 =>
            array (
                'id' => '184',
                'name' => 'جولکی',
                'state_id' => '13',
                'city_id' => '12',
                'amar_code' => '62602',
            ),
            184 =>
            array (
                'id' => '185',
                'name' => 'جونقان',
                'state_id' => '9',
                'city_id' => '297',
                'amar_code' => '140304',
            ),
            185 =>
            array (
                'id' => '186',
                'name' => 'جویم',
                'state_id' => '17',
                'city_id' => '373',
                'amar_code' => '71102',
            ),
            186 =>
            array (
                'id' => '187',
                'name' => 'چابکسر',
                'state_id' => '25',
                'city_id' => '219',
                'amar_code' => '10704',
            ),
            187 =>
            array (
                'id' => '188',
                'name' => 'چاپشلو',
                'state_id' => '11',
                'city_id' => '179',
                'amar_code' => '90701',
            ),
            188 =>
            array (
                'id' => '189',
                'name' => 'چاروسا',
                'state_id' => '23',
                'city_id' => '355',
                'amar_code' => '170202',
            ),
            189 =>
            array (
                'id' => '190',
                'name' => 'چاه دادخدا',
                'state_id' => '21',
                'city_id' => '326',
                'amar_code' => '81601',
            ),
            190 =>
            array (
                'id' => '191',
                'name' => 'چاه مبارک',
                'state_id' => '7',
                'city_id' => '294',
                'amar_code' => '181002',
            ),
            191 =>
            array (
                'id' => '192',
                'name' => 'چاه مرید',
                'state_id' => '21',
                'city_id' => '356',
                'amar_code' => '80905',
            ),
            192 =>
            array (
                'id' => '193',
                'name' => 'چاه ورز',
                'state_id' => '17',
                'city_id' => '375',
                'amar_code' => '71505',
            ),
            193 =>
            array (
                'id' => '194',
                'name' => 'چترود',
                'state_id' => '21',
                'city_id' => '338',
                'amar_code' => '80807',
            ),
            194 =>
            array (
                'id' => '195',
                'name' => 'چشمه ساران',
                'state_id' => '24',
                'city_id' => '8',
                'amar_code' => '271001',
            ),
            195 =>
            array (
                'id' => '196',
                'name' => 'چغادک',
                'state_id' => '7',
                'city_id' => '88',
                'amar_code' => '180103',
            ),
            196 =>
            array (
                'id' => '197',
                'name' => 'چغامیش',
                'state_id' => '13',
                'city_id' => '183',
                'amar_code' => '60803',
            ),
            197 =>
            array (
                'id' => '198',
                'name' => 'چگنی',
                'state_id' => '26',
                'city_id' => '147',
                'amar_code' => '151001',
            ),
            198 =>
            array (
                'id' => '199',
                'name' => 'چلو',
                'state_id' => '13',
                'city_id' => '45',
                'amar_code' => '62102',
            ),
            199 =>
            array (
                'id' => '200',
                'name' => 'چم خلف عیسی',
                'state_id' => '13',
                'city_id' => '444',
                'amar_code' => '61802',
            ),
            200 =>
            array (
                'id' => '201',
                'name' => 'چمستان',
                'state_id' => '27',
                'city_id' => '426',
                'amar_code' => '21402',
            ),
            201 =>
            array (
                'id' => '202',
                'name' => 'چنارود',
                'state_id' => '4',
                'city_id' => '139',
                'amar_code' => '102001',
            ),
            202 =>
            array (
                'id' => '203',
                'name' => 'چندار',
                'state_id' => '5',
                'city_id' => '235',
                'amar_code' => '300203',
            ),
            203 =>
            array (
                'id' => '204',
                'name' => 'چنگ الماس',
                'state_id' => '20',
                'city_id' => '98',
                'amar_code' => '120201',
            ),
            204 =>
            array (
                'id' => '205',
                'name' => 'چوار',
                'state_id' => '6',
                'city_id' => '53',
                'amar_code' => '160102',
            ),
            205 =>
            array (
                'id' => '206',
                'name' => 'چورزق',
                'state_id' => '14',
                'city_id' => '288',
                'amar_code' => '190801',
            ),
            206 =>
            array (
                'id' => '207',
                'name' => 'چهارباغ',
                'state_id' => '5',
                'city_id' => '235',
                'amar_code' => '300204',
            ),
            207 =>
            array (
                'id' => '208',
                'name' => 'چهاردانگه',
                'state_id' => '1',
                'city_id' => '445',
                'amar_code' => '32702',
            ),
            208 =>
            array (
                'id' => '209',
                'name' => 'چهاردانگه',
                'state_id' => '8',
                'city_id' => '33',
                'amar_code' => '231001',
            ),
            209 =>
            array (
                'id' => '210',
                'name' => 'چهاردانگه',
                'state_id' => '27',
                'city_id' => '233',
                'amar_code' => '20701',
            ),
            210 =>
            array (
                'id' => '211',
                'name' => 'چهاردولی',
                'state_id' => '20',
                'city_id' => '321',
                'amar_code' => '120504',
            ),
            211 =>
            array (
                'id' => '212',
                'name' => 'حاجیلار',
                'state_id' => '2',
                'city_id' => '144',
                'amar_code' => '41601',
            ),
            212 =>
            array (
                'id' => '213',
                'name' => 'حبیب آباد',
                'state_id' => '4',
                'city_id' => '68',
                'amar_code' => '102201',
            ),
            213 =>
            array (
                'id' => '214',
                'name' => 'حتی',
                'state_id' => '13',
                'city_id' => '374',
                'amar_code' => '61702',
            ),
            214 =>
            array (
                'id' => '215',
                'name' => 'حرا',
                'state_id' => '29',
                'city_id' => '323',
                'amar_code' => '220404',
            ),
            215 =>
            array (
                'id' => '216',
                'name' => 'حسن آباد',
                'state_id' => '17',
                'city_id' => '39',
                'amar_code' => '70303',
            ),
            216 =>
            array (
                'id' => '217',
                'name' => 'حسین آباد',
                'state_id' => '20',
                'city_id' => '259',
                'amar_code' => '120405',
            ),
            217 =>
            array (
                'id' => '218',
                'name' => 'حلب',
                'state_id' => '14',
                'city_id' => '50',
                'amar_code' => '190601',
            ),
            218 =>
            array (
                'id' => '219',
                'name' => 'حمیل',
                'state_id' => '22',
                'city_id' => '32',
                'amar_code' => '50101',
            ),
            219 =>
            array (
                'id' => '220',
                'name' => 'حناء',
                'state_id' => '17',
                'city_id' => '66',
                'amar_code' => '73502',
            ),
            220 =>
            array (
                'id' => '221',
                'name' => 'حور',
                'state_id' => '21',
                'city_id' => '299',
                'amar_code' => '82202',
            ),
            221 =>
            array (
                'id' => '222',
                'name' => 'حومه',
                'state_id' => '1',
                'city_id' => '5',
                'amar_code' => '32103',
            ),
            222 =>
            array (
                'id' => '223',
                'name' => 'حویق',
                'state_id' => '25',
                'city_id' => '291',
                'amar_code' => '10406',
            ),
            223 =>
            array (
                'id' => '224',
                'name' => 'خارک',
                'state_id' => '7',
                'city_id' => '88',
                'amar_code' => '180101',
            ),
            224 =>
            array (
                'id' => '225',
                'name' => 'خاروانا',
                'state_id' => '1',
                'city_id' => '436',
                'amar_code' => '32401',
            ),
            225 =>
            array (
                'id' => '226',
                'name' => 'خاوران',
                'state_id' => '8',
                'city_id' => '221',
                'amar_code' => '230305',
            ),
            226 =>
            array (
                'id' => '227',
                'name' => 'خاوومیرآباد',
                'state_id' => '20',
                'city_id' => '393',
                'amar_code' => '120601',
            ),
            227 =>
            array (
                'id' => '228',
                'name' => 'خاوه',
                'state_id' => '26',
                'city_id' => '188',
                'amar_code' => '150403',
            ),
            228 =>
            array (
                'id' => '229',
                'name' => 'خبر',
                'state_id' => '21',
                'city_id' => '60',
                'amar_code' => '80104',
            ),
            229 =>
            array (
                'id' => '230',
                'name' => 'خبوشان',
                'state_id' => '12',
                'city_id' => '298',
                'amar_code' => '280501',
            ),
            230 =>
            array (
                'id' => '231',
                'name' => 'خرانق',
                'state_id' => '31',
                'city_id' => '22',
                'amar_code' => '210101',
            ),
            231 =>
            array (
                'id' => '232',
                'name' => 'خرقان',
                'state_id' => '28',
                'city_id' => '228',
                'amar_code' => '1001',
            ),
            232 =>
            array (
                'id' => '233',
                'name' => 'خرم آباد',
                'state_id' => '27',
                'city_id' => '123',
                'amar_code' => '20504',
            ),
            233 =>
            array (
                'id' => '234',
                'name' => 'خرمدشت',
                'state_id' => '18',
                'city_id' => '113',
                'amar_code' => '260202',
            ),
            234 =>
            array (
                'id' => '235',
                'name' => 'خزل',
                'state_id' => '30',
                'city_id' => '428',
                'amar_code' => '130301',
            ),
            235 =>
            array (
                'id' => '236',
                'name' => 'خسروشاه',
                'state_id' => '1',
                'city_id' => '115',
                'amar_code' => '30304',
            ),
            236 =>
            array (
                'id' => '237',
                'name' => 'خشت',
                'state_id' => '17',
                'city_id' => '331',
                'amar_code' => '71006',
            ),
            237 =>
            array (
                'id' => '238',
                'name' => 'خشکبیجار',
                'state_id' => '25',
                'city_id' => '211',
                'amar_code' => '10506',
            ),
            238 =>
            array (
                'id' => '239',
                'name' => 'خضرآباد',
                'state_id' => '31',
                'city_id' => '35',
                'amar_code' => '210801',
            ),
            239 =>
            array (
                'id' => '240',
                'name' => 'خلجستان',
                'state_id' => '19',
                'city_id' => '327',
                'amar_code' => '250102',
            ),
            240 =>
            array (
                'id' => '241',
                'name' => 'خلیفان',
                'state_id' => '2',
                'city_id' => '404',
                'amar_code' => '40701',
            ),
            241 =>
            array (
                'id' => '242',
                'name' => 'خمام',
                'state_id' => '25',
                'city_id' => '211',
                'amar_code' => '10501',
            ),
            242 =>
            array (
                'id' => '243',
                'name' => 'خنافره',
                'state_id' => '13',
                'city_id' => '268',
                'amar_code' => '61102',
            ),
            243 =>
            array (
                'id' => '244',
                'name' => 'خنجین',
                'state_id' => '28',
                'city_id' => '302',
                'amar_code' => '1302',
            ),
            244 =>
            array (
                'id' => '245',
                'name' => 'خواجه',
                'state_id' => '1',
                'city_id' => '439',
                'amar_code' => '31601',
            ),
            245 =>
            array (
                'id' => '246',
                'name' => 'خورش رستم',
                'state_id' => '3',
                'city_id' => '162',
                'amar_code' => '240301',
            ),
            246 =>
            array (
                'id' => '247',
                'name' => 'خورگام',
                'state_id' => '25',
                'city_id' => '217',
                'amar_code' => '10604',
            ),
            247 =>
            array (
                'id' => '248',
                'name' => 'دابودشت',
                'state_id' => '27',
                'city_id' => '14',
                'amar_code' => '20103',
            ),
            248 =>
            array (
                'id' => '249',
                'name' => 'دارخوین',
                'state_id' => '13',
                'city_id' => '268',
                'amar_code' => '61103',
            ),
            249 =>
            array (
                'id' => '250',
                'name' => 'داشلی برون',
                'state_id' => '24',
                'city_id' => '371',
                'amar_code' => '270602',
            ),
            250 =>
            array (
                'id' => '251',
                'name' => 'دالخانی',
                'state_id' => '27',
                'city_id' => '203',
                'amar_code' => '20602',
            ),
            251 =>
            array (
                'id' => '252',
                'name' => 'دامن',
                'state_id' => '16',
                'city_id' => '52',
                'amar_code' => '110106',
            ),
            252 =>
            array (
                'id' => '253',
                'name' => 'درب گنبد',
                'state_id' => '26',
                'city_id' => '352',
                'amar_code' => '150605',
            ),
            253 =>
            array (
                'id' => '254',
                'name' => 'درح',
                'state_id' => '10',
                'city_id' => '243',
                'amar_code' => '290303',
            ),
            254 =>
            array (
                'id' => '255',
                'name' => 'درودزن',
                'state_id' => '17',
                'city_id' => '392',
                'amar_code' => '71205',
            ),
            255 =>
            array (
                'id' => '256',
                'name' => 'دستگردان',
                'state_id' => '10',
                'city_id' => '290',
                'amar_code' => '291101',
            ),
            256 =>
            array (
                'id' => '257',
                'name' => 'دشت سر',
                'state_id' => '27',
                'city_id' => '14',
                'amar_code' => '20105',
            ),
            257 =>
            array (
                'id' => '258',
                'name' => 'دشت عباس',
                'state_id' => '6',
                'city_id' => '196',
                'amar_code' => '160305',
            ),
            258 =>
            array (
                'id' => '259',
                'name' => 'دشتابی',
                'state_id' => '18',
                'city_id' => '91',
                'amar_code' => '260106',
            ),
            259 =>
            array (
                'id' => '260',
                'name' => 'دشتک',
                'state_id' => '2',
                'city_id' => '141',
                'amar_code' => '41401',
            ),
            260 =>
            array (
                'id' => '261',
                'name' => 'دشمن زیاری',
                'state_id' => '17',
                'city_id' => '401',
                'amar_code' => '71305',
            ),
            261 =>
            array (
                'id' => '262',
                'name' => 'دلبران',
                'state_id' => '20',
                'city_id' => '321',
                'amar_code' => '120505',
            ),
            262 =>
            array (
                'id' => '263',
                'name' => 'دلوار',
                'state_id' => '7',
                'city_id' => '124',
                'amar_code' => '180201',
            ),
            263 =>
            array (
                'id' => '264',
                'name' => 'دوآب صمصامی',
                'state_id' => '9',
                'city_id' => '353',
                'amar_code' => '140603',
            ),
            264 =>
            array (
                'id' => '265',
                'name' => 'دودانگه',
                'state_id' => '27',
                'city_id' => '233',
                'amar_code' => '20702',
            ),
            265 =>
            array (
                'id' => '266',
                'name' => 'دهبکری',
                'state_id' => '21',
                'city_id' => '77',
                'amar_code' => '80208',
            ),
            266 =>
            array (
                'id' => '267',
                'name' => 'دهج',
                'state_id' => '21',
                'city_id' => '279',
                'amar_code' => '80702',
            ),
            267 =>
            array (
                'id' => '268',
                'name' => 'دهدز',
                'state_id' => '13',
                'city_id' => '51',
                'amar_code' => '60402',
            ),
            268 =>
            array (
                'id' => '269',
                'name' => 'دهرم',
                'state_id' => '17',
                'city_id' => '301',
                'amar_code' => '72202',
            ),
            269 =>
            array (
                'id' => '270',
                'name' => 'دهفری',
                'state_id' => '27',
                'city_id' => '307',
                'amar_code' => '22301',
            ),
            270 =>
            array (
                'id' => '271',
                'name' => 'دیشموک',
                'state_id' => '23',
                'city_id' => '355',
                'amar_code' => '170206',
            ),
            271 =>
            array (
                'id' => '272',
                'name' => 'دیلمان',
                'state_id' => '25',
                'city_id' => '262',
                'amar_code' => '11501',
            ),
            272 =>
            array (
                'id' => '273',
                'name' => 'دینور',
                'state_id' => '22',
                'city_id' => '286',
                'amar_code' => '51001',
            ),
            273 =>
            array (
                'id' => '274',
                'name' => 'دیهوک',
                'state_id' => '10',
                'city_id' => '290',
                'amar_code' => '291103',
            ),
            274 =>
            array (
                'id' => '275',
                'name' => 'ذلقی',
                'state_id' => '26',
                'city_id' => '41',
                'amar_code' => '150104',
            ),
            275 =>
            array (
                'id' => '276',
                'name' => 'رامند',
                'state_id' => '18',
                'city_id' => '91',
                'amar_code' => '260102',
            ),
            276 =>
            array (
                'id' => '277',
                'name' => 'رانکوه',
                'state_id' => '25',
                'city_id' => '42',
                'amar_code' => '11301',
            ),
            277 =>
            array (
                'id' => '278',
                'name' => 'راهگان',
                'state_id' => '17',
                'city_id' => '161',
                'amar_code' => '73402',
            ),
            278 =>
            array (
                'id' => '279',
                'name' => 'راین',
                'state_id' => '21',
                'city_id' => '338',
                'amar_code' => '80806',
            ),
            279 =>
            array (
                'id' => '280',
                'name' => 'رحمت آباد',
                'state_id' => '17',
                'city_id' => '226',
                'amar_code' => '73002',
            ),
            280 =>
            array (
                'id' => '281',
                'name' => 'رحمت آباد و بلوکات',
                'state_id' => '25',
                'city_id' => '217',
                'amar_code' => '10601',
            ),
            281 =>
            array (
                'id' => '282',
                'name' => 'رحیم آباد',
                'state_id' => '25',
                'city_id' => '219',
                'amar_code' => '10702',
            ),
            282 =>
            array (
                'id' => '283',
                'name' => 'رستاق',
                'state_id' => '17',
                'city_id' => '175',
                'amar_code' => '70502',
            ),
            283 =>
            array (
                'id' => '284',
                'name' => 'رضویه',
                'state_id' => '11',
                'city_id' => '396',
                'amar_code' => '91606',
            ),
            284 =>
            array (
                'id' => '285',
                'name' => 'رغیوه',
                'state_id' => '13',
                'city_id' => '441',
                'amar_code' => '62201',
            ),
            285 =>
            array (
                'id' => '286',
                'name' => 'روداب',
                'state_id' => '11',
                'city_id' => '237',
                'amar_code' => '90805',
            ),
            286 =>
            array (
                'id' => '287',
                'name' => 'روداب',
                'state_id' => '21',
                'city_id' => '420',
                'amar_code' => '82102',
            ),
            287 =>
            array (
                'id' => '288',
                'name' => 'رودبارالموت شرقی',
                'state_id' => '18',
                'city_id' => '322',
                'amar_code' => '260302',
            ),
            288 =>
            array (
                'id' => '289',
                'name' => 'رودبارالموت غربی',
                'state_id' => '18',
                'city_id' => '322',
                'amar_code' => '260303',
            ),
            289 =>
            array (
                'id' => '290',
                'name' => 'رودبارقصران',
                'state_id' => '8',
                'city_id' => '275',
                'amar_code' => '230401',
            ),
            290 =>
            array (
                'id' => '291',
                'name' => 'رودبست',
                'state_id' => '27',
                'city_id' => '56',
                'amar_code' => '21604',
            ),
            291 =>
            array (
                'id' => '292',
                'name' => 'رودبنه',
                'state_id' => '25',
                'city_id' => '376',
                'amar_code' => '11104',
            ),
            292 =>
            array (
                'id' => '293',
                'name' => 'رودپی',
                'state_id' => '27',
                'city_id' => '233',
                'amar_code' => '20706',
            ),
            293 =>
            array (
                'id' => '294',
                'name' => 'رودپی شمالی',
                'state_id' => '27',
                'city_id' => '233',
                'amar_code' => '20707',
            ),
            294 =>
            array (
                'id' => '295',
                'name' => 'رودخانه',
                'state_id' => '29',
                'city_id' => '216',
                'amar_code' => '220701',
            ),
            295 =>
            array (
                'id' => '296',
                'name' => 'روددشت',
                'state_id' => '9',
                'city_id' => '377',
                'amar_code' => '140405',
            ),
            296 =>
            array (
                'id' => '297',
                'name' => 'رودزرد',
                'state_id' => '13',
                'city_id' => '205',
                'amar_code' => '61003',
            ),
            297 =>
            array (
                'id' => '298',
                'name' => 'رودهن',
                'state_id' => '8',
                'city_id' => '191',
                'amar_code' => '230203',
            ),
            298 =>
            array (
                'id' => '299',
                'name' => 'رونیز',
                'state_id' => '17',
                'city_id' => '28',
                'amar_code' => '70201',
            ),
            299 =>
            array (
                'id' => '300',
                'name' => 'رویدر',
                'state_id' => '29',
                'city_id' => '164',
                'amar_code' => '221001',
            ),
            300 =>
            array (
                'id' => '301',
                'name' => 'ریز',
                'state_id' => '7',
                'city_id' => '133',
                'amar_code' => '180901',
            ),
            301 =>
            array (
                'id' => '302',
                'name' => 'ریگ',
                'state_id' => '7',
                'city_id' => '370',
                'amar_code' => '180702',
            ),
            302 =>
            array (
                'id' => '303',
                'name' => 'ریگ ملک',
                'state_id' => '16',
                'city_id' => '415',
                'amar_code' => '111703',
            ),
            303 =>
            array (
                'id' => '304',
                'name' => 'زارچ',
                'state_id' => '31',
                'city_id' => '448',
                'amar_code' => '210504',
            ),
            304 =>
            array (
                'id' => '305',
                'name' => 'زاغه',
                'state_id' => '26',
                'city_id' => '157',
                'amar_code' => '150305',
            ),
            305 =>
            array (
                'id' => '306',
                'name' => 'زاگرس',
                'state_id' => '6',
                'city_id' => '146',
                'amar_code' => '160405',
            ),
            306 =>
            array (
                'id' => '307',
                'name' => 'زالیان',
                'state_id' => '28',
                'city_id' => '269',
                'amar_code' => '703',
            ),
            307 =>
            array (
                'id' => '308',
                'name' => 'زاوین',
                'state_id' => '11',
                'city_id' => '340',
                'amar_code' => '92801',
            ),
            308 =>
            array (
                'id' => '309',
                'name' => 'زاینده رود',
                'state_id' => '9',
                'city_id' => '234',
                'amar_code' => '140802',
            ),
            309 =>
            array (
                'id' => '310',
                'name' => 'زبرخان',
                'state_id' => '11',
                'city_id' => '432',
                'amar_code' => '91702',
            ),
            310 =>
            array (
                'id' => '311',
                'name' => 'زرآباد',
                'state_id' => '16',
                'city_id' => '345',
                'amar_code' => '110901',
            ),
            311 =>
            array (
                'id' => '312',
                'name' => 'زرنه',
                'state_id' => '6',
                'city_id' => '54',
                'amar_code' => '160701',
            ),
            312 =>
            array (
                'id' => '313',
                'name' => 'زرین آباد',
                'state_id' => '6',
                'city_id' => '196',
                'amar_code' => '160301',
            ),
            313 =>
            array (
                'id' => '314',
                'name' => 'زرین دشت',
                'state_id' => '30',
                'city_id' => '428',
                'amar_code' => '130303',
            ),
            314 =>
            array (
                'id' => '315',
                'name' => 'ززوماهرو',
                'state_id' => '26',
                'city_id' => '41',
                'amar_code' => '150102',
            ),
            315 =>
            array (
                'id' => '316',
                'name' => 'زمکان',
                'state_id' => '22',
                'city_id' => '128',
                'amar_code' => '51203',
            ),
            316 =>
            array (
                'id' => '317',
                'name' => 'زنجانرود',
                'state_id' => '14',
                'city_id' => '230',
                'amar_code' => '190402',
            ),
            317 =>
            array (
                'id' => '318',
                'name' => 'زند',
                'state_id' => '30',
                'city_id' => '398',
                'amar_code' => '130204',
            ),
            318 =>
            array (
                'id' => '319',
                'name' => 'زنده رود',
                'state_id' => '4',
                'city_id' => '305',
                'amar_code' => '100604',
            ),
            319 =>
            array (
                'id' => '320',
                'name' => 'زواره',
                'state_id' => '4',
                'city_id' => '21',
                'amar_code' => '100102',
            ),
            320 =>
            array (
                'id' => '321',
                'name' => 'زهان',
                'state_id' => '10',
                'city_id' => '232',
                'amar_code' => '290902',
            ),
            321 =>
            array (
                'id' => '322',
                'name' => 'زیدآباد',
                'state_id' => '21',
                'city_id' => '264',
                'amar_code' => '80604',
            ),
            322 =>
            array (
                'id' => '323',
                'name' => 'زیدون',
                'state_id' => '13',
                'city_id' => '95',
                'amar_code' => '60602',
            ),
            323 =>
            array (
                'id' => '324',
                'name' => 'زیرآب',
                'state_id' => '27',
                'city_id' => '260',
                'amar_code' => '20803',
            ),
            324 =>
            array (
                'id' => '325',
                'name' => 'زیلایی',
                'state_id' => '23',
                'city_id' => '381',
                'amar_code' => '170902',
            ),
            325 =>
            array (
                'id' => '326',
                'name' => 'زیویه',
                'state_id' => '20',
                'city_id' => '252',
                'amar_code' => '120301',
            ),
            326 =>
            array (
                'id' => '327',
                'name' => 'سارال',
                'state_id' => '20',
                'city_id' => '199',
                'amar_code' => '120703',
            ),
            327 =>
            array (
                'id' => '328',
                'name' => 'ساربوک',
                'state_id' => '16',
                'city_id' => '325',
                'amar_code' => '111802',
            ),
            328 =>
            array (
                'id' => '329',
                'name' => 'ساردوییه',
                'state_id' => '21',
                'city_id' => '138',
                'amar_code' => '80301',
            ),
            329 =>
            array (
                'id' => '330',
                'name' => 'ساروق',
                'state_id' => '28',
                'city_id' => '19',
                'amar_code' => '103',
            ),
            330 =>
            array (
                'id' => '331',
                'name' => 'سامن',
                'state_id' => '30',
                'city_id' => '398',
                'amar_code' => '130202',
            ),
            331 =>
            array (
                'id' => '332',
                'name' => 'سبلان',
                'state_id' => '3',
                'city_id' => '249',
                'amar_code' => '241002',
            ),
            332 =>
            array (
                'id' => '333',
                'name' => 'سجاس رود',
                'state_id' => '14',
                'city_id' => '155',
                'amar_code' => '190304',
            ),
            333 =>
            array (
                'id' => '334',
                'name' => 'سده',
                'state_id' => '10',
                'city_id' => '318',
                'amar_code' => '290405',
            ),
            334 =>
            array (
                'id' => '335',
                'name' => 'سده',
                'state_id' => '17',
                'city_id' => '39',
                'amar_code' => '70301',
            ),
            335 =>
            array (
                'id' => '336',
                'name' => 'سراب باغ',
                'state_id' => '6',
                'city_id' => '3',
                'amar_code' => '160603',
            ),
            336 =>
            array (
                'id' => '337',
                'name' => 'سراب میمه',
                'state_id' => '6',
                'city_id' => '196',
                'amar_code' => '160304',
            ),
            337 =>
            array (
                'id' => '338',
                'name' => 'سراجو',
                'state_id' => '1',
                'city_id' => '389',
                'amar_code' => '30603',
            ),
            338 =>
            array (
                'id' => '339',
                'name' => 'سربند',
                'state_id' => '28',
                'city_id' => '269',
                'amar_code' => '702',
            ),
            339 =>
            array (
                'id' => '340',
                'name' => 'سرحد',
                'state_id' => '12',
                'city_id' => '284',
                'amar_code' => '280401',
            ),
            340 =>
            array (
                'id' => '341',
                'name' => 'سرخرود',
                'state_id' => '27',
                'city_id' => '388',
                'amar_code' => '21801',
            ),
            341 =>
            array (
                'id' => '342',
                'name' => 'سرخه',
                'state_id' => '15',
                'city_id' => '247',
                'amar_code' => '200801',
            ),
            342 =>
            array (
                'id' => '343',
                'name' => 'سردارجنگل',
                'state_id' => '25',
                'city_id' => '312',
                'amar_code' => '10903',
            ),
            343 =>
            array (
                'id' => '344',
                'name' => 'سردرود',
                'state_id' => '30',
                'city_id' => '209',
                'amar_code' => '130801',
            ),
            344 =>
            array (
                'id' => '345',
                'name' => 'سردشت',
                'state_id' => '13',
                'city_id' => '183',
                'amar_code' => '60801',
            ),
            345 =>
            array (
                'id' => '346',
                'name' => 'سرشیو',
                'state_id' => '20',
                'city_id' => '252',
                'amar_code' => '120303',
            ),
            346 =>
            array (
                'id' => '347',
                'name' => 'سرشیو',
                'state_id' => '20',
                'city_id' => '393',
                'amar_code' => '120602',
            ),
            347 =>
            array (
                'id' => '348',
                'name' => 'سرفاریاب',
                'state_id' => '23',
                'city_id' => '145',
                'amar_code' => '170602',
            ),
            348 =>
            array (
                'id' => '349',
                'name' => 'سرولایت',
                'state_id' => '11',
                'city_id' => '432',
                'amar_code' => '91703',
            ),
            349 =>
            array (
                'id' => '350',
                'name' => 'سریش آباد',
                'state_id' => '20',
                'city_id' => '321',
                'amar_code' => '120503',
            ),
            350 =>
            array (
                'id' => '351',
                'name' => 'سعدآباد',
                'state_id' => '7',
                'city_id' => '185',
                'amar_code' => '180301',
            ),
            351 =>
            array (
                'id' => '352',
                'name' => 'سلامی',
                'state_id' => '11',
                'city_id' => '169',
                'amar_code' => '91904',
            ),
            352 =>
            array (
                'id' => '353',
                'name' => 'سلطان آباد',
                'state_id' => '13',
                'city_id' => '205',
                'amar_code' => '61004',
            ),
            353 =>
            array (
                'id' => '354',
                'name' => 'سلفچگان',
                'state_id' => '19',
                'city_id' => '327',
                'amar_code' => '250105',
            ),
            354 =>
            array (
                'id' => '355',
                'name' => 'سلمانشهر',
                'state_id' => '27',
                'city_id' => '292',
                'amar_code' => '22402',
            ),
            355 =>
            array (
                'id' => '356',
                'name' => 'سلیمان',
                'state_id' => '11',
                'city_id' => '224',
                'amar_code' => '93502',
            ),
            356 =>
            array (
                'id' => '357',
                'name' => 'سملقان',
                'state_id' => '12',
                'city_id' => '384',
                'amar_code' => '280601',
            ),
            357 =>
            array (
                'id' => '358',
                'name' => 'سندرک',
                'state_id' => '29',
                'city_id' => '416',
                'amar_code' => '220502',
            ),
            358 =>
            array (
                'id' => '359',
                'name' => 'سنگان',
                'state_id' => '11',
                'city_id' => '169',
                'amar_code' => '91901',
            ),
            359 =>
            array (
                'id' => '360',
                'name' => 'سنگر',
                'state_id' => '25',
                'city_id' => '211',
                'amar_code' => '10502',
            ),
            360 =>
            array (
                'id' => '361',
                'name' => 'سورنا',
                'state_id' => '17',
                'city_id' => '210',
                'amar_code' => '72601',
            ),
            361 =>
            array (
                'id' => '362',
                'name' => 'سوری',
                'state_id' => '26',
                'city_id' => '220',
                'amar_code' => '151101',
            ),
            362 =>
            array (
                'id' => '363',
                'name' => 'سوسن',
                'state_id' => '13',
                'city_id' => '51',
                'amar_code' => '60404',
            ),
            363 =>
            array (
                'id' => '364',
                'name' => 'سوق',
                'state_id' => '23',
                'city_id' => '355',
                'amar_code' => '170207',
            ),
            364 =>
            array (
                'id' => '365',
                'name' => 'سومار',
                'state_id' => '22',
                'city_id' => '324',
                'amar_code' => '50601',
            ),
            365 =>
            array (
                'id' => '366',
                'name' => 'سویسه',
                'state_id' => '13',
                'city_id' => '330',
                'amar_code' => '62702',
            ),
            366 =>
            array (
                'id' => '367',
                'name' => 'سه قلعه',
                'state_id' => '10',
                'city_id' => '241',
                'amar_code' => '290602',
            ),
            367 =>
            array (
                'id' => '368',
                'name' => 'سیجوال',
                'state_id' => '24',
                'city_id' => '118',
                'amar_code' => '270203',
            ),
            368 =>
            array (
                'id' => '369',
                'name' => 'سیدان',
                'state_id' => '17',
                'city_id' => '392',
                'amar_code' => '71206',
            ),
            369 =>
            array (
                'id' => '370',
                'name' => 'سیراف',
                'state_id' => '7',
                'city_id' => '346',
                'amar_code' => '180603',
            ),
            370 =>
            array (
                'id' => '371',
                'name' => 'سیروان',
                'state_id' => '20',
                'city_id' => '259',
                'amar_code' => '120406',
            ),
            371 =>
            array (
                'id' => '372',
                'name' => 'سیلاخور',
                'state_id' => '26',
                'city_id' => '193',
                'amar_code' => '150501',
            ),
            372 =>
            array (
                'id' => '373',
                'name' => 'سیلوانه',
                'state_id' => '2',
                'city_id' => '26',
                'amar_code' => '40102',
            ),
            373 =>
            array (
                'id' => '374',
                'name' => 'سیمکان',
                'state_id' => '17',
                'city_id' => '137',
                'amar_code' => '70402',
            ),
            374 =>
            array (
                'id' => '375',
                'name' => 'سیمینه',
                'state_id' => '2',
                'city_id' => '89',
                'amar_code' => '41001',
            ),
            375 =>
            array (
                'id' => '376',
                'name' => 'سیوان',
                'state_id' => '6',
                'city_id' => '53',
                'amar_code' => '160104',
            ),
            376 =>
            array (
                'id' => '377',
                'name' => 'سیه رود',
                'state_id' => '1',
                'city_id' => '132',
                'amar_code' => '31901',
            ),
            377 =>
            array (
                'id' => '378',
                'name' => 'شادمهر',
                'state_id' => '11',
                'city_id' => '403',
                'amar_code' => '93001',
            ),
            378 =>
            array (
                'id' => '379',
                'name' => 'شادیان',
                'state_id' => '1',
                'city_id' => '140',
                'amar_code' => '32301',
            ),
            379 =>
            array (
                'id' => '380',
                'name' => 'شاسکوه',
                'state_id' => '10',
                'city_id' => '232',
                'amar_code' => '290903',
            ),
            380 =>
            array (
                'id' => '381',
                'name' => 'شال',
                'state_id' => '18',
                'city_id' => '91',
                'amar_code' => '260103',
            ),
            381 =>
            array (
                'id' => '382',
                'name' => 'شاندرمن',
                'state_id' => '25',
                'city_id' => '382',
                'amar_code' => '11601',
            ),
            382 =>
            array (
                'id' => '383',
                'name' => 'شاندیز',
                'state_id' => '11',
                'city_id' => '102',
                'amar_code' => '93202',
            ),
            383 =>
            array (
                'id' => '384',
                'name' => 'شاوور',
                'state_id' => '13',
                'city_id' => '276',
                'amar_code' => '61401',
            ),
            384 =>
            array (
                'id' => '385',
                'name' => 'شاهرود',
                'state_id' => '3',
                'city_id' => '162',
                'amar_code' => '240303',
            ),
            385 =>
            array (
                'id' => '386',
                'name' => 'شاهنجرین',
                'state_id' => '30',
                'city_id' => '180',
                'amar_code' => '131001',
            ),
            386 =>
            array (
                'id' => '387',
                'name' => 'شاهو',
                'state_id' => '22',
                'city_id' => '215',
                'amar_code' => '51401',
            ),
            387 =>
            array (
                'id' => '388',
                'name' => 'شاهیوند',
                'state_id' => '26',
                'city_id' => '147',
                'amar_code' => '151002',
            ),
            388 =>
            array (
                'id' => '389',
                'name' => 'شباب',
                'state_id' => '6',
                'city_id' => '146',
                'amar_code' => '160404',
            ),
            389 =>
            array (
                'id' => '390',
                'name' => 'شبانکاره',
                'state_id' => '7',
                'city_id' => '185',
                'amar_code' => '180302',
            ),
            390 =>
            array (
                'id' => '391',
                'name' => 'شراء',
                'state_id' => '30',
                'city_id' => '443',
                'amar_code' => '130405',
            ),
            391 =>
            array (
                'id' => '392',
                'name' => 'شریف آباد',
                'state_id' => '8',
                'city_id' => '106',
                'amar_code' => '231301',
            ),
            392 =>
            array (
                'id' => '393',
                'name' => 'ششتمد',
                'state_id' => '11',
                'city_id' => '237',
                'amar_code' => '90806',
            ),
            393 =>
            array (
                'id' => '394',
                'name' => 'ششده وقره بلاغ',
                'state_id' => '17',
                'city_id' => '309',
                'amar_code' => '70801',
            ),
            394 =>
            array (
                'id' => '395',
                'name' => 'ششطراز',
                'state_id' => '11',
                'city_id' => '163',
                'amar_code' => '92901',
            ),
            395 =>
            array (
                'id' => '396',
                'name' => 'شعیبیه',
                'state_id' => '13',
                'city_id' => '277',
                'amar_code' => '61203',
            ),
            396 =>
            array (
                'id' => '397',
                'name' => 'شنبه و طسوج',
                'state_id' => '7',
                'city_id' => '186',
                'amar_code' => '180403',
            ),
            397 =>
            array (
                'id' => '398',
                'name' => 'شوسف',
                'state_id' => '10',
                'city_id' => '429',
                'amar_code' => '290501',
            ),
            398 =>
            array (
                'id' => '399',
                'name' => 'شهاب',
                'state_id' => '29',
                'city_id' => '323',
                'amar_code' => '220401',
            ),
            399 =>
            array (
                'id' => '400',
                'name' => 'شهداد',
                'state_id' => '21',
                'city_id' => '338',
                'amar_code' => '80802',
            ),
            400 =>
            array (
                'id' => '401',
                'name' => 'شهرآباد',
                'state_id' => '11',
                'city_id' => '69',
                'amar_code' => '92303',
            ),
            401 =>
            array (
                'id' => '402',
                'name' => 'شهمیرزاد',
                'state_id' => '15',
                'city_id' => '405',
                'amar_code' => '200501',
            ),
            402 =>
            array (
                'id' => '403',
                'name' => 'شهیون',
                'state_id' => '13',
                'city_id' => '183',
                'amar_code' => '60804',
            ),
            403 =>
            array (
                'id' => '404',
                'name' => 'شیبکوه',
                'state_id' => '17',
                'city_id' => '309',
                'amar_code' => '70802',
            ),
            404 =>
            array (
                'id' => '405',
                'name' => 'شیبکوه',
                'state_id' => '29',
                'city_id' => '84',
                'amar_code' => '220302',
            ),
            405 =>
            array (
                'id' => '406',
                'name' => 'شیدا',
                'state_id' => '9',
                'city_id' => '79',
                'amar_code' => '140902',
            ),
            406 =>
            array (
                'id' => '407',
                'name' => 'شیرین سو',
                'state_id' => '30',
                'city_id' => '335',
                'amar_code' => '130503',
            ),
            407 =>
            array (
                'id' => '408',
                'name' => 'صابری',
                'state_id' => '16',
                'city_id' => '434',
                'amar_code' => '111502',
            ),
            408 =>
            array (
                'id' => '409',
                'name' => 'صالح آباد',
                'state_id' => '6',
                'city_id' => '407',
                'amar_code' => '160502',
            ),
            409 =>
            array (
                'id' => '410',
                'name' => 'صالح آباد',
                'state_id' => '30',
                'city_id' => '93',
                'amar_code' => '130703',
            ),
            410 =>
            array (
                'id' => '411',
                'name' => 'صحرای باغ',
                'state_id' => '17',
                'city_id' => '373',
                'amar_code' => '71108',
            ),
            411 =>
            array (
                'id' => '412',
                'name' => 'صفا دشت',
                'state_id' => '8',
                'city_id' => '397',
                'amar_code' => '231702',
            ),
            412 =>
            array (
                'id' => '413',
                'name' => 'صفاییه',
                'state_id' => '2',
                'city_id' => '174',
                'amar_code' => '40303',
            ),
            413 =>
            array (
                'id' => '414',
                'name' => 'صوغان',
                'state_id' => '21',
                'city_id' => '24',
                'amar_code' => '82302',
            ),
            414 =>
            array (
                'id' => '415',
                'name' => 'صوفیان',
                'state_id' => '1',
                'city_id' => '273',
                'amar_code' => '31401',
            ),
            415 =>
            array (
                'id' => '416',
                'name' => 'صومای برادوست',
                'state_id' => '2',
                'city_id' => '26',
                'amar_code' => '40103',
            ),
            416 =>
            array (
                'id' => '417',
                'name' => 'صیدون',
                'state_id' => '13',
                'city_id' => '59',
                'amar_code' => '61503',
            ),
            417 =>
            array (
                'id' => '418',
                'name' => 'ضیاءآباد',
                'state_id' => '18',
                'city_id' => '113',
                'amar_code' => '260203',
            ),
            418 =>
            array (
                'id' => '419',
                'name' => 'طارم سفلی',
                'state_id' => '18',
                'city_id' => '322',
                'amar_code' => '260304',
            ),
            419 =>
            array (
                'id' => '420',
                'name' => 'طاغنکوه',
                'state_id' => '11',
                'city_id' => '316',
                'amar_code' => '93302',
            ),
            420 =>
            array (
                'id' => '421',
                'name' => 'طرقبه',
                'state_id' => '11',
                'city_id' => '102',
                'amar_code' => '93201',
            ),
            421 =>
            array (
                'id' => '422',
                'name' => 'طرهان',
                'state_id' => '26',
                'city_id' => '352',
                'amar_code' => '150602',
            ),
            422 =>
            array (
                'id' => '423',
                'name' => 'طسوج',
                'state_id' => '17',
                'city_id' => '348',
                'amar_code' => '72802',
            ),
            423 =>
            array (
                'id' => '424',
                'name' => 'طغرل الجرد',
                'state_id' => '21',
                'city_id' => '351',
                'amar_code' => '81401',
            ),
            424 =>
            array (
                'id' => '425',
                'name' => 'عطاملک',
                'state_id' => '11',
                'city_id' => '136',
                'amar_code' => '93602',
            ),
            425 =>
            array (
                'id' => '426',
                'name' => 'عقدا',
                'state_id' => '31',
                'city_id' => '22',
                'amar_code' => '210103',
            ),
            426 =>
            array (
                'id' => '427',
                'name' => 'عقیلی',
                'state_id' => '13',
                'city_id' => '359',
                'amar_code' => '62001',
            ),
            427 =>
            array (
                'id' => '428',
                'name' => 'علامرودشت',
                'state_id' => '17',
                'city_id' => '375',
                'amar_code' => '71504',
            ),
            428 =>
            array (
                'id' => '429',
                'name' => 'عمارلو',
                'state_id' => '25',
                'city_id' => '217',
                'amar_code' => '10602',
            ),
            429 =>
            array (
                'id' => '430',
                'name' => 'عنبر',
                'state_id' => '13',
                'city_id' => '394',
                'amar_code' => '61305',
            ),
            430 =>
            array (
                'id' => '431',
                'name' => 'عنبران',
                'state_id' => '3',
                'city_id' => '425',
                'amar_code' => '240803',
            ),
            431 =>
            array (
                'id' => '432',
                'name' => 'غلامان',
                'state_id' => '12',
                'city_id' => '201',
                'amar_code' => '280802',
            ),
            432 =>
            array (
                'id' => '433',
                'name' => 'غیزانیه',
                'state_id' => '13',
                'city_id' => '49',
                'amar_code' => '60305',
            ),
            433 =>
            array (
                'id' => '434',
                'name' => 'فارغان',
                'state_id' => '29',
                'city_id' => '149',
                'amar_code' => '220801',
            ),
            434 =>
            array (
                'id' => '435',
                'name' => 'فتح المبین',
                'state_id' => '13',
                'city_id' => '276',
                'amar_code' => '61403',
            ),
            435 =>
            array (
                'id' => '436',
                'name' => 'فرح دشت',
                'state_id' => '11',
                'city_id' => '333',
                'amar_code' => '91405',
            ),
            436 =>
            array (
                'id' => '437',
                'name' => 'فرخشهر',
                'state_id' => '9',
                'city_id' => '281',
                'amar_code' => '140206',
            ),
            437 =>
            array (
                'id' => '438',
                'name' => 'فردوس',
                'state_id' => '21',
                'city_id' => '214',
                'amar_code' => '80405',
            ),
            438 =>
            array (
                'id' => '439',
                'name' => 'فسارود',
                'state_id' => '17',
                'city_id' => '175',
                'amar_code' => '70506',
            ),
            439 =>
            array (
                'id' => '440',
                'name' => 'فشاپویه',
                'state_id' => '8',
                'city_id' => '221',
                'amar_code' => '230302',
            ),
            440 =>
            array (
                'id' => '441',
                'name' => 'فلارد',
                'state_id' => '9',
                'city_id' => '377',
                'amar_code' => '140401',
            ),
            441 =>
            array (
                'id' => '442',
                'name' => 'فندرسک',
                'state_id' => '24',
                'city_id' => '206',
                'amar_code' => '271101',
            ),
            442 =>
            array (
                'id' => '443',
                'name' => 'فندقلو',
                'state_id' => '1',
                'city_id' => '48',
                'amar_code' => '30205',
            ),
            443 =>
            array (
                'id' => '444',
                'name' => 'فورگ',
                'state_id' => '17',
                'city_id' => '175',
                'amar_code' => '70504',
            ),
            444 =>
            array (
                'id' => '445',
                'name' => 'فیرورق',
                'state_id' => '2',
                'city_id' => '174',
                'amar_code' => '40306',
            ),
            445 =>
            array (
                'id' => '446',
                'name' => 'فیروز',
                'state_id' => '3',
                'city_id' => '349',
                'amar_code' => '240702',
            ),
            446 =>
            array (
                'id' => '447',
                'name' => 'فیروزآباد',
                'state_id' => '26',
                'city_id' => '253',
                'amar_code' => '150901',
            ),
            447 =>
            array (
                'id' => '448',
                'name' => 'فیروزاباد',
                'state_id' => '22',
                'city_id' => '339',
                'amar_code' => '50203',
            ),
            448 =>
            array (
                'id' => '449',
                'name' => 'فین',
                'state_id' => '29',
                'city_id' => '82',
                'amar_code' => '220203',
            ),
            449 =>
            array (
                'id' => '450',
                'name' => 'قرقری',
                'state_id' => '16',
                'city_id' => '447',
                'amar_code' => '111102',
            ),
            450 =>
            array (
                'id' => '451',
                'name' => 'قره پشتلو',
                'state_id' => '14',
                'city_id' => '230',
                'amar_code' => '190406',
            ),
            451 =>
            array (
                'id' => '452',
                'name' => 'قره چای',
                'state_id' => '28',
                'city_id' => '168',
                'amar_code' => '1201',
            ),
            452 =>
            array (
                'id' => '453',
                'name' => 'قره قویون',
                'state_id' => '2',
                'city_id' => '278',
                'amar_code' => '41701',
            ),
            453 =>
            array (
                'id' => '454',
                'name' => 'قره کهریز',
                'state_id' => '28',
                'city_id' => '269',
                'amar_code' => '704',
            ),
            454 =>
            array (
                'id' => '455',
                'name' => 'قشلاق دشت',
                'state_id' => '3',
                'city_id' => '101',
                'amar_code' => '240202',
            ),
            455 =>
            array (
                'id' => '456',
                'name' => 'قصابه',
                'state_id' => '3',
                'city_id' => '395',
                'amar_code' => '240405',
            ),
            456 =>
            array (
                'id' => '457',
                'name' => 'قطرویه',
                'state_id' => '17',
                'city_id' => '430',
                'amar_code' => '71404',
            ),
            457 =>
            array (
                'id' => '458',
                'name' => 'قطور',
                'state_id' => '2',
                'city_id' => '174',
                'amar_code' => '40305',
            ),
            458 =>
            array (
                'id' => '459',
                'name' => 'قلعه چای',
                'state_id' => '1',
                'city_id' => '293',
                'amar_code' => '32501',
            ),
            459 =>
            array (
                'id' => '460',
                'name' => 'قلعه شاهین',
                'state_id' => '22',
                'city_id' => '244',
                'amar_code' => '50402',
            ),
            460 =>
            array (
                'id' => '461',
                'name' => 'قلعه قاضی',
                'state_id' => '29',
                'city_id' => '82',
                'amar_code' => '220206',
            ),
            461 =>
            array (
                'id' => '462',
                'name' => 'قلعه نو',
                'state_id' => '8',
                'city_id' => '221',
                'amar_code' => '230306',
            ),
            462 =>
            array (
                'id' => '463',
                'name' => 'قلقل رود',
                'state_id' => '30',
                'city_id' => '125',
                'amar_code' => '130101',
            ),
            463 =>
            array (
                'id' => '464',
                'name' => 'قلندرآباد',
                'state_id' => '11',
                'city_id' => '308',
                'amar_code' => '92201',
            ),
            464 =>
            array (
                'id' => '465',
                'name' => 'قمصر',
                'state_id' => '4',
                'city_id' => '332',
                'amar_code' => '101002',
            ),
            465 =>
            array (
                'id' => '466',
                'name' => 'قوشخانه',
                'state_id' => '12',
                'city_id' => '284',
                'amar_code' => '280403',
            ),
            466 =>
            array (
                'id' => '467',
                'name' => 'قهدریجان',
                'state_id' => '4',
                'city_id' => '310',
                'amar_code' => '100803',
            ),
            467 =>
            array (
                'id' => '468',
                'name' => 'قهستان',
                'state_id' => '10',
                'city_id' => '181',
                'amar_code' => '290201',
            ),
            468 =>
            array (
                'id' => '469',
                'name' => 'کاخک',
                'state_id' => '11',
                'city_id' => '369',
                'amar_code' => '91503',
            ),
            469 =>
            array (
                'id' => '470',
                'name' => 'کارزان',
                'state_id' => '6',
                'city_id' => '265',
                'amar_code' => '160902',
            ),
            470 =>
            array (
                'id' => '471',
                'name' => 'کاغذکنان',
                'state_id' => '1',
                'city_id' => '413',
                'amar_code' => '31002',
            ),
            471 =>
            array (
                'id' => '472',
                'name' => 'کاکاوند',
                'state_id' => '26',
                'city_id' => '188',
                'amar_code' => '150401',
            ),
            472 =>
            array (
                'id' => '473',
                'name' => 'کاکی',
                'state_id' => '7',
                'city_id' => '186',
                'amar_code' => '180401',
            ),
            473 =>
            array (
                'id' => '474',
                'name' => 'کالپوش',
                'state_id' => '15',
                'city_id' => '410',
                'amar_code' => '200702',
            ),
            474 =>
            array (
                'id' => '475',
                'name' => 'کامفیروز',
                'state_id' => '17',
                'city_id' => '392',
                'amar_code' => '71203',
            ),
            475 =>
            array (
                'id' => '476',
                'name' => 'کامفیروز شمالی',
                'state_id' => '17',
                'city_id' => '392',
                'amar_code' => '71207',
            ),
            476 =>
            array (
                'id' => '477',
                'name' => 'کبگیان',
                'state_id' => '23',
                'city_id' => '90',
                'amar_code' => '170105',
            ),
            477 =>
            array (
                'id' => '478',
                'name' => 'کجور',
                'state_id' => '27',
                'city_id' => '427',
                'amar_code' => '21502',
            ),
            478 =>
            array (
                'id' => '479',
                'name' => 'کدکن',
                'state_id' => '11',
                'city_id' => '117',
                'amar_code' => '90505',
            ),
            479 =>
            array (
                'id' => '480',
                'name' => 'کرانی',
                'state_id' => '20',
                'city_id' => '98',
                'amar_code' => '120202',
            ),
            480 =>
            array (
                'id' => '481',
                'name' => 'کربال',
                'state_id' => '17',
                'city_id' => '156',
                'amar_code' => '72901',
            ),
            481 =>
            array (
                'id' => '482',
                'name' => 'کرچمبو',
                'state_id' => '4',
                'city_id' => '86',
                'amar_code' => '102402',
            ),
            482 =>
            array (
                'id' => '483',
                'name' => 'کردیان',
                'state_id' => '17',
                'city_id' => '137',
                'amar_code' => '70403',
            ),
            483 =>
            array (
                'id' => '484',
                'name' => 'کرفتو',
                'state_id' => '20',
                'city_id' => '199',
                'amar_code' => '120701',
            ),
            484 =>
            array (
                'id' => '485',
                'name' => 'کرگان رود',
                'state_id' => '25',
                'city_id' => '291',
                'amar_code' => '10404',
            ),
            485 =>
            array (
                'id' => '486',
                'name' => 'کرون',
                'state_id' => '4',
                'city_id' => '127',
                'amar_code' => '101901',
            ),
            486 =>
            array (
                'id' => '487',
                'name' => 'کشاورز',
                'state_id' => '2',
                'city_id' => '271',
                'amar_code' => '41102',
            ),
            487 =>
            array (
                'id' => '488',
                'name' => 'کشکوییه',
                'state_id' => '21',
                'city_id' => '214',
                'amar_code' => '80403',
            ),
            488 =>
            array (
                'id' => '489',
                'name' => 'کلات',
                'state_id' => '6',
                'city_id' => '3',
                'amar_code' => '160601',
            ),
            489 =>
            array (
                'id' => '490',
                'name' => 'کلاتان',
                'state_id' => '16',
                'city_id' => '78',
                'amar_code' => '112002',
            ),
            490 =>
            array (
                'id' => '491',
                'name' => 'کلاترزان',
                'state_id' => '20',
                'city_id' => '259',
                'amar_code' => '120404',
            ),
            491 =>
            array (
                'id' => '492',
                'name' => 'کلاچای',
                'state_id' => '25',
                'city_id' => '219',
                'amar_code' => '10705',
            ),
            492 =>
            array (
                'id' => '493',
                'name' => 'کلار',
                'state_id' => '27',
                'city_id' => '292',
                'amar_code' => '22403',
            ),
            493 =>
            array (
                'id' => '494',
                'name' => 'کلاشی',
                'state_id' => '22',
                'city_id' => '134',
                'amar_code' => '50904',
            ),
            494 =>
            array (
                'id' => '495',
                'name' => 'کلباد',
                'state_id' => '27',
                'city_id' => '367',
                'amar_code' => '22201',
            ),
            495 =>
            array (
                'id' => '496',
                'name' => 'کلیایی',
                'state_id' => '22',
                'city_id' => '258',
                'amar_code' => '50502',
            ),
            496 =>
            array (
                'id' => '497',
                'name' => 'کلیجان رستاق',
                'state_id' => '27',
                'city_id' => '233',
                'amar_code' => '20705',
            ),
            497 =>
            array (
                'id' => '498',
                'name' => 'کمالان',
                'state_id' => '24',
                'city_id' => '295',
                'amar_code' => '270302',
            ),
            498 =>
            array (
                'id' => '499',
                'name' => 'کمره',
                'state_id' => '28',
                'city_id' => '165',
                'amar_code' => '402',
            ),
            499 =>
            array (
                'id' => '500',
                'name' => 'کن',
                'state_id' => '8',
                'city_id' => '126',
                'amar_code' => '230101',
            ),
        ));
        \DB::table('districts')->insert(array (
            0 =>
            array (
                'id' => '501',
                'name' => 'کنار تخته و کمارج',
                'state_id' => '17',
                'city_id' => '331',
                'amar_code' => '71002',
            ),
            1 =>
            array (
                'id' => '502',
                'name' => 'کندوان',
                'state_id' => '1',
                'city_id' => '413',
                'amar_code' => '31003',
            ),
            2 =>
            array (
                'id' => '503',
                'name' => 'کوچصفهان',
                'state_id' => '25',
                'city_id' => '211',
                'amar_code' => '10503',
            ),
            3 =>
            array (
                'id' => '504',
                'name' => 'کوخرد-هرنگ',
                'state_id' => '29',
                'city_id' => '74',
                'amar_code' => '220903',
            ),
            4 =>
            array (
                'id' => '505',
                'name' => 'کوراییم',
                'state_id' => '3',
                'city_id' => '431',
                'amar_code' => '240902',
            ),
            5 =>
            array (
                'id' => '506',
                'name' => 'کورین',
                'state_id' => '16',
                'city_id' => '225',
                'amar_code' => '110504',
            ),
            6 =>
            array (
                'id' => '507',
                'name' => 'کوزران',
                'state_id' => '22',
                'city_id' => '339',
                'amar_code' => '50207',
            ),
            7 =>
            array (
                'id' => '508',
                'name' => 'کوشکنار',
                'state_id' => '29',
                'city_id' => '104',
                'amar_code' => '221101',
            ),
            8 =>
            array (
                'id' => '509',
                'name' => 'کومله',
                'state_id' => '25',
                'city_id' => '380',
                'amar_code' => '11003',
            ),
            9 =>
            array (
                'id' => '510',
                'name' => 'کوهپایه',
                'state_id' => '4',
                'city_id' => '37',
                'amar_code' => '100202',
            ),
            10 =>
            array (
                'id' => '511',
                'name' => 'کوهسار',
                'state_id' => '2',
                'city_id' => '255',
                'amar_code' => '40502',
            ),
            11 =>
            array (
                'id' => '512',
                'name' => 'کوهسارات',
                'state_id' => '24',
                'city_id' => '417',
                'amar_code' => '270705',
            ),
            12 =>
            array (
                'id' => '513',
                'name' => 'کوهساران',
                'state_id' => '21',
                'city_id' => '207',
                'amar_code' => '81101',
            ),
            13 =>
            array (
                'id' => '514',
                'name' => 'کوهمره',
                'state_id' => '17',
                'city_id' => '350',
                'amar_code' => '73302',
            ),
            14 =>
            array (
                'id' => '515',
                'name' => 'کوهنانی',
                'state_id' => '26',
                'city_id' => '352',
                'amar_code' => '150604',
            ),
            15 =>
            array (
                'id' => '516',
                'name' => 'کوهنجان',
                'state_id' => '17',
                'city_id' => '251',
                'amar_code' => '72501',
            ),
            16 =>
            array (
                'id' => '517',
                'name' => 'کوهین',
                'state_id' => '18',
                'city_id' => '322',
                'amar_code' => '260307',
            ),
            17 =>
            array (
                'id' => '518',
                'name' => 'کویرات',
                'state_id' => '4',
                'city_id' => '7',
                'amar_code' => '101801',
            ),
            18 =>
            array (
                'id' => '519',
                'name' => 'کهریزک',
                'state_id' => '8',
                'city_id' => '221',
                'amar_code' => '230303',
            ),
            19 =>
            array (
                'id' => '520',
                'name' => 'کهک',
                'state_id' => '19',
                'city_id' => '327',
                'amar_code' => '250104',
            ),
            20 =>
            array (
                'id' => '521',
                'name' => 'کهن آباد',
                'state_id' => '15',
                'city_id' => '6',
                'amar_code' => '200602',
            ),
            21 =>
            array (
                'id' => '522',
                'name' => 'کیاشهر',
                'state_id' => '25',
                'city_id' => '10',
                'amar_code' => '10201',
            ),
            22 =>
            array (
                'id' => '523',
                'name' => 'کیش',
                'state_id' => '29',
                'city_id' => '84',
                'amar_code' => '220303',
            ),
            23 =>
            array (
                'id' => '524',
                'name' => 'کیشکور',
                'state_id' => '16',
                'city_id' => '242',
                'amar_code' => '112302',
            ),
            24 =>
            array (
                'id' => '525',
                'name' => 'گاریزات',
                'state_id' => '31',
                'city_id' => '119',
                'amar_code' => '210304',
            ),
            25 =>
            array (
                'id' => '526',
                'name' => 'گافروپارمون',
                'state_id' => '29',
                'city_id' => '75',
                'amar_code' => '221303',
            ),
            26 =>
            array (
                'id' => '527',
                'name' => 'گتاب',
                'state_id' => '27',
                'city_id' => '55',
                'amar_code' => '20206',
            ),
            27 =>
            array (
                'id' => '528',
                'name' => 'گتیج',
                'state_id' => '16',
                'city_id' => '311',
                'amar_code' => '111902',
            ),
            28 =>
            array (
                'id' => '529',
                'name' => 'گچی',
                'state_id' => '6',
                'city_id' => '400',
                'amar_code' => '160802',
            ),
            29 =>
            array (
                'id' => '530',
                'name' => 'گرکن جنوبی',
                'state_id' => '4',
                'city_id' => '386',
                'amar_code' => '101702',
            ),
            30 =>
            array (
                'id' => '531',
                'name' => 'گرمادوز',
                'state_id' => '1',
                'city_id' => '154',
                'amar_code' => '32602',
            ),
            31 =>
            array (
                'id' => '532',
                'name' => 'گرمخان',
                'state_id' => '12',
                'city_id' => '65',
                'amar_code' => '280205',
            ),
            32 =>
            array (
                'id' => '533',
                'name' => 'گزیک',
                'state_id' => '10',
                'city_id' => '181',
                'amar_code' => '290202',
            ),
            33 =>
            array (
                'id' => '534',
                'name' => 'گل تپه',
                'state_id' => '30',
                'city_id' => '335',
                'amar_code' => '130501',
            ),
            34 =>
            array (
                'id' => '535',
                'name' => 'گلباف',
                'state_id' => '21',
                'city_id' => '338',
                'amar_code' => '80803',
            ),
            35 =>
            array (
                'id' => '536',
                'name' => 'گلبهار',
                'state_id' => '11',
                'city_id' => '148',
                'amar_code' => '91801',
            ),
            36 =>
            array (
                'id' => '537',
                'name' => 'گلدشت',
                'state_id' => '24',
                'city_id' => '368',
                'amar_code' => '271302',
            ),
            37 =>
            array (
                'id' => '538',
                'name' => 'گلزار',
                'state_id' => '21',
                'city_id' => '70',
                'amar_code' => '81003',
            ),
            38 =>
            array (
                'id' => '539',
                'name' => 'گلستان',
                'state_id' => '8',
                'city_id' => '94',
                'amar_code' => '231901',
            ),
            39 =>
            array (
                'id' => '540',
                'name' => 'گلستان',
                'state_id' => '21',
                'city_id' => '264',
                'amar_code' => '80603',
            ),
            40 =>
            array (
                'id' => '541',
                'name' => 'گلگیر',
                'state_id' => '13',
                'city_id' => '394',
                'amar_code' => '61304',
            ),
            41 =>
            array (
                'id' => '542',
                'name' => 'گله دار',
                'state_id' => '17',
                'city_id' => '406',
                'amar_code' => '72101',
            ),
            42 =>
            array (
                'id' => '543',
                'name' => 'گلی داغ',
                'state_id' => '24',
                'city_id' => '390',
                'amar_code' => '271202',
            ),
            43 =>
            array (
                'id' => '544',
                'name' => 'گمبوعه',
                'state_id' => '13',
                'city_id' => '150',
                'amar_code' => '62502',
            ),
            44 =>
            array (
                'id' => '545',
                'name' => 'گنبکی',
                'state_id' => '21',
                'city_id' => '222',
                'amar_code' => '81701',
            ),
            45 =>
            array (
                'id' => '546',
                'name' => 'گندمان',
                'state_id' => '9',
                'city_id' => '72',
                'amar_code' => '140102',
            ),
            46 =>
            array (
                'id' => '547',
                'name' => 'گواور',
                'state_id' => '22',
                'city_id' => '372',
                'amar_code' => '50802',
            ),
            47 =>
            array (
                'id' => '548',
                'name' => 'گوگان',
                'state_id' => '1',
                'city_id' => '5',
                'amar_code' => '32101',
            ),
            48 =>
            array (
                'id' => '549',
                'name' => 'گوهران',
                'state_id' => '29',
                'city_id' => '75',
                'amar_code' => '221302',
            ),
            49 =>
            array (
                'id' => '550',
                'name' => 'گوهرکوه',
                'state_id' => '16',
                'city_id' => '120',
                'amar_code' => '112103',
            ),
            50 =>
            array (
                'id' => '551',
                'name' => 'گهرباران',
                'state_id' => '27',
                'city_id' => '412',
                'amar_code' => '22502',
            ),
            51 =>
            array (
                'id' => '552',
                'name' => 'گهواره',
                'state_id' => '22',
                'city_id' => '176',
                'amar_code' => '51301',
            ),
            52 =>
            array (
                'id' => '553',
                'name' => 'گیان',
                'state_id' => '30',
                'city_id' => '428',
                'amar_code' => '130304',
            ),
            53 =>
            array (
                'id' => '554',
                'name' => 'گیل خوران',
                'state_id' => '27',
                'city_id' => '135',
                'amar_code' => '22101',
            ),
            54 =>
            array (
                'id' => '555',
                'name' => 'گیلوان',
                'state_id' => '14',
                'city_id' => '288',
                'amar_code' => '190803',
            ),
            55 =>
            array (
                'id' => '556',
                'name' => 'لاجان',
                'state_id' => '2',
                'city_id' => '111',
                'amar_code' => '40202',
            ),
            56 =>
            array (
                'id' => '557',
                'name' => 'لادیز',
                'state_id' => '16',
                'city_id' => '415',
                'amar_code' => '111702',
            ),
            57 =>
            array (
                'id' => '558',
                'name' => 'لاران',
                'state_id' => '9',
                'city_id' => '281',
                'amar_code' => '140205',
            ),
            58 =>
            array (
                'id' => '559',
                'name' => 'لاریجان',
                'state_id' => '27',
                'city_id' => '14',
                'amar_code' => '20101',
            ),
            59 =>
            array (
                'id' => '560',
                'name' => 'لاشار',
                'state_id' => '16',
                'city_id' => '433',
                'amar_code' => '110704',
            ),
            60 =>
            array (
                'id' => '561',
                'name' => 'لالجین',
                'state_id' => '30',
                'city_id' => '93',
                'amar_code' => '130701',
            ),
            61 =>
            array (
                'id' => '562',
                'name' => 'لاله آباد',
                'state_id' => '27',
                'city_id' => '55',
                'amar_code' => '20205',
            ),
            62 =>
            array (
                'id' => '563',
                'name' => 'لاله زار',
                'state_id' => '21',
                'city_id' => '70',
                'amar_code' => '81002',
            ),
            63 =>
            array (
                'id' => '564',
                'name' => 'لشت نشاء',
                'state_id' => '25',
                'city_id' => '211',
                'amar_code' => '10504',
            ),
            64 =>
            array (
                'id' => '565',
                'name' => 'لطف آباد',
                'state_id' => '11',
                'city_id' => '179',
                'amar_code' => '90702',
            ),
            65 =>
            array (
                'id' => '566',
                'name' => 'لواسانات',
                'state_id' => '8',
                'city_id' => '275',
                'amar_code' => '230402',
            ),
            66 =>
            array (
                'id' => '567',
                'name' => 'لوداب',
                'state_id' => '23',
                'city_id' => '90',
                'amar_code' => '170104',
            ),
            67 =>
            array (
                'id' => '568',
                'name' => 'لوندویل',
                'state_id' => '25',
                'city_id' => '9',
                'amar_code' => '10102',
            ),
            68 =>
            array (
                'id' => '569',
                'name' => 'لوه',
                'state_id' => '24',
                'city_id' => '358',
                'amar_code' => '271402',
            ),
            69 =>
            array (
                'id' => '570',
                'name' => 'لیردف',
                'state_id' => '29',
                'city_id' => '130',
                'amar_code' => '220603',
            ),
            70 =>
            array (
                'id' => '571',
                'name' => 'لیلان',
                'state_id' => '1',
                'city_id' => '399',
                'amar_code' => '32001',
            ),
            71 =>
            array (
                'id' => '572',
                'name' => 'ماژین',
                'state_id' => '6',
                'city_id' => '182',
                'amar_code' => '160204',
            ),
            72 =>
            array (
                'id' => '573',
                'name' => 'مانه',
                'state_id' => '12',
                'city_id' => '384',
                'amar_code' => '280602',
            ),
            73 =>
            array (
                'id' => '574',
                'name' => 'ماهان',
                'state_id' => '21',
                'city_id' => '338',
                'amar_code' => '80804',
            ),
            74 =>
            array (
                'id' => '575',
                'name' => 'ماهورمیلانی',
                'state_id' => '17',
                'city_id' => '401',
                'amar_code' => '71302',
            ),
            75 =>
            array (
                'id' => '576',
                'name' => 'ماهیدشت',
                'state_id' => '22',
                'city_id' => '339',
                'amar_code' => '50206',
            ),
            76 =>
            array (
                'id' => '577',
                'name' => 'محمدیار',
                'state_id' => '2',
                'city_id' => '423',
                'amar_code' => '40903',
            ),
            77 =>
            array (
                'id' => '578',
                'name' => 'محمدیه',
                'state_id' => '18',
                'city_id' => '40',
                'amar_code' => '260501',
            ),
            78 =>
            array (
                'id' => '579',
                'name' => 'محمله',
                'state_id' => '17',
                'city_id' => '167',
                'amar_code' => '72401',
            ),
            79 =>
            array (
                'id' => '580',
                'name' => 'مرادلو',
                'state_id' => '3',
                'city_id' => '395',
                'amar_code' => '240404',
            ),
            80 =>
            array (
                'id' => '581',
                'name' => 'مرحمت آباد',
                'state_id' => '2',
                'city_id' => '411',
                'amar_code' => '40804',
            ),
            81 =>
            array (
                'id' => '582',
                'name' => 'مرزداران',
                'state_id' => '11',
                'city_id' => '246',
                'amar_code' => '92001',
            ),
            82 =>
            array (
                'id' => '583',
                'name' => 'مرزن آباد',
                'state_id' => '27',
                'city_id' => '142',
                'amar_code' => '22003',
            ),
            83 =>
            array (
                'id' => '584',
                'name' => 'مرکزی',
                'state_id' => '3',
                'city_id' => '20',
                'amar_code' => '240101',
            ),
            84 =>
            array (
                'id' => '585',
                'name' => 'مرکزی',
                'state_id' => '3',
                'city_id' => '38',
                'amar_code' => '241101',
            ),
            85 =>
            array (
                'id' => '586',
                'name' => 'مرکزی',
                'state_id' => '3',
                'city_id' => '101',
                'amar_code' => '240201',
            ),
            86 =>
            array (
                'id' => '587',
                'name' => 'مرکزی',
                'state_id' => '3',
                'city_id' => '103',
                'amar_code' => '240601',
            ),
            87 =>
            array (
                'id' => '588',
                'name' => 'مرکزی',
                'state_id' => '3',
                'city_id' => '162',
                'amar_code' => '240304',
            ),
            88 =>
            array (
                'id' => '589',
                'name' => 'مرکزی',
                'state_id' => '3',
                'city_id' => '249',
                'amar_code' => '241001',
            ),
            89 =>
            array (
                'id' => '590',
                'name' => 'مرکزی',
                'state_id' => '3',
                'city_id' => '349',
                'amar_code' => '240701',
            ),
            90 =>
            array (
                'id' => '591',
                'name' => 'مرکزی',
                'state_id' => '3',
                'city_id' => '365',
                'amar_code' => '240504',
            ),
            91 =>
            array (
                'id' => '592',
                'name' => 'مرکزی',
                'state_id' => '3',
                'city_id' => '395',
                'amar_code' => '240402',
            ),
            92 =>
            array (
                'id' => '593',
                'name' => 'مرکزی',
                'state_id' => '3',
                'city_id' => '425',
                'amar_code' => '240802',
            ),
            93 =>
            array (
                'id' => '594',
                'name' => 'مرکزی',
                'state_id' => '3',
                'city_id' => '431',
                'amar_code' => '240901',
            ),
            94 =>
            array (
                'id' => '595',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '21',
                'amar_code' => '100101',
            ),
            95 =>
            array (
                'id' => '596',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '37',
                'amar_code' => '100203',
            ),
            96 =>
            array (
                'id' => '597',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '7',
                'amar_code' => '101802',
            ),
            97 =>
            array (
                'id' => '598',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '68',
                'amar_code' => '102202',
            ),
            98 =>
            array (
                'id' => '599',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '86',
                'amar_code' => '102401',
            ),
            99 =>
            array (
                'id' => '600',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '127',
                'amar_code' => '101902',
            ),
            100 =>
            array (
                'id' => '601',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '139',
                'amar_code' => '102002',
            ),
            101 =>
            array (
                'id' => '602',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '166',
                'amar_code' => '100301',
            ),
            102 =>
            array (
                'id' => '603',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '170',
                'amar_code' => '100401',
            ),
            103 =>
            array (
                'id' => '604',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '171',
                'amar_code' => '102301',
            ),
            104 =>
            array (
                'id' => '605',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '194',
                'amar_code' => '102101',
            ),
            105 =>
            array (
                'id' => '606',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '257',
                'amar_code' => '100502',
            ),
            106 =>
            array (
                'id' => '607',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '272',
                'amar_code' => '101603',
            ),
            107 =>
            array (
                'id' => '608',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '280',
                'amar_code' => '100902',
            ),
            108 =>
            array (
                'id' => '609',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '305',
                'amar_code' => '100603',
            ),
            109 =>
            array (
                'id' => '610',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '306',
                'amar_code' => '100701',
            ),
            110 =>
            array (
                'id' => '611',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '310',
                'amar_code' => '100802',
            ),
            111 =>
            array (
                'id' => '612',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '332',
                'amar_code' => '101003',
            ),
            112 =>
            array (
                'id' => '613',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '366',
                'amar_code' => '101101',
            ),
            113 =>
            array (
                'id' => '614',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '378',
                'amar_code' => '101203',
            ),
            114 =>
            array (
                'id' => '615',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '386',
                'amar_code' => '101701',
            ),
            115 =>
            array (
                'id' => '616',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '418',
                'amar_code' => '101303',
            ),
            116 =>
            array (
                'id' => '617',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '419',
                'amar_code' => '101402',
            ),
            117 =>
            array (
                'id' => '618',
                'name' => 'مرکزی',
                'state_id' => '4',
                'city_id' => '421',
                'amar_code' => '101501',
            ),
            118 =>
            array (
                'id' => '619',
                'name' => 'مرکزی',
                'state_id' => '5',
                'city_id' => '34',
                'amar_code' => '300501',
            ),
            119 =>
            array (
                'id' => '620',
                'name' => 'مرکزی',
                'state_id' => '5',
                'city_id' => '235',
                'amar_code' => '300201',
            ),
            120 =>
            array (
                'id' => '621',
                'name' => 'مرکزی',
                'state_id' => '5',
                'city_id' => '289',
                'amar_code' => '300401',
            ),
            121 =>
            array (
                'id' => '622',
                'name' => 'مرکزی',
                'state_id' => '5',
                'city_id' => '304',
                'amar_code' => '300601',
            ),
            122 =>
            array (
                'id' => '623',
                'name' => 'مرکزی',
                'state_id' => '5',
                'city_id' => '336',
                'amar_code' => '300101',
            ),
            123 =>
            array (
                'id' => '624',
                'name' => 'مرکزی',
                'state_id' => '5',
                'city_id' => '422',
                'amar_code' => '300301',
            ),
            124 =>
            array (
                'id' => '625',
                'name' => 'مرکزی',
                'state_id' => '6',
                'city_id' => '53',
                'amar_code' => '160103',
            ),
            125 =>
            array (
                'id' => '626',
                'name' => 'مرکزی',
                'state_id' => '6',
                'city_id' => '54',
                'amar_code' => '160702',
            ),
            126 =>
            array (
                'id' => '627',
                'name' => 'مرکزی',
                'state_id' => '6',
                'city_id' => '3',
                'amar_code' => '160602',
            ),
            127 =>
            array (
                'id' => '628',
                'name' => 'مرکزی',
                'state_id' => '6',
                'city_id' => '67',
                'amar_code' => '161001',
            ),
            128 =>
            array (
                'id' => '629',
                'name' => 'مرکزی',
                'state_id' => '6',
                'city_id' => '146',
                'amar_code' => '160402',
            ),
            129 =>
            array (
                'id' => '630',
                'name' => 'مرکزی',
                'state_id' => '6',
                'city_id' => '182',
                'amar_code' => '160203',
            ),
            130 =>
            array (
                'id' => '631',
                'name' => 'مرکزی',
                'state_id' => '6',
                'city_id' => '196',
                'amar_code' => '160302',
            ),
            131 =>
            array (
                'id' => '632',
                'name' => 'مرکزی',
                'state_id' => '6',
                'city_id' => '265',
                'amar_code' => '160901',
            ),
            132 =>
            array (
                'id' => '633',
                'name' => 'مرکزی',
                'state_id' => '6',
                'city_id' => '400',
                'amar_code' => '160801',
            ),
            133 =>
            array (
                'id' => '634',
                'name' => 'مرکزی',
                'state_id' => '6',
                'city_id' => '407',
                'amar_code' => '160503',
            ),
            134 =>
            array (
                'id' => '635',
                'name' => 'مرکزی',
                'state_id' => '6',
                'city_id' => '442',
                'amar_code' => '161101',
            ),
            135 =>
            array (
                'id' => '636',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '31',
                'amar_code' => '32202',
            ),
            136 =>
            array (
                'id' => '637',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '48',
                'amar_code' => '30202',
            ),
            137 =>
            array (
                'id' => '638',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '73',
                'amar_code' => '31302',
            ),
            138 =>
            array (
                'id' => '639',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '80',
                'amar_code' => '31201',
            ),
            139 =>
            array (
                'id' => '640',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '115',
                'amar_code' => '30303',
            ),
            140 =>
            array (
                'id' => '641',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '132',
                'amar_code' => '31902',
            ),
            141 =>
            array (
                'id' => '642',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '140',
                'amar_code' => '32302',
            ),
            142 =>
            array (
                'id' => '643',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '154',
                'amar_code' => '32601',
            ),
            143 =>
            array (
                'id' => '644',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '239',
                'amar_code' => '30501',
            ),
            144 =>
            array (
                'id' => '645',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '273',
                'amar_code' => '31402',
            ),
            145 =>
            array (
                'id' => '646',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '293',
                'amar_code' => '32502',
            ),
            146 =>
            array (
                'id' => '647',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '343',
                'amar_code' => '31502',
            ),
            147 =>
            array (
                'id' => '648',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '389',
                'amar_code' => '30602',
            ),
            148 =>
            array (
                'id' => '649',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '391',
                'amar_code' => '30702',
            ),
            149 =>
            array (
                'id' => '650',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '399',
                'amar_code' => '32002',
            ),
            150 =>
            array (
                'id' => '651',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '413',
                'amar_code' => '31004',
            ),
            151 =>
            array (
                'id' => '652',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '439',
                'amar_code' => '31602',
            ),
            152 =>
            array (
                'id' => '653',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '440',
                'amar_code' => '31102',
            ),
            153 =>
            array (
                'id' => '654',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '445',
                'amar_code' => '32701',
            ),
            154 =>
            array (
                'id' => '655',
                'name' => 'مرکزی',
                'state_id' => '1',
                'city_id' => '436',
                'amar_code' => '32402',
            ),
            155 =>
            array (
                'id' => '656',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '26',
                'amar_code' => '40104',
            ),
            156 =>
            array (
                'id' => '657',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '36',
                'amar_code' => '41301',
            ),
            157 =>
            array (
                'id' => '658',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '89',
                'amar_code' => '41002',
            ),
            158 =>
            array (
                'id' => '659',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '110',
                'amar_code' => '41502',
            ),
            159 =>
            array (
                'id' => '660',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '111',
                'amar_code' => '40201',
            ),
            160 =>
            array (
                'id' => '661',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '122',
                'amar_code' => '41201',
            ),
            161 =>
            array (
                'id' => '662',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '141',
                'amar_code' => '41402',
            ),
            162 =>
            array (
                'id' => '663',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '144',
                'amar_code' => '41602',
            ),
            163 =>
            array (
                'id' => '664',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '174',
                'amar_code' => '40302',
            ),
            164 =>
            array (
                'id' => '665',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '248',
                'amar_code' => '40401',
            ),
            165 =>
            array (
                'id' => '666',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '255',
                'amar_code' => '40501',
            ),
            166 =>
            array (
                'id' => '667',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '271',
                'amar_code' => '41101',
            ),
            167 =>
            array (
                'id' => '668',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '278',
                'amar_code' => '41702',
            ),
            168 =>
            array (
                'id' => '669',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '383',
                'amar_code' => '40604',
            ),
            169 =>
            array (
                'id' => '670',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '404',
                'amar_code' => '40702',
            ),
            170 =>
            array (
                'id' => '671',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '411',
                'amar_code' => '40803',
            ),
            171 =>
            array (
                'id' => '672',
                'name' => 'مرکزی',
                'state_id' => '2',
                'city_id' => '423',
                'amar_code' => '40902',
            ),
            172 =>
            array (
                'id' => '673',
                'name' => 'مرکزی',
                'state_id' => '7',
                'city_id' => '88',
                'amar_code' => '180102',
            ),
            173 =>
            array (
                'id' => '674',
                'name' => 'مرکزی',
                'state_id' => '7',
                'city_id' => '124',
                'amar_code' => '180202',
            ),
            174 =>
            array (
                'id' => '675',
                'name' => 'مرکزی',
                'state_id' => '7',
                'city_id' => '133',
                'amar_code' => '180902',
            ),
            175 =>
            array (
                'id' => '676',
                'name' => 'مرکزی',
                'state_id' => '7',
                'city_id' => '185',
                'amar_code' => '180303',
            ),
            176 =>
            array (
                'id' => '677',
                'name' => 'مرکزی',
                'state_id' => '7',
                'city_id' => '186',
                'amar_code' => '180402',
            ),
            177 =>
            array (
                'id' => '678',
                'name' => 'مرکزی',
                'state_id' => '7',
                'city_id' => '197',
                'amar_code' => '180502',
            ),
            178 =>
            array (
                'id' => '679',
                'name' => 'مرکزی',
                'state_id' => '7',
                'city_id' => '198',
                'amar_code' => '180802',
            ),
            179 =>
            array (
                'id' => '680',
                'name' => 'مرکزی',
                'state_id' => '7',
                'city_id' => '294',
                'amar_code' => '181001',
            ),
            180 =>
            array (
                'id' => '681',
                'name' => 'مرکزی',
                'state_id' => '7',
                'city_id' => '346',
                'amar_code' => '180602',
            ),
            181 =>
            array (
                'id' => '682',
                'name' => 'مرکزی',
                'state_id' => '7',
                'city_id' => '370',
                'amar_code' => '180703',
            ),
            182 =>
            array (
                'id' => '683',
                'name' => 'مرکزی',
                'state_id' => '8',
                'city_id' => '33',
                'amar_code' => '231002',
            ),
            183 =>
            array (
                'id' => '684',
                'name' => 'مرکزی',
                'state_id' => '8',
                'city_id' => '106',
                'amar_code' => '231302',
            ),
            184 =>
            array (
                'id' => '685',
                'name' => 'مرکزی',
                'state_id' => '8',
                'city_id' => '112',
                'amar_code' => '231801',
            ),
            185 =>
            array (
                'id' => '686',
                'name' => 'مرکزی',
                'state_id' => '8',
                'city_id' => '126',
                'amar_code' => '230102',
            ),
            186 =>
            array (
                'id' => '687',
                'name' => 'مرکزی',
                'state_id' => '8',
                'city_id' => '191',
                'amar_code' => '230202',
            ),
            187 =>
            array (
                'id' => '688',
                'name' => 'مرکزی',
                'state_id' => '8',
                'city_id' => '208',
                'amar_code' => '231202',
            ),
            188 =>
            array (
                'id' => '689',
                'name' => 'مرکزی',
                'state_id' => '8',
                'city_id' => '221',
                'amar_code' => '230304',
            ),
            189 =>
            array (
                'id' => '690',
                'name' => 'مرکزی',
                'state_id' => '8',
                'city_id' => '282',
                'amar_code' => '230902',
            ),
            190 =>
            array (
                'id' => '691',
                'name' => 'مرکزی',
                'state_id' => '8',
                'city_id' => '315',
                'amar_code' => '231402',
            ),
            191 =>
            array (
                'id' => '692',
                'name' => 'مرکزی',
                'state_id' => '8',
                'city_id' => '319',
                'amar_code' => '231601',
            ),
            192 =>
            array (
                'id' => '693',
                'name' => 'مرکزی',
                'state_id' => '8',
                'city_id' => '320',
                'amar_code' => '232101',
            ),
            193 =>
            array (
                'id' => '694',
                'name' => 'مرکزی',
                'state_id' => '8',
                'city_id' => '397',
                'amar_code' => '231701',
            ),
            194 =>
            array (
                'id' => '695',
                'name' => 'مرکزی',
                'state_id' => '8',
                'city_id' => '435',
                'amar_code' => '230603',
            ),
            195 =>
            array (
                'id' => '696',
                'name' => 'مرکزی',
                'state_id' => '9',
                'city_id' => '23',
                'amar_code' => '140501',
            ),
            196 =>
            array (
                'id' => '697',
                'name' => 'مرکزی',
                'state_id' => '9',
                'city_id' => '72',
                'amar_code' => '140103',
            ),
            197 =>
            array (
                'id' => '698',
                'name' => 'مرکزی',
                'state_id' => '9',
                'city_id' => '79',
                'amar_code' => '140901',
            ),
            198 =>
            array (
                'id' => '699',
                'name' => 'مرکزی',
                'state_id' => '9',
                'city_id' => '153',
                'amar_code' => '141001',
            ),
            199 =>
            array (
                'id' => '700',
                'name' => 'مرکزی',
                'state_id' => '9',
                'city_id' => '234',
                'amar_code' => '140801',
            ),
            200 =>
            array (
                'id' => '701',
                'name' => 'مرکزی',
                'state_id' => '9',
                'city_id' => '281',
                'amar_code' => '140202',
            ),
            201 =>
            array (
                'id' => '702',
                'name' => 'مرکزی',
                'state_id' => '9',
                'city_id' => '297',
                'amar_code' => '140302',
            ),
            202 =>
            array (
                'id' => '703',
                'name' => 'مرکزی',
                'state_id' => '9',
                'city_id' => '353',
                'amar_code' => '140602',
            ),
            203 =>
            array (
                'id' => '704',
                'name' => 'مرکزی',
                'state_id' => '9',
                'city_id' => '357',
                'amar_code' => '140701',
            ),
            204 =>
            array (
                'id' => '705',
                'name' => 'مرکزی',
                'state_id' => '9',
                'city_id' => '377',
                'amar_code' => '140402',
            ),
            205 =>
            array (
                'id' => '706',
                'name' => 'مرکزی',
                'state_id' => '10',
                'city_id' => '76',
                'amar_code' => '290801',
            ),
            206 =>
            array (
                'id' => '707',
                'name' => 'مرکزی',
                'state_id' => '10',
                'city_id' => '99',
                'amar_code' => '290104',
            ),
            207 =>
            array (
                'id' => '708',
                'name' => 'مرکزی',
                'state_id' => '10',
                'city_id' => '172',
                'amar_code' => '291001',
            ),
            208 =>
            array (
                'id' => '709',
                'name' => 'مرکزی',
                'state_id' => '10',
                'city_id' => '181',
                'amar_code' => '290203',
            ),
            209 =>
            array (
                'id' => '710',
                'name' => 'مرکزی',
                'state_id' => '10',
                'city_id' => '232',
                'amar_code' => '290901',
            ),
            210 =>
            array (
                'id' => '711',
                'name' => 'مرکزی',
                'state_id' => '10',
                'city_id' => '243',
                'amar_code' => '290301',
            ),
            211 =>
            array (
                'id' => '712',
                'name' => 'مرکزی',
                'state_id' => '10',
                'city_id' => '290',
                'amar_code' => '291102',
            ),
            212 =>
            array (
                'id' => '713',
                'name' => 'مرکزی',
                'state_id' => '10',
                'city_id' => '318',
                'amar_code' => '290402',
            ),
            213 =>
            array (
                'id' => '714',
                'name' => 'مرکزی',
                'state_id' => '10',
                'city_id' => '429',
                'amar_code' => '290502',
            ),
            214 =>
            array (
                'id' => '715',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '57',
                'amar_code' => '93701',
            ),
            215 =>
            array (
                'id' => '716',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '64',
                'amar_code' => '93101',
            ),
            216 =>
            array (
                'id' => '717',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '69',
                'amar_code' => '92302',
            ),
            217 =>
            array (
                'id' => '718',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '114',
                'amar_code' => '90402',
            ),
            218 =>
            array (
                'id' => '719',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '116',
                'amar_code' => '90602',
            ),
            219 =>
            array (
                'id' => '720',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '117',
                'amar_code' => '90506',
            ),
            220 =>
            array (
                'id' => '721',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '131',
                'amar_code' => '93401',
            ),
            221 =>
            array (
                'id' => '722',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '136',
                'amar_code' => '93601',
            ),
            222 =>
            array (
                'id' => '723',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '148',
                'amar_code' => '91802',
            ),
            223 =>
            array (
                'id' => '724',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '163',
                'amar_code' => '92902',
            ),
            224 =>
            array (
                'id' => '725',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '169',
                'amar_code' => '91902',
            ),
            225 =>
            array (
                'id' => '726',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '173',
                'amar_code' => '93801',
            ),
            226 =>
            array (
                'id' => '727',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '178',
                'amar_code' => '93901',
            ),
            227 =>
            array (
                'id' => '728',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '179',
                'amar_code' => '90703',
            ),
            228 =>
            array (
                'id' => '729',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '212',
                'amar_code' => '92702',
            ),
            229 =>
            array (
                'id' => '730',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '224',
                'amar_code' => '93501',
            ),
            230 =>
            array (
                'id' => '731',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '237',
                'amar_code' => '90807',
            ),
            231 =>
            array (
                'id' => '732',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '246',
                'amar_code' => '92002',
            ),
            232 =>
            array (
                'id' => '733',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '285',
                'amar_code' => '94001',
            ),
            233 =>
            array (
                'id' => '734',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '308',
                'amar_code' => '92202',
            ),
            234 =>
            array (
                'id' => '735',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '316',
                'amar_code' => '93301',
            ),
            235 =>
            array (
                'id' => '736',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '328',
                'amar_code' => '91303',
            ),
            236 =>
            array (
                'id' => '737',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '333',
                'amar_code' => '91404',
            ),
            237 =>
            array (
                'id' => '738',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '340',
                'amar_code' => '92802',
            ),
            238 =>
            array (
                'id' => '739',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '354',
                'amar_code' => '94101',
            ),
            239 =>
            array (
                'id' => '740',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '369',
                'amar_code' => '91502',
            ),
            240 =>
            array (
                'id' => '741',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '396',
                'amar_code' => '91605',
            ),
            241 =>
            array (
                'id' => '742',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '403',
                'amar_code' => '93002',
            ),
            242 =>
            array (
                'id' => '743',
                'name' => 'مرکزی',
                'state_id' => '11',
                'city_id' => '432',
                'amar_code' => '91704',
            ),
            243 =>
            array (
                'id' => '744',
                'name' => 'مرکزی',
                'state_id' => '12',
                'city_id' => '30',
                'amar_code' => '280102',
            ),
            244 =>
            array (
                'id' => '745',
                'name' => 'مرکزی',
                'state_id' => '12',
                'city_id' => '65',
                'amar_code' => '280204',
            ),
            245 =>
            array (
                'id' => '746',
                'name' => 'مرکزی',
                'state_id' => '12',
                'city_id' => '129',
                'amar_code' => '280303',
            ),
            246 =>
            array (
                'id' => '747',
                'name' => 'مرکزی',
                'state_id' => '12',
                'city_id' => '201',
                'amar_code' => '280801',
            ),
            247 =>
            array (
                'id' => '748',
                'name' => 'مرکزی',
                'state_id' => '12',
                'city_id' => '284',
                'amar_code' => '280402',
            ),
            248 =>
            array (
                'id' => '749',
                'name' => 'مرکزی',
                'state_id' => '12',
                'city_id' => '298',
                'amar_code' => '280502',
            ),
            249 =>
            array (
                'id' => '750',
                'name' => 'مرکزی',
                'state_id' => '12',
                'city_id' => '364',
                'amar_code' => '280701',
            ),
            250 =>
            array (
                'id' => '751',
                'name' => 'مرکزی',
                'state_id' => '12',
                'city_id' => '384',
                'amar_code' => '280603',
            ),
            251 =>
            array (
                'id' => '752',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '43',
                'amar_code' => '61602',
            ),
            252 =>
            array (
                'id' => '753',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '45',
                'amar_code' => '62103',
            ),
            253 =>
            array (
                'id' => '754',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '46',
                'amar_code' => '60202',
            ),
            254 =>
            array (
                'id' => '755',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '49',
                'amar_code' => '60302',
            ),
            255 =>
            array (
                'id' => '756',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '51',
                'amar_code' => '60403',
            ),
            256 =>
            array (
                'id' => '757',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '1',
                'amar_code' => '60102',
            ),
            257 =>
            array (
                'id' => '758',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '12',
                'amar_code' => '62601',
            ),
            258 =>
            array (
                'id' => '759',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '59',
                'amar_code' => '61502',
            ),
            259 =>
            array (
                'id' => '760',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '63',
                'amar_code' => '62401',
            ),
            260 =>
            array (
                'id' => '761',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '85',
                'amar_code' => '60502',
            ),
            261 =>
            array (
                'id' => '762',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '95',
                'amar_code' => '60603',
            ),
            262 =>
            array (
                'id' => '763',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '150',
                'amar_code' => '62501',
            ),
            263 =>
            array (
                'id' => '764',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '160',
                'amar_code' => '60701',
            ),
            264 =>
            array (
                'id' => '765',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '183',
                'amar_code' => '60802',
            ),
            265 =>
            array (
                'id' => '766',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '184',
                'amar_code' => '60902',
            ),
            266 =>
            array (
                'id' => '767',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '204',
                'amar_code' => '61901',
            ),
            267 =>
            array (
                'id' => '768',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '205',
                'amar_code' => '61002',
            ),
            268 =>
            array (
                'id' => '769',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '268',
                'amar_code' => '61101',
            ),
            269 =>
            array (
                'id' => '770',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '276',
                'amar_code' => '61402',
            ),
            270 =>
            array (
                'id' => '771',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '277',
                'amar_code' => '61202',
            ),
            271 =>
            array (
                'id' => '772',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '330',
                'amar_code' => '62701',
            ),
            272 =>
            array (
                'id' => '773',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '359',
                'amar_code' => '62002',
            ),
            273 =>
            array (
                'id' => '774',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '374',
                'amar_code' => '61701',
            ),
            274 =>
            array (
                'id' => '775',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '394',
                'amar_code' => '61303',
            ),
            275 =>
            array (
                'id' => '776',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '441',
                'amar_code' => '62202',
            ),
            276 =>
            array (
                'id' => '777',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '444',
                'amar_code' => '61801',
            ),
            277 =>
            array (
                'id' => '778',
                'name' => 'مرکزی',
                'state_id' => '13',
                'city_id' => '446',
                'amar_code' => '62302',
            ),
            278 =>
            array (
                'id' => '779',
                'name' => 'مرکزی',
                'state_id' => '14',
                'city_id' => '18',
                'amar_code' => '190103',
            ),
            279 =>
            array (
                'id' => '780',
                'name' => 'مرکزی',
                'state_id' => '14',
                'city_id' => '50',
                'amar_code' => '190602',
            ),
            280 =>
            array (
                'id' => '781',
                'name' => 'مرکزی',
                'state_id' => '14',
                'city_id' => '155',
                'amar_code' => '190303',
            ),
            281 =>
            array (
                'id' => '782',
                'name' => 'مرکزی',
                'state_id' => '14',
                'city_id' => '159',
                'amar_code' => '190701',
            ),
            282 =>
            array (
                'id' => '783',
                'name' => 'مرکزی',
                'state_id' => '14',
                'city_id' => '230',
                'amar_code' => '190405',
            ),
            283 =>
            array (
                'id' => '784',
                'name' => 'مرکزی',
                'state_id' => '14',
                'city_id' => '254',
                'amar_code' => '191001',
            ),
            284 =>
            array (
                'id' => '785',
                'name' => 'مرکزی',
                'state_id' => '14',
                'city_id' => '288',
                'amar_code' => '190802',
            ),
            285 =>
            array (
                'id' => '786',
                'name' => 'مرکزی',
                'state_id' => '14',
                'city_id' => '385',
                'amar_code' => '190902',
            ),
            286 =>
            array (
                'id' => '787',
                'name' => 'مرکزی',
                'state_id' => '15',
                'city_id' => '6',
                'amar_code' => '200601',
            ),
            287 =>
            array (
                'id' => '788',
                'name' => 'مرکزی',
                'state_id' => '15',
                'city_id' => '177',
                'amar_code' => '200102',
            ),
            288 =>
            array (
                'id' => '789',
                'name' => 'مرکزی',
                'state_id' => '15',
                'city_id' => '256',
                'amar_code' => '200201',
            ),
            289 =>
            array (
                'id' => '790',
                'name' => 'مرکزی',
                'state_id' => '15',
                'city_id' => '270',
                'amar_code' => '200303',
            ),
            290 =>
            array (
                'id' => '791',
                'name' => 'مرکزی',
                'state_id' => '15',
                'city_id' => '363',
                'amar_code' => '200402',
            ),
            291 =>
            array (
                'id' => '792',
                'name' => 'مرکزی',
                'state_id' => '15',
                'city_id' => '405',
                'amar_code' => '200502',
            ),
            292 =>
            array (
                'id' => '793',
                'name' => 'مرکزی',
                'state_id' => '15',
                'city_id' => '410',
                'amar_code' => '200701',
            ),
            293 =>
            array (
                'id' => '794',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '52',
                'amar_code' => '110105',
            ),
            294 =>
            array (
                'id' => '795',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '78',
                'amar_code' => '112001',
            ),
            295 =>
            array (
                'id' => '796',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '120',
                'amar_code' => '112101',
            ),
            296 =>
            array (
                'id' => '797',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '143',
                'amar_code' => '110202',
            ),
            297 =>
            array (
                'id' => '798',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '152',
                'amar_code' => '110301',
            ),
            298 =>
            array (
                'id' => '799',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '187',
                'amar_code' => '112201',
            ),
            299 =>
            array (
                'id' => '800',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '189',
                'amar_code' => '111201',
            ),
            300 =>
            array (
                'id' => '801',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '202',
                'amar_code' => '110803',
            ),
            301 =>
            array (
                'id' => '802',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '223',
                'amar_code' => '110404',
            ),
            302 =>
            array (
                'id' => '803',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '225',
                'amar_code' => '110501',
            ),
            303 =>
            array (
                'id' => '804',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '231',
                'amar_code' => '111002',
            ),
            304 =>
            array (
                'id' => '805',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '240',
                'amar_code' => '110604',
            ),
            305 =>
            array (
                'id' => '806',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '242',
                'amar_code' => '112301',
            ),
            306 =>
            array (
                'id' => '807',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '263',
                'amar_code' => '111401',
            ),
            307 =>
            array (
                'id' => '808',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '311',
                'amar_code' => '111901',
            ),
            308 =>
            array (
                'id' => '809',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '325',
                'amar_code' => '111801',
            ),
            309 =>
            array (
                'id' => '810',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '345',
                'amar_code' => '110902',
            ),
            310 =>
            array (
                'id' => '811',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '408',
                'amar_code' => '111301',
            ),
            311 =>
            array (
                'id' => '812',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '415',
                'amar_code' => '111701',
            ),
            312 =>
            array (
                'id' => '813',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '433',
                'amar_code' => '110705',
            ),
            313 =>
            array (
                'id' => '814',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '434',
                'amar_code' => '111501',
            ),
            314 =>
            array (
                'id' => '815',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '437',
                'amar_code' => '111601',
            ),
            315 =>
            array (
                'id' => '816',
                'name' => 'مرکزی',
                'state_id' => '16',
                'city_id' => '447',
                'amar_code' => '111101',
            ),
            316 =>
            array (
                'id' => '817',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '25',
                'amar_code' => '71701',
            ),
            317 =>
            array (
                'id' => '818',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '28',
                'amar_code' => '70202',
            ),
            318 =>
            array (
                'id' => '819',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '39',
                'amar_code' => '70302',
            ),
            319 =>
            array (
                'id' => '820',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '47',
                'amar_code' => '73601',
            ),
            320 =>
            array (
                'id' => '821',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '2',
                'amar_code' => '70104',
            ),
            321 =>
            array (
                'id' => '822',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '66',
                'amar_code' => '73501',
            ),
            322 =>
            array (
                'id' => '823',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '87',
                'amar_code' => '71602',
            ),
            323 =>
            array (
                'id' => '824',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '100',
                'amar_code' => '73101',
            ),
            324 =>
            array (
                'id' => '825',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '105',
                'amar_code' => '72301',
            ),
            325 =>
            array (
                'id' => '826',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '137',
                'amar_code' => '70404',
            ),
            326 =>
            array (
                'id' => '827',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '156',
                'amar_code' => '72902',
            ),
            327 =>
            array (
                'id' => '828',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '158',
                'amar_code' => '71802',
            ),
            328 =>
            array (
                'id' => '829',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '161',
                'amar_code' => '73401',
            ),
            329 =>
            array (
                'id' => '830',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '167',
                'amar_code' => '72402',
            ),
            330 =>
            array (
                'id' => '831',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '175',
                'amar_code' => '70503',
            ),
            331 =>
            array (
                'id' => '832',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '210',
                'amar_code' => '72602',
            ),
            332 =>
            array (
                'id' => '833',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '226',
                'amar_code' => '73001',
            ),
            333 =>
            array (
                'id' => '834',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '229',
                'amar_code' => '71902',
            ),
            334 =>
            array (
                'id' => '835',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '238',
                'amar_code' => '70602',
            ),
            335 =>
            array (
                'id' => '836',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '245',
                'amar_code' => '73201',
            ),
            336 =>
            array (
                'id' => '837',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '251',
                'amar_code' => '72502',
            ),
            337 =>
            array (
                'id' => '838',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '283',
                'amar_code' => '70705',
            ),
            338 =>
            array (
                'id' => '839',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '301',
                'amar_code' => '72201',
            ),
            339 =>
            array (
                'id' => '840',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '309',
                'amar_code' => '70803',
            ),
            340 =>
            array (
                'id' => '841',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '314',
                'amar_code' => '70903',
            ),
            341 =>
            array (
                'id' => '842',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '329',
                'amar_code' => '72002',
            ),
            342 =>
            array (
                'id' => '843',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '331',
                'amar_code' => '71004',
            ),
            343 =>
            array (
                'id' => '844',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '348',
                'amar_code' => '72801',
            ),
            344 =>
            array (
                'id' => '845',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '350',
                'amar_code' => '73301',
            ),
            345 =>
            array (
                'id' => '846',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '361',
                'amar_code' => '72701',
            ),
            346 =>
            array (
                'id' => '847',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '373',
                'amar_code' => '71104',
            ),
            347 =>
            array (
                'id' => '848',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '375',
                'amar_code' => '71501',
            ),
            348 =>
            array (
                'id' => '849',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '392',
                'amar_code' => '71204',
            ),
            349 =>
            array (
                'id' => '850',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '401',
                'amar_code' => '71303',
            ),
            350 =>
            array (
                'id' => '851',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '406',
                'amar_code' => '72102',
            ),
            351 =>
            array (
                'id' => '852',
                'name' => 'مرکزی',
                'state_id' => '17',
                'city_id' => '430',
                'amar_code' => '71403',
            ),
            352 =>
            array (
                'id' => '853',
                'name' => 'مرکزی',
                'state_id' => '18',
                'city_id' => '40',
                'amar_code' => '260502',
            ),
            353 =>
            array (
                'id' => '854',
                'name' => 'مرکزی',
                'state_id' => '18',
                'city_id' => '4',
                'amar_code' => '260402',
            ),
            354 =>
            array (
                'id' => '855',
                'name' => 'مرکزی',
                'state_id' => '18',
                'city_id' => '15',
                'amar_code' => '260601',
            ),
            355 =>
            array (
                'id' => '856',
                'name' => 'مرکزی',
                'state_id' => '18',
                'city_id' => '91',
                'amar_code' => '260104',
            ),
            356 =>
            array (
                'id' => '857',
                'name' => 'مرکزی',
                'state_id' => '18',
                'city_id' => '113',
                'amar_code' => '260204',
            ),
            357 =>
            array (
                'id' => '858',
                'name' => 'مرکزی',
                'state_id' => '18',
                'city_id' => '322',
                'amar_code' => '260305',
            ),
            358 =>
            array (
                'id' => '859',
                'name' => 'مرکزی',
                'state_id' => '19',
                'city_id' => '327',
                'amar_code' => '250103',
            ),
            359 =>
            array (
                'id' => '860',
                'name' => 'مرکزی',
                'state_id' => '20',
                'city_id' => '62',
                'amar_code' => '120102',
            ),
            360 =>
            array (
                'id' => '861',
                'name' => 'مرکزی',
                'state_id' => '20',
                'city_id' => '98',
                'amar_code' => '120203',
            ),
            361 =>
            array (
                'id' => '862',
                'name' => 'مرکزی',
                'state_id' => '20',
                'city_id' => '195',
                'amar_code' => '121001',
            ),
            362 =>
            array (
                'id' => '863',
                'name' => 'مرکزی',
                'state_id' => '20',
                'city_id' => '199',
                'amar_code' => '120702',
            ),
            363 =>
            array (
                'id' => '864',
                'name' => 'مرکزی',
                'state_id' => '20',
                'city_id' => '250',
                'amar_code' => '120902',
            ),
            364 =>
            array (
                'id' => '865',
                'name' => 'مرکزی',
                'state_id' => '20',
                'city_id' => '252',
                'amar_code' => '120302',
            ),
            365 =>
            array (
                'id' => '866',
                'name' => 'مرکزی',
                'state_id' => '20',
                'city_id' => '259',
                'amar_code' => '120403',
            ),
            366 =>
            array (
                'id' => '867',
                'name' => 'مرکزی',
                'state_id' => '20',
                'city_id' => '321',
                'amar_code' => '120502',
            ),
            367 =>
            array (
                'id' => '868',
                'name' => 'مرکزی',
                'state_id' => '20',
                'city_id' => '334',
                'amar_code' => '120801',
            ),
            368 =>
            array (
                'id' => '869',
                'name' => 'مرکزی',
                'state_id' => '20',
                'city_id' => '393',
                'amar_code' => '120603',
            ),
            369 =>
            array (
                'id' => '870',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '24',
                'amar_code' => '82301',
            ),
            370 =>
            array (
                'id' => '871',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '60',
                'amar_code' => '80103',
            ),
            371 =>
            array (
                'id' => '872',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '70',
                'amar_code' => '81001',
            ),
            372 =>
            array (
                'id' => '873',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '77',
                'amar_code' => '80203',
            ),
            373 =>
            array (
                'id' => '874',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '138',
                'amar_code' => '80304',
            ),
            374 =>
            array (
                'id' => '875',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '200',
                'amar_code' => '81801',
            ),
            375 =>
            array (
                'id' => '876',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '207',
                'amar_code' => '81102',
            ),
            376 =>
            array (
                'id' => '877',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '214',
                'amar_code' => '80402',
            ),
            377 =>
            array (
                'id' => '878',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '218',
                'amar_code' => '81502',
            ),
            378 =>
            array (
                'id' => '879',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '222',
                'amar_code' => '81702',
            ),
            379 =>
            array (
                'id' => '880',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '227',
                'amar_code' => '80502',
            ),
            380 =>
            array (
                'id' => '881',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '264',
                'amar_code' => '80601',
            ),
            381 =>
            array (
                'id' => '882',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '279',
                'amar_code' => '80701',
            ),
            382 =>
            array (
                'id' => '883',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '296',
                'amar_code' => '81201',
            ),
            383 =>
            array (
                'id' => '884',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '299',
                'amar_code' => '82201',
            ),
            384 =>
            array (
                'id' => '885',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '313',
                'amar_code' => '81901',
            ),
            385 =>
            array (
                'id' => '886',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '326',
                'amar_code' => '81602',
            ),
            386 =>
            array (
                'id' => '887',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '338',
                'amar_code' => '80805',
            ),
            387 =>
            array (
                'id' => '888',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '356',
                'amar_code' => '80904',
            ),
            388 =>
            array (
                'id' => '889',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '351',
                'amar_code' => '81402',
            ),
            389 =>
            array (
                'id' => '890',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '402',
                'amar_code' => '81301',
            ),
            390 =>
            array (
                'id' => '891',
                'name' => 'مرکزی',
                'state_id' => '21',
                'city_id' => '420',
                'amar_code' => '82101',
            ),
            391 =>
            array (
                'id' => '892',
                'name' => 'مرکزی',
                'state_id' => '23',
                'city_id' => '58',
                'amar_code' => '170701',
            ),
            392 =>
            array (
                'id' => '893',
                'name' => 'مرکزی',
                'state_id' => '23',
                'city_id' => '97',
                'amar_code' => '170502',
            ),
            393 =>
            array (
                'id' => '894',
                'name' => 'مرکزی',
                'state_id' => '23',
                'city_id' => '90',
                'amar_code' => '170103',
            ),
            394 =>
            array (
                'id' => '895',
                'name' => 'مرکزی',
                'state_id' => '23',
                'city_id' => '145',
                'amar_code' => '170601',
            ),
            395 =>
            array (
                'id' => '896',
                'name' => 'مرکزی',
                'state_id' => '23',
                'city_id' => '192',
                'amar_code' => '170403',
            ),
            396 =>
            array (
                'id' => '897',
                'name' => 'مرکزی',
                'state_id' => '23',
                'city_id' => '355',
                'amar_code' => '170205',
            ),
            397 =>
            array (
                'id' => '898',
                'name' => 'مرکزی',
                'state_id' => '23',
                'city_id' => '360',
                'amar_code' => '170302',
            ),
            398 =>
            array (
                'id' => '899',
                'name' => 'مرکزی',
                'state_id' => '23',
                'city_id' => '379',
                'amar_code' => '170801',
            ),
            399 =>
            array (
                'id' => '900',
                'name' => 'مرکزی',
                'state_id' => '23',
                'city_id' => '381',
                'amar_code' => '170901',
            ),
            400 =>
            array (
                'id' => '901',
                'name' => 'مرکزی',
                'state_id' => '22',
                'city_id' => '32',
                'amar_code' => '50103',
            ),
            401 =>
            array (
                'id' => '902',
                'name' => 'مرکزی',
                'state_id' => '22',
                'city_id' => '107',
                'amar_code' => '50302',
            ),
            402 =>
            array (
                'id' => '903',
                'name' => 'مرکزی',
                'state_id' => '22',
                'city_id' => '128',
                'amar_code' => '51201',
            ),
            403 =>
            array (
                'id' => '904',
                'name' => 'مرکزی',
                'state_id' => '22',
                'city_id' => '134',
                'amar_code' => '50903',
            ),
            404 =>
            array (
                'id' => '905',
                'name' => 'مرکزی',
                'state_id' => '22',
                'city_id' => '176',
                'amar_code' => '51302',
            ),
            405 =>
            array (
                'id' => '906',
                'name' => 'مرکزی',
                'state_id' => '22',
                'city_id' => '215',
                'amar_code' => '51402',
            ),
            406 =>
            array (
                'id' => '907',
                'name' => 'مرکزی',
                'state_id' => '22',
                'city_id' => '244',
                'amar_code' => '50401',
            ),
            407 =>
            array (
                'id' => '908',
                'name' => 'مرکزی',
                'state_id' => '22',
                'city_id' => '258',
                'amar_code' => '50501',
            ),
            408 =>
            array (
                'id' => '909',
                'name' => 'مرکزی',
                'state_id' => '22',
                'city_id' => '286',
                'amar_code' => '51002',
            ),
            409 =>
            array (
                'id' => '910',
                'name' => 'مرکزی',
                'state_id' => '22',
                'city_id' => '324',
                'amar_code' => '50602',
            ),
            410 =>
            array (
                'id' => '911',
                'name' => 'مرکزی',
                'state_id' => '22',
                'city_id' => '339',
                'amar_code' => '50204',
            ),
            411 =>
            array (
                'id' => '912',
                'name' => 'مرکزی',
                'state_id' => '22',
                'city_id' => '347',
                'amar_code' => '50701',
            ),
            412 =>
            array (
                'id' => '913',
                'name' => 'مرکزی',
                'state_id' => '22',
                'city_id' => '372',
                'amar_code' => '50801',
            ),
            413 =>
            array (
                'id' => '914',
                'name' => 'مرکزی',
                'state_id' => '22',
                'city_id' => '438',
                'amar_code' => '51102',
            ),
            414 =>
            array (
                'id' => '915',
                'name' => 'مرکزی',
                'state_id' => '24',
                'city_id' => '8',
                'amar_code' => '271002',
            ),
            415 =>
            array (
                'id' => '916',
                'name' => 'مرکزی',
                'state_id' => '24',
                'city_id' => '13',
                'amar_code' => '270802',
            ),
            416 =>
            array (
                'id' => '917',
                'name' => 'مرکزی',
                'state_id' => '24',
                'city_id' => '83',
                'amar_code' => '270101',
            ),
            417 =>
            array (
                'id' => '918',
                'name' => 'مرکزی',
                'state_id' => '24',
                'city_id' => '118',
                'amar_code' => '270202',
            ),
            418 =>
            array (
                'id' => '919',
                'name' => 'مرکزی',
                'state_id' => '24',
                'city_id' => '206',
                'amar_code' => '271102',
            ),
            419 =>
            array (
                'id' => '920',
                'name' => 'مرکزی',
                'state_id' => '24',
                'city_id' => '295',
                'amar_code' => '270301',
            ),
            420 =>
            array (
                'id' => '921',
                'name' => 'مرکزی',
                'state_id' => '24',
                'city_id' => '337',
                'amar_code' => '270401',
            ),
            421 =>
            array (
                'id' => '922',
                'name' => 'مرکزی',
                'state_id' => '24',
                'city_id' => '342',
                'amar_code' => '270902',
            ),
            422 =>
            array (
                'id' => '923',
                'name' => 'مرکزی',
                'state_id' => '24',
                'city_id' => '358',
                'amar_code' => '271401',
            ),
            423 =>
            array (
                'id' => '924',
                'name' => 'مرکزی',
                'state_id' => '24',
                'city_id' => '362',
                'amar_code' => '270502',
            ),
            424 =>
            array (
                'id' => '925',
                'name' => 'مرکزی',
                'state_id' => '24',
                'city_id' => '368',
                'amar_code' => '271301',
            ),
            425 =>
            array (
                'id' => '926',
                'name' => 'مرکزی',
                'state_id' => '24',
                'city_id' => '371',
                'amar_code' => '270604',
            ),
            426 =>
            array (
                'id' => '927',
                'name' => 'مرکزی',
                'state_id' => '24',
                'city_id' => '390',
                'amar_code' => '271201',
            ),
            427 =>
            array (
                'id' => '928',
                'name' => 'مرکزی',
                'state_id' => '24',
                'city_id' => '417',
                'amar_code' => '270704',
            ),
            428 =>
            array (
                'id' => '929',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '42',
                'amar_code' => '11302',
            ),
            429 =>
            array (
                'id' => '930',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '9',
                'amar_code' => '10101',
            ),
            430 =>
            array (
                'id' => '931',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '10',
                'amar_code' => '10202',
            ),
            431 =>
            array (
                'id' => '932',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '81',
                'amar_code' => '10301',
            ),
            432 =>
            array (
                'id' => '933',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '211',
                'amar_code' => '10505',
            ),
            433 =>
            array (
                'id' => '934',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '213',
                'amar_code' => '11402',
            ),
            434 =>
            array (
                'id' => '935',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '217',
                'amar_code' => '10603',
            ),
            435 =>
            array (
                'id' => '936',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '219',
                'amar_code' => '10703',
            ),
            436 =>
            array (
                'id' => '937',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '262',
                'amar_code' => '11502',
            ),
            437 =>
            array (
                'id' => '938',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '274',
                'amar_code' => '11202',
            ),
            438 =>
            array (
                'id' => '939',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '287',
                'amar_code' => '10802',
            ),
            439 =>
            array (
                'id' => '940',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '291',
                'amar_code' => '10403',
            ),
            440 =>
            array (
                'id' => '941',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '312',
                'amar_code' => '10902',
            ),
            441 =>
            array (
                'id' => '942',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '376',
                'amar_code' => '11102',
            ),
            442 =>
            array (
                'id' => '943',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '380',
                'amar_code' => '11001',
            ),
            443 =>
            array (
                'id' => '944',
                'name' => 'مرکزی',
                'state_id' => '25',
                'city_id' => '382',
                'amar_code' => '11602',
            ),
            444 =>
            array (
                'id' => '945',
                'name' => 'مرکزی',
                'state_id' => '26',
                'city_id' => '27',
                'amar_code' => '150702',
            ),
            445 =>
            array (
                'id' => '946',
                'name' => 'مرکزی',
                'state_id' => '26',
                'city_id' => '41',
                'amar_code' => '150103',
            ),
            446 =>
            array (
                'id' => '947',
                'name' => 'مرکزی',
                'state_id' => '26',
                'city_id' => '71',
                'amar_code' => '150202',
            ),
            447 =>
            array (
                'id' => '948',
                'name' => 'مرکزی',
                'state_id' => '26',
                'city_id' => '109',
                'amar_code' => '150801',
            ),
            448 =>
            array (
                'id' => '949',
                'name' => 'مرکزی',
                'state_id' => '26',
                'city_id' => '157',
                'amar_code' => '150306',
            ),
            449 =>
            array (
                'id' => '950',
                'name' => 'مرکزی',
                'state_id' => '26',
                'city_id' => '188',
                'amar_code' => '150402',
            ),
            450 =>
            array (
                'id' => '951',
                'name' => 'مرکزی',
                'state_id' => '26',
                'city_id' => '193',
                'amar_code' => '150502',
            ),
            451 =>
            array (
                'id' => '952',
                'name' => 'مرکزی',
                'state_id' => '26',
                'city_id' => '220',
                'amar_code' => '151102',
            ),
            452 =>
            array (
                'id' => '953',
                'name' => 'مرکزی',
                'state_id' => '26',
                'city_id' => '253',
                'amar_code' => '150902',
            ),
            453 =>
            array (
                'id' => '954',
                'name' => 'مرکزی',
                'state_id' => '26',
                'city_id' => '352',
                'amar_code' => '150603',
            ),
            454 =>
            array (
                'id' => '955',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '14',
                'amar_code' => '20102',
            ),
            455 =>
            array (
                'id' => '956',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '55',
                'amar_code' => '20202',
            ),
            456 =>
            array (
                'id' => '957',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '56',
                'amar_code' => '21602',
            ),
            457 =>
            array (
                'id' => '958',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '96',
                'amar_code' => '20402',
            ),
            458 =>
            array (
                'id' => '959',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '123',
                'amar_code' => '20502',
            ),
            459 =>
            array (
                'id' => '960',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '135',
                'amar_code' => '22102',
            ),
            460 =>
            array (
                'id' => '961',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '142',
                'amar_code' => '22002',
            ),
            461 =>
            array (
                'id' => '962',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '203',
                'amar_code' => '20601',
            ),
            462 =>
            array (
                'id' => '963',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '233',
                'amar_code' => '20703',
            ),
            463 =>
            array (
                'id' => '964',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '260',
                'amar_code' => '20802',
            ),
            464 =>
            array (
                'id' => '965',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '261',
                'amar_code' => '22701',
            ),
            465 =>
            array (
                'id' => '966',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '267',
                'amar_code' => '22601',
            ),
            466 =>
            array (
                'id' => '967',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '292',
                'amar_code' => '22401',
            ),
            467 =>
            array (
                'id' => '968',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '307',
                'amar_code' => '22302',
            ),
            468 =>
            array (
                'id' => '969',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '317',
                'amar_code' => '21002',
            ),
            469 =>
            array (
                'id' => '970',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '341',
                'amar_code' => '22801',
            ),
            470 =>
            array (
                'id' => '971',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '367',
                'amar_code' => '22202',
            ),
            471 =>
            array (
                'id' => '972',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '388',
                'amar_code' => '21802',
            ),
            472 =>
            array (
                'id' => '973',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '412',
                'amar_code' => '22501',
            ),
            473 =>
            array (
                'id' => '974',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '424',
                'amar_code' => '21901',
            ),
            474 =>
            array (
                'id' => '975',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '426',
                'amar_code' => '21403',
            ),
            475 =>
            array (
                'id' => '976',
                'name' => 'مرکزی',
                'state_id' => '27',
                'city_id' => '427',
                'amar_code' => '21504',
            ),
            476 =>
            array (
                'id' => '977',
                'name' => 'مرکزی',
                'state_id' => '28',
                'city_id' => '19',
                'amar_code' => '102',
            ),
            477 =>
            array (
                'id' => '978',
                'name' => 'مرکزی',
                'state_id' => '28',
                'city_id' => '11',
                'amar_code' => '201',
            ),
            478 =>
            array (
                'id' => '979',
                'name' => 'مرکزی',
                'state_id' => '28',
                'city_id' => '121',
                'amar_code' => '302',
            ),
            479 =>
            array (
                'id' => '980',
                'name' => 'مرکزی',
                'state_id' => '28',
                'city_id' => '165',
                'amar_code' => '401',
            ),
            480 =>
            array (
                'id' => '981',
                'name' => 'مرکزی',
                'state_id' => '28',
                'city_id' => '168',
                'amar_code' => '1202',
            ),
            481 =>
            array (
                'id' => '982',
                'name' => 'مرکزی',
                'state_id' => '28',
                'city_id' => '190',
                'amar_code' => '501',
            ),
            482 =>
            array (
                'id' => '983',
                'name' => 'مرکزی',
                'state_id' => '28',
                'city_id' => '228',
                'amar_code' => '1002',
            ),
            483 =>
            array (
                'id' => '984',
                'name' => 'مرکزی',
                'state_id' => '28',
                'city_id' => '236',
                'amar_code' => '603',
            ),
            484 =>
            array (
                'id' => '985',
                'name' => 'مرکزی',
                'state_id' => '28',
                'city_id' => '269',
                'amar_code' => '701',
            ),
            485 =>
            array (
                'id' => '986',
                'name' => 'مرکزی',
                'state_id' => '28',
                'city_id' => '302',
                'amar_code' => '1301',
            ),
            486 =>
            array (
                'id' => '987',
                'name' => 'مرکزی',
                'state_id' => '28',
                'city_id' => '344',
                'amar_code' => '1101',
            ),
            487 =>
            array (
                'id' => '988',
                'name' => 'مرکزی',
                'state_id' => '28',
                'city_id' => '387',
                'amar_code' => '901',
            ),
            488 =>
            array (
                'id' => '989',
                'name' => 'مرکزی',
                'state_id' => '29',
                'city_id' => '17',
                'amar_code' => '220102',
            ),
            489 =>
            array (
                'id' => '990',
                'name' => 'مرکزی',
                'state_id' => '29',
                'city_id' => '74',
                'amar_code' => '220902',
            ),
            490 =>
            array (
                'id' => '991',
                'name' => 'مرکزی',
                'state_id' => '29',
                'city_id' => '75',
                'amar_code' => '221301',
            ),
            491 =>
            array (
                'id' => '992',
                'name' => 'مرکزی',
                'state_id' => '29',
                'city_id' => '82',
                'amar_code' => '220204',
            ),
            492 =>
            array (
                'id' => '993',
                'name' => 'مرکزی',
                'state_id' => '29',
                'city_id' => '84',
                'amar_code' => '220305',
            ),
            493 =>
            array (
                'id' => '994',
                'name' => 'مرکزی',
                'state_id' => '29',
                'city_id' => '104',
                'amar_code' => '221102',
            ),
            494 =>
            array (
                'id' => '995',
                'name' => 'مرکزی',
                'state_id' => '29',
                'city_id' => '130',
                'amar_code' => '220602',
            ),
            495 =>
            array (
                'id' => '996',
                'name' => 'مرکزی',
                'state_id' => '29',
                'city_id' => '149',
                'amar_code' => '220802',
            ),
            496 =>
            array (
                'id' => '997',
                'name' => 'مرکزی',
                'state_id' => '29',
                'city_id' => '164',
                'amar_code' => '221002',
            ),
            497 =>
            array (
                'id' => '998',
                'name' => 'مرکزی',
                'state_id' => '29',
                'city_id' => '216',
                'amar_code' => '220702',
            ),
            498 =>
            array (
                'id' => '999',
                'name' => 'مرکزی',
                'state_id' => '29',
                'city_id' => '266',
                'amar_code' => '221202',
            ),
            499 =>
            array (
                'id' => '1000',
                'name' => 'مرکزی',
                'state_id' => '29',
                'city_id' => '323',
                'amar_code' => '220402',
            ),
        ));
        \DB::table('districts')->insert(array (
            0 =>
            array (
                'id' => '1001',
                'name' => 'مرکزی',
                'state_id' => '29',
                'city_id' => '416',
                'amar_code' => '220503',
            ),
            1 =>
            array (
                'id' => '1002',
                'name' => 'مرکزی',
                'state_id' => '30',
                'city_id' => '29',
                'amar_code' => '130601',
            ),
            2 =>
            array (
                'id' => '1003',
                'name' => 'مرکزی',
                'state_id' => '30',
                'city_id' => '93',
                'amar_code' => '130702',
            ),
            3 =>
            array (
                'id' => '1004',
                'name' => 'مرکزی',
                'state_id' => '30',
                'city_id' => '125',
                'amar_code' => '130102',
            ),
            4 =>
            array (
                'id' => '1005',
                'name' => 'مرکزی',
                'state_id' => '30',
                'city_id' => '180',
                'amar_code' => '131002',
            ),
            5 =>
            array (
                'id' => '1006',
                'name' => 'مرکزی',
                'state_id' => '30',
                'city_id' => '209',
                'amar_code' => '130803',
            ),
            6 =>
            array (
                'id' => '1007',
                'name' => 'مرکزی',
                'state_id' => '30',
                'city_id' => '300',
                'amar_code' => '130901',
            ),
            7 =>
            array (
                'id' => '1008',
                'name' => 'مرکزی',
                'state_id' => '30',
                'city_id' => '335',
                'amar_code' => '130502',
            ),
            8 =>
            array (
                'id' => '1009',
                'name' => 'مرکزی',
                'state_id' => '30',
                'city_id' => '398',
                'amar_code' => '130203',
            ),
            9 =>
            array (
                'id' => '1010',
                'name' => 'مرکزی',
                'state_id' => '30',
                'city_id' => '428',
                'amar_code' => '130302',
            ),
            10 =>
            array (
                'id' => '1011',
                'name' => 'مرکزی',
                'state_id' => '30',
                'city_id' => '443',
                'amar_code' => '130407',
            ),
            11 =>
            array (
                'id' => '1012',
                'name' => 'مرکزی',
                'state_id' => '31',
                'city_id' => '16',
                'amar_code' => '210702',
            ),
            12 =>
            array (
                'id' => '1013',
                'name' => 'مرکزی',
                'state_id' => '31',
                'city_id' => '22',
                'amar_code' => '210102',
            ),
            13 =>
            array (
                'id' => '1014',
                'name' => 'مرکزی',
                'state_id' => '31',
                'city_id' => '35',
                'amar_code' => '210802',
            ),
            14 =>
            array (
                'id' => '1015',
                'name' => 'مرکزی',
                'state_id' => '31',
                'city_id' => '61',
                'amar_code' => '210202',
            ),
            15 =>
            array (
                'id' => '1016',
                'name' => 'مرکزی',
                'state_id' => '31',
                'city_id' => '92',
                'amar_code' => '211102',
            ),
            16 =>
            array (
                'id' => '1017',
                'name' => 'مرکزی',
                'state_id' => '31',
                'city_id' => '119',
                'amar_code' => '210302',
            ),
            17 =>
            array (
                'id' => '1018',
                'name' => 'مرکزی',
                'state_id' => '31',
                'city_id' => '151',
                'amar_code' => '210901',
            ),
            18 =>
            array (
                'id' => '1019',
                'name' => 'مرکزی',
                'state_id' => '31',
                'city_id' => '409',
                'amar_code' => '210401',
            ),
            19 =>
            array (
                'id' => '1020',
                'name' => 'مرکزی',
                'state_id' => '31',
                'city_id' => '414',
                'amar_code' => '210601',
            ),
            20 =>
            array (
                'id' => '1021',
                'name' => 'مرکزی',
                'state_id' => '31',
                'city_id' => '448',
                'amar_code' => '210503',
            ),
            21 =>
            array (
                'id' => '1022',
                'name' => 'مروست',
                'state_id' => '31',
                'city_id' => '151',
                'amar_code' => '210902',
            ),
            22 =>
            array (
                'id' => '1023',
                'name' => 'مزایجان',
                'state_id' => '17',
                'city_id' => '87',
                'amar_code' => '71603',
            ),
            23 =>
            array (
                'id' => '1024',
                'name' => 'مشراگه',
                'state_id' => '13',
                'city_id' => '204',
                'amar_code' => '61902',
            ),
            24 =>
            array (
                'id' => '1025',
                'name' => 'مشکان',
                'state_id' => '11',
                'city_id' => '173',
                'amar_code' => '93802',
            ),
            25 =>
            array (
                'id' => '1026',
                'name' => 'مشکین دشت',
                'state_id' => '5',
                'city_id' => '304',
                'amar_code' => '300602',
            ),
            26 =>
            array (
                'id' => '1027',
                'name' => 'مشکین شرقی',
                'state_id' => '3',
                'city_id' => '395',
                'amar_code' => '240403',
            ),
            27 =>
            array (
                'id' => '1028',
                'name' => 'مشهدمرغاب',
                'state_id' => '17',
                'city_id' => '158',
                'amar_code' => '71801',
            ),
            28 =>
            array (
                'id' => '1029',
                'name' => 'معصومیه',
                'state_id' => '28',
                'city_id' => '19',
                'amar_code' => '104',
            ),
            29 =>
            array (
                'id' => '1030',
                'name' => 'معمولان',
                'state_id' => '26',
                'city_id' => '109',
                'amar_code' => '150802',
            ),
            30 =>
            array (
                'id' => '1031',
                'name' => 'ممقان',
                'state_id' => '1',
                'city_id' => '5',
                'amar_code' => '32102',
            ),
            31 =>
            array (
                'id' => '1032',
                'name' => 'منج',
                'state_id' => '9',
                'city_id' => '377',
                'amar_code' => '140404',
            ),
            32 =>
            array (
                'id' => '1033',
                'name' => 'منجوان',
                'state_id' => '1',
                'city_id' => '154',
                'amar_code' => '32603',
            ),
            33 =>
            array (
                'id' => '1034',
                'name' => 'موچش',
                'state_id' => '20',
                'city_id' => '334',
                'amar_code' => '120802',
            ),
            34 =>
            array (
                'id' => '1035',
                'name' => 'مود',
                'state_id' => '10',
                'city_id' => '243',
                'amar_code' => '290302',
            ),
            35 =>
            array (
                'id' => '1036',
                'name' => 'موران',
                'state_id' => '3',
                'city_id' => '365',
                'amar_code' => '240505',
            ),
            36 =>
            array (
                'id' => '1037',
                'name' => 'موسیان',
                'state_id' => '6',
                'city_id' => '196',
                'amar_code' => '160303',
            ),
            37 =>
            array (
                'id' => '1038',
                'name' => 'موگرمون',
                'state_id' => '23',
                'city_id' => '379',
                'amar_code' => '170802',
            ),
            38 =>
            array (
                'id' => '1039',
                'name' => 'مهاباد',
                'state_id' => '4',
                'city_id' => '21',
                'amar_code' => '100103',
            ),
            39 =>
            array (
                'id' => '1040',
                'name' => 'مهران',
                'state_id' => '29',
                'city_id' => '84',
                'amar_code' => '220306',
            ),
            40 =>
            array (
                'id' => '1041',
                'name' => 'مهربان',
                'state_id' => '1',
                'city_id' => '239',
                'amar_code' => '30502',
            ),
            41 =>
            array (
                'id' => '1042',
                'name' => 'مهردشت',
                'state_id' => '4',
                'city_id' => '419',
                'amar_code' => '101403',
            ),
            42 =>
            array (
                'id' => '1043',
                'name' => 'مهرگان',
                'state_id' => '16',
                'city_id' => '240',
                'amar_code' => '110606',
            ),
            43 =>
            array (
                'id' => '1044',
                'name' => 'میان آب',
                'state_id' => '13',
                'city_id' => '277',
                'amar_code' => '61204',
            ),
            44 =>
            array (
                'id' => '1045',
                'name' => 'میان جلگه',
                'state_id' => '11',
                'city_id' => '432',
                'amar_code' => '91705',
            ),
            45 =>
            array (
                'id' => '1046',
                'name' => 'میان ولایت',
                'state_id' => '11',
                'city_id' => '114',
                'amar_code' => '90403',
            ),
            46 =>
            array (
                'id' => '1047',
                'name' => 'میانکوه',
                'state_id' => '9',
                'city_id' => '23',
                'amar_code' => '140502',
            ),
            47 =>
            array (
                'id' => '1048',
                'name' => 'میداود',
                'state_id' => '13',
                'city_id' => '59',
                'amar_code' => '61504',
            ),
            48 =>
            array (
                'id' => '1049',
                'name' => 'میرزاکوچک جنگلی',
                'state_id' => '25',
                'city_id' => '287',
                'amar_code' => '10803',
            ),
            49 =>
            array (
                'id' => '1050',
                'name' => 'میلاجرد',
                'state_id' => '28',
                'city_id' => '344',
                'amar_code' => '1102',
            ),
            50 =>
            array (
                'id' => '1051',
                'name' => 'میمند',
                'state_id' => '17',
                'city_id' => '314',
                'amar_code' => '70904',
            ),
            51 =>
            array (
                'id' => '1052',
                'name' => 'میمه',
                'state_id' => '4',
                'city_id' => '272',
                'amar_code' => '101602',
            ),
            52 =>
            array (
                'id' => '1053',
                'name' => 'مینان',
                'state_id' => '16',
                'city_id' => '242',
                'amar_code' => '112303',
            ),
            53 =>
            array (
                'id' => '1054',
                'name' => 'مینو',
                'state_id' => '13',
                'city_id' => '160',
                'amar_code' => '60702',
            ),
            54 =>
            array (
                'id' => '1055',
                'name' => 'نارنجستان',
                'state_id' => '27',
                'city_id' => '261',
                'amar_code' => '22702',
            ),
            55 =>
            array (
                'id' => '1056',
                'name' => 'نازلو',
                'state_id' => '2',
                'city_id' => '26',
                'amar_code' => '40105',
            ),
            56 =>
            array (
                'id' => '1057',
                'name' => 'نازیل',
                'state_id' => '16',
                'city_id' => '120',
                'amar_code' => '112102',
            ),
            57 =>
            array (
                'id' => '1058',
                'name' => 'ناغان',
                'state_id' => '9',
                'city_id' => '357',
                'amar_code' => '140702',
            ),
            58 =>
            array (
                'id' => '1059',
                'name' => 'نالوس',
                'state_id' => '2',
                'city_id' => '36',
                'amar_code' => '41302',
            ),
            59 =>
            array (
                'id' => '1060',
                'name' => 'ندوشن',
                'state_id' => '31',
                'city_id' => '414',
                'amar_code' => '210602',
            ),
            60 =>
            array (
                'id' => '1061',
                'name' => 'نسکند',
                'state_id' => '16',
                'city_id' => '242',
                'amar_code' => '112304',
            ),
            61 =>
            array (
                'id' => '1062',
                'name' => 'نشتا',
                'state_id' => '27',
                'city_id' => '123',
                'amar_code' => '20503',
            ),
            62 =>
            array (
                'id' => '1063',
                'name' => 'نصرآباد',
                'state_id' => '11',
                'city_id' => '116',
                'amar_code' => '90603',
            ),
            63 =>
            array (
                'id' => '1064',
                'name' => 'نصرت آباد',
                'state_id' => '16',
                'city_id' => '225',
                'amar_code' => '110503',
            ),
            64 =>
            array (
                'id' => '1065',
                'name' => 'نظرکهریزی',
                'state_id' => '1',
                'city_id' => '440',
                'amar_code' => '31103',
            ),
            65 =>
            array (
                'id' => '1066',
                'name' => 'نگار',
                'state_id' => '21',
                'city_id' => '70',
                'amar_code' => '81004',
            ),
            66 =>
            array (
                'id' => '1067',
                'name' => 'نگین کویر',
                'state_id' => '21',
                'city_id' => '313',
                'amar_code' => '81902',
            ),
            67 =>
            array (
                'id' => '1068',
                'name' => 'نمشیر',
                'state_id' => '20',
                'city_id' => '62',
                'amar_code' => '120103',
            ),
            68 =>
            array (
                'id' => '1069',
                'name' => 'ننور',
                'state_id' => '20',
                'city_id' => '62',
                'amar_code' => '120104',
            ),
            69 =>
            array (
                'id' => '1070',
                'name' => 'نوبران',
                'state_id' => '28',
                'city_id' => '236',
                'amar_code' => '604',
            ),
            70 =>
            array (
                'id' => '1071',
                'name' => 'نوبندگان',
                'state_id' => '17',
                'city_id' => '309',
                'amar_code' => '70804',
            ),
            71 =>
            array (
                'id' => '1072',
                'name' => 'نوخندان',
                'state_id' => '11',
                'city_id' => '179',
                'amar_code' => '90704',
            ),
            72 =>
            array (
                'id' => '1073',
                'name' => 'نوده انقلاب',
                'state_id' => '11',
                'city_id' => '173',
                'amar_code' => '93803',
            ),
            73 =>
            array (
                'id' => '1074',
                'name' => 'نوسود',
                'state_id' => '22',
                'city_id' => '107',
                'amar_code' => '50303',
            ),
            74 =>
            array (
                'id' => '1075',
                'name' => 'نوق',
                'state_id' => '21',
                'city_id' => '214',
                'amar_code' => '80404',
            ),
            75 =>
            array (
                'id' => '1076',
                'name' => 'نوکنده',
                'state_id' => '24',
                'city_id' => '83',
                'amar_code' => '270102',
            ),
            76 =>
            array (
                'id' => '1077',
                'name' => 'نیاسر',
                'state_id' => '4',
                'city_id' => '332',
                'amar_code' => '101004',
            ),
            77 =>
            array (
                'id' => '1078',
                'name' => 'نیر',
                'state_id' => '31',
                'city_id' => '119',
                'amar_code' => '210303',
            ),
            78 =>
            array (
                'id' => '1079',
                'name' => 'نیسان',
                'state_id' => '13',
                'city_id' => '446',
                'amar_code' => '62301',
            ),
            79 =>
            array (
                'id' => '1080',
                'name' => 'نیمبلوک',
                'state_id' => '10',
                'city_id' => '318',
                'amar_code' => '290403',
            ),
            80 =>
            array (
                'id' => '1081',
                'name' => 'وراوی',
                'state_id' => '17',
                'city_id' => '406',
                'amar_code' => '72103',
            ),
            81 =>
            array (
                'id' => '1082',
                'name' => 'وردشت',
                'state_id' => '4',
                'city_id' => '257',
                'amar_code' => '100504',
            ),
            82 =>
            array (
                'id' => '1083',
                'name' => 'وزینه',
                'state_id' => '2',
                'city_id' => '248',
                'amar_code' => '40402',
            ),
            83 =>
            array (
                'id' => '1084',
                'name' => 'وشمگیر',
                'state_id' => '24',
                'city_id' => '13',
                'amar_code' => '270801',
            ),
            84 =>
            array (
                'id' => '1085',
                'name' => 'ویس',
                'state_id' => '13',
                'city_id' => '63',
                'amar_code' => '62402',
            ),
            85 =>
            array (
                'id' => '1086',
                'name' => 'ویسیان',
                'state_id' => '26',
                'city_id' => '147',
                'amar_code' => '151003',
            ),
            86 =>
            array (
                'id' => '1087',
                'name' => 'ویلکیج',
                'state_id' => '3',
                'city_id' => '425',
                'amar_code' => '240801',
            ),
            87 =>
            array (
                'id' => '1088',
                'name' => 'هرمز',
                'state_id' => '29',
                'city_id' => '82',
                'amar_code' => '220207',
            ),
            88 =>
            array (
                'id' => '1089',
                'name' => 'هزارجریب',
                'state_id' => '27',
                'city_id' => '424',
                'amar_code' => '21902',
            ),
            89 =>
            array (
                'id' => '1090',
                'name' => 'هلالی',
                'state_id' => '11',
                'city_id' => '131',
                'amar_code' => '93402',
            ),
            90 =>
            array (
                'id' => '1091',
                'name' => 'همایجان',
                'state_id' => '17',
                'city_id' => '238',
                'amar_code' => '70603',
            ),
            91 =>
            array (
                'id' => '1092',
                'name' => 'هندمینی',
                'state_id' => '6',
                'city_id' => '67',
                'amar_code' => '161002',
            ),
            92 =>
            array (
                'id' => '1093',
                'name' => 'هنزا',
                'state_id' => '21',
                'city_id' => '200',
                'amar_code' => '81802',
            ),
            93 =>
            array (
                'id' => '1094',
                'name' => 'هیدوچ',
                'state_id' => '16',
                'city_id' => '263',
                'amar_code' => '111402',
            ),
            94 =>
            array (
                'id' => '1095',
                'name' => 'هیر',
                'state_id' => '3',
                'city_id' => '20',
                'amar_code' => '240104',
            ),
            95 =>
            array (
                'id' => '1096',
                'name' => 'یامچی',
                'state_id' => '1',
                'city_id' => '391',
                'amar_code' => '30703',
            ),
            96 =>
            array (
                'id' => '1097',
                'name' => 'یانه سر',
                'state_id' => '27',
                'city_id' => '96',
                'amar_code' => '20404',
            ),
            97 =>
            array (
                'id' => '1098',
                'name' => 'یزدان آباد',
                'state_id' => '21',
                'city_id' => '227',
                'amar_code' => '80503',
            ),
            98 =>
            array (
                'id' => '1099',
                'name' => 'یونسی',
                'state_id' => '11',
                'city_id' => '64',
                'amar_code' => '93102',
            ),
        ));


    }
}
