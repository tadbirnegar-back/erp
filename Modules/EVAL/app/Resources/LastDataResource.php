<?php

namespace Modules\EVAL\app\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LastDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {

        return [

            'id'            => $this->id,
            'name'          => $this->title,
            'description'   => $this->description,
            'maximum_value' => $this->maximum_value,
            'file_id'       => $this->file_id,
            'download_url'  => $this->whenLoaded('file', fn() => $this->file->slug),
            'create_date'   => $this->create_date,
            'expired_date'  => convertDateTimeGregorianToJalaliDateTime($this->expired_date),
        ];    }
}
