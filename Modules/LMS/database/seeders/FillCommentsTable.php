<?php

namespace Modules\LMS\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\LMS\app\Models\Comment;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Lesson;

class FillCommentsTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comment::create([
            'text' => "سلام از دوره بسیار راضی بودم عالی بود" ,
            'creator_id' => 2174 ,
            'commentable_type' => Lesson::class,
            'commentable_id' => 14,
            'create_date' => now()
        ]);


        Comment::create([
            'text' => "خیلی بد بود" ,
            'creator_id' => 2174 ,
            'commentable_type' => Lesson::class,
            'commentable_id' => 14,
            'create_date' => now()
        ]);


        Comment::create([
            'text' => "سلام از دوره بسیار راضی بودم عالی بود" ,
            'creator_id' => 2295 ,
            'commentable_type' => Lesson::class,
            'commentable_id' => 14,
            'create_date' => now()
        ]);

        Comment::create([
            'text' => "سلام از دوره بسیار راضی بودم عالی بود" ,
            'creator_id' => 2174 ,
            'commentable_type' => Course::class,
            'commentable_id' => 14,
            'create_date' => now()
        ]);

    }
}
