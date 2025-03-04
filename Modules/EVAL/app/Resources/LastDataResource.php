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
            'title' => $this['query']['name'],
            'description' => $this['query']['description'],
            'MaximumValue' => $this['query']['MaximumValue'],
            'fileID' => $this['query']['fileID'],
            'fileSize' => $this['query']['fileSize'],
            'type' => $this['query']['extensionName'],
            'download_url' => url($this['query']['downloadUrl']),
            'create_date' => explode(' ', convertDateTimeGregorianToJalaliDateTime($this['query']['createDate']))[0],
            'expired_date' => explode(' ', convertDateTimeGregorianToJalaliDateTime($this['query']['expiredDate']))[0],

        ];
    }
}
