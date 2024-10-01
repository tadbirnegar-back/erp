<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('roles')->delete();

        \DB::table('roles')->upsert(array(
            0 =>
                array(
                    'id' => '1',
                    'name' => 'ادمین',
                    'section_id' => NULL,
                    'status_id' => '13',
                ),
            1 =>
                array(
                    'id' => '2',
                    'name' => 'کاربر',
                    'section_id' => NULL,
                    'status_id' => '13',
                ),
            2 =>
                array(
                    'id' => '3',
                    'name' => 'مدیرکل',
                    'section_id' => NULL,
                    'status_id' => '13',
                ),
            3 =>
                array(
                    'id' => '4',
                    'name' => 'کاربر بخشداری',
                    'section_id' => NULL,
                    'status_id' => '13',
                ),
            4 =>
                array(
                    'id' => '5',
                    'name' => 'بخشدار',
                    'section_id' => NULL,
                    'status_id' => '13',
                ),
            5 =>
                array(
                    'id' => '6',
                    'name' => 'کارشناس مشورتی',
                    'section_id' => NULL,
                    'status_id' => '13',
                ),
            6 =>
                array(
                    'id' => '7',
                    'name' => 'عضو هیئت',
                    'section_id' => NULL,
                    'status_id' => '13',
                ),
            7 =>
                array(
                    'id' => '8',
                    'name' => 'کارناشناس استانداری',
                    'section_id' => NULL,
                    'status_id' => '13',
                ),
        ), ['id']);


    }
}
