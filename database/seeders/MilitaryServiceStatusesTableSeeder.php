<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MilitaryServiceStatusesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('military_service_statuses')->delete();
        
        \DB::table('military_service_statuses')->insert(array (
            0 => 
            array (
                'id' => '1',
                'name' => 'معاف',
            ),
            1 => 
            array (
                'id' => '2',
                'name' => 'درحال خدمت',
            ),
            2 => 
            array (
                'id' => '3',
                'name' => 'دارای کارت پایان خدمت',
            ),
            3 => 
            array (
                'id' => '4',
                'name' => 'معافیت تحصیلی',
            ),
        ));
        
        
    }
}