<?php

namespace Modules\PFM\app\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\PFM\app\Http\Enums\LevyStatusEnum;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\LevyCircular;

trait LevyTrait
{


    public function FillLevies($circularId)
    {
        $activeStatus = $this->ActiveStatus();
        $levies = Levy::where('status_id', $activeStatus->id)->get();
        $data = [];

        $levies->map(function ($levy) use (&$data, $circularId) {
            $data[] = [
                'levy_id' => $levy->id,
                'circular_id' => $circularId,
                'created_date' => now(),
            ];
        });

        LevyCircular::insert($data);
    }


    public function ActiveStatus()
    {
        return Cache::rememberForever('levy_active_status', function () {
            return Levy::GetAllStatuses()->firstWhere('name', LevyStatusEnum::ACTIVE->value);
        });
    }

    public function NotActiveStatus()
    {
        return Cache::rememberForever('levy_not_active_status', function () {
            return Levy::GetAllStatuses()->firstWhere('name', LevyStatusEnum::NOT_ACTIVE->value);
        });
    }
}
