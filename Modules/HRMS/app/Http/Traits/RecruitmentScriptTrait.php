<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\HRMS\app\Models\FileScript;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\StatusMS\app\Models\Status;

trait RecruitmentScriptTrait
{
    use ScriptAgentScriptTrait;

    private static string $activeRsStatus = 'فعال';
    private static string $inActiveRsStatus = 'غیرفعال';
    private static string $pendingRsStatus = 'در انتظار تایید';


    public function rsIndex(array $data)
    {
        $statusID = $data['statusID'] ?? null;
        $scriptTypeID = $data['scriptTypeID'] ?? null;
        $perPage = $data['perPage'] ?? 10;
        $page = $data['pageNum'] ?? 1;
        $searchTerm = $data['name'] ?? null;

        $query = RecruitmentScript::when($statusID, function ($q) use ($statusID) {
            $q->whereHas('latestStatus', function ($query) use ($statusID) {
                $query->join('recruitment_script_status as rss', 'recruitment_scripts.id', '=', 'rss.recruitment_script_id')
                    ->join('statuses as s', 'rss.status_id', '=', 's.id')
                    ->where('s.id', $statusID)
                    ->where('rss.create_date', function ($subQuery) {
                        $subQuery->selectRaw('MAX(create_date)')
                            ->from('recruitment_script_status as sub_rss')
                            ->whereColumn('sub_rss.recruitment_script_id', 'rss.recruitment_script_id');
                    });
            });
        });

        $query->when($searchTerm, function ($query) use ($searchTerm) {

            $query->whereHas('employee.person', function ($query) use ($searchTerm) {

                $query->whereRaw('MATCH(display_name) AGAINST(?)', [$searchTerm])
                    ->orWhere('display_name', 'LIKE', '%' . $searchTerm . '%')
                    ->selectRaw('persons.*, MATCH(display_name) AGAINST(?) AS relevance', [$searchTerm])
                    ->orderByDesc('relevance');
            });
        });

        $query->when($scriptTypeID, function ($query) use ($scriptTypeID) {
            $query->where('script_type_id', $scriptTypeID);
        })
            ->with([
                'pendingScriptApproving.assignedTo',
                'latestStatus',
                'hireType',
                'scriptType',
                'employee.person',
                'organizationUnit',
            ])->orderBy('create_date', 'desc')
            ->distinct();

        $result = $query->paginate($perPage, page: $page);

        return $result;
    }

    public function rsStore(array|collection $data, int $employeeID, ?Status $status = null)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $dataToInsert = $this->rsDataPreparation($data, $employeeID);
        if (is_null($status)) {
            $status = $this->activeRsStatus();

        }

        $result = [];
        foreach ($dataToInsert as $key => $item) {

            $rs = RecruitmentScript::create($item);
            $rs->status()->attach($status->id);
            $fileScriptsData = !empty($data[$key]['files']) ? json_decode($data[$key]['files'], true) : [];
            if (isset($data[$key]['files']) && is_array($fileScriptsData)) {
                $fileScriptsData = collect($fileScriptsData)->map(fn($fs) => [
                    'file_id' => $fs['fileID'],
                    'script_id' => $rs->id,
                    'title' => $fs['title'],
                ]);

                FileScript::insert($fileScriptsData->toArray());
            }
            if (isset($data[$key]['scriptAgents'])) {
                $agents = json_decode($data[$key]['scriptAgents'], true);
                $scriptAgentsScripts = $this->sasStore($agents, $rs);

            }
            $rs->load('scriptType.confirmationTypes');


            $result[] = $rs;
        }
        return $result;


    }

    private function rsDataPreparation(array|collection $data, int $employeeID)
    {
        if (is_array($data)) {
            $data = collect($data);
        }

        $data = $data->map(fn($RS) => [
            'id' => $RS['rsID'] ?? null,
            'employee_id' => $employeeID,
            'organization_unit_id' => $RS['ounitID'],
            'level_id' => $RS['levelID'] ?? null,
            'position_id' => $RS['positionID'] ?? null,
            'create_date' => $RS['rsDate'] ?? now(),
            'description' => $RS['description'] ?? null,
            'hire_type_id' => $RS['hireTypeID'] ?? null,
            'job_id' => $RS['jobID'] ?? null,
            'operator_id' => $RS['operatorID'] ?? null,
            'script_type_id' => $RS['scriptTypeID'] ?? null,
            'parent_id' => $RS['parentID'] ?? null,
            'start_date' => $RS['startDate'] ?? null,
            'expire_date' => $RS['expireDate'] ?? null,
        ]);
        return $data;
    }

    public function activeRsStatus()
    {
        return RecruitmentScript::GetAllStatuses()->firstWhere('name', '=', self::$activeRsStatus);
    }

    public function pendingRsStatus()
    {
        return RecruitmentScript::GetAllStatuses()->firstWhere('name', '=', self::$pendingRsStatus);
    }

    public function rsSingleStore(array|Collection $data, int $employeeID, ?Status $status = null)
    {
        $dataToInsert = $this->rsDataPreparation([$data], $employeeID);

        if (is_null($status)) {
            $status = $this->activeRsStatus();

        }
        $rs = RecruitmentScript::create($dataToInsert->toArray()[0]);
        $rs->status()->attach($status->id);

        if (isset($data['scriptAgents'])) {
            $agents = json_decode($data['scriptAgents'], true);
            $scriptAgentsScripts = $this->sasStore($agents, $rs);
        }
        $rs->load('scriptType.confirmationTypes');


        $result[] = $rs;
        return $result;

    }

    public function rsBulkUpdate(array|collection $data, int $employeeID)
    {
        $dataToUpsert = $this->rsDataPreparation($data, $employeeID);
        $insertCount = $dataToUpsert->where('id', null)->count();
        $result = RecruitmentScript::upsert($dataToUpsert->toArray(), ['id']

        );

        if ($insertCount > 0) {
            $activeStatus = $this->activeRsStatus();

            $rses = RecruitmentScript::orderBy('id', 'desc')->take($insertCount)->get();
            $rses->map(fn(RecruitmentScript $recruitmentScript) => $recruitmentScript->status()->attach($activeStatus->id));
        }
        return $result;
    }

    public function rsDelete(array $data)
    {
        $rses = RecruitmentScript::find($data);
        $deleteStatus = $this->inActiveRsStatus();
        foreach ($rses as $item) {
            $item->status()->attach($deleteStatus->id);
        }

        return true;
    }

    public function inActiveRsStatus()
    {
        return RecruitmentScript::GetAllStatuses()->firstWhere('name', '=', self::$inActiveRsStatus);
    }

    public function rsShow(RecruitmentScript $script)
    {
        $script->load(['position', 'level', 'job', 'scriptAgents', 'approvers.status', 'approvers.assignedTo']);
    }
}
