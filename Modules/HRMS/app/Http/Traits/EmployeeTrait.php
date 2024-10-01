<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Http\Enums\FormulaEnum;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\HireType;
use Modules\HRMS\app\Models\IssueTime;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptType;
use Modules\HRMS\app\Models\WorkForce;
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
        $employeeQuery = Employee::with('person.avatar', 'status', 'positions')->distinct();

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
            $employee->possitions()->sync($positionsAsArray);
        }

        if (isset($data['levels'])) {
            $levelsAsArray = json_decode($data['levels'], true);
            $employee->levels()->sync($levelsAsArray);
        }
        if (isset($data['skills'])) {

            $skills = json_decode($data['skills'], true);

            $workForce->skills()->sync($skills);
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
        $workForce = $employee->workForce;
        $workForce->person_id = $data['personID'];
        $workForce->isMarried = isset($data['isMarried']) ? 1 : 0;
        $workForce->military_service_status_id = $data['militaryStatusID'] ?? null;

        $employee->workForce()->save($workForce);

        $workForceStatus = $this->activeEmployeeStatus();

        $workForce->statuses()->attach($workForceStatus->id);


        if (isset($data['positions'])) {
            $positionsAsArray = json_decode($data['positions'], true);
            $employee->possitions()->sync($positionsAsArray);
        }

        if (isset($data['levels'])) {
            $levelsAsArray = json_decode($data['levels'], true);
            $employee->levels()->sync($levelsAsArray);
        }
        if (isset($data['skills'])) {

            $skills = json_decode($data['skills'], true);

            $workForce->skills()->sync($skills);
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
                })->with('issueTime');
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

    public function getScriptAgentCombos(HireType $hireType, ScriptType $scriptType)
    {
        $hireTypeId = $hireType->id;
        $scriptTypeId = $scriptType->id;

        $hireType->load(['scriptAgents' => function ($query) use ($scriptTypeId) {
            $query->where('script_type_id', $scriptTypeId);

        }]);
        $scriptAgents = $hireType->scriptAgents;

        $scriptAgents->each(function ($scriptAgent) {
            if (!is_null($scriptAgent->pivot->formula)) {
                $scriptAgent->pivot->default_value = FormulaEnum::from($scriptAgent->pivot->formula)->getPrice();
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
        $parentScript->status()->attach($status->id);

    }
}
