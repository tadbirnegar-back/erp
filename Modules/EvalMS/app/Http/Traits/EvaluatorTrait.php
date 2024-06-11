<?php

namespace Modules\EvalMS\app\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\AAA\app\Models\User;
use Modules\EvalMS\app\Models\EvalParameterAnswer;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\EvalMS\app\Models\Evaluator;
use Modules\OUnitMS\app\Models\OrganizationUnit;

trait EvaluatorTrait
{

    public function answerStore(array|collection $data, int $evaluatorID)
    {
        $dataToInsert = $this->answerDataPreparation($data, $evaluatorID);

        $result = EvalParameterAnswer::insert($dataToInsert);

        return $result;

    }

    public function evaluatorStore(array|collection $data, int $evaluationID, int $userID)
    {
        $dataToInsert = $this->dataPreparation($data, $evaluationID, $userID);
        $result = Evaluator::create($dataToInsert);

        return $result;

    }


    public function getOunits(User $user, bool $loadEvaluations = false, bool $loadHeads = false)
    {
        $ous = $user->organizationUnits;
        $res = [];
        foreach ($ous as $key => $ou) {
            $model = $ou->unitable()->with('organizationUnit')->when($loadEvaluations, function ($query) {
                return $query->with('organizationUnit.evaluations');
            })->when($loadHeads, function ($query) {
                return $query->with('organizationUnit.head.person');
            })->first();
            $res[] = $this->getOunitsWithSubsOfUser($model, loadEvaluations: $loadEvaluations, loadHeads: $loadHeads);
        }
        if (!is_null($res)) {
            $a = collect($res);
            return $a->flatten();
        } else {
            return [];
        }


    }

    public function getOunitsWithSubsOfUser(Model $model, bool $loadEvaluations = false, bool $loadHeads = false)
    {

        $children = [];


        if (!method_exists($model, 'children')) {
            // Reached the bottom level (village)
            return $model;
        }

        $children[] = $model;
        $subUnits = $model->children()->with('organizationUnit')->when($loadEvaluations, function ($query) {
            return $query->with('organizationUnit.evaluations');
        })->when($loadHeads, function ($query) {
            return $query->with('organizationUnit.head.person');
        })->get();


        foreach ($subUnits as $child) {
            $children[] = $this->getOunitsWithSubsOfUser($child, loadEvaluations: $loadEvaluations, loadHeads: $loadHeads);
        }



        return $children;

    }

    public function getOunitsParents(OrganizationUnit $ou)
    {
        $parents = [];


        $model = $ou->unitable()->with(['organizationUnit.head.person', 'organizationUnit.evaluations'])->first();
        while (method_exists($model, 'parent') === true) {
            $parents[] = $model;
            $model = $model->parent()->with(['organizationUnit.head.person', 'organizationUnit.evaluations'])->first();

        }
        $parents[] = $model->load(['organizationUnit.head.person', 'organizationUnit.evaluations']);
        $result = collect($parents);
        return $result;
    }

    public function evalOfOunits(array $ounitIDs, int $EvalID, int $perPage = 10, int $pageNum = 1)
    {
        $eval = Evaluation::find($EvalID);
        $organizationUnits = $eval->organizationUnits()->whereIn('organization_unit_id', $ounitIDs)->paginate($perPage, page: $pageNum);

        return ['eval' => $eval, 'organizationUnits' => $organizationUnits];
    }

    public function getEvalOunitHistory(int $evalID, int $organizationID, array $userIDs, int $pageNum = 0, int $perPage = 0)
    {

        $relations = [
            'parameters' => function ($query) use ($organizationID, $userIDs, $pageNum, $perPage) {
                $query->with([
                    'evalParameterAnswers' => function ($query) use ($userIDs, $organizationID) {
                        $query->whereExists(function ($subQuery) use ($userIDs, $organizationID) {
                            $subQuery->selectRaw('1')
                                ->from('evaluators')
                                ->whereColumn('evaluators.id', 'eval_parameter_answers.evaluator_id')
                                ->whereIn('evaluators.user_id', $userIDs)
                                ->where('evaluators.organization_unit_id', $organizationID);
                        })->with([
                            'evaluator' => function ($query) use ($organizationID) {
                                $query->where('organization_unit_id', $organizationID)
                                    ->with(['organizationUnit']);
                            },
                            'evaluator.person' // Already eager loaded in evaluation (optional)
                        ]);
                    },
                    'evalIndicator', 'evalPart'
                ]);
                if ($perPage > 0) {
                    $query->paginate($perPage, ['*'], 'page', $pageNum);
                }

            }
        ];

        if ($pageNum == 1) {
            $relations['evaluators'] = function ($query) use ($organizationID, $userIDs) {
                $query->whereIn('user_id', $userIDs)
                    ->where('organization_unit_id', $organizationID)
                    ->with(['organizationUnit', 'person']);
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

    private function dataPreparation(array|collection $data, int $evaluationID, int $userID)
    {
        if (($data instanceof Collection)) {
            $data = $data->toArray();
        }

        $evalData = [
            'sum' => $data['sum'] ?? null,
            'average' => $data['average'] ?? null,
            'parent_id' => $data['parentID'] ?? null,
            'evaluation_id' => $evaluationID,
            'user_id' => $userID,
            'organization_unit_id' => $data['organizationUnitID'] ?? null

        ];

        return $evalData;
    }


    private function answerDataPreparation(array|collection $data, int $evaluatorID)
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
