<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Http\Enums\FormulaEnum;
use Modules\HRMS\app\Http\Enums\HireTypeEnum;
use Modules\HRMS\app\Http\Enums\ScriptTypeOriginEnum;
use Modules\HRMS\app\Http\Enums\ScriptTypesEnum;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\HireType;
use Modules\HRMS\app\Models\IssueTime;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptType;
use Modules\HRMS\app\Models\WorkForce;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Http\Enums\PersonStatusEnum;
use Modules\PersonMS\app\Models\Person;

trait EmployeeTrait
{
    use RecruitmentScriptTrait, ScriptTypeTrait;

    private static string $activeEmployeeStatus = 'فعال';
    private static string $inActiveEmployeeStatus = 'غیرفعال';
    private static string $pendingEmployeeStatus = 'در انتظار تایید';
    private array $compatibleIssueTimes = [
        null => ['شروع به همکاری'],
        'قطع همکاری' => ['شروع به همکاری'],
        'اتمام دوران همکاری' => ['شروع به همکاری'],
        'شروع به همکاری' => ['دوران همکاری', 'اتمام دوران همکاری', 'قطع همکاری'],
        'دوران همکاری' => ['دوران همکاری', 'اتمام دوران همکاری', 'قطع همکاری'],

    ];
    private array $compatibleIssueTimesForNewScript = [
        'قطع همکاری' => ['شروع به همکاری'],
        'اتمام دوران همکاری' => ['شروع به همکاری'],
        'شروع به همکاری' => ['دوران همکاری', 'اتمام دوران همکاری', 'قطع همکاری'],
        'دوران همکاری' => ['شروع به همکاری'],
    ];
    private array $parentScriptStatusChangeByIssueTime = [
        'شروع به همکاری' => null,
        'دوران همکاری' => null,
        'اتمام دوران همکاری' => 'غیرفعال',
        'قطع همکاری' => 'غیرفعال',
    ];

    public function employeeIndex(int $perPage = 10, int $pageNumber = 1, array $data = [])
    {
        $employeeQuery = Employee::with(['person.avatar', 'status', 'positions'])->distinct();

        $searchTerm = $data['name'] ?? null;
        $position = $data['positionID'] ?? null;
        $scriptType = $data['scriptTypeID'] ?? null;
        $status = $data['statusID'] ?? null;

        $employeeQuery->when($searchTerm, function ($query, $searchTerm) {
            $query->whereHas('person', function ($query) use ($searchTerm) {
                $query->whereRaw('MATCH(display_name) AGAINST(?)', [$searchTerm])
                    ->orWhere('display_name', 'LIKE', '%' . $searchTerm . '%')
                    ->selectRaw('persons.*, MATCH(display_name) AGAINST(?) AS relevance', [$searchTerm])
                    ->orderByDesc('relevance');
            })
                ->with(['person' => function ($query) use ($searchTerm) {
                    $query->selectRaw('persons.*, MATCH(display_name) AGAINST(?) AS relevance', [$searchTerm]);
                }, 'person.user', 'person.avatar']);
        });
        $employeeQuery->when($position, function ($query, $position) {
            $query->whereHas('latestRecruitmentScript', function ($query) use ($position) {
                $query->where('position_id', '=', $position);
            });
        });

        $employeeQuery->when($scriptType, function ($query, $scriptType) {
            $query->whereHas('latestRecruitmentScript', function ($query) use ($scriptType) {
                $query->where('script_type_id', '=', $scriptType);
            });
        });
        $employeeQuery->when($status, function ($query, $status) {
            $query->whereHas('status', function ($query) use ($status) {
                $query->where('statuses.id', '=', $status);
            });
        });
        $employeeQuery->orderBy('id', 'desc');
        $result = $employeeQuery->paginate($perPage, page: $pageNumber);

        return $result;
    }

