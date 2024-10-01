<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PositionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('positions')->delete();
        
        \DB::table('positions')->insert(array (
            0 => 
            array (
                'id' => '1',
                'name' => 'دهیار',
                'status_id' => '29',
                'ounit_cat' => '5',
            ),
            1 => 
            array (
                'id' => '2',
                'name' => 'بخشدار',
                'status_id' => '29',
                'ounit_cat' => '3',
            ),
            2 => 
            array (
                'id' => '3',
                'name' => 'شورا',
                'status_id' => '29',
                'ounit_cat' => '5',
            ),
            3 => 
            array (
                'id' => '4',
                'name' => 'پرسنل دهیاری',
                'status_id' => '29',
                'ounit_cat' => '5',
            ),
            4 => 
            array (
                'id' => '5',
                'name' => 'کارشناس آموزش استانداری',
                'status_id' => '29',
                'ounit_cat' => '1',
            ),
            5 => 
            array (
                'id' => '6',
                'name' => 'سمت تست',
                'status_id' => '30',
                'ounit_cat' => NULL,
            ),
            6 => 
            array (
                'id' => '7',
                'name' => 'پرسنل فرمانداری',
                'status_id' => '30',
                'ounit_cat' => '2',
            ),
            7 => 
            array (
                'id' => '8',
                'name' => 'مدیر بخشداری',
                'status_id' => '30',
                'ounit_cat' => '3',
            ),
            8 => 
            array (
                'id' => '9',
                'name' => 'پرسنل بخشداری',
                'status_id' => '30',
                'ounit_cat' => '3',
            ),
        ));
        
        
    }
}