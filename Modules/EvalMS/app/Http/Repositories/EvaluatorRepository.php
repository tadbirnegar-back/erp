<?php

namespace Modules\EvalMS\app\Http\Repositories;

use Illuminate\Support\Collection;
use Modules\AAA\app\Models\User;
use Modules\EvalMS\app\Models\EvalParameterAnswer;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\EvalMS\app\Models\Evaluator;
use Modules\OUnitMS\app\Models\VillageOfc;

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


//    public static function getOunitsWithSubsOfUser(User $user)
//    {
//        $ous = $user->organizationUnits;
//        $children = [];
//
//
//        foreach ($ous as $key => $ou) {
//            $model = $ou->unitable()->with(['organizationUnit.evaluations','organizationUnit.heads'])->first();
//            while (method_exists($model, 'children') === true) {
//                $children[$key][] = $model;
//                $model = $model->children()->with(['organizationUnit.evaluations','organizationUnit.heads'])->first();
//
//            }
//            $children[$key][] = $model->load(['organizationUnit.evaluations','organizationUnit.heads']);
//
//        }
//        $uniqueUnits = collect($children)->flatten(1)->unique();
//
//        return $uniqueUnits;
//    }

    public static function getOunitsWithSubsOfUser(User $user, bool $loadEvaluations = false, bool $loadHeads = false)
    {
        $ous = $user->organizationUnits;
        $children = [];

        foreach ($ous as $key => $ou) {
            $model = $ou->unitable()->when($loadEvaluations, function ($query) {
                return $query->with('organizationUnit.evaluations');
            })->when($loadHeads, function ($query) {
                return $query->with('organizationUnit.head');
            })->first();

            while (method_exists($model, 'children')) {
                $children[$key][] = $model;
                $model = $model->children()->when($loadEvaluations, function ($query) {
                    return $query->with('organizationUnit.evaluations');
                })->when($loadHeads, function ($query) {
                    return $query->with('organizationUnit.head');
                })->first();
            }

            $children[$key][] = $model->load(['organizationUnit' => function ($query) use ($loadEvaluations, $loadHeads) {
                if ($loadEvaluations) {
                    $query->with('evaluations');
                }
                if ($loadHeads) {
                    $query->with('head');
                }
            }]);
        }

        $uniqueUnits = collect($children)->flatten(1)->unique();

        return $uniqueUnits;
    }

    public static function evalOfOunits(array $ounitIDs, int $EvalID)
    {
        $eval = Evaluation::with(['organizationUnits' => function ($query) use ($ounitIDs) {
            $query->whereIn('organization_unit_id', $ounitIDs);
        }])->find($EvalID);

        return $eval;
    }

    public static function getEvalOunitHistory(int $evalID, int $ounitID, array $userIDs, int $pageNum = 1, int $perPage = 10)
    {

        $relations = [
            // Eager load parameters with answers and filter evaluators
            'parameters' => function ($query) use ($ounitID, $userIDs, $pageNum, $perPage) {
                $query->with([
                    'evalParameterAnswers' => function ($query) use ($ounitID, $userIDs) {
                        $query->with([
                            'evaluator' => function ($query) use ($ounitID, $userIDs) {
                                $query->whereIn('user_id', $userIDs)
                                    ->where('organization_unit_id', $ounitID);
                            },
                            'evaluator.person' // Already eager loaded in evaluation
                        ]);
                    }
                ])->paginate($perPage, ['*'], 'page', $pageNum);
            }
        ];

        if ($pageNum == 1) {
            $relations['evaluators'] = function ($query) use ($ounitID, $userIDs) {
                $query->whereIn('user_id', $userIDs)
                    ->where('organization_unit_id', $ounitID)
                    ->with('organizationUnit.person');
            };
        }

        $evaluation = Evaluation::with($relations)
            ->find($evalID);
        $totalParameters = $evaluation->parameters()->count();
        $totalPages = ceil($totalParameters / 10);
        return [
            'data' => $evaluation,
            'currentPage' => $pageNum,
            'lastPage' => $totalPages
        ];
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
            'organization_unit_id' => $organizationUnitID ?? null

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
