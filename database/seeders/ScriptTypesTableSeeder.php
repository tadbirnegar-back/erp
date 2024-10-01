<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ScriptTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('script_types')->delete();
        
        \DB::table('script_types')->insert(array (
            0 => 
            array (
                'id' => '1',
                'title' => 'حکم پایان خدمت',
                'issue_time_id' => '4',
                'employee_status_id' => '33',
                'status_id' => '56',
                'isHeadable' => '0',
            ),
            1 => 
            array (
                'id' => '2',
                'title' => 'حکم استخدام',
                'issue_time_id' => '2',
                'employee_status_id' => '33',
                'status_id' => '56',
                'isHeadable' => '0',
            ),
            2 => 
            array (
                'id' => '3',
                'title' => 'مرخصی زایمان',
                'issue_time_id' => '1',
                'employee_status_id' => '34',
                'status_id' => '56',
                'isHeadable' => '0',
            ),
            3 => 
            array (
                'id' => '8',
                'title' => 'برکناری',
                'issue_time_id' => '3',
                'employee_status_id' => '34',
                'status_id' => '57',
                'isHeadable' => '0',
            ),
            4 => 
            array (
                'id' => '9',
                'title' => 'ماموریت',
                'issue_time_id' => '1',
                'employee_status_id' => '33',
                'status_id' => '57',
                'isHeadable' => '0',
            ),
            5 => 
            array (
                'id' => '16',
                'title' => 'انتصاب دهیار',
                'issue_time_id' => '2',
                'employee_status_id' => '65',
                'status_id' => '56',
                'isHeadable' => '1',
            ),
        ));
        
        
    }
}