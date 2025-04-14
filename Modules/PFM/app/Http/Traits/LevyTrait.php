<?php

namespace Modules\PFM\app\Http\Traits;

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

        foreach ($levies as $levy) {
            $data[] = [
                'circular_id' => $circularId,
                'levy_id' => $levy->id,
                'created_date' => now(),
            ];
        }

        LevyCircular::insert($data);
    }


    public function ActiveStatus()
    {
        return Levy::GetAllStatuses()->firstWhere('name', LevyStatusEnum::ACTIVE->value);
    }

    public function NotActiveStatus()
    {
        return Levy::GetAllStatuses()->firstWhere('name', LevyStatusEnum::NOT_ACTIVE->value);
    }
}
