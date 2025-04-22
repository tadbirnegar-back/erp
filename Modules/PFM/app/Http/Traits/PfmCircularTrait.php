<?php

namespace Modules\PFM\app\Http\Traits;


use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Log;
use Modules\AAA\app\Models\User;
use Modules\ACMS\app\Http\Trait\FiscalYearTrait;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\AddressMS\app\Models\Village;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PFM\app\Http\Enums\PfmCircularStatusesEnum;
use Modules\PFM\app\Jobs\PublishPfmCircularJob;
use Modules\PFM\app\Models\Booklet;
use Modules\PFM\app\Models\PfmCirculars;
use Modules\PFM\app\Models\PfmCircularStatus;
use Morilog\Jalali\Jalalian;

trait PfmCircularTrait

{

    use LevyTrait, BookletTrait , FiscalYearTrait;

    public function storeCircular($data, $user)
    {
        $circular = PfmCirculars::create([
            'name' => $data['name'],
            'fiscal_year_id' => $data['fiscalYearID'],
            'description' => $data['description'],
            'file_id' => $data['file_id'],
            'created_date' => now(),
            'start_date' => convertPersianToGregorianBothHaveTimeAndDont($data['startDate']),
            'end_date' => convertPersianToGregorianBothHaveTimeAndDont($data['endDate']),
        ]);

        $this->FillLevies($circular->id);

        $this->attachDraftStatus($circular->id, $user->id);

    }

    public function indexCirculars($data , $perPage , $pageNum)
    {
        $query = PfmCirculars::joinRelationship('fiscalYear')
            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join->whereRaw('pfm_circular_statuses.created_date = (SELECT MAX(created_date) FROM pfm_circular_statuses WHERE pfm_circular_id = pfm_circulars.id)');
            }])
            ->select([
                'pfm_circulars.id',
                'pfm_circulars.name as circular_name',
                'pfm_circulars.file_id',
                'pfm_circulars.created_date',
                'pfm_circulars.fiscal_year_id',
                'fiscal_years.name as fiscal_year_name',
                'statuses.name as status_name',
                'statuses.class_name as status_class',
                'pfm_circular_statuses.created_date as status_created_date',
            ])
            ->distinct('pfm_circulars.id')
            ->when(isset($data['name']) , function ($query) use ($data) {
                $query->where('pfm_circulars.name', 'like', '%' . $data['name'] . '%');
            })
            ->paginate($perPage, ['*'], 'page', $pageNum);

        return $query;
    }

    public function showCircular($id)
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


        $countOfVillages = $this->takeValidVillagesCount();
        $bookletsWithStatuses = $this->bookletsWithStatuses($id);
        return [
            'data' => $query->first(),
            'countOfVillages' => $countOfVillages,
            'reportOfPublishedBooklets' => $bookletsWithStatuses,
        ];
    }

    public function showForUpdating($id)
    {
        $query = PfmCirculars::joinRelationship('fiscalYear')
            ->joinRelationship('file')
            ->select([
                'pfm_circulars.id',
                'pfm_circulars.name as circular_name',
                'pfm_circulars.description as circular_description',
                'pfm_circulars.file_id',
                'pfm_circulars.start_date as start_date',
                'pfm_circulars.end_date as end_date',
                'fiscal_years.name as fiscal_year_name',
                'fiscal_years.id as fiscal_year_id',
                'files.slug as file_slug',
                'files.size as file_size',
                'files.name as file_name'
            ])
            ->distinct('pfm_circulars.id')
            ->where('pfm_circulars.id', $id)
            ->get();

        return $query->first();

    }

    public function updateCircular($data, $id)
    {
        $circular = PfmCirculars::find($id);

        $circular->fiscal_year_id = $data['fiscal_year_id'];
        $circular->name = $data['name'];
        $circular->description = $data['description'];
        $circular->file_id = $data['file_id'];
        $circular->start_date = convertPersianToGregorianBothHaveTimeAndDont($data['start_date']);
        $circular->end_date = convertPersianToGregorianBothHaveTimeAndDont($data['end_date']);
        $circular->save();
    }

    public function publishCircular($circularId)
    {
        $includedOunitsForBooklet = $this->ounitsIncludedForPublish($circularId)->chunk(150)->values();
        $user = User::find(2174);
        $jobs = [];

        $ounitIds = [];

        foreach ($includedOunitsForBooklet as $ounit) {
            foreach ($ounit->values() as $item) {
                $delayInSeconds = rand(1, 45);
                $jobs[] = (new PublishPfmCircularJob($circularId, $user->id, $item['ounitID']))
                    ->delay(Carbon::now()->addSeconds($delayInSeconds));
            }
        }

        Bus::batch($jobs)
            ->then(function (Batch $batch) {
                Log::info("All jobs in the batch have completed successfully.");
            })
            ->catch(function (Batch $batch, \Throwable $e) {
                Log::error("An error occurred in the batch: " . 'error');
            })
            ->finally(function (Batch $batch) {
                Log::info("Batch processing is complete.");
            })
            ->name('PublishPfmCircularJob')
            ->onQueue('default')
            ->dispatchAfterResponse();

    }

    public function takeValidVillagesCount()
    {
        return OrganizationUnit::where('unitable_type', VillageOfc::class)
            ->join('village_ofcs as village_ofcs', 'village_ofcs.id', '=', 'organization_units.unitable_id')
            ->where('village_ofcs.hasLicense', true)
            ->count();
    }

    private function ounitsIncludedForPublish($id)
    {
        $data = OrganizationUnit::where('unitable_type', VillageOfc::class)
            ->leftJoin('pfm_circular_booklets', function ($join) use ($id) {
                $join->on('pfm_circular_booklets.ounit_id', '=', 'organization_units.id')
                    ->where('pfm_circular_booklets.pfm_circular_id', $id);
            })
            ->join('village_ofcs as village_ofcs', 'village_ofcs.id', '=', 'organization_units.unitable_id')
            ->where('village_ofcs.hasLicense', true)
            ->where('pfm_circular_booklets.id', null)
            ->select([
                'organization_units.id as ounitID',
            ])
            ->get();

        return $data;
    }

    public function deleteCircular($id)
    {
        $lastStatus = PfmCircularStatus::where('pfm_circular_id', $id)->orderBy('created_date', 'desc')->first();
        $DraftStatusId = $this->draftStatus()->id;
        if ($lastStatus->status_id == $DraftStatusId) {
            $pfmCircular = PfmCirculars::find($id);
            $pfmCircular->delete();
        }
    }

    //Global Status Actions
    public function attachPublishedStatus($id, $user)
    {
        PfmCircularStatus::create([
            'pfm_circular_id' => $id,
            'status_id' => $this->publishedStatus()->id,
            'created_date' => now(),
            'creator_id' => $user,
        ]);
    }

    public function attachDraftStatus($id, $user)
    {
        PfmCircularStatus::create([
            'pfm_circular_id' => $id,
            'status_id' => $this->draftStatus()->id,
            'created_date' => now(),
            'creator_id' => $user,
        ]);
    }

    public function publishedStatus()
    {
        return Cache::rememberForever('pfm_circular_published_status', function () {
            return PfmCirculars::GetAllStatuses()->firstWhere('name', PfmCircularStatusesEnum::PUBLISHED->value);
        });
    }

    public function draftStatus()
    {
        return Cache::rememberForever('pfm_circular_draft_status', function () {
            return PfmCirculars::GetAllStatuses()->firstWhere('name', PfmCircularStatusesEnum::DRAFT->value);
        });
    }

}
