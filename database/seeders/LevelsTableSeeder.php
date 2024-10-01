<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LevelsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('levels')->delete();
        
        \DB::table('levels')->insert(array (
            0 => 
            array (
                'id' => '1',
                'name' => 'پایه',
                'status_id' => '27',
            ),
            1 => 
            array (
                'id' => '2',
                'name' => 'سنیور',
                'status_id' => '27',
            ),
            2 => 
            array (
                'id' => '3',
                'name' => 'جونیور',
                'status_id' => '28',
            ),
            3 => 
            array (
                'id' => '4',
                'name' => 'جونیور',
                'status_id' => '28',
            ),
            4 => 
            array (
                'id' => '5',
                'name' => 'معاونت',
                'status_id' => '28',
            ),
            5 => 
            array (
                'id' => '6',
                'name' => 'مدیریت',
                'status_id' => '28',
            ),
            6 => 
            array (
                'id' => '7',
                'name' => 'جونیور',
                'status_id' => '27',
            ),
        ));
        
        
    }
}