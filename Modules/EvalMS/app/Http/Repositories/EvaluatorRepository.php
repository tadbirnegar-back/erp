<?php

namespace Modules\EvalMS\app\Http\Repositories;

use Illuminate\Support\Collection;
use Modules\EvalMS\app\Models\EvalParameterAnswer;
use Modules\EvalMS\app\Models\Evaluator;

class EvaluatorRepository
{
    public static function answerStore(array|collection $data, int $evaluatorID)
    {
        $dataToInsert = self::answerDataPreparation($data, $evaluatorID);

        $result = EvalParameterAnswer::insert($dataToInsert);

        return $result;

    }

    public static function evaluatorStore(array|collection $data, int $evaluationID, int $userID)
    {
        $dataToInsert = self::dataPreparation($data, $evaluationID, $userID);
//        return $dataToInsert;
        $result = Evaluator::create($dataToInsert);

        return $result;

    }


    private static function dataPreparation(array|collection $data, int $evaluationID, int $userID)
    {
        if (($data instanceof Collection)) {
            $data = $data->toArray();
        }

//        $answers = $data->map(function ($answer) use ($evaluationID, $userID) {
//            return [
//                'sum' => $answer['sum'] ?? null,
//                'average' => $answer['average'] ?? null,
//                'parent_id' => $answer['parentID'] ?? null,
//                'evaluation_id' => $evaluationID,
//                'user_id' => $userID,
//            ];
//
//        });

        $evalData = [
            'sum' => $data['sum'] ?? null,
            'average' => $data['average'] ?? null,
            'parent_id' => $data['parentID'] ?? null,
            'evaluation_id' => $evaluationID,
            'user_id' => $userID,

        ];

        return $evalData;
    }


    private static function answerDataPreparation(array|collection $data, int $evaluatorID)
    {
        if (!($data instanceof Collection)) {
            $data = collect($data);
        }

        $answers = $data->map(function ($answer) use ($evaluatorID) {
            return [
                'value' => $answer['value'],
                'eval_parameter_id' => $answer['evalParameterID'],
                'evaluator_id' => $evaluatorID,
            ];

        });

        return $answers->toArray();
    }
}
