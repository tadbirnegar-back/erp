<?php

namespace Modules\PFM\app\Http\Traits;


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\HRMS\app\Http\Enums\ScriptTypesEnum;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptType;
use Modules\PFM\app\Http\Enums\ApplicationsForTablesEnum;
use Modules\PFM\app\Http\Enums\BookletStatusEnum;
use Modules\PFM\app\Http\Enums\LevyStatusEnum;
use Modules\PFM\app\Models\Booklet;
use Modules\PFM\app\Models\BookletStatus;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\LevyCircular;
use Modules\PFM\app\Models\LevyItem;
use Modules\PFM\app\Models\PfmCirculars;
use Modules\PFM\app\Models\PropApplication;
use Modules\PFM\app\Models\Tarrifs;

trait BookletTrait
{
    public function bookletsWithStatuses($circularId)
    {

        $MosavabStatus = $this->MosavabStatus();
        $DarEntazarSabtStatus = $this->DarEntazarSabtStatus();
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
                'pfm_booklet_statuses.id as status_id',
                'statuses.name as status_name',
                'statuses.class_name as status_class',
                'pfm_booklet_statuses.created_date as status_created_date',
                'pfm_circular_booklets.pfm_circular_id as circular_id',
                'pfm_circular_booklets.p_residential as p1',
                'pfm_circular_booklets.p_commercial as p2',
                'pfm_circular_booklets.p_administrative as p3',
                'pfm_circular_booklets.pfm_circular_id as circular_id',
                'pfm_circular_booklets.ounit_id as ounit_id'
            ])
            ->where('pfm_circular_booklets.id', $id)
            ->get();


        $circularId = $query->first()->circular_id;
        $statusName = $query->first()->status_name;
        $ounitId = $query->first()->ounit_id;


        $scriptType = ScriptType::where('title', ScriptTypesEnum::MASOULE_FAANI->value)->first();

        $user->load(['activeRecruitmentScripts' => function ($query) use ($ounitId, $scriptType) {
            $query->where('organization_unit_id', $ounitId)
                ->where('script_type_id', $scriptType->id);
        }]);

        if ($user->activeRecruitmentScripts->count() == 0) {
            return ['message' => 'شما به این دفترچه دسترسی ندارید', 'status' => 403];
        }


        if ($statusName == BookletStatusEnum::RAD_SHODE->value) {
            return ['message' => 'شما به این دفترچه دسترسی ندارید', 'status' => 403];
        }

        $timeLine = BookletStatusEnum::getTimeLine(BookletStatusEnum::from($statusName));

        $declined = $this->declinedBooklets($circularId, $ounitId);


        $circularData = $this->getCircularDatasInBooklet($circularId);

        return ['circular_data' => $circularData, 'booklet_data' => $query->first(), 'timeLine' => $timeLine, 'declined' => $declined, 'status' => 200];
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
                ['class' => 'success', 'name' => BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value],
                ['class' => 'success', 'name' => BookletStatusEnum::DAR_ENTEZAR_SHURA->value],
                ['class' => 'success', 'name' => BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value]
            ],
            BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value => [
                ['class' => 'primary', 'name' => BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value],
                ['class' => 'gray', 'name' => BookletStatusEnum::DAR_ENTEZAR_SHURA->value],
                ['class' => 'gray', 'name' => BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value]
            ],
            BookletStatusEnum::DAR_ENTEZAR_SHURA->value => [
                ['class' => 'success', 'name' => BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value],
                ['class' => 'primary', 'name' => BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value],
                ['class' => 'gray', 'name' => BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value]
            ],
            BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value => [
                ['class' => 'success', 'name' => BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value],
                ['class' => 'success', 'name' => BookletStatusEnum::DAR_ENTEZAR_SHURA->value],
                ['class' => 'primary', 'name' => BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value]
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
                'booklet_statuses.created_date as date',
                'booklet_statuses.booklet_id as booklet_id'
            ])
            ->where('pfm_circular_booklets.ounit_id', $ounitId)
            ->where('pfm_circular_booklets.pfm_circular_id', $circularId)
            ->where('statuses.name', BookletStatusEnum::RAD_SHODE->value)
            ->get()->groupBy('booklet_id');


        $data = [];

        $query->map(function ($items) use (&$data) {
            $sorted = $items->sortBy('created_date');

            $OneToLastStatus = $sorted->count() >= 2 ? [$sorted[$sorted->count() - 2]] : [];
            $timeline = $this->getTimeLineDeclined()[$OneToLastStatus[0]['status_name']];
            $data[] = ['statuses' => $sorted, 'timeLine' => $timeline];

        });

        return $data;
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

    public function showTable($levyId, $bookletId, $status)
    {
        $bookletId = Booklet::find($bookletId);
        $circularId = $bookletId->pfm_circular_id;
        $levyCirculars = LevyCircular::where('levy_id', $levyId)->where('circular_id', $circularId)->first();
        if ($status == BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value) {
            $canEdit = true;
        } else {
            $canEdit = false;
        }

        $shuruh = LevyItem::where('circular_levy_id', $levyCirculars->id)->get();
        $levy = Levy::find($levyId);

        if ($levy->has_app) {
            $levyName = $levy->name;

            $applications = '';
            $multipleAppsIDs = [];
            switch ($levyName) {
                case ApplicationsForTablesEnum::AMLAK_MOSTAGHELAT_SINGLES->value:
                    $applications = ApplicationsForTablesEnum::AMLAK_MOSTAGHELAT_SINGLES->values();
                    $multipleAppsIDs = ApplicationsForTablesEnum::AMLAK_MOSTAGHELAT_MULTIPLES->values();
                    break;
                case ApplicationsForTablesEnum::TAFKIK_ARAZI_SINGLES->value:
                    $applications = ApplicationsForTablesEnum::TAFKIK_ARAZI_SINGLES->values();
                    $multipleAppsIDs = ApplicationsForTablesEnum::TAFKIK_ARAZI_MULTIPLES->values();
                    break;
                case ApplicationsForTablesEnum::PARVANEH_HESAR_SINGLES->value:
                    $applications = ApplicationsForTablesEnum::PARVANEH_HESAR_SINGLES->values();
                    $multipleAppsIDs = ApplicationsForTablesEnum::PARVANEH_HESAR_MULTIPLES->values();
                    break;
                case ApplicationsForTablesEnum::PARVANE_ZIRBANA_SINGLES->value:
                    $applications = ApplicationsForTablesEnum::PARVANE_ZIRBANA_SINGLES->values();
                    $multipleAppsIDs = ApplicationsForTablesEnum::PARVANE_ZIRBANA_MULTIPLES->values();
                    break;
                case ApplicationsForTablesEnum::PARVANE_BALKON_SINGLES->value:
                    $applications = ApplicationsForTablesEnum::PARVANE_BALKON_SINGLES->values();
                    $multipleAppsIDs = ApplicationsForTablesEnum::PARVANE_BALKON_MULTIPLES->values();
                    break;
                case ApplicationsForTablesEnum::PARVANEH_MOSTAHADESAT_SINGLES->value:
                    $applications = ApplicationsForTablesEnum::PARVANEH_MOSTAHADESAT_SINGLES->values();
                    $multipleAppsIDs = ApplicationsForTablesEnum::PARVANEH_MOSTAHADESAT_MULTIPLES->values();
                    break;
            }


            $applicationsInsideTable = [];

            foreach ($applications as $app) {
                $applicationsInsideTable[] = PropApplication::find($app);
            }

            $addMultiples = function ($items) use (&$addMultiples) {
                $result = [];

                foreach ($items as $value) {
                    if (is_array($value)) {
                        $result[] = $addMultiples($value);
                    } else {
                        $result[] = PropApplication::find($value);
                    }
                }

                return $result;
            };

            $structuredMultiples = $addMultiples($multipleAppsIDs);

            $karbariHa = array_merge($applicationsInsideTable, $structuredMultiples);


            $tariffs = [];
            $shuruh->map(function ($item) use (&$bookletId, &$tariffs) {
                $data = Tarrifs::Where('item_id', $item->id)->where('booklet_id', $bookletId->id)->first();
                if (!is_null($data)) {
                    $tariffs[] = [
                        'app_id' => $data->app_id,
                        'item_id' => $data->item_id,
                        'value' => $data->value,
                    ];
                }
            });
            return ['tarrifs' => $tariffs, 'applications' => $karbariHa, 'shuruh' => $shuruh, 'tableModel' => 'Model1', 'canEdit' => $canEdit];
        } else {
            $tariffs = [];
            $shuruh->map(function ($item) use (&$bookletId, &$tariffs) {
                $data = Tarrifs::Where('item_id', $item->id)->where('booklet_id', $bookletId->id)->first();
                if (!is_null($data)) {
                    $tariffs[] = [
                        'item_id' => $data->item_id,
                        'value' => $data->value,
                    ];
                }
            });
            return ['tarrifs' => $tariffs, 'shuruh' => $shuruh, 'tableModel' => 'Model2', 'canEdit' => $canEdit];
        }


    }

    public function submitting($id, $user)
    {
        $query = Booklet::joinRelationship('statuses', ['statuses' => function ($join) {
            $join->whereRaw('pfm_booklet_statuses.created_date = (SELECT MAX(created_date) FROM pfm_booklet_statuses WHERE booklet_id = pfm_circular_booklets.id)');
        }])
            ->select([
                'statuses.name as status_name',
            ])
            ->where('pfm_circular_booklets.id', $id)
            ->get();
        $status = $query->first()->status_name;
        $nextStatus = match ($status) {
            BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value => $this->EntezarShuraStatus()->id,
            BookletStatusEnum::DAR_ENTEZAR_SHURA->value => $this->EntezareHeyateTatbighStatus()->id,
            BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value => $this->MosavabStatus()->id,
            default => null,
        };

        if ($nextStatus) {
            BookletStatus::create([
                'booklet_id' => $id,
                'status_id' => $nextStatus,
                'created_date' => now(),
                'creator_id' => $user->id,
            ]);
        }
    }

    //Attaching statuses
    public function attachRadShodeStatus($id, $user, $descrption = null, $fileID = null)
    {
        BookletStatus::create([
            'booklet_id' => $id,
            'status_id' => $this->RadShodeStatus()->id,
            'created_date' => now(),
            'creator_id' => $user,
            'description' => $descrption,
            'file_id' => $fileID,
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

    public function douplicateBooklet($id, $user)
    {
        $oldBooklet = Booklet::find($id);
        $oldBooklet->load('tariffs');

        $newBooklet = new Booklet();
        $newBooklet->p_residential = $oldBooklet->p_residential;
        $newBooklet->p_commercial = $oldBooklet->p_commercial;
        $newBooklet->p_administrative = $oldBooklet->p_administrative;
        $newBooklet->ounit_id = $oldBooklet->ounit_id;
        $newBooklet->pfm_circular_id = $oldBooklet->pfm_circular_id;
        $newBooklet->created_date = now();
        $newBooklet->save();

        foreach ($oldBooklet->tariffs as $tariff) {
            $newTariff = $tariff->replicate();
            $newTariff->booklet_id = $newBooklet->id;
            $newTariff->save();
        }
    }


    //Get statuses
    public function MosavabStatus()
    {
        return Cache::rememberForever('booklet_mosavab_status', function () {
            return Booklet::GetAllStatuses()->firstWhere('name', BookletStatusEnum::MOSAVAB->value);
        });
    }

    public function DarEntazarSabtStatus()
    {
        return Cache::rememberForever('booklet_dar_entazar_sabt_status', function () {
            return Booklet::GetAllStatuses()->firstWhere('name', BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value);
        });
    }

    public function EntezareHeyateTatbighStatus()
    {
        return Cache::rememberForever('booklet_entezare_heyate_tatbigh_status', function () {
            return Booklet::GetAllStatuses()->firstWhere('name', BookletStatusEnum::DAR_ENTEZARE_HEYATE_TATBIGH->value);
        });
    }

    public function EntezarShuraStatus()
    {
        return Cache::rememberForever('booklet_entezar_shura_status', function () {
            return Booklet::GetAllStatuses()->firstWhere('name', BookletStatusEnum::DAR_ENTEZAR_SHURA->value);
        });
    }

    public function RadShodeStatus()
    {
        return Cache::rememberForever('booklet_rad_shode_status', function () {
            return Booklet::GetAllStatuses()->firstWhere('name', BookletStatusEnum::RAD_SHODE->value);
        });
    }

}
