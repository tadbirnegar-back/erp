<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Student;

trait ExamsTrait
{

    public function examsIndex(array $data = [], Student $auth)
    {

        $query = AnswerSheet::joinRelationship('repository')
            ->joinRelationship('questionType', 'question_type_alias')
            ->joinRelationship('exam', 'exam_alias')
            ->joinRelationship('status');

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
        ])->where('answer_sheets.student_id', $auth->id);


        return $query->get();
//            ->paginate($perPage, ['*'], 'page', $pageNumber);


    }
}
