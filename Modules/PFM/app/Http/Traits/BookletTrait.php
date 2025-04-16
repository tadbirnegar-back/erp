<?php

namespace Modules\PFM\app\Http\Traits;


use Illuminate\Support\Carbon;
use Modules\HRMS\app\Http\Enums\ScriptTypesEnum;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptType;
use Modules\PFM\app\Http\Enums\BookletStatusEnum;
use Modules\PFM\app\Http\Enums\LevyStatusEnum;
use Modules\PFM\app\Models\Booklet;
use Modules\PFM\app\Models\BookletStatus;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\PfmCirculars;

trait BookletTrait
{
    public function bookletsWithStatuses($circularId)
    {

        $MosavabStatus = $this->MosavabStatus();
        $DarEntazarSabtStatus = $this->DarEntazarStatus();
        $DarEntazarShuraStatus = $this->EntezarShuraStatus();
        $DarEntazarHeyaatStatus = $this->EntezareHeyateTatbighStatus();
        $PishnahadShodeStatus = $this->RadShodeStatus();

        $mosavabStatus = Booklet::joinRelationship('statuses', ['statuses' => function ($join) {
            $join->whereRaw('pfm_booklet_statuses.created_date = (SELECT MAX(created_date) FROM pfm_booklet_statuses WHERE booklet_id = pfm_circular_booklets.id)');
        }])
            ->select([
                'pfm_circular_booklets.id',
            ])
            ->distinct('pfm_circular_booklets.id')
            ->where('pfm_booklet_statuses.status_id', $MosavabStatus->id)
            ->where('pfm_circular_booklets.pfm_circular_id', $circularId)
            ->get();

        $countOfMosavabStatus = count($mosavabStatus);

        $darEntazarStatus = Booklet::joinRelationship('statuses', ['statuses' => function ($join) {
            $join->whereRaw('pfm_booklet_statuses.created_date = (SELECT MAX(created_date) FROM pfm_booklet_statuses WHERE booklet_id = pfm_circular_booklets.id)');
        }])
            ->select([
                'pfm_circular_booklets.id',
            ])
            ->distinct('pfm_circular_booklets.id')
            ->whereIn('pfm_booklet_statuses.status_id', [$DarEntazarSabtStatus->id, $DarEntazarShuraStatus->id, $DarEntazarHeyaatStatus->id])
            ->where('pfm_circular_booklets.pfm_circular_id', $circularId)
            ->get();

        $countOfDarEntazarStatus = count($darEntazarStatus);

        $pishnahadShodeStatus = Booklet::joinRelationship('statuses', ['statuses' => function ($join) {
            $join->whereRaw('pfm_booklet_statuses.created_date = (SELECT MAX(created_date) FROM pfm_booklet_statuses WHERE booklet_id = pfm_circular_booklets.id)');
        }])
            ->select([
                'pfm_circular_booklets.id',
            ])
            ->distinct('pfm_circular_booklets.id')
            ->where('pfm_booklet_statuses.status_id', $PishnahadShodeStatus->id)
            ->where('pfm_circular_booklets.pfm_circular_id', $circularId)
            ->get();

        $countOfPishnahadShodeStatus = count($pishnahadShodeStatus);

        return [
            'countOfMosavabStatus' => $countOfMosavabStatus,
            'countOfDarEntazarStatus' => $countOfDarEntazarStatus,
            'countOfPishnahadShodeStatus' => $countOfPishnahadShodeStatus,
        ];
    }

