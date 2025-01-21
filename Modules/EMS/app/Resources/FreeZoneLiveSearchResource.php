<?php

namespace Modules\EMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FreeZoneLiveSearchResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'unitable_type' => $this->unitable_type,
            'unitable_id' => $this->unitable_id,
            'head_id' => $this->head_id,
            'parent_id' => $this->parent_id,
            'status_id' => $this->status_id,
            'depth' => $this->organization_unit->depth ?? null,
            'path' => $this->organization_unit->path ?? null,
            'ancestors' => $this->organization_unit->ancestorsAndSelf->map(function ($ancestor) {
                return [
                    'id' => $ancestor->id,
                    'name' => $ancestor->name,
                    'unitable_type' => $ancestor->unitable_type,
                    'unitable_id' => $ancestor->unitable_id,
                    'head_id' => $ancestor->head_id,
                    'parent_id' => $ancestor->parent_id,
                    'status_id' => $ancestor->status_id,
                    'depth' => $ancestor->depth,
                    'path' => $ancestor->path,
                ];
            }),
            'unitable' => $this->village_with_free_zone->map(function ($village) {
                return [
                    'id' => $village->id,
                    'town_ofc_id' => $village->town_ofc_id,
                    'degree' => $village->degree,
                    'hierarchy_code' => $village->hierarchy_code,
                    'national_uid' => $village->national_uid,
                    'abadi_code' => $village->abadi_code,
                    'ofc_code' => $village->ofc_code,
                    'population_1395' => $village->population_1395,
                    'household_1395' => $village->household_1395,
                    'isTourism' => $village->isTourism,
                    'isFarm' => $village->isFarm,
                    'isAttached_to_city' => $village->isAttached_to_city,
                    'hasLicense' => $village->hasLicense,
                    'license_number' => $village->license_number,
                    'license_date' => $village->license_date,
                    'village_name_in_85' => $village->village_name_in_85,
                    'village_name_in_90' => $village->village_name_in_90,
                    'village_name_in_95' => $village->village_name_in_95,
                    'free_zone_id' => $village->free_zone_id,
                ];
            }),
        ];
    }
}
