<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ScriptAgentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('script_agents')->delete();
        
        \DB::table('script_agents')->insert(array (
            0 => 
            array (
                'id' => '3',
                'title' => 'حقوق پایه',
                'script_agent_type_id' => '9',
                'status_id' => '58',
            ),
            1 => 
            array (
                'id' => '8',
                'title' => 'حقوق پایه انتصاب',
                'script_agent_type_id' => '9',
                'status_id' => '58',
            ),
        ));
        
        
    }
}