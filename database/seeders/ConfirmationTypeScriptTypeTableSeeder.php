<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConfirmationTypeScriptTypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('confirmation_type_script_type')->delete();
        
        \DB::table('confirmation_type_script_type')->insert(array (
            0 => 
            array (
                'id' => '1',
                'confirmation_type_id' => '1',
                'script_type_id' => '2',
                'option_id' => NULL,
                'priority' => '1',
                'option_type' => 'Modules\\HRMS\\app\\Http\\Services\\HierarchyService',
            ),
            1 => 
            array (
                'id' => '4',
                'confirmation_type_id' => '1',
                'script_type_id' => '8',
                'option_id' => NULL,
                'priority' => '2',
                'option_type' => 'Modules\\HRMS\\app\\Http\\Services\\HierarchyService',
            ),
            2 => 
            array (
                'id' => '5',
                'confirmation_type_id' => '3',
                'script_type_id' => '9',
                'option_id' => '2547',
                'priority' => '1',
                'option_type' => 'Modules\\HRMS\\app\\Http\\Services\\HeadUnitService',
            ),
            3 => 
            array (
                'id' => '6',
                'confirmation_type_id' => '2',
                'script_type_id' => '9',
                'option_id' => NULL,
                'priority' => '2',
                'option_type' => 'Modules\\HRMS\\app\\Http\\Services\\ManagerService',
            ),
            4 => 
            array (
                'id' => '25',
                'confirmation_type_id' => '2',
                'script_type_id' => '2',
                'option_id' => NULL,
                'priority' => '2',
                'option_type' => 'Modules\\HRMS\\app\\Http\\Services\\ManagerService',
            ),
            5 => 
            array (
                'id' => '27',
                'confirmation_type_id' => '4',
                'script_type_id' => '2',
                'option_id' => '1905',
                'priority' => '3',
                'option_type' => 'Modules\\HRMS\\app\\Http\\Services\\SpecificEmployeeService',
            ),
            6 => 
            array (
                'id' => '29',
                'confirmation_type_id' => '3',
                'script_type_id' => '2',
                'option_id' => '2727',
                'priority' => '4',
                'option_type' => 'Modules\\HRMS\\app\\Http\\Services\\HeadUnitService',
            ),
            7 => 
            array (
                'id' => '33',
                'confirmation_type_id' => '1',
                'script_type_id' => '16',
                'option_id' => NULL,
                'priority' => '1',
                'option_type' => 'Modules\\HRMS\\app\\Http\\Services\\HierarchyService',
            ),
        ));
        
        
    }
}