<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\EMS\app\Models\Attachmentable;
use Modules\HRMS\app\Http\Enums\EducationalRecordStatusEnum;
use Modules\HRMS\app\Models\EducationalRecord;

trait EducationRecordTrait
{
    public function EducationalRecordStore(array|Collection $dataToInsert, ?int $personID)
    {
        if (!isset($dataToInsert[0]) || !is_array($dataToInsert[0])) {
            $dataToInsert = [$dataToInsert];
        }

        $preparedData = $this->EducationalRecordDataPreparation($dataToInsert, $personID);

        $educationalRecord = EducationalRecord::create($preparedData->toArray()[0]);

        $records = EducationalRecord::orderBy('id', 'desc')->take(count($dataToInsert))->get();


        return $records;

    }

    public function EducationalRecordSingleStore(array|Collection $data, ?int $personID)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $dataToInsert = [$data];
        }

        $preparedData = $this->EducationalRecordDataPreparationForUpsert($dataToInsert, $personID);

        $educationalRecord = EducationalRecord::create($preparedData->toArray()[0]);
        $educationalRecord->load('levelOfEducation');

        $files = json_decode($data['files'], true);
        $this->attachEducationalRecordFiles($educationalRecord, $files);

        return $educationalRecord;

    }

    public function EducationalRecordSingleUpdate(array|Collection $data, EducationalRecord $educationalRecord)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $dataToInsert = [$data];
        }

        $preparedData = $this->EducationalRecordDataPreparationForUpsert($dataToInsert, $educationalRecord->person_id);

        $educationalRecord->update($preparedData->toArray()[0]);
        $educationalRecord->load('levelOfEducation');

        $files = json_decode($data['files'], true);
        $this->attachEducationalRecordFiles($educationalRecord, $files);

        return $educationalRecord;

    }

    public function educationUpsert(array|Collection $dataToUpdate, int $personID)
    {
        $preparedData = $this->EducationalRecordDataPreparationForUpsert($dataToUpdate, $personID);
        EducationalRecord::upsert($preparedData->toArray(), ['id']);
    }


    private function EducationalRecordDataPreparation(array|Collection $educations, int $personID)
    {
        if (is_array($educations)) {
            $educations = collect($educations);
        }
        $status = $this->pendingApproveEducationalRecordStatus();
        $recordsToInsert = $educations->map(fn($data) => [
            'id' => $data['erID'] ?? null,
            'university_name' => $data['universityName'] ?? null,
            'field_of_study' => $data['fieldOfStudy'] ?? null,
            'start_date' => $data['startDate'] ?? null,
            'end_date' => $data['endDate'] ?? null,
            'average' => $data['average'] ?? null,
            'person_id' => $personID,
            'status_id' => $status->id,
            'level_of_educational_id' => $data['levelOfEducationalID'] ?? null,
        ]);
        return $recordsToInsert;
    }


    private function EducationalRecordDataPreparationForUpsert(array|Collection $educations, int $personID)
    {
        if (is_array($educations)) {
            $educations = collect($educations);
        }
        $status = $this->pendingApproveEducationalRecordStatus();

        $recordsToInsert = $educations->map(fn($data) => [
            'id' => $data['erID'] ?? null,
            'university_name' => $data['universityName'] ?? null,
            'field_of_study' => $data['fieldOfStudy'] ?? null,
            'start_date' => convertJalaliPersianCharactersToGregorian($data['startDate']) ?? null,
            'end_date' => convertJalaliPersianCharactersToGregorian($data['endDate']) ?? null,
            'average' => $data['average'] ?? null,
            'person_id' => $personID,
            'status_id' => $status->id,
            'level_of_educational_id' => $data['levelOfEducationalID'] ?? null,
        ]);
        return $recordsToInsert;
    }

    public function EducationalRecordUpdate(array $data, EducationalRecord $educationalRecord)
    {

        $educationalRecord->university_name = $data['universityName'];
        $educationalRecord->field_of_study = $data['fieldOfStudy'];
        $educationalRecord->start_date = $data['startDate'];
        $educationalRecord->end_date = $data['endDate'] ?? null;
        $educationalRecord->average = $data['average'] ?? null;
        $educationalRecord->level_of_educational_id = $data['levelOfEducationalID'] ?? null;

        $educationalRecord->save();


        return $educationalRecord;

    }

    public function EducationalRecordBulkUpdate(array|Collection $data, int $workForceID)
    {
        $dataToUpsert = $this->EducationalRecordDataPreparation($data, $workForceID);
        $result = EducationalRecord::upsert($dataToUpsert->toArray(), ['id']);
        return $result;
    }

    public function attachEducationalRecordFiles(EducationalRecord $educationalRecord, array $files)
    {
        $attachments = collect($files)->map(function ($file) use ($educationalRecord) {
            return [
                'id' => $file['attachID'] ?? null,
                'attachment_id' => $file['fileID'],
                'title' => $file['title'] ?? null,
                'attachmentable_id' => $educationalRecord->id,
                'attachmentable_type' => EducationalRecord::class,
            ];
        })->toArray();

        Attachmentable::upsert($attachments, ['id']);
    }


    public function EducationHardDelete(array $EduIds)
    {
        EducationalRecord::whereIn('id', $EduIds)->Delete();
    }

    public function pendingApproveEducationalRecordStatus()
    {
        return Cache::rememberForever('educational_record_pending_approve_status', function () {
            return EducationalRecord::GetAllStatuses()->firstWhere('name', EducationalRecordStatusEnum::PENDING_APPROVE->value);
        });
    }

    public function approvedEducationalRecordStatus()
    {
        return Cache::rememberForever('educational_record_approved_status', function () {
            return EducationalRecord::GetAllStatuses()->firstWhere('name', EducationalRecordStatusEnum::APPROVED->value);
        });
    }

}
