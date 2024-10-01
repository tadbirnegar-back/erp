<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReligionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('religions')->delete();
        
        \DB::table('religions')->insert(array (
            0 => 
            array (
                'id' => '1',
                'title' => 'اسلام',
            ),
            1 => 
            array (
                'id' => '2',
                'title' => 'مسیحی',
            ),
        ));
        
        
    }
}