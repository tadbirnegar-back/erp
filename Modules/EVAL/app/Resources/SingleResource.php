<?php

namespace Modules\EVAL\app\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleResource extends JsonResource
{
    public function toArray($request)
    {
        $expiredDate = Carbon::parse($this->expiredDate);
        $createdDate = Carbon::parse($this->createDate);
        $now = Carbon::now();
        $deadLine = $now->diffInDays($expiredDate, false); // مقدار ممکن است منفی باشد

        return [
            'id' => $this->id,
            'title' => $this->name,
            'description' => $this->description,
            'maximum_value' => $this->MaximumValue,
            'file_id' => $this->fileID,
            'create_date' => $createdDate->format('Y-m-d'),
            'expired_date' => explode(' ', convertDateTimeGregorianToJalaliDateTime($expiredDate))[0],
            'status' => [
                'name' => $this->statusName,
                'class' => $this->className,
            ],
            'deadline' => $deadLine,
            'file' => [
                'download_url' => url($this->downloadUrl),
                'size' => $this->formatFileSize($this->fileSize),
                'fileType' => $this->extensionName,
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
