<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\ConfirmationType;

trait ConfirmationTypeTrait
{
    public function createConformationType(array $data): ConfirmationType
    {
        $conformationType = new ConfirmationType();
        $conformationType->title = $data['title'];
        $conformationType->save();

        return $conformationType;
    }

    public function getConformationType(int $id): ?ConfirmationType
    {
        return ConfirmationType::find($id);
    }

    public function getAllConformationTypes()
    {
        return ConfirmationType::all();
    }

    public function updateConformationType(ConfirmationType $conformationType, array $data): ConfirmationType
    {
        $conformationType->title = $data['title'] ?? $conformationType->title;
        $conformationType->save();

        return $conformationType;
    }

    public function deleteConformationType(int $id)
    {
        $conformationType = ConfirmationType::findOrFail($id);
        return $conformationType;
    }
}
