<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\Chapter;

trait ChapterTrait
{
    public function storeChapter($data)
    {
        return Chapter::create([
            "course_id" => $data['courseID'],
            "description" => null,
            "title" => $data['chapterTitle'],
            "read_only" => $data['readOnly'],
        ]);
    }

    public function getChapter($data)
    {
        return Chapter::find($data['chapterID']);
    }
}
