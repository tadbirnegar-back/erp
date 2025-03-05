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
            'fileName' => $this['query']['fileName'],
            'fileSize' => $this->formatFileSize($this['query']['fileSize']),
            'type' => $this['query']['extensionName'],
            'download_url' => url($this['query']['downloadUrl']),
            'create_date' => explode(' ', convertDateTimeGregorianToJalaliDateTime($this['query']['createDate']))[0],
            'expired_date' => explode(' ', convertDateTimeGregorianToJalaliDateTime($this['query']['expiredDate']))[0],

        ];
    }
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
