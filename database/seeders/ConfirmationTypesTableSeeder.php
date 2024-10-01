<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConfirmationTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('confirmation_types')->delete();
        
        \DB::table('confirmation_types')->insert(array (
            0 => 
            array (
                'id' => '1',
                'title' => 'سلسله مراتبی',
            ),
            1 => 
            array (
                'id' => '2',
                'title' => 'مدیر مستقیم',
            ),
            2 => 
            array (
                'id' => '3',
                'title' => 'مدیر واحد خاص',
            ),
            3 => 
            array (
                'id' => '4',
                'title' => 'شخص خاص',
            ),
        ));
        
        
    }
}