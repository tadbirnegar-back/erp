<?php

namespace Modules\HRMS\app\Http\Repositories;

use Modules\HRMS\app\Models\Relative;

class RelativeRepository
{
    protected Relative $relative;

    /**
     * @param Relative $relative
     */
    public function __construct(Relative $relative)
    {
        $this->relative = $relative;
    }

    public function store(array $data)
    {
        try {

            \DB::beginTransaction();
            /** @var Relative $relative */
            $relative = new $this->relative();

            $relative->full_name = $data['fullName'];
            $relative->birthdate = $data['birthdate'] ?? null;
            $relative->mobile = $data['mobile'];
            $relative->level_of_educational_id = $data['levelOfEducationalId'] ?? null;
            $relative->relative_type_id = $data['relativeTypeId'] ?? null;
            $relative->work_force_id = $data['workForceId'];

            $relative->save();
            \DB::commit();
            return $relative;
        } catch (\Exception $e) {
            \DB::rollBack();

            return $e;

        }
    }


}
