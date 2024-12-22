<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\AnswerSheet;

trait ExamsTrait
{

    public function examsIndex(int $perPage = 10, int $pageNumber = 1, array $data = [])
    {
        $query = AnswerSheet::joinRelationship('repository')
            ->joinRelationship('questionType', 'question_type_alias')
            ->joinRelationship('exam', 'exam_alias')
            ->joinRelationship('status',);

        $query->addSelect([
            'repositories.name as repository_name',
            'repositories.id as repository_id',
            'exams.id as exam_id',
            'exams.title as exam_title',
            'question_types.id as question_type_id',
            'statuses.name as status_name',
            'statuses.id as status_id'
        ]);

        return $query->paginate($perPage, ['*'], 'page', $pageNumber);


    }
}
