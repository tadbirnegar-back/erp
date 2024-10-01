<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReligionTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('religion_types')->delete();
        
        \DB::table('religion_types')->insert(array (
            0 => 
            array (
                'id' => '1',
                'title' => 'شیعه',
            ),
            1 => 
            array (
                'id' => '2',
                'title' => 'سنی',
            ),
        ));
        
        
    }
}