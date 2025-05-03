<?php

namespace Modules\BDM\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BDM\app\Http\Enums\BdmTypesEnum;

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


        ];
    }

    private function getBdmTypeName($bdmTypeID)
    {
        $bdmType = BdmTypesEnum::listWithIds();
        $bdmTypeName = $bdmType->where('id' , $bdmTypeID)->first()['name'];
        return $bdmTypeName;
    }
}