    public function listOfBooklets($data, $user, $pageNum = 1, $perPage = 10)
    {

        $scriptType = ScriptType::where('title', ScriptTypesEnum::MASOULE_FAANI->value)->first();


        $user->load(['activeRecruitmentScripts' => function ($query) use ($scriptType) {
            $query->where('script_type_id', $scriptType->id);
        }]);

        $ounits = $user->activeRecruitmentScripts->pluck('organization_unit_id')->toArray();

        $query = Booklet::query()
            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join->whereRaw('pfm_booklet_statuses.created_date = (SELECT MAX(created_date) FROM pfm_booklet_statuses WHERE booklet_id = pfm_circular_booklets.id)');
            }])
            ->joinRelationship('ounit')
            ->select([
                'pfm_circular_booklets.id',
                'organization_units.name as ounit_name',
                'statuses.name as status_name',
                'statuses.class_name as status_class',
                'pfm_booklet_statuses.created_date as status_created_date',
            ])
            ->distinct('pfm_circular_booklets.id')
            ->whereIn('pfm_circular_booklets.ounit_id', $ounits)
            ->whereNot('statuses.name', BookletStatusEnum::RAD_SHODE->value)
            ->when(isset($data['title']), function ($query) use ($data) {
                $query->where('organization_units.name', 'like', '%' . $data['title'] . '%');
            })
            ->when($data['isThisYear'], function ($query) {
                $query->join('pfm_circulars', 'pfm_circular_booklets.pfm_circular_id', '=', 'pfm_circulars.id')
                    ->join('fiscal_years', 'pfm_circulars.fiscal_year_id', '=', 'fiscal_years.id')
                    ->where('fiscal_years.name', '=', convertGregorianYearToJalaliYear(Carbon::now()->year));
            })
            ->paginate($perPage, ['*'], 'page', $pageNum);
        return $query;

    }

    public function showBooklet($id, $user)
    {
        $query = Booklet::query()
            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join->whereRaw('pfm_booklet_statuses.created_date = (SELECT MAX(created_date) FROM pfm_booklet_statuses WHERE booklet_id = pfm_circular_booklets.id)');
            }])
            ->select([
                'pfm_circular_booklets.id',
                'statuses.name as status_name',
                'statuses.class_name as status_class',
                'pfm_booklet_statuses.created_date as status_created_date',
                'pfm_circular_booklets.pfm_circular_id as circular_id',
                'pfm_circular_booklets.p1 as p1',
                'pfm_circular_booklets.p2 as p2',
                'pfm_circular_booklets.p3 as p3',
                'pfm_circular_booklets.pfm_circular_id as circular_id',
                'pfm_circular_booklets.ounit_id as ounit_id'
            ])
            ->distinct('pfm_circular_booklets.id')
            ->where('pfm_circular_booklets.id', $id)
            ->get();


        $circularId = $query->first()->circular_id;
        $statusName = $query->first()->status_name;
        $ounitId = $query->first()->ounit_id;

        if ($statusName == BookletStatusEnum::RAD_SHODE->value) {
            return ['message' => 'شما به این دفترچه دسترسی ندارید', 'status' => 403];
        }

        $timeLine = $this->getTimeLine()[$statusName];

        $declined =  $this->declinedBooklets($circularId, $ounitId);


        $circularData = $this->getCircularDatasInBooklet($circularId);

        return ['circular_data' => $circularData, 'booklet_data' => $query, 'timeLine' => $timeLine, 'declined' => $declined , 'status' => 200];
    }

    private function getCircularDatasInBooklet($id)
    {
        $query = PfmCirculars::joinRelationship('fiscalYear')
            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join->whereRaw('pfm_circular_statuses.created_date = (SELECT MAX(created_date) FROM pfm_circular_statuses WHERE pfm_circular_id = pfm_circulars.id)');
            }])
            ->joinRelationship('file.extension')
            ->select([
                'pfm_circulars.id',
                'pfm_circulars.name as circular_name',
                'pfm_circulars.description as circular_description',
                'pfm_circulars.file_id',
                'pfm_circulars.created_date',
                'fiscal_years.name as fiscal_year_name',
                'statuses.name as status_name',
                'statuses.class_name as status_class',
                'pfm_circular_statuses.created_date as status_created_date',
                'files.slug as file_slug',
                'files.size as file_size',
                'extensions.name as extension_name',

            ])
            ->distinct('pfm_circulars.id')
            ->withCount('booklets')
            ->where('pfm_circulars.id', $id)
            ->get();

        $levies = PfmCirculars::join('pfm_levy_circular as levy_circular', 'pfm_circulars.id', '=', 'levy_circular.circular_id')
            ->join('pfm_levies as levies', 'levy_circular.levy_id', '=', 'levies.id')
            ->select([
                'levy_circular.id as levy_id',
                'levies.name as levy_name',
            ])
            ->distinct('pfm_circulars.id')
            ->where('pfm_circulars.id', $id)
            ->get();

        $query[0]['levies'] = $levies;


        return $query->first();
    }

    public function getTimeLine()
    {
        return [
            BookletStatusEnum::MOSAVAB->value => [
                BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value => ['sub_status' => 'Done'],
                BookletStatusEnum::DAR_ENTEZAR_SHURA->value => ['sub_status' => 'Done'],
                BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value => ['sub_status' => 'Done']
            ],
            BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value => [
                BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value => ['sub_status' => 'NotDone'],
                BookletStatusEnum::DAR_ENTEZAR_SHURA->value => ['sub_status' => 'NotDone'],
                BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value => ['sub_status' => 'NotDone']
            ],
            BookletStatusEnum::DAR_ENTEZAR_SHURA->value => [
                BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value => ['sub_status' => 'Done'],
                BookletStatusEnum::DAR_ENTEZAR_SHURA->value => ['sub_status' => 'NotDone'],
                BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value => ['sub_status' => 'NotDone']
            ],
            BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value => [
                BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value => ['sub_status' => 'Done'],
                BookletStatusEnum::DAR_ENTEZAR_SHURA->value => ['sub_status' => 'Done'],
                BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value => ['sub_status' => 'NotDone']
            ],
        ];
    }

    private function declinedBooklets($circularId, $ounitId)
    {
        $query = Booklet::query()
            ->joinRelationship('statuses')
            ->join('pfm_booklet_statuses as booklet_statuses', 'booklet_statuses.booklet_id', '=', 'pfm_circular_booklets.id')
            ->join('statuses as declines_statuses', 'declines_statuses.id', '=', 'booklet_statuses.status_id')
            ->select([
                'declines_statuses.name as status_name',
                'booklet_statuses.created_date as date'
            ])
            ->distinct('pfm_circular_booklets.id')
            ->where('pfm_circular_booklets.ounit_id', $ounitId)
            ->where('pfm_circular_booklets.pfm_circular_id', $circularId)
            ->where('statuses.name', BookletStatusEnum::RAD_SHODE->value)
            ->get();


        $statusesArray = $query->pluck('status_name')->toArray();
        if(empty($statusesArray))
        {
            return [];
        }

        $lengthOfArray =  count($statusesArray);
        $myIndex = $lengthOfArray - 2;
        $timeLine =  $this->getTimeLineDeclined()[$statusesArray[$myIndex]];
        return [$query , $timeLine];
    }

    public function getTimeLineDeclined()
    {
        return [
            BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value => [
                BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value => ['sub_status' => 'danger'],
                BookletStatusEnum::DAR_ENTEZAR_SHURA->value => ['sub_status' => 'gray'],
                BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value => ['sub_status' => 'gray']
            ],
            BookletStatusEnum::DAR_ENTEZAR_SHURA->value => [
                BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value => ['sub_status' => 'success'],
                BookletStatusEnum::DAR_ENTEZAR_SHURA->value => ['sub_status' => 'danger'],
                BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value => ['sub_status' => 'gray']
            ],
            BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value => [
                BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value => ['sub_status' => 'success'],
                BookletStatusEnum::DAR_ENTEZAR_SHURA->value => ['sub_status' => 'success'],
                BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value => ['sub_status' => 'danger']
            ],
        ];
    }


    //Attaching statuses

    public function attachRadShodeStatus($id, $user)
    {
        BookletStatus::create([
            'booklet_id' => $id,
            'status_id' => $this->RadShodeStatus()->id,
            'created_date' => now(),
            'creator_id' => $user,
        ]);
    }

    public function attachMosavabStatus($id, $user)
    {
        BookletStatus::create([
            'booklet_id' => $id,
            'status_id' => $this->MosavabStatus()->id,
            'created_date' => now(),
            'creator_id' => $user,
        ]);
    }

    public function attachDarEntazarStatus($id, $user)
    {
        BookletStatus::create([
            'booklet_id' => $id,
            'status_id' => $this->DarEntazarSabtStatus()->id,
            'created_date' => now(),
            'creator_id' => $user,
        ]);
    }

    public function attachEntezareHeyateTatbighStatus($id, $user)
    {
        BookletStatus::create([
            'booklet_id' => $id,
            'status_id' => $this->EntezareHeyateTatbighStatus()->id,
            'created_date' => now(),
            'creator_id' => $user,
        ]);
    }

    public function attachEntezarShuraStatus($id, $user)
    {
        BookletStatus::create([
            'booklet_id' => $id,
            'status_id' => $this->EntezarShuraStatus()->id,
            'created_date' => now(),
            'creator_id' => $user,
        ]);
    }


    //Get statuses
    public function MosavabStatus()
    {
        return Booklet::GetAllStatuses()->firstWhere('name', BookletStatusEnum::MOSAVAB->value);
    }

    public function DarEntazarSabtStatus()
    {
        return Booklet::GetAllStatuses()->firstWhere('name', BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value);
    }

    public function EntezareHeyateTatbighStatus()
    {
        return Booklet::GetAllStatuses()->firstWhere('name', BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value);
    }

    public function EntezarShuraStatus()
    {
        return Booklet::GetAllStatuses()->firstWhere('name', BookletStatusEnum::DAR_ENTEZAR_SHURA->value);
    }

    public function RadShodeStatus()
    {
        return Booklet::GetAllStatuses()->firstWhere('name', BookletStatusEnum::RAD_SHODE->value);
    }

}
