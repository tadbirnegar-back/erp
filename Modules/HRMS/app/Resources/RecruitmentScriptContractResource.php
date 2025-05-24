<?php

namespace Modules\HRMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRMS\app\Http\Enums\HireTypeEnum;

class RecruitmentScriptContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $agents = $this->agents;
        return [
            'contractDays' => HireTypeEnum::getcontactDaysByOunit($this->ounit),
            'ounit' => [
                'name' => $this->ounit->name,
                'ancestors' => [
                    'district' => $this->ounit->ancestors[0]->name,
                    'city' => $this->ounit->ancestors[1]->name,
                    'state' => $this->ounit->ancestors[2]->name,
                ],
            ],
            'fullName' => $this->person->display_name,
            'nationalCode' => $this->person->national_code,
            'bcCode' => $this->person->natural->bc_code,
            'fatherName' => $this->person->natural->father_name,
            'birthDate' => convertGregorianToJalali($this->person->natural->birth_date),
            'birthLocation' => $this->person->natural->birth_location,
            'issueLocation' => $this->person->natural->bc_issue_location,
            'gender' => $this->person->natural->gender_id == 1 ? 'مرد' : 'زن',
            'married' => $this->person->natural->ismarried == 1 ? 'متاهل' : 'مجرد',
            'startDate' => convertGregorianToJalali($this->start_date),
            'expireDate' => $this->expire_date != null ? convertGregorianToJalali($this->expire_date) : null,
            'educationalRecord' => $this->latestEducationRecord,
            'isar' => $this->person->isar,
            'militaryService' => $this->person->militaryService,
            'position' => $this->position->name,
            'heirsCount' => $this->heirs_count,
            'agents' => $agents->map(function ($agents, $type) {
                return [
                    'type' => $type,
                    'agents' =>
                        $agents->map(function ($agent) {
                            return [
                                'name' => $agent->title,
                                'contract' => $agent->default_value,
                            ];
                        })->toArray(),


                ];


            })->values(),
        ];
    }
}
