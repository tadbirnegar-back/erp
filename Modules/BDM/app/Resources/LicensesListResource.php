<?php

namespace Modules\BDM\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BDM\app\Http\Enums\BdmTypesEnum;
use Modules\BDM\app\Http\Enums\PermitStatusesEnum;

class LicensesListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->dossier_id,
            'tracking_code' => $this->tracking_code,
            'type' => BdmTypesEnum::getNameById($this->bdm_type_id),
            'user_name' => $this->main_owner_name,
            'district' => $this->district_name,
            'village' => $this->village_name,
            'created_date' => $this->created_date,
            'dossier_status' => [
                'name' => $this->dossier_status_name,
                'class_name' => $this->dossier_status_class_name,
            ],
            'permit_status' => [
                'name' => $this->permit_status_name,
                'class_name' => $this->permit_status_class_name,
                'suggested_name' => $this->getPermitStatusSuggestedName($this->permit_status_name),
            ],
            'mobile' => $this->mobile,
        ];
    }

    private function getBdmTypeName($bdmTypeID)
    {
        $bdmType = BdmTypesEnum::listWithIds();
        $bdmTypeName = $bdmType->where('id' , $bdmTypeID)->first()['name'];
        return $bdmTypeName;
    }

    private function getPermitStatusSuggestedName(string $name): ?string
    {
        foreach (PermitStatusesEnum::cases() as $case) {
            if ($case->value === $name) {
                return $case->whichNumber();
            }
        }

        return null;
    }
}
