<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ContractTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('contract_types')->delete();
        
        \DB::table('contract_types')->insert(array (
            0 => 
            array (
                'id' => '1',
                'title' => 'پیمانی',
            ),
            1 => 
            array (
                'id' => '2',
                'title' => 'روزمزد',
            ),
            2 => 
            array (
                'id' => '3',
                'title' => 'رسمی',
            ),
            3 => 
            array (
                'id' => '4',
                'title' => 'پاره وقت',
            ),
        ));
        
        
    }
}