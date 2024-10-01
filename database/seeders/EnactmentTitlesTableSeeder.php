<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EnactmentTitlesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('enactment_titles')->delete();
        
        \DB::table('enactment_titles')->insert(array (
            0 => 
            array (
                'id' => '2',
                'title' => 'آسفالت خیابان',
            ),
            1 => 
            array (
                'id' => '1',
                'title' => 'تصویب مصوبه',
            ),
        ));
        
        
    }
}