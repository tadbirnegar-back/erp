<?php

namespace Modules\SMM\app\Traits;

use Auth;
use Cache;
use Modules\SMM\app\Enums\CircularStatusEnum;
use Modules\SMM\app\Models\Circular as SmmCircular;
use Modules\SMM\app\Models\CircularStatus;
use Modules\StatusMS\app\Models\Status;

trait CircularTrait
{
    public function storeSmmCircular(array $data)
    {
        $preparedData = $this->smmCircularDataPreparation($data);
        $result = SmmCircular::create($preparedData->toArray()[0]);

        $this->attachStatusToSmmCircular($result, $this->pendingCircularStatus(), Auth::user()?->id ?? null);

        return $result;

    }

    public function updateSmmCircular(array $data, SmmCircular $smmCircular)
    {
        $preparedData = $this->smmCircularDataPreparation($data);
        $result = $smmCircular->update($preparedData->toArray()[0]);

        return $result;

    }

    public function attachStatusToSmmCircular(SmmCircular $smmCircular, Status $statusID, ?int $userID = null)
    {
        $smmCircularStatus = new CircularStatus();
        $smmCircularStatus->circular_id = $smmCircular->id;
        $smmCircularStatus->status_id = $statusID->id;
        $smmCircularStatus->creator_id = $userID ?? null;
        $smmCircularStatus->save();

    }

    public function smmCircularDataPreparation(array $data)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data);
        $data = $data->map(function ($item) {

            return [
                'title' => $item['title'] ?? null,
                'description' => $item['description'] ?? null,
                'file_id' => $item['fileID'],
                'fiscal_year_id' => $item['fiscalYearID'],
                'min_wage' => $item['minWage'] ?? null,
                'marriage_benefit' => $item['marriageBenefit'] ?? null,
                'rent_benefit' => $item['rentBenefit'] ?? null,
                'grocery_benefit' => $item['groceryBenefit'] ?? null,
            ];

        });

        return $data;
    }

    public function publishCircularStatus()
    {
        return Cache::rememberForever('smm_circular_publish_status', function () {
            return SmmCircular::GetAllStatuses()->where('statuses.name', CircularStatusEnum::DISPATCHED->value)->first();
        });
    }

    public function pendingCircularStatus()
    {
        return Cache::rememberForever('smm_circular_pending_status', function () {
            return SmmCircular::GetAllStatuses()->where('statuses.name', CircularStatusEnum::DRAFT->value)->first();
        });
    }


}
