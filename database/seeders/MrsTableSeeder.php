<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MrsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('mrs')->delete();
        
        \DB::table('mrs')->insert(array (
            0 => 
            array (
                'id' => '5',
                'title' => 'بخشدار',
            ),
            1 => 
            array (
                'id' => '1',
                'title' => 'دبیر',
            ),
            2 => 
            array (
                'id' => '3',
                'title' => 'رئیس
',
            ),
            3 => 
            array (
                'id' => '7',
                'title' => 'عضو شورای شهرستان',
            ),
            4 => 
            array (
                'id' => '6',
                'title' => 'قاضی',
            ),
            5 => 
            array (
                'id' => '2',
                'title' => 'کارشناس مشورتی',
            ),
            6 => 
            array (
                'id' => '9',
                'title' => 'مسئول دبیرخانه',
            ),
            7 => 
            array (
                'id' => '4',
                'title' => 'منشی
',
            ),
            8 => 
            array (
                'id' => '8',
                'title' => 'نماینده استانداری',
            ),
        ));
        
        
    }
}