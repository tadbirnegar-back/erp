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
            'answer_sheets.start_date_time as startDate',
            'answer_sheets.finish_date_time as finishDate',
            'repositories.name as repositoryName',
            'repositories.id as repositoryID',
            'exams.id as examID',
            'exams.title as examTitle',
            'question_types.id as questionTypeID',
            'statuses.name as statusName',
            'statuses.id as statusID'
        ]);


        return $query->paginate($perPage, ['*'], 'page', $pageNumber);


    }
}
