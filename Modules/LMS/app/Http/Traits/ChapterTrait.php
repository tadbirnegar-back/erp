<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\Chapter;
use Modules\LMS\app\Models\Course;

trait ChapterTrait
{
    public function storeChapter($data)
    {
        return Chapter::create([
            "course_id" => $data['courseID'],
            "description" => null,
            "title" => $data['chapterTitle'],
            "read_only" => false,
        ]);
    }

    public function getChapter($data)
    {
        return Chapter::find($data['chapterID']);
    }

    public function isCourseHasNoneChapter($id)
    {
        $course = Course::with(['chapters' => function ($query) {
            $query -> where('title' , 'بدون فصل');
        }])->find($id);
        if(empty($course -> chapters[0]))
        {
           return $this -> storeHasNoneChapter($id);
        }else{
            return $course -> chapters[0];
        }
    }

    public function storeHasNoneChapter($id)
    {
        return Chapter::create([
            'course_id' => $id,
            'title' => 'بدون فصل',
            'read_only' => false,
            'description' => null
        ]);
    }
}
