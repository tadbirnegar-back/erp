<?php

namespace Modules\SMM\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffDetailListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'employeeID' => $this->employee_id,
            'displayName' => $this->display_name,
            'nationalCode' => $this->national_code,
            'gender' => is_null($this->gender) ? '-' : ($this->gender == 1 ? 'مرد' : 'زن'),
            'personID' => $this->person_id,
            'positionName' => $this->position_name,
            'scriptName' => $this->script_name,
            'salary' => '-',
            'startDate' => '-',
            'file' => !is_null($this->file_id) ? [
                'fileID' => $this->file_id,
                'fileSlug' => $this->file_slug,
                'fileSize' => $this->file_size,
                'fileName' => $this->file_name,
            ] : null,
        ];
    }
}
