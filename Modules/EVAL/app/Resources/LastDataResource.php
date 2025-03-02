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

            'id' => $this['query']['id'],
            'name' => $this['query']['name'],
            'description' => $this['query']['description'],
            'MaximumValue' => $this['query']['MaximumValue'],
            'fileID' => $this['query']['fileID'],
            'download_url' => url($this['query']['downloadUrl']),
            'create_date' => $this['query']['createDate'],
            'expired_date' => convertDateTimeGregorianToJalaliDateTime($this['query']['expiredDate']),

        ];
    }
}