    public function employeeListWithFilter(array $data)
    {
        $searchTerm = $data['name'] ?? null;
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;
        $ounitID = $data['ounitID'] ?? null;
        $positionID = $data['positionID'] ?? null;
        $personStatuses= $data['personStatus'] ?? null;

        $startDate = isset($data['startDate']) ? convertJalaliPersianCharactersToGregorian(($data['startDate'])) : null;
        $endDate = isset($data['endDate']) ? convertJalaliPersianCharactersToGregorian($data['endDate']) : null;

        if ($ounitID) {
            $ounit = OrganizationUnit::with(['descendantsAndSelf'])->find($ounitID);
            $ounitIDs = $ounit->descendantsAndSelf->pluck('id')->toArray();
        } else {
            $ounitIDs = null;
        }

        $pList = Employee::joinRelationship('workForce.person.natural', [
            'person' => function ($join) use ($searchTerm,$personStatuses) {
                $join->finalPersonStatus()
                    ->when($personStatuses, function ($query) use ($personStatuses) {
                        $query->whereIn('statuses.name', $personStatuses);
                    })
                    ->when($searchTerm, function ($query) use ($searchTerm) {
                        $query->searchDisplayName($searchTerm);
                    });
            }
        ])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('person_status.create_date', [$startDate, $endDate]);
            })
            ->when($positionID || $ounitIDs, function ($query) use ($ounitIDs, $positionID) {
                $query
                    ->joinRelationship('recruitmentScripts')
                    ->when($ounitIDs, function ($query) use ($ounitIDs) {
                        $query
                            ->whereIntegerInRaw('recruitment_scripts.organization_unit_id', $ounitIDs);
                    })
                    ->when($positionID, function ($query) use ($ounitIDs, $positionID) {
                        $query
                            ->where('recruitment_scripts.position_id', $positionID);
                    });
            })
            ->addSelect([
                'naturals.mobile',
                'naturals.gender_id',
                'naturals.isMarried',
                'persons.display_name',
                'persons.national_code',
                'persons.id as p_id',
                'person_status.create_date as last_updated',
            ])
            ->with(['recruitmentScripts' => function ($query) use ($ounitIDs) {
                $query
                    ->finalStatus()
                    ->join('positions', 'recruitment_scripts.position_id', '=', 'positions.id')
                    ->join('script_types', 'recruitment_scripts.script_type_id', '=', 'script_types.id')
                    ->select([
                        'recruitment_scripts.*',
                        'positions.name as position_name',
                        'script_types.title as script_type_title',
                        'statuses.name as status_name',
                        'statuses.class_name as status_class_name',
                    ])
                    ->with(['organizationUnit' => function ($query) {
                        $query->leftJoin('village_ofcs', function ($join) {
                            $join->on('village_ofcs.id', '=', 'organization_units.unitable_id')
                                ->where('unitable_type', '=', VillageOfc::class);
                        })
                            ->select([
                                'village_ofcs.abadi_code as abadi_code',
                                'organization_units.*'
                            ])
                            ->with(['ancestors' => function ($query) {
                                $query->where('unitable_type', '!=', StateOfc::class);
                            }]);
                    },]);
            }])
            ->distinct('employees.id')
            ->paginate($perPage, page: $pageNum);

        return $pList;
    }

    public function getEmployeesByPersonName(string $searchTerm)
    {
        return WorkForce::where('workforceable_type', Employee::class)
            ->whereHas('person', function ($query) use ($searchTerm) {
                $query->whereRaw('MATCH(display_name) AGAINST(?)', [$searchTerm])
                    ->orWhere('display_name', 'LIKE', '%' . $searchTerm . '%')
                    ->selectRaw('persons.*, MATCH(display_name) AGAINST(?) AS relevance', [$searchTerm])
                    ->orderByDesc('relevance');
            })
            ->with(['person' => function ($query) use ($searchTerm) {
                $query->selectRaw('persons.*, MATCH(display_name) AGAINST(?) AS relevance', [$searchTerm]);
            }, 'person.user'])
            ->get();


    }


    public function employeeStore(array $data)
    {


        $employee = new Employee();
        $employee->personnel_code = $data['personnelCode'] ?? null;
        $employee->signature_file_id = $data['signatureFileID'] ?? null;
        $employee->save();

        $workForce = new WorkForce();
        $workForce->person_id = $data['personID'];
        $workForce->isMarried = isset($data['isMarried']) && $data['isMarried'] === true ? 1 : 0;
        $workForce->military_service_status_id = $data['militaryStatusID'] ?? null;

        $employee->workForce()->save($workForce);

        $workForceStatus = $this->activeEmployeeStatus();

        $workForce->statuses()->attach($workForceStatus->id);

        if (isset($data['positions'])) {
            $positionsAsArray = json_decode($data['positions'], true);
            $employee->positions()->sync($positionsAsArray);
        }

        if (isset($data['levels'])) {
            $levelsAsArray = json_decode($data['levels'], true);
            $employee->levels()->sync($levelsAsArray);
        }
        if (isset($data['skills'])) {

            $skills = json_decode($data['skills'], true);

            $workForce->stdSkills()->sync($skills);
        }

        $employee->load('workForce');
        return $employee;

    }

    public function activeEmployeeStatus()
    {
        return Employee::GetAllStatuses()
            ->firstWhere('name', '=', self::$activeEmployeeStatus);
    }

    public function employeeUpdate(array $data, Employee $employee)
    {


        $employee->personnel_code = $data['personnelCode'] ?? null;
        $employee->signature_file_id = $data['signatureFileID'] ?? null;
        $employee->save();

        $workForce = $employee->workForce;
        $workForce->person_id = $data['personID'];
        $workForce->isMarried = isset($data['isMarried']) ? 1 : 0;
        $workForce->military_service_status_id = $data['militaryStatusID'] ?? null;

        $employee->workForce()->save($workForce);

        $workForceStatus = $this->activeEmployeeStatus();

        $workForce->statuses()->attach($workForceStatus->id);


        if (isset($data['positions'])) {
            $positionsAsArray = json_decode($data['positions'], true);
            $employee->positions()->sync($positionsAsArray);
        }

        if (isset($data['levels'])) {
            $levelsAsArray = json_decode($data['levels'], true);
            $employee->levels()->sync($levelsAsArray);
        }
        if (isset($data['skills'])) {

            $skills = json_decode($data['skills'], true);

            $workForce->stdSkills()->sync($skills);
        }

        return $employee;

    }

    public function employeeShow(int $id)
    {
        return Employee::with('workForce')->findOrFail($id);
    }

    public function isEmployee(int $personID)
    {

        $employee = Employee::whereHas('workforce', function ($query) use ($personID) {
            $query->where('person_id', '=', $personID);
        })->with('workforce')->first();
        return $employee;
    }

    public function hasEmployee(Person $person)
    {
        return $person->employee;
    }

    public function addEmployeeScriptTypes()
    {
        $result = IssueTime::firstWhere('title', 'شروع به همکاری')->with('scriptTypes');

        return $result->scriptTypes;
    }

    public function loadLatestActiveScript(Employee $employee)
    {
        return $employee->load(['latestRecruitmentScript' => function ($query) {
            $query->where('expire_date', '>', now())
                ->whereDoesntHave('latestStatus', function ($query) {
                    $query->where('name', '=', self::$inActiveRsStatus);
                });
        }]);
    }

    public function getCompatibleIssueTimesByName(string $name = null)
    {
        $compatibleIssueTimes = $this->compatibleIssueTimes[$name];
        $issueTimes = IssueTime::whereIn('title', $compatibleIssueTimes)
            ->with(['scriptTypes' => function ($query) {
                $query->whereHas('status', function ($query) {
                    $query->where('name', '=', $this->activeScriptTypeStatus);
                });
            }])
            ->get();
        return $issueTimes->pluck('scriptTypes')->flatten();
    }

    public function getCompatibleIssueTimesForNewScript(string $name, int $employeeID)
    {
        $compatibleIssueTimes = $this->compatibleIssueTimesForNewScript[$name];

        $issueTimes = IssueTime::whereIn('title', $compatibleIssueTimes)
            ->with(['recruitmentScripts' => function ($query) use ($employeeID) {

                $query->where('employee_id', $employeeID)
                    ->whereHas('latestStatus', function ($query) {

//                    $query->where(function ($query) {
                        $query->where('name', '=', self::$activeEmployeeStatus);
//                            ->orWhere('name', '=', self::$inActiveRsStatus);
//                    });
                    })->with(['scriptType', 'hireType', 'organizationUnit']);
            }])
            ->get();

        return $issueTimes
            ->pluck('recruitmentScripts')
            ->flatten();
    }

    public function getCompatibleParentScriptsBySubOrigin(int $employeeID)
    {
        $recruitmentScripts = RecruitmentScript::where('employee_id', $employeeID)->whereHas('scriptType', function ($q) {
            $q->where('origin_id', ScriptTypeOriginEnum::Sub->value);
        });

        return $recruitmentScripts;
    }

    public function getScriptAgentCombos(HireType $hireType, ScriptType $scriptType, ?OrganizationUnit $ounit = null)
    {
        $hireTypeId = $hireType->id;
        $scriptTypeId = $scriptType->id;
        $hireType->load(['scriptAgents' => function ($query) use ($scriptTypeId) {
            $query->where('script_type_id', $scriptTypeId);

        }]);

        $a = ScriptTypesEnum::tryFrom($scriptType->title);
        $b = HireTypeEnum::tryFrom($hireType->title);
        $scriptAgents = $hireType->scriptAgents;
        $class = 'Modules\HRMS\app\Calculations\\' . $a->getCalculateClassPrefix() . 'ScriptType' . $b->getCalculateClassPrefix() . 'HireTypeCalculator';
        $calculator = new $class($scriptType, $hireType, $ounit, \Auth::user()->person);

        $scriptAgents->each(function ($scriptAgent) use ($calculator) {
            if (!is_null($scriptAgent->pivot->formula)) {

                $formula = FormulaEnum::from($scriptAgent->pivot->formula);
                $fn = $formula->getFnName();

                $scriptAgent->pivot->default_value = $calculator->$fn();
            }
        });

        return $scriptAgents;
    }

    public function changeParentRecruitmentScriptStatus(Employee $employee, int $parentID, IssueTime $issueTime)
    {
        $parentScript = $employee->recruitmentScripts()->where('id', $parentID)->with('issueTime')->first();

        $statusName = $this->parentScriptStatusChangeByIssueTime[$issueTime->title];
        if (is_null($statusName)) {
            return;
        }
        $status = RecruitmentScript::GetAllStatuses()->firstWhere('name', '=', $statusName);
        $this->attachStatusToRs($parentScript, $status);


    }
}
