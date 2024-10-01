<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ScriptAgentTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('script_agent_types')->delete();
        
        \DB::table('script_agent_types')->insert(array (
            0 => 
            array (
                'id' => '9',
                'title' => 'مزایا',
                'status_id' => '48',
            ),
            1 => 
            array (
                'id' => '10',
                'title' => 'کسورات',
                'status_id' => '48',
            ),
        ));
        
        
    }
}