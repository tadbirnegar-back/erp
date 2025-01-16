<?php

namespace Modules\ACMS\app\Http\Trait;

use Modules\ACMS\app\Http\Enums\CircularStatusEnum;
use Modules\ACMS\app\Models\Circular;
use Modules\ACMS\app\Models\CircularStatus;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;

trait CircularTrait
{

    public function createCircular(array $data, FiscalYear $fiscalYear)
    {
        $preparedData = $this->circularDataPreparation($data, $fiscalYear);
        $circular = Circular::create($preparedData->toArray()[0]);

        $circularStatuses = $this->circularStatusAttach($data, $circular);;
        return $circular;

    }

    public function circularStatusAttach(array $data, Circular $circular)
    {
        $circularStatuses = $this->circularStatusDataPreparation($data, $circular);

        $circularStatus = CircularStatus::create($circularStatuses->toArray()[0]);
        return $circularStatus;
    }

    public function indexCircular(array $data)
    {
        $searchTerm = $data['name'] ?? null;
        $pageNumber = $data['pageNum'] ?? 1;
        $perPage = $data['perPage'] ?? 10;

        $circulars = Circular::joinRelationship('finalStatus.status', [
            'finalStatus' => function ($join) {
                $join->on('bgtCircular_status.id', '=', \DB::raw('(
                                SELECT id
                                FROM bgtCircular_status AS ps
                                WHERE ps.circular_id = bgt_circulars.id
                                ORDER BY ps.create_date DESC
                                LIMIT 1
                            )'));
            },
        ])
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->whereRaw("MATCH (bgt_circulars.name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm])
                    ->orWhere('bgt_circulars.name', 'like', '%' . $searchTerm . '%');
            })
            ->select([
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                'bgt_circulars.id as circular_id',
                'bgt_circulars.name as circular_name'
            ])
            ->paginate($perPage, page: $pageNumber);

        return $circulars;
    }

    public function circularDataPreparation(array $data, FiscalYear $fiscalYear)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data)->map(function ($item) use ($fiscalYear) {


            return [
                'name' => $item['circularName'],
                'file_id' => $item['fileID'] ?? null,
                'fiscal_year_id' => $fiscalYear->id,
            ];
        });

        return $data;

    }

    public function circularStatusDataPreparation(array $data, Circular $circular)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $draftCircularStatus = $this->draftCircularStatus();

        $data = collect($data)->map(function ($item) use ($circular, $draftCircularStatus) {

            return [
                'circular_id' => $circular->id,
                'status_id' => $item['statusID'] ?? $draftCircularStatus->id,
                'creator_id' => $item['userID'],
                'create_date' => now(),
            ];
        });

        return $data;

    }

    public function draftCircularStatus()
    {
        return Circular::GetAllStatuses()->firstWhere('name', CircularStatusEnum::DRAFT->value);
    }

    public function approvedCircularStatus()
    {
        return Circular::GetAllStatuses()->firstWhere('name', CircularStatusEnum::APPROVED->value);
    }

    public function deleteCircularStatus()
    {
        return Circular::GetAllStatuses()->firstWhere('name', CircularStatusEnum::DELETED->value);
    }

    public function ounitsIncludingForAddingBudget(Circular $circular, bool $count = false, bool $isDispatch = false)
    {
        $vills = OrganizationUnit::where('unitable_type', VillageOfc::class)->joinRelationship('village', function ($join) {
            $join->where('hasLicense', true);
        })
            ->leftJoinRelationship('ounitFiscalYears.budget', [
                'ounitFiscalYears' => function ($join) use ($circular) {
                    $join->where('ounit_fiscalYear.fiscal_year_id', $circular->fiscal_year_id);
                },
                'budget' => function ($join) use ($circular) {
                    $join->where('circular_id', $circular->id);
                }
            ])->when($isDispatch, function ($query) use ($circular) {
                $query->whereNotNull('ounit_fiscalYear.fiscal_year_id')
                    ->orWhereNotNull('bgt_budgets.ounitFiscalYear_id');
            }, function ($query) use ($circular) {
                $query->where(function ($query) {
                    $query->whereNull('ounit_fiscalYear.fiscal_year_id')
                        ->orWhereNull('bgt_budgets.ounitFiscalYear_id');
                });
            });

        return $count ? $vills->count() : $vills->select([
            'organization_units.id as ounitID'])
            ->get();
    }


}
