<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\AnswerSheet;

trait ExamResultTrait
{
    public function result(array $data = [],)
    {
        $query = AnswerSheet::joinRelationship('answer')
            ->joinRelationship('questionExam')
            ->joinRelationship('question');


    }
}
