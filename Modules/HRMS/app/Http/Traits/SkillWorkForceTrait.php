<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\HRMS\app\Models\SkillWorkForce;
use Modules\HRMS\app\Models\WorkForce;

trait SkillWorkForceTrait
{

    public function swStore(array|Collection $data, WorkForce $workForce)
    {
        $preparedData = $this->skillWorkforceDataPreparation($data, $workForce);

        $result = SkillWorkForce::insert($preparedData->toArray());

        return $result;
    }

    public function swSingleStore(array $data, WorkForce $workForce)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }

        $preparedData = $this->skillWorkforceDataPreparation($data, $workForce);

        $result = SkillWorkForce::create($preparedData->toArray());

        return $result;
    }


    public function swUpdate(array $data, WorkForce $workForce)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }

        $preparedData = $this->skillWorkforceDataPreparation($data, $workForce);

        $result = SkillWorkForce::upsert(
            $preparedData->toArray(),
            ['id']
        );
        return $result;
    }

    public function skillWorkforceDataPreparation(array|Collection $data, WorkForce $workForce)
    {
        if (is_array($data)) {
            $data = collect($data);
        }

        return $data->map(function ($item) use ($workForce) {
            return [
                'id' => $item['swID'],
                'workforce_id' => $workForce->id,
                'skill_id' => $item['skillID'],
                'percentage' => $item['percentage'],
            ];
        });
    }

}
