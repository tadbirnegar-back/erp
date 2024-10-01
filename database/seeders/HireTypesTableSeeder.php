<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HireTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('hire_types')->delete();
        
        \DB::table('hire_types')->insert(array (
            0 => 
            array (
                'id' => '1',
                'title' => 'مرامی',
                'work_hour' => '188.00',
                'contract_type_id' => '3',
                'status_id' => NULL,
            ),
            1 => 
            array (
                'id' => '2',
                'title' => 'مرامی',
                'work_hour' => '188.00',
                'contract_type_id' => '3',
                'status_id' => '55',
            ),
            2 => 
            array (
                'id' => '3',
                'title' => 'فامیلی',
                'work_hour' => '230.00',
                'contract_type_id' => '1',
                'status_id' => '55',
            ),
            3 => 
            array (
                'id' => '4',
                'title' => 'ساعتی 1',
                'work_hour' => '1.50',
                'contract_type_id' => '2',
                'status_id' => '55',
            ),
            4 => 
            array (
                'id' => '5',
                'title' => 'ساعتی 2',
                'work_hour' => '2.00',
                'contract_type_id' => '2',
                'status_id' => '55',
            ),
            5 => 
            array (
                'id' => '6',
                'title' => 'ساعتی 3',
                'work_hour' => '3.00',
                'contract_type_id' => '2',
                'status_id' => '55',
            ),
            6 => 
            array (
                'id' => '7',
                'title' => 'ساعتی',
                'work_hour' => '1.00',
                'contract_type_id' => '2',
                'status_id' => '55',
            ),
            7 => 
            array (
                'id' => '8',
                'title' => 'ساعتی',
                'work_hour' => '1.00',
                'contract_type_id' => '2',
                'status_id' => '55',
            ),
            8 => 
            array (
                'id' => '9',
                'title' => 'تمام وقت',
                'work_hour' => '8.00',
                'contract_type_id' => '3',
                'status_id' => '54',
            ),
            9 => 
            array (
                'id' => '10',
                'title' => 'پاره وقت',
                'work_hour' => '5.50',
                'contract_type_id' => '3',
                'status_id' => '54',
            ),
        ));
        
        
    }
}