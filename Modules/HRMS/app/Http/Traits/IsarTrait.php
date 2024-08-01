<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\Isar;

trait IsarTrait
{
//write CRUD for isars table

    public function isarStore(array $data, int $workForceID)
    {
        $isar = new Isar();
        $isar->isar_status_id = $data['isarStatusID']??null;
        $isar->relative_type_id = $data['relativeTypeID']??null;
        $isar->length = $data['length']??null;
        $isar->percentage = $data['percentage']??null;
        $isar->work_force_id = $workForceID;
        $isar->save();

        return $isar;
    }

    public function readIsar(int $id): ?Isar
    {
        return Isar::find($id);
    }

    public function isarUpdate(Isar $isar, array $data): ?Isar
    {
        $isar->isar_status_id = $data['isarStatusID']??null;
        $isar->relative_type_id = $data['relativeTypeID']??null;
        $isar->length = $data['length']??null;
        $isar->percentage = $data['percentage']??null;
        $isar->work_force_id = $data['workForceID'];
        $isar->save();

        return $isar;
    }
}
