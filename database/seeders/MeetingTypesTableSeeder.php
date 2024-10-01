<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MeetingTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('meeting_types')->delete();
        
        \DB::table('meeting_types')->insert(array (
            0 => 
            array (
                'id' => '1',
                'title' => 'جلسه شورا روستا',
                'status_id' => '75',
            ),
            1 => 
            array (
                'id' => '2',
                'title' => 'جلسه هیئت تطبیق',
                'status_id' => '75',
            ),
            2 => 
            array (
                'id' => '3',
                'title' => 'الگو',
                'status_id' => '75',
            ),
        ));
        
        
    }
}