<?php

namespace Modules\AddressMS\database\seeders;

use Illuminate\Database\Seeder;

class StatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('states')->delete();

        \DB::table('states')->insert(array (
            0 =>
            array (
                'id' => '1',
                'name' => 'آذربایجان شرقی',
                'amar_code' => '3',
                'country_id' => '1',
            ),
            1 =>
            array (
                'id' => '2',
                'name' => 'آذربایجان غربی',
                'amar_code' => '4',
                'country_id' => '1',
            ),
            2 =>
            array (
                'id' => '3',
                'name' => 'اردبیل',
                'amar_code' => '24',
                'country_id' => '1',
            ),
            3 =>
            array (
                'id' => '4',
                'name' => 'اصفهان',
                'amar_code' => '10',
                'country_id' => '1',
            ),
            4 =>
            array (
                'id' => '5',
                'name' => 'البرز',
                'amar_code' => '30',
                'country_id' => '1',
            ),
            5 =>
            array (
                'id' => '6',
                'name' => 'ایلام',
                'amar_code' => '16',
                'country_id' => '1',
            ),
            6 =>
            array (
                'id' => '7',
                'name' => 'بوشهر',
                'amar_code' => '18',
                'country_id' => '1',
            ),
            7 =>
            array (
                'id' => '8',
                'name' => 'تهران',
                'amar_code' => '23',
                'country_id' => '1',
            ),
            8 =>
            array (
                'id' => '9',
                'name' => 'چهارمحال وبختیاری',
                'amar_code' => '14',
                'country_id' => '1',
            ),
            9 =>
            array (
                'id' => '10',
                'name' => 'خراسان جنوبی',
                'amar_code' => '29',
                'country_id' => '1',
            ),
            10 =>
            array (
                'id' => '11',
                'name' => 'خراسان رضوی',
                'amar_code' => '9',
                'country_id' => '1',
            ),
            11 =>
            array (
                'id' => '12',
                'name' => 'خراسان شمالی',
                'amar_code' => '28',
                'country_id' => '1',
            ),
            12 =>
            array (
                'id' => '13',
                'name' => 'خوزستان',
                'amar_code' => '6',
                'country_id' => '1',
            ),
            13 =>
            array (
                'id' => '14',
                'name' => 'زنجان',
                'amar_code' => '19',
                'country_id' => '1',
            ),
            14 =>
            array (
                'id' => '15',
                'name' => 'سمنان',
                'amar_code' => '20',
                'country_id' => '1',
            ),
            15 =>
            array (
                'id' => '16',
                'name' => 'سیستان وبلوچستان',
                'amar_code' => '11',
                'country_id' => '1',
            ),
            16 =>
            array (
                'id' => '17',
                'name' => 'فارس',
                'amar_code' => '7',
                'country_id' => '1',
            ),
            17 =>
            array (
                'id' => '18',
                'name' => 'قزوین',
                'amar_code' => '26',
                'country_id' => '1',
            ),
            18 =>
            array (
                'id' => '19',
                'name' => 'قم',
                'amar_code' => '25',
                'country_id' => '1',
            ),
            19 =>
            array (
                'id' => '20',
                'name' => 'کردستان',
                'amar_code' => '12',
                'country_id' => '1',
            ),
            20 =>
            array (
                'id' => '21',
                'name' => 'کرمان',
                'amar_code' => '8',
                'country_id' => '1',
            ),
            21 =>
            array (
                'id' => '22',
                'name' => 'کرمانشاه',
                'amar_code' => '5',
                'country_id' => '1',
            ),
            22 =>
            array (
                'id' => '23',
                'name' => 'کهگیلویه وبویراحمد',
                'amar_code' => '17',
                'country_id' => '1',
            ),
            23 =>
            array (
                'id' => '24',
                'name' => 'گلستان',
                'amar_code' => '27',
                'country_id' => '1',
            ),
            24 =>
            array (
                'id' => '25',
                'name' => 'گیلان',
                'amar_code' => '1',
                'country_id' => '1',
            ),
            25 =>
            array (
                'id' => '26',
                'name' => 'لرستان',
                'amar_code' => '15',
                'country_id' => '1',
            ),
            26 =>
            array (
                'id' => '27',
                'name' => 'مازندران',
                'amar_code' => '2',
                'country_id' => '1',
            ),
            27 =>
            array (
                'id' => '28',
                'name' => 'مرکزی',
                'amar_code' => '0',
                'country_id' => '1',
            ),
            28 =>
            array (
                'id' => '29',
                'name' => 'هرمزگان',
                'amar_code' => '22',
                'country_id' => '1',
            ),
            29 =>
            array (
                'id' => '30',
                'name' => 'همدان',
                'amar_code' => '13',
                'country_id' => '1',
            ),
            30 =>
            array (
                'id' => '31',
                'name' => 'یزد',
                'amar_code' => '21',
                'country_id' => '1',
            ),
        ));


    }
}
