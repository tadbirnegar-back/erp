<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IssueTimesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('issue_times')->delete();
        
        \DB::table('issue_times')->insert(array (
            0 => 
            array (
                'id' => '1',
                'title' => 'دوران همکاری',
            ),
            1 => 
            array (
                'id' => '2',
                'title' => 'شروع به همکاری',
            ),
            2 => 
            array (
                'id' => '3',
                'title' => 'قطع همکاری',
            ),
            3 => 
            array (
                'id' => '4',
                'title' => 'اتمام دوران همکاری',
            ),
        ));
        
        
    }
}