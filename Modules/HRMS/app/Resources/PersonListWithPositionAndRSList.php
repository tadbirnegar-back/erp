<?php

namespace Modules\HRMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;

class PersonListWithPositionAndRSList extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'displayName' => $this->display_name,
            'personnelCode' => $this->personnel_code,
            'nationalCode' => $this->national_code,
            'mobile' => $this->mobile,
            'lastUpdated' => convertGregorianToJalali($this->last_updated),
            'positions' => $this->recruitmentScripts->map(function ($rs) {
                return [
                    $rs->position_name . ' - ' . OunitCategoryEnum::getOunitCatByUnitableType($rs?->organizationUnit->unitable_type)->getLabel() . ' ' . $rs?->organizationUnit->name,
                ];
            })->flatten(1),
            'scripts' => $this->recruitmentScripts->map(function ($rs) {
                return [
                    'scriptType' => $rs->script_type_title,
                    'mainOunit' => $rs->organizationUnit->name,
                    'ancestors' => $rs->organizationUnit->ancestors->pluck('name'),
                    'abadiCode' => $rs->organizationUnit->abadi_code,
                    'status' => [
                        'name' => $rs->status_name,
                        'className' => $rs->status_class_name,
                    ]
                ];
            }),
            'gender' => $this->gender_id == 1 ? 'مرد' : 'زن',

        ];
    }
}
