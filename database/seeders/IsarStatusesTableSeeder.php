<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IsarStatusesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('isar_statuses')->delete();
        
        \DB::table('isar_statuses')->insert(array (
            0 => 
            array (
                'id' => '1',
                'title' => 'آزاده',
            ),
            1 => 
            array (
                'id' => '2',
                'title' => 'فرزند شهید',
            ),
            2 => 
            array (
                'id' => '3',
                'title' => 'ایثارگر',
            ),
            3 => 
            array (
                'id' => '4',
                'title' => 'جانباز',
            ),
        ));
        
        
    }
}