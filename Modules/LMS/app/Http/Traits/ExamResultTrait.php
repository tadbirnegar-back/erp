<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\AnswerSheet;

trait ExamResultTrait
{
    public function result()
    {
        $query = AnswerSheet::joinRelationship('answers')
            ->joinRelationship('questionExam')
            ->joinRelationship('questionExam.question')
            ->joinRelationship('questionExam.question.options');
        return $query;


    }
}
