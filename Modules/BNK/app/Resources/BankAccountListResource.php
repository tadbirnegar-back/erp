<?php

namespace Modules\BNK\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'branch' => [
                'name' => $this->branch_name,
                'branch_code' => $this->branch_code,
            ],
            'bank' => [
                'name' => $this->bank_name,
                'logo' => url('/') . '/' . $this->logo_slug,
            ],
            'status' => [
                'name' => $this->status_name,
                'class_name' => $this->status_class_name,
            ],
        ];
    }
}
