<?php

namespace Modules\EVAL\App\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class SingleResource extends JsonResource
{
    public function toArray($request)
    {
        $expiredDate=Carbon::parse($this->expiredDate);
        $createdDate=Carbon::parse($this->createDate);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'maximum_value' => $this->MaximumValue,
            'file_id' => $this->fileID,
            'create_date' =>convertDateTimeGregorianToJalaliDateTime( $createdDate),
            'expired_date' => convertDateTimeGregorianToJalaliDateTime( $expiredDate),
            'status' => [
                'name' => $this->statusName,
                'class' => $this->className,
            ],

            'file' => [
                'download_url' => url($this->downloadUrl),
                'size' =>$this->formatFileSize($this->fileSize),
                'extension' => $this->extensionName,
            ],

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
