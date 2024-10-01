<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class JobsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('jobs')->delete();
        
        \DB::table('jobs')->insert(array (
            0 => 
            array (
                'id' => '1',
                'title' => 'دهیار',
                'description' => 'مدیریت سازمان دهیاری',
                'status_id' => '51',
                'introduction_video_id' => NULL,
            ),
            1 => 
            array (
                'id' => '2',
                'title' => 'دهیار',
                'description' => 'مدیر دهیاری است',
                'status_id' => NULL,
                'introduction_video_id' => NULL,
            ),
            2 => 
            array (
                'id' => '3',
                'title' => 'دهیار',
                'description' => 'مدیر دهیاری است',
                'status_id' => '51',
                'introduction_video_id' => '140',
            ),
            3 => 
            array (
                'id' => '4',
                'title' => 'بخشداری',
                'description' => 'رئیس بخش',
                'status_id' => '51',
                'introduction_video_id' => '139',
            ),
            4 => 
            array (
                'id' => '5',
                'title' => 'فرماندار',
                'description' => 'رئیس فرمانداری',
                'status_id' => '51',
                'introduction_video_id' => NULL,
            ),
            5 => 
            array (
                'id' => '6',
                'title' => 'ارزیاب دهیاری ها',
                'description' => 'ارزیابی دهیاری ها را انجام میدهد',
                'status_id' => '51',
                'introduction_video_id' => '139',
            ),
            6 => 
            array (
                'id' => '7',
                'title' => 'بخشدار',
                'description' => 'رئیس بخشداری',
                'status_id' => '50',
                'introduction_video_id' => NULL,
            ),
            7 => 
            array (
                'id' => '8',
                'title' => 'فرماندار',
                'description' => 'رئیس فرماندرای',
                'status_id' => '51',
                'introduction_video_id' => NULL,
            ),
            8 => 
            array (
                'id' => '9',
                'title' => 'دهیار',
                'description' => 'رئیس دهیاری',
                'status_id' => '51',
                'introduction_video_id' => '139',
            ),
            9 => 
            array (
                'id' => '10',
                'title' => 'دهیار',
                'description' => 'مدیریت دهیاری روستا را بر عهده دارد',
                'status_id' => '50',
                'introduction_video_id' => '139',
            ),
            10 => 
            array (
                'id' => '11',
                'title' => 'فرماندار',
                'description' => 'ریاست فرمانداری',
                'status_id' => '51',
                'introduction_video_id' => NULL,
            ),
            11 => 
            array (
                'id' => '12',
                'title' => 'فرماندار',
                'description' => 'ت',
                'status_id' => '50',
                'introduction_video_id' => NULL,
            ),
        ));
        
        
    }
}