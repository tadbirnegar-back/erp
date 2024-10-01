<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LevelPositionTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        \DB::table('level_position')->delete();

        \DB::table('level_position')->insert(array(
            0 =>
                array(
                    'level_id' => '1',
                    'position_id' => '7',
                ),
            1 =>
                array(
                    'level_id' => '2',
                    'position_id' => '7',
                ),
            2 =>
                array(
                    'level_id' => '2',
                    'position_id' => '8',
                ),
            3 =>
                array(
                    'level_id' => '1',
                    'position_id' => '8',
                ),
            4 =>
                array(
                    'level_id' => '4',
                    'position_id' => '9',
                ),
            5 =>
                array(
                    'level_id' => '1',
                    'position_id' => '1',
                ),
            6 =>
                array(
                    'level_id' => '2',
                    'position_id' => '1',
                ),
            7 =>
                array(
                    'level_id' => '2',
                    'position_id' => '2',
                ),
            8 =>
                array(
                    'level_id' => '7',
                    'position_id' => '2',
                ),
            9 =>
                array(
                    'level_id' => '1',
                    'position_id' => '3',
                ),
            10 =>
                array(
                    'level_id' => '1',
                    'position_id' => '4',
                ),
            11 =>
                array(
                    'level_id' => '7',
                    'position_id' => '5',
                ),
        ));


    }
}
