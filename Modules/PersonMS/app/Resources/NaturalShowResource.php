<?php

namespace Modules\PersonMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NaturalShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'fatherName' => $this->father_name,
            'mobile' => $this->mobile,
            'birthDate' => !is_null($this->birth_date) ? convertGregorianToJalali($this->birth_date) : null,
            'bcCode' => $this->bc_code,
            'isMarried' => $this->isMarried,
            'gender' => [
                'id' => $this->gender_id,
                'name' => $this->gender_id == 1 ? 'مرد' : 'زن',
            ],
            'bcIssueDate' => !is_null($this->bc_issue_date) ? convertGregorianToJalali($this->bc_issue_date) : null,
            'bcIssueLocation' => $this->bc_issue_location,
            'birthLocation' => $this->birth_location,
            'bcSerial' => $this->bc_serial,
            'religion' => $this->religion,
            'religionType' => $this->religionType,
            'militaryServiceStatus' => $this->military,
            'licenses' => $this->licenses->map(function ($license) {
                return [
                    'id' => $license->id,
                    'file' => [
                        'id' => $license->file->id,
                        'slug' => $license->file->slug,
                        'size' => $license->file->size,
                        'type' => $license->file->mimeType->name,
                    ],
                    'licenseType' => [
                        'id' => $license->license_type,
                        'name' => $license->license_type->name(),
                    ],
                ];
            }),
        ];
    }
}
