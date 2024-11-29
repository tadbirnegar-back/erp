<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class  PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


//        \DB::table('permissions')->delete();

        \DB::table('permissions')->upsert(array(
            0 =>
                array(
                    'id' => '1',
                    'name' => 'لیست نقش ها',
                    'slug' => '/users/roles/list',
                    'module_id' => '1',
                    'permission_type_id' => '1',
                ),
            1 =>
                array(
                    'id' => '2',
                    'name' => 'افزودن نقش',
                    'slug' => '/users/roles/add',
                    'module_id' => '1',
                    'permission_type_id' => '1',
                ),
            2 =>
                array(
                    'id' => '3',
                    'name' => 'مشاهده نقش',
                    'slug' => '/users/roles/{id}',
                    'module_id' => '1',
                    'permission_type_id' => '2',
                ),
            3 =>
                array(
                    'id' => '4',
                    'name' => 'ویرایش نقش',
                    'slug' => '/users/roles/edit/{id}',
                    'module_id' => '1',
                    'permission_type_id' => '2',
                ),
            4 =>
                array(
                    'id' => '5',
                    'name' => 'بروزرسانی نقش',
                    'slug' => '/users/roles/update/{id}',
                    'module_id' => '1',
                    'permission_type_id' => '2',
                ),
            5 =>
                array(
                    'id' => '6',
                    'name' => 'حذف نقش',
                    'slug' => '/users/roles/delete/{id}',
                    'module_id' => '1',
                    'permission_type_id' => '2',
                ),
            6 =>
                array(
                    'id' => '7',
                    'name' => 'لیست کاربران',
                    'slug' => '/users/list',
                    'module_id' => '1',
                    'permission_type_id' => '1',
                ),
            7 =>
                array(
                    'id' => '8',
                    'name' => 'افزودن کاربر',
                    'slug' => '/users/add',
                    'module_id' => '1',
                    'permission_type_id' => '1',
                ),
            8 =>
                array(
                    'id' => '9',
                    'name' => 'مشاهده کاربر',
                    'slug' => '/users/view/{id}',
                    'module_id' => '1',
                    'permission_type_id' => '2',
                ),
            9 =>
                array(
                    'id' => '10',
                    'name' => 'ویرایش کاربر',
                    'slug' => '/users/edit/{id}',
                    'module_id' => '1',
                    'permission_type_id' => '2',
                ),
            10 =>
                array(
                    'id' => '11',
                    'name' => 'بروزرسانی کاربر',
                    'slug' => '/users/update/{id}',
                    'module_id' => '1',
                    'permission_type_id' => '2',
                ),
            11 =>
                array(
                    'id' => '12',
                    'name' => 'حذف کاربر',
                    'slug' => '/users/delete/{id}',
                    'module_id' => '1',
                    'permission_type_id' => '2',
                ),
            12 =>
                array(
                    'id' => '13',
                    'name' => 'آدرس های من',
                    'slug' => '/address/list',
                    'module_id' => '2',
                    'permission_type_id' => '2',
                ),
            13 =>
                array(
                    'id' => '14',
                    'name' => 'ثبت آدرس جدید',
                    'slug' => '/address/add',
                    'module_id' => '2',
                    'permission_type_id' => '2',
                ),
            14 =>
                array(
                    'id' => '15',
                    'name' => 'بروزرسانی آدرس',
                    'slug' => '/address/update/{id}',
                    'module_id' => '2',
                    'permission_type_id' => '2',
                ),
            15 =>
                array(
                    'id' => '16',
                    'name' => 'مشاهده آدرس',
                    'slug' => '/address/{id}',
                    'module_id' => '2',
                    'permission_type_id' => '2',
                ),
            16 =>
                array(
                    'id' => '17',
                    'name' => 'ویرایش آدرس',
                    'slug' => '/address/edit/{id}',
                    'module_id' => '2',
                    'permission_type_id' => '2',
                ),
            17 =>
                array(
                    'id' => '18',
                    'name' => 'حذف آدرس',
                    'slug' => '/address/delete/{id}',
                    'module_id' => '2',
                    'permission_type_id' => '2',
                ),
            18 =>
                array(
                    'id' => '19',
                    'name' => 'افزودن شعبه جدید',
                    'slug' => '/branch/add',
                    'module_id' => '3',
                    'permission_type_id' => '1',
                ),
            19 =>
                array(
                    'id' => '20',
                    'name' => 'لیست شعب',
                    'slug' => '/branch/list',
                    'module_id' => '3',
                    'permission_type_id' => '1',
                ),
            20 =>
                array(
                    'id' => '21',
                    'name' => 'مشاهده شعبه',
                    'slug' => '/branch/{id}',
                    'module_id' => '3',
                    'permission_type_id' => '2',
                ),
            21 =>
                array(
                    'id' => '22',
                    'name' => 'بروزرسانی شعبه',
                    'slug' => '/branch/update/{id}',
                    'module_id' => '3',
                    'permission_type_id' => '2',
                ),
            22 =>
                array(
                    'id' => '23',
                    'name' => 'ویرایش شعبه',
                    'slug' => '/branch/edit/{id}',
                    'module_id' => '3',
                    'permission_type_id' => '2',
                ),
            23 =>
                array(
                    'id' => '24',
                    'name' => 'حذف شعبه',
                    'slug' => '/branch/delete/{id}',
                    'module_id' => '3',
                    'permission_type_id' => '2',
                ),
            24 =>
                array(
                    'id' => '25',
                    'name' => 'لیست دپارتمان',
                    'slug' => '/branch/departments/list',
                    'module_id' => '3',
                    'permission_type_id' => '1',
                ),
            25 =>
                array(
                    'id' => '26',
                    'name' => 'افزودن دپارتمان',
                    'slug' => '/branch/departments/add',
                    'module_id' => '3',
                    'permission_type_id' => '1',
                ),
            26 =>
                array(
                    'id' => '27',
                    'name' => 'مشاهده دپارتمان',
                    'slug' => '/branch/departments/{id}',
                    'module_id' => '3',
                    'permission_type_id' => '2',
                ),
            27 =>
                array(
                    'id' => '28',
                    'name' => 'ویرایش دپارتمان',
                    'slug' => '/branch/departments/edit/{id}',
                    'module_id' => '3',
                    'permission_type_id' => '2',
                ),
            28 =>
                array(
                    'id' => '29',
                    'name' => 'بروزرسانی دپارتمان',
                    'slug' => '/branch/departments/update/{id}',
                    'module_id' => '3',
                    'permission_type_id' => '2',
                ),
            29 =>
                array(
                    'id' => '30',
                    'name' => 'حذف دپارتمان',
                    'slug' => '/branch/departments/delete/{id}',
                    'module_id' => '3',
                    'permission_type_id' => '2',
                ),
            30 =>
                array(
                    'id' => '31',
                    'name' => 'افزودن بخش',
                    'slug' => '/branch/departments/sections/add',
                    'module_id' => '3',
                    'permission_type_id' => '1',
                ),
            31 =>
                array(
                    'id' => '32',
                    'name' => 'ویرایش بخش',
                    'slug' => '/branch/departments/sections/edit/{id}',
                    'module_id' => '3',
                    'permission_type_id' => '2',
                ),
            32 =>
                array(
                    'id' => '33',
                    'name' => 'بروزرسانی بخش',
                    'slug' => '/branch/departments/sections/update/{id}',
                    'module_id' => '3',
                    'permission_type_id' => '2',
                ),
            33 =>
                array(
                    'id' => '34',
                    'name' => 'حذف بخش',
                    'slug' => '/branch/departments/sections/delete/{id}',
                    'module_id' => '3',
                    'permission_type_id' => '2',
                ),
            34 =>
                array(
                    'id' => '35',
                    'name' => 'مشاهده بخش',
                    'slug' => '/branch/departments/sections/{id}',
                    'module_id' => '3',
                    'permission_type_id' => '2',
                ),
            35 =>
                array(
                    'id' => '36',
                    'name' => 'لیست بخش',
                    'slug' => '/branch/departments/sections/list',
                    'module_id' => '3',
                    'permission_type_id' => '2',
                ),
            36 =>
                array(
                    'id' => '37',
                    'name' => 'افزودن مشتری جدید',
                    'slug' => '/customers/add',
                    'module_id' => '4',
                    'permission_type_id' => '1',
                ),
            37 =>
                array(
                    'id' => '38',
                    'name' => 'لیست مشتریان',
                    'slug' => '/customers/list',
                    'module_id' => '4',
                    'permission_type_id' => '1',
                ),
            38 =>
                array(
                    'id' => '39',
                    'name' => 'مشاهده مشتری',
                    'slug' => '/customers/{id}',
                    'module_id' => '4',
                    'permission_type_id' => '2',
                ),
            39 =>
                array(
                    'id' => '40',
                    'name' => 'بروزرسانی مشتری',
                    'slug' => '/customers/update/{id}',
                    'module_id' => '4',
                    'permission_type_id' => '2',
                ),
            40 =>
                array(
                    'id' => '41',
                    'name' => 'ویرایش مشتری',
                    'slug' => '/customers/edit/{id}',
                    'module_id' => '4',
                    'permission_type_id' => '2',
                ),
            41 =>
                array(
                    'id' => '42',
                    'name' => 'حذف مشتری',
                    'slug' => '/customers/delete/{id}',
                    'module_id' => '4',
                    'permission_type_id' => '2',
                ),
            42 =>
                array(
                    'id' => '43',
                    'name' => 'جست و جو مشتری حقیقی',
                    'slug' => '/customers/natural/search',
                    'module_id' => '4',
                    'permission_type_id' => '2',
                ),
            43 =>
                array(
                    'id' => '44',
                    'name' => 'جست و جو مشتری حقوقی',
                    'slug' => '/customers/legal/search',
                    'module_id' => '4',
                    'permission_type_id' => '2',
                ),
            44 =>
                array(
                    'id' => '48',
                    'name' => 'افزودن فایل جدید',
                    'slug' => '/files/add',
                    'module_id' => '6',
                    'permission_type_id' => '2',
                ),
            45 =>
                array(
                    'id' => '49',
                    'name' => 'لیست فایل ها',
                    'slug' => '/files/list',
                    'module_id' => '6',
                    'permission_type_id' => '2',
                ),
            46 =>
                array(
                    'id' => '50',
                    'name' => 'حذف فایل',
                    'slug' => '/files/delete/{id}',
                    'module_id' => '6',
                    'permission_type_id' => '2',
                ),
            47 =>
                array(
                    'id' => '51',
                    'name' => 'بروزرسانی فایل',
                    'slug' => '/files/update/{id}',
                    'module_id' => '6',
                    'permission_type_id' => '2',
                ),
            48 =>
                array(
                    'id' => '52',
                    'name' => 'ویرایش فایل',
                    'slug' => '/files/edit/{id}',
                    'module_id' => '6',
                    'permission_type_id' => '2',
                ),
            49 =>
                array(
                    'id' => '53',
                    'name' => 'بروزرسانی فایل',
                    'slug' => '/files/edit/{id}',
                    'module_id' => '6',
                    'permission_type_id' => '2',
                ),
            50 =>
                array(
                    'id' => '54',
                    'name' => 'مشاهده فایل',
                    'slug' => '/files/{id}',
                    'module_id' => '6',
                    'permission_type_id' => '2',
                ),
            51 =>
                array(
                    'id' => '55',
                    'name' => 'لیست فرم ها',
                    'slug' => '/forms/list',
                    'module_id' => '7',
                    'permission_type_id' => '1',
                ),
            52 =>
                array(
                    'id' => '56',
                    'name' => 'افزودن فرم',
                    'slug' => '/forms/add',
                    'module_id' => '7',
                    'permission_type_id' => '1',
                ),
            53 =>
                array(
                    'id' => '57',
                    'name' => 'مشاهده فرم',
                    'slug' => '/forms/{id}',
                    'module_id' => '7',
                    'permission_type_id' => '2',
                ),
            54 =>
                array(
                    'id' => '58',
                    'name' => 'بروزرسانی فرم',
                    'slug' => '/forms/update/{id}',
                    'module_id' => '7',
                    'permission_type_id' => '2',
                ),
            55 =>
                array(
                    'id' => '59',
                    'name' => 'حذف فرم',
                    'slug' => '/forms/delete/{id}',
                    'module_id' => '7',
                    'permission_type_id' => '2',
                ),
            56 =>
                array(
                    'id' => '60',
                    'name' => 'لیست پاسخ ها',
                    'slug' => '/forms/answers/list',
                    'module_id' => '7',
                    'permission_type_id' => '1',
                ),
            57 =>
                array(
                    'id' => '61',
                    'name' => 'مشاهده پاسخ',
                    'slug' => '/forms/answers/{id}',
                    'module_id' => '7',
                    'permission_type_id' => '2',
                ),
            58 =>
                array(
                    'id' => '62',
                    'name' => 'حذف پاسخ',
                    'slug' => '/forms/answers/delete/{id}',
                    'module_id' => '7',
                    'permission_type_id' => '2',
                ),
            59 =>
                array(
                    'id' => '63',
                    'name' => 'لیست کارمندان',
                    'slug' => '/hrm/employee/list',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            60 =>
                array(
                    'id' => '64',
                    'name' => 'افزودن کارمند',
                    'slug' => '/hrm/employee/add',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            61 =>
                array(
                    'id' => '65',
                    'name' => 'مشاهده کارمند',
                    'slug' => '/hrm/employee/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            62 =>
                array(
                    'id' => '66',
                    'name' => 'بروزرسانی کارمند',
                    'slug' => '/hrm/employee/update/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            63 =>
                array(
                    'id' => '67',
                    'name' => 'حذف کارمند',
                    'slug' => '/hrm/employee/delete/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            64 =>
                array(
                    'id' => '68',
                    'name' => 'لیست سمت',
                    'slug' => '/hrm/positions/list',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            65 =>
                array(
                    'id' => '69',
                    'name' => 'افزودن سمت',
                    'slug' => '/hrm/positions/add',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            66 =>
                array(
                    'id' => '70',
                    'name' => 'مشاهده سمت',
                    'slug' => '/hrm/positions/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            67 =>
                array(
                    'id' => '71',
                    'name' => 'بروزرسانی سمت',
                    'slug' => '/hrm/positions/update/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            68 =>
                array(
                    'id' => '72',
                    'name' => 'حذف سمت',
                    'slug' => '/hrm/positions/delete/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            69 =>
                array(
                    'id' => '73',
                    'name' => 'لیست مهارت',
                    'slug' => '/hrm/skills/list',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            70 =>
                array(
                    'id' => '74',
                    'name' => 'افزودن مهارت',
                    'slug' => '/hrm/skills/add',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            71 =>
                array(
                    'id' => '75',
                    'name' => 'مشاهده مهارت',
                    'slug' => '/hrm/skills/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            72 =>
                array(
                    'id' => '76',
                    'name' => 'بروزرسانی مهارت',
                    'slug' => '/hrm/skills/update/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            73 =>
                array(
                    'id' => '77',
                    'name' => 'حذف مهارت',
                    'slug' => '/hrm/skills/delete/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            74 =>
                array(
                    'id' => '78',
                    'name' => 'لیست سطح سازمانی',
                    'slug' => '/hrm/levels/list',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            75 =>
                array(
                    'id' => '79',
                    'name' => 'افزودن سطح سازمانی',
                    'slug' => '/hrm/levels/add',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            76 =>
                array(
                    'id' => '80',
                    'name' => 'مشاهده سطح سازمانی',
                    'slug' => '/hrm/levels/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            77 =>
                array(
                    'id' => '81',
                    'name' => 'بروزرسانی سطح سازمانی',
                    'slug' => '/hrm/levels/update/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            78 =>
                array(
                    'id' => '82',
                    'name' => 'حذف سطح سازمانی',
                    'slug' => '/hrm/levels/delete/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            79 =>
                array(
                    'id' => '83',
                    'name' => 'لیست فراگیر',
                    'slug' => '/students/list',
                    'module_id' => '9',
                    'permission_type_id' => '1',
                ),
            80 =>
                array(
                    'id' => '84',
                    'name' => 'افزودن فراگیر',
                    'slug' => '/hrm/students/add',
                    'module_id' => '9',
                    'permission_type_id' => '1',
                ),
            81 =>
                array(
                    'id' => '85',
                    'name' => 'ثبت نام فراگیر',
                    'slug' => '/dehyari/add',
                    'module_id' => '9',
                    'permission_type_id' => '1',
                ),
            82 =>
                array(
                    'id' => '86',
                    'name' => 'مشاهده فراگیر',
                    'slug' => '/students/{id}',
                    'module_id' => '9',
                    'permission_type_id' => '2',
                ),
            83 =>
                array(
                    'id' => '87',
                    'name' => 'بروزرسانی فراگیر',
                    'slug' => '/students/update/{id}',
                    'module_id' => '9',
                    'permission_type_id' => '2',
                ),
            84 =>
                array(
                    'id' => '88',
                    'name' => 'حذف فراگیر',
                    'slug' => '/students/delete/{id}',
                    'module_id' => '9',
                    'permission_type_id' => '2',
                ),
            85 =>
                array(
                    'id' => '89',
                    'name' => 'لیست کسب و کار ها',
                    'slug' => '/person/legal/list',
                    'module_id' => '10',
                    'permission_type_id' => '1',
                ),
            86 =>
                array(
                    'id' => '90',
                    'name' => 'لیست افراد',
                    'slug' => '/person/natural/list',
                    'module_id' => '10',
                    'permission_type_id' => '1',
                ),
            87 =>
                array(
                    'id' => '91',
                    'name' => 'افزودن فرد',
                    'slug' => '/person/natural/add',
                    'module_id' => '10',
                    'permission_type_id' => '1',
                ),
            88 =>
                array(
                    'id' => '92',
                    'name' => 'افزودن کسب و کار',
                    'slug' => '/person/legal/add',
                    'module_id' => '10',
                    'permission_type_id' => '1',
                ),
            89 =>
                array(
                    'id' => '93',
                    'name' => 'بروزرسانی فرد',
                    'slug' => '/person/natural/update/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            90 =>
                array(
                    'id' => '94',
                    'name' => 'بروزرسانی کسب و کار',
                    'slug' => '/person/legal/update/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            91 =>
                array(
                    'id' => '95',
                    'name' => 'ویرایش فرد',
                    'slug' => '/person/natural/edit/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            92 =>
                array(
                    'id' => '96',
                    'name' => 'ویرایش کسب و کار',
                    'slug' => '/person/legal/edit/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            93 =>
                array(
                    'id' => '97',
                    'name' => 'مشاهده فرد',
                    'slug' => '/person/natural/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            94 =>
                array(
                    'id' => '98',
                    'name' => 'مشاهده کسب و کار',
                    'slug' => '/person/legal/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            95 =>
                array(
                    'id' => '99',
                    'name' => 'حذف فرد',
                    'slug' => '/person/natural/delete/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            96 =>
                array(
                    'id' => '100',
                    'name' => 'حذف کسب و کار',
                    'slug' => '/person/legal/delete/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            97 =>
                array(
                    'id' => '101',
                    'name' => 'لیست محصولات',
                    'slug' => '/products/merchandise/list',
                    'module_id' => '11',
                    'permission_type_id' => '1',
                ),
            98 =>
                array(
                    'id' => '102',
                    'name' => 'افزودن محصول',
                    'slug' => '/products/merchandise/add',
                    'module_id' => '11',
                    'permission_type_id' => '1',
                ),
            99 =>
                array(
                    'id' => '103',
                    'name' => 'مشاهده محصول',
                    'slug' => '/products/merchandise/{id}',
                    'module_id' => '11',
                    'permission_type_id' => '2',
                ),
            100 =>
                array(
                    'id' => '104',
                    'name' => 'بروزرسانی محصول',
                    'slug' => '/products/merchandise/update/{id}',
                    'module_id' => '11',
                    'permission_type_id' => '2',
                ),
            101 =>
                array(
                    'id' => '105',
                    'name' => 'حذف محصول',
                    'slug' => '/products/merchandise/delete/{id}',
                    'module_id' => '11',
                    'permission_type_id' => '2',
                ),
            102 =>
                array(
                    'id' => '106',
                    'name' => 'لیست متغیر',
                    'slug' => '/products/merchandise/variants/list',
                    'module_id' => '11',
                    'permission_type_id' => '1',
                ),
            103 =>
                array(
                    'id' => '107',
                    'name' => 'افزودن متغیر',
                    'slug' => '/products/merchandise/variants/add',
                    'module_id' => '11',
                    'permission_type_id' => '1',
                ),
            104 =>
                array(
                    'id' => '108',
                    'name' => 'مشاهده متغیر',
                    'slug' => '/products/merchandise/variants/{id}',
                    'module_id' => '11',
                    'permission_type_id' => '2',
                ),
            105 =>
                array(
                    'id' => '109',
                    'name' => 'بروزرسانی متغیر',
                    'slug' => '/products/merchandise/variants/update/{id}',
                    'module_id' => '11',
                    'permission_type_id' => '2',
                ),
            106 =>
                array(
                    'id' => '110',
                    'name' => 'حذف متغیر',
                    'slug' => '/products/merchandise/variants/delete/{id}',
                    'module_id' => '11',
                    'permission_type_id' => '2',
                ),
            107 =>
                array(
                    'id' => '111',
                    'name' => 'لیست دسته بندی',
                    'slug' => '/products/merchandise/category/list',
                    'module_id' => '11',
                    'permission_type_id' => '1',
                ),
            108 =>
                array(
                    'id' => '112',
                    'name' => 'افزودن دسته بندی',
                    'slug' => '/products/merchandise/category/add',
                    'module_id' => '11',
                    'permission_type_id' => '1',
                ),
            109 =>
                array(
                    'id' => '113',
                    'name' => 'مشاهده دسته بندی',
                    'slug' => '/products/merchandise/category/{id}',
                    'module_id' => '11',
                    'permission_type_id' => '2',
                ),
            110 =>
                array(
                    'id' => '114',
                    'name' => 'بروزرسانی دسته بندی',
                    'slug' => '/products/merchandise/category/update/{id}',
                    'module_id' => '11',
                    'permission_type_id' => '2',
                ),
            111 =>
                array(
                    'id' => '115',
                    'name' => 'حذف دسته بندی',
                    'slug' => '/products/merchandise/category/delete/{id}',
                    'module_id' => '11',
                    'permission_type_id' => '2',
                ),
            112 =>
                array(
                    'id' => '116',
                    'name' => 'مشاهده مشخصات کاربر',
                    'slug' => '/widget/profile',
                    'module_id' => '1',
                    'permission_type_id' => '3',
                ),
            113 =>
                array(
                    'id' => '118',
                    'name' => 'لیست پرداخت ها',
                    'slug' => '/payments/list',
                    'module_id' => '12',
                    'permission_type_id' => '1',
                ),
            114 =>
                array(
                    'id' => '119',
                    'name' => 'نتایج ارزیابی',
                    'slug' => '/evaluations/list',
                    'module_id' => '5',
                    'permission_type_id' => '1',
                ),
            115 =>
                array(
                    'id' => '120',
                    'name' => 'افزودن فرم جدید',
                    'slug' => NULL,
                    'module_id' => '5',
                    'permission_type_id' => '1',
                ),
            116 =>
                array(
                    'id' => '121',
                    'name' => 'فرم های ارزیابی',
                    'slug' => NULL,
                    'module_id' => '5',
                    'permission_type_id' => '1',
                ),
            117 =>
                array(
                    'id' => '122',
                    'name' => 'ویجت اطلاعات دهیاری',
                    'slug' => '/widget/village_ofc',
                    'module_id' => '1',
                    'permission_type_id' => '3',
                ),
            118 =>
                array(
                    'id' => '123',
                    'name' => 'گزارش وضعیت دهیاران',
                    'slug' => '/payments/log/district/list',
                    'module_id' => '12',
                    'permission_type_id' => '1',
                ),
            119 =>
                array(
                    'id' => '124',
                    'name' => 'فیلتر لیست کارمندان',
                    'slug' => '/hrm/employee/list/filter',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            120 =>
                array(
                    'id' => '125',
                    'name' => 'پیکربندی منابع انسانی',
                    'slug' => '/hrm/setting',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            121 =>
                array(
                    'id' => '126',
                    'name' => 'جستجوی کارمند',
                    'slug' => '/employee/natural/search',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            122 =>
                array(
                    'id' => '127',
                    'name' => 'جستجوی کد ملی کارمند',
                    'slug' => '/employee/national-code/search',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            123 =>
                array(
                    'id' => '128',
                    'name' => 'لیست استان‌های استخدام',
                    'slug' => '/recruitment/list/state_ofc',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            124 =>
                array(
                    'id' => '129',
                    'name' => 'لیست شهرهای استخدام',
                    'slug' => '/recruitment/list/city_ofc',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            125 =>
                array(
                    'id' => '130',
                    'name' => 'لیست مناطق استخدام',
                    'slug' => '/recruitment/list/district_ofc',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            126 =>
                array(
                    'id' => '131',
                    'name' => 'لیست شهرک‌های استخدام',
                    'slug' => '/recruitment/list/town_ofc',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            127 =>
                array(
                    'id' => '132',
                    'name' => 'لیست روستاهای استخدام',
                    'slug' => '/recruitment/list/village_ofc',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            128 =>
                array(
                    'id' => '133',
                    'name' => 'افزودن نوع عامل حکم',
                    'slug' => '/hrm/script-agent-type/add',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            129 =>
                array(
                    'id' => '134',
                    'name' => 'بروزرسانی نوع عامل حکم',
                    'slug' => '/hrm/script-agent-type/update/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            130 =>
                array(
                    'id' => '135',
                    'name' => 'حذف نوع عامل حکم',
                    'slug' => '/hrm/script-agent-type/delete/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            131 =>
                array(
                    'id' => '136',
                    'name' => 'افزودن شغل منابع انسانی',
                    'slug' => '/hrm/jobs/add',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            132 =>
                array(
                    'id' => '137',
                    'name' => 'بروزرسانی شغل منابع انسانی',
                    'slug' => '/hrm/jobs/update/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            133 =>
                array(
                    'id' => '138',
                    'name' => 'حذف شغل منابع انسانی',
                    'slug' => '/hrm/jobs/delete/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            134 =>
                array(
                    'id' => '139',
                    'name' => 'افزودن نوع استخدام',
                    'slug' => '/hrm/hire-types/add',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            135 =>
                array(
                    'id' => '140',
                    'name' => 'بروزرسانی نوع استخدام',
                    'slug' => '/hrm/hire-types/update/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            136 =>
                array(
                    'id' => '141',
                    'name' => 'حذف نوع استخدام',
                    'slug' => '/hrm/hire-types/delete/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            137 =>
                array(
                    'id' => '142',
                    'name' => 'افزودن نوع حکم',
                    'slug' => '/hrm/script-types/add',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            138 =>
                array(
                    'id' => '143',
                    'name' => 'بروزرسانی نوع حکم',
                    'slug' => '/hrm/script-types/update/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            139 =>
                array(
                    'id' => '144',
                    'name' => 'حذف نوع حکم',
                    'slug' => '/hrm/script-types/delete/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            140 =>
                array(
                    'id' => '145',
                    'name' => 'افزودن عامل حکم',
                    'slug' => '/hrm/script-agents/add',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            141 =>
                array(
                    'id' => '146',
                    'name' => 'بروزرسانی عامل حکم',
                    'slug' => '/hrm/script-agents/update/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            142 =>
                array(
                    'id' => '147',
                    'name' => 'حذف عامل حکم',
                    'slug' => '/hrm/script-agents/delete/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            143 =>
                array(
                    'id' => '148',
                    'name' => 'لیست موقعیت‌های واحد سازمانی',
                    'slug' => '/hrm/ounit/positions/list',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            144 =>
                array(
                    'id' => '149',
                    'name' => 'ترکیبات حکم کارمند',
                    'slug' => '/hrm/employee/script-combos',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            145 =>
                array(
                    'id' => '150',
                    'name' => 'نوع حکم کارمند',
                    'slug' => '/hrm/employee/script-types',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            146 =>
                array(
                    'id' => '151',
                    'name' => 'احکام صادر شده',
                    'slug' => '/hrm/rc/list',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            147 =>
                array(
                    'id' => '152',
                    'name' => 'احکام در انتظار تایید من',
                    'slug' => '/hrm/prc/list',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            148 =>
                array(
                    'id' => '153',
                    'name' => 'جزئیات منابع انسانی پرچ',
                    'slug' => '/hrm/prc/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            149 =>
                array(
                    'id' => '154',
                    'name' => 'تایید حکم توسط تایید کننده',
                    'slug' => '/hrm/rc/grant/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            150 =>
                array(
                    'id' => '155',
                    'name' => 'صدور حکم',
                    'slug' => '/hrm/rc/insert/add',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            151 =>
                array(
                    'id' => '156',
                    'name' => 'لیست سطح تحصیلات',
                    'slug' => '/hrm/education-levels/list',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            152 =>
                array(
                    'id' => '157',
                    'name' => 'لیست انواع ایثارگران',
                    'slug' => '/hrm/isar-types/list',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            153 =>
                array(
                    'id' => '158',
                    'name' => 'لیست انواع نسبت‌ها',
                    'slug' => '/hrm/relative-types/list',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            154 =>
                array(
                    'id' => '159',
                    'name' => 'ثبت دهیار',
                    'slug' => '/hrm/register/dehyar',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            155 =>
                array(
                    'id' => '160',
                    'name' => 'تأیید کارمند',
                    'slug' => '/hrm/employee/verify',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            156 =>
                array(
                    'id' => '161',
                    'name' => 'تأیید شده‌های منابع انسانی',
                    'slug' => '/hrm/verified',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            157 =>
                array(
                    'id' => '162',
                    'name' => 'تأیید منابع انسانی',
                    'slug' => '/hrm/confirm',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            158 =>
                array(
                    'id' => '163',
                    'name' => 'تأیید اعتبار منابع انسانی',
                    'slug' => '/hrm/verify',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            159 =>
                array(
                    'id' => '164',
                    'name' => 'ویرایش تأییدیه کارمند',
                    'slug' => '/hrm/employee/confirm/edit',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            160 =>
                array(
                    'id' => '165',
                    'name' => 'لیست شهرستان ها',
                    'slug' => '/oms/cityofc/list',
                    'module_id' => '14',
                    'permission_type_id' => '1',
                ),
            161 =>
                array(
                    'id' => '166',
                    'name' => 'افزودن شهرستان',
                    'slug' => '/oms/cityofc/add',
                    'module_id' => '14',
                    'permission_type_id' => '1',
                ),
            162 =>
                array(
                    'id' => '167',
                    'name' => 'لیست بخشداری',
                    'slug' => '/oms/districtofc/list',
                    'module_id' => '14',
                    'permission_type_id' => '1',
                ),
            163 =>
                array(
                    'id' => '168',
                    'name' => 'افزودن بخشداری',
                    'slug' => '/oms/districtofc/add',
                    'module_id' => '14',
                    'permission_type_id' => '1',
                ),
            164 =>
                array(
                    'id' => '169',
                    'name' => 'لیست دهستان',
                    'slug' => '/oms/townofc/list',
                    'module_id' => '14',
                    'permission_type_id' => '1',
                ),
            165 =>
                array(
                    'id' => '170',
                    'name' => 'افزودن دهستان',
                    'slug' => '/oms/townofc/add',
                    'module_id' => '14',
                    'permission_type_id' => '1',
                ),
            166 =>
                array(
                    'id' => '171',
                    'name' => 'لیست روستاها',
                    'slug' => '/oms/villageofc/list',
                    'module_id' => '14',
                    'permission_type_id' => '1',
                ),
            167 =>
                array(
                    'id' => '172',
                    'name' => 'افزودن روستا',
                    'slug' => '/oms/villageofc/add',
                    'module_id' => '14',
                    'permission_type_id' => '1',
                ),
            168 =>
                array(
                    'id' => '173',
                    'name' => 'جزئیات واحد سازمانی',
                    'slug' => '/oms/organization_unit/{id}',
                    'module_id' => '14',
                    'permission_type_id' => '2',
                ),
            169 =>
                array(
                    'id' => '174',
                    'name' => 'بروزرسانی واحد سازمانی',
                    'slug' => '/oms/organization_unit/update/{id}',
                    'module_id' => '14',
                    'permission_type_id' => '2',
                ),
            170 =>
                array(
                    'id' => '175',
                    'name' => 'جستجوی کارمند',
                    'slug' => '/oms/employee/search',
                    'module_id' => '14',
                    'permission_type_id' => '2',
                ),
            171 =>
                array(
                    'id' => '176',
                    'name' => 'جستجوی واحد سازمانی',
                    'slug' => '/oms/organization-unit/search',
                    'module_id' => '14',
                    'permission_type_id' => '2',
                ),
            172 =>
                array(
                    'id' => '177',
                    'name' => 'جستجوی شخص حقیقی',
                    'slug' => '/person/natural/search',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            173 =>
                array(
                    'id' => '178',
                    'name' => 'لیست ادیان افراد',
                    'slug' => '/person/religions/list',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            174 =>
                array(
                    'id' => '179',
                    'name' => 'لیست وضعیت نظام وظیفه',
                    'slug' => '/person/military-status/list',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            175 =>
                array(
                    'id' => '180',
                    'name' => 'لاگ افراد',
                    'slug' => '/person/log/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            176 =>
                array(
                    'id' => '181',
                    'name' => 'بروزرسانی اطلاعات کاربری',
                    'slug' => '/person/user-data/update/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            177 =>
                array(
                    'id' => '182',
                    'name' => 'بروزرسانی اطلاعات شخصی',
                    'slug' => '/person/personal-data/update/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            178 =>
                array(
                    'id' => '183',
                    'name' => 'بروزرسانی کد پرسنلی',
                    'slug' => '/person/personnel-code/update/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            179 =>
                array(
                    'id' => '184',
                    'name' => 'افزودن مهارت شخصی',
                    'slug' => '/person/skills/add/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            180 =>
                array(
                    'id' => '185',
                    'name' => 'ویرایش مهارت شخصی',
                    'slug' => '/person/skills/edit/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            181 =>
                array(
                    'id' => '186',
                    'name' => 'حذف مهارت شخصی',
                    'slug' => '/person/skills/delete/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            182 =>
                array(
                    'id' => '187',
                    'name' => 'افزودن تحصیلات شخصی',
                    'slug' => '/person/educations/add/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            183 =>
                array(
                    'id' => '188',
                    'name' => 'ویرایش تحصیلات شخصی',
                    'slug' => '/person/educations/edit/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            184 =>
                array(
                    'id' => '189',
                    'name' => 'حذف تحصیلات شخصی',
                    'slug' => '/person/educations/delete/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            185 =>
                array(
                    'id' => '190',
                    'name' => 'افزودن سابقه دوره',
                    'slug' => '/person/course-record/add/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            186 =>
                array(
                    'id' => '191',
                    'name' => 'ویرایش سابقه دوره',
                    'slug' => '/person/course-record/edit/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            187 =>
                array(
                    'id' => '192',
                    'name' => 'حذف سابقه دوره',
                    'slug' => '/person/course-record/delete/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            188 =>
                array(
                    'id' => '193',
                    'name' => 'افزودن رزومه',
                    'slug' => '/person/resume/add/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            189 =>
                array(
                    'id' => '194',
                    'name' => 'ویرایش رزومه',
                    'slug' => '/person/resume/edit/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            190 =>
                array(
                    'id' => '195',
                    'name' => 'حذف رزومه',
                    'slug' => '/person/resume/delete/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            191 =>
                array(
                    'id' => '196',
                    'name' => 'افزودن وابستگان',
                    'slug' => '/person/relative/add/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            192 =>
                array(
                    'id' => '197',
                    'name' => 'ویرایش وابستگان',
                    'slug' => '/person/relative/edit/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            193 =>
                array(
                    'id' => '198',
                    'name' => 'حذف وابستگان',
                    'slug' => '/person/relative/delete/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            194 =>
                array(
                    'id' => '199',
                    'name' => 'افزودن خدمت نظام وظیفه',
                    'slug' => '/person/military-service/add/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            195 =>
                array(
                    'id' => '200',
                    'name' => 'افزودن ایثارگری',
                    'slug' => '/person/isar/add/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            196 =>
                array(
                    'id' => '201',
                    'name' => 'بروزرسانی اطلاعات تماس',
                    'slug' => '/person/contact-data/update/{id}',
                    'module_id' => '10',
                    'permission_type_id' => '2',
                ),
            197 =>
                array(
                    'id' => '202',
                    'name' => 'افزودن مصوبه توسط شورا',
                    'slug' => '/mes/enactment/add-by-board',
                    'module_id' => '13',
                    'permission_type_id' => '1',
                ),
            198 =>
                array(
                    'id' => '203',
                    'name' => 'جستجوی روستاهای واحد سازمانی',
                    'slug' => '/mes/ounit-villages/search',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            199 =>
                array(
                    'id' => '204',
                    'name' => 'لیست مصوبات در انتظار دبیر',
                    'slug' => '/mes/pbs-enactments/list',
                    'module_id' => '13',
                    'permission_type_id' => '1',
                ),
            200 =>
                array(
                    'id' => '205',
                    'name' => 'لیست مصوبات در انتظار هیئت',
                    'slug' => '/mes/pbc-enactments/list',
                    'module_id' => '13',
                    'permission_type_id' => '1',
                ),
            201 =>
                array(
                    'id' => '206',
                    'name' => 'لیست تمامی مصوبات',
                    'slug' => '/mes/all-enactments/list',
                    'module_id' => '13',
                    'permission_type_id' => '1',
                ),
            202 =>
                array(
                    'id' => '207',
                    'name' => 'جزئیات مصوبه',
                    'slug' => '/mes/enactments/{id}',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            203 =>
                array(
                    'id' => '208',
                    'name' => 'تأیید مصوبه',
                    'slug' => '/mes/enactments/approve/{id}',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            204 =>
                array(
                    'id' => '209',
                    'name' => 'عدم وصول مصوبه',
                    'slug' => '/mes/enactments/decline/{id}',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            205 =>
                array(
                    'id' => '210',
                    'name' => 'مغایرت مصوبه',
                    'slug' => '/mes/enactments/deny/{id}',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            206 =>
                array(
                    'id' => '211',
                    'name' => 'پذیرش مصوبه',
                    'slug' => '/mes/enactments/accept/{id}',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            207 =>
                array(
                    'id' => '212',
                    'name' => 'پیکر بندی دبیر',
                    'slug' => '/mes/setting/secretary',
                    'module_id' => '13',
                    'permission_type_id' => '1',
                ),
            208 =>
                array(
                    'id' => '214',
                    'name' => 'افزودن مصوبه',
                    'slug' => '/mes/enactment/add-by-secretary',
                    'module_id' => '13',
                    'permission_type_id' => '1',
                ),
            209 =>
                array(
                    'id' => '215',
                    'name' => 'مشاهده حکم/حکم من',
                    'slug' => '/hrm/rc/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            210 =>
                array(
                    'id' => '216',
                    'name' => 'پیکربندی مصوبات',
                    'slug' => '/mes/setting',
                    'module_id' => '13',
                    'permission_type_id' => '1',
                ),
            211 =>
                array(
                    'id' => '217',
                    'name' => 'بروزرسانی اعضای هیئت بخشداری',
                    'slug' => '/mes/settings/district-members',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            212 =>
                array(
                    'id' => '218',
                    'name' => 'بروزرسانی تایید خودکار رای مصوبه',
                    'slug' => '/mes/settings/auto-moghayerat',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            213 =>
                array(
                    'id' => '219',
                    'name' => 'لیست عناوین مصوبه',
                    'slug' => '/mes/settings/enactment-titles/list',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            214 =>
                array(
                    'id' => '220',
                    'name' => 'بروزرسانی عناوین مصوبه',
                    'slug' => '/mes/settings/enactment-titles/{id}',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            215 =>
                array(
                    'id' => '221',
                    'name' => 'افزودن عناوین مصوبه',
                    'slug' => '/mes/settings/enactment-titles/add',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            216 =>
                array(
                    'id' => '222',
                    'name' => 'گزارش وضعیت من',
                    'slug' => '/mes/reports/my-report',
                    'module_id' => '13',
                    'permission_type_id' => '1',
                ),
            217 =>
                array(
                    'id' => '223',
                    'name' => 'گزارش مصوبات دیگر اعضا هیئت',
                    'slug' => '/mes/reports/member',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            218 =>
                array(
                    'id' => '224',
                    'name' => 'گزارش وضعیت هیئت',
                    'slug' => '/mes/reports/district-report',
                    'module_id' => '13',
                    'permission_type_id' => '1',
                ),
            219 =>
                array(
                    'id' => '225',
                    'name' => 'رد حکم توسط تایید کننده',
                    'slug' => '/hrm/rc/decline/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            220 =>
                array(
                    'id' => '226',
                    'name' => 'احکام منقضی شده',
                    'slug' => '/hrm/erc/list',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            221 =>
                array(
                    'id' => '227',
                    'name' => 'لیست اعضای هیئت بخشداری',
                    'slug' => '/mes/settings/district-members/list',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            222 =>
                array(
                    'id' => '228',
                    'name' => 'باطل کردن حکم',
                    'slug' => '/hrm/rc/cancel/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            223 =>
                array(
                    'id' => '229',
                    'name' => 'تمدید حکم',
                    'slug' => '/hrm/rc/renew/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            224 =>
                array(
                    'id' => '230',
                    'name' => 'قطع همکاری حکم',
                    'slug' => '/hrm/rc/terminate/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            225 =>
                array(
                    'id' => '231',
                    'name' => 'اتمام همکاری حکم',
                    'slug' => '/hrm/rc/service-end/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            226 =>
                array(
                    'id' => '232',
                    'name' => 'بروزرسانی تاریخ جلسه',
                    'slug' => '/mes/meeting/ChangeMeetingDate/{id}',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            227 =>
                array(
                    'id' => '233',
                    'name' => 'افزودن دپارتمان',
                    'slug' => '/oms/department/add',
                    'module_id' => '14',
                    'permission_type_id' => '1',
                ),
            228 =>
                array(
                    'id' => '234',
                    'name' => 'لیست دپارتمان',
                    'slug' => '/oms/department/list',
                    'module_id' => '14',
                    'permission_type_id' => '1',
                ),
            229 =>
                array(
                    'id' => '235',
                    'name' => 'بروزرسانی دپارتمان',
                    'slug' => '/oms/department/{id}',
                    'module_id' => '14',
                    'permission_type_id' => '2',
                ),
            230 =>
                array(
                    'id' => '236',
                    'name' => 'صدور حکم اصلاحی',
                    'slug' => '/hrm/rc/reissue/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            231 =>
                array(
                    'id' => '237',
                    'name' => 'رد حکم توسط مدیرکل',
                    'slug' => '/hrm/rc/manager-reject/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            232 =>
                array(
                    'id' => '238',
                    'name' => 'تایید حکم توسط مدیرکل',
                    'slug' => '/hrm/rc/manager-approve/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            233 =>
                array(
                    'id' => '239',
                    'name' => 'درخواست انتصاب جدید',
                    'slug' => '/hrm/dehyar/request',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            234 =>
                array(
                    'id' => '240',
                    'name' => 'جست و جو دهیاری با کد آبادی',
                    'slug' => '/hrm/village/search-by-abadi-code',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            235 =>
                array(
                    'id' => '241',
                    'name' => 'ثبت درخواست انتصاب دهیاری جدید',
                    'slug' => '/hrm/request-new-village',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            236 =>
                array(
                    'id' => '242',
                    'name' => 'احکام در انتظار عزل',
                    'slug' => '/hrm/ptprc/list',
                    'module_id' => '8',
                    'permission_type_id' => '1',
                ),
            237 =>
                array(
                    'id' => '243',
                    'name' => 'گزارش وضعیت فرمانداری',
                    'slug' => '/mes/reports/city-report',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            238 =>
                array(
                    'id' => '244',
                    'name' => 'بررسی جلسه های روز های آتی',
                    'slug' => '/mes/meeting/selection',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
            239 =>
                array(
                    'id' => '245',
                    'name' => 'نمایش یک حکم عزل شده',
                    'slug' => '/hrm/rc/ptp/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            240 =>
                array(
                    'id' => '246',
                    'name' => 'تایید عزل حکم',
                    'slug' => '/hrm/rc/ptp/terminate/{id}',
                    'module_id' => '8',
                    'permission_type_id' => '2',
                ),
            241 =>
                array(
                    'id' => '247',
                    'name' => 'گزارش جامع',
                    'slug' => '/mes/reports/comprehensive',
                    'module_id' => '13',
                    'permission_type_id' => '1',
                ),
            242 =>
                array(
                    'id' => '248',
                    'name' => 'گزارش وضعیت دیگر هیئت',
                    'slug' => '/mes/reports/other-district-report',
                    'module_id' => '13',
                    'permission_type_id' => '2',
                ),
        ), ['slug']);


    }
}
