<?php

namespace Modules\HRMS\app\Http\Repositories;

use Modules\HRMS\app\Models\Position;

class PositionRepository
{
    protected Position $position;

    public function __construct(Position $position)
    {
        $this->position = $position;
    }

    public function index()
    {
        $result = $this->position::all();

        return $result;
    }

    public function store(array $data)
    {

        try {
            \DB::beginTransaction();
            /**
             * @var Position $position
             */
            $position = new $this->position();
            $position->name = $data['positionName'];
            $position->section_id = $data['sectionID']??null;
            $status = $this->position::GetAllStatuses()->where('name', '=', 'فعال')->first();
            $position->status_id = $status->id;
            $position->save();
            \DB::commit();

            return $position;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }

    }

    public function update(array $data, int $ID)
    {
        try {
            \DB::beginTransaction();
            /**
             * @var Position $position
             */
            $position = $this->position::findOrFail($ID);
            $position->name = $data['positionName'];
            $position->section_id = $data['sectionID'];
//            $position->status_id = $data['statusID'];
            $position->save();
            \DB::commit();

            return $position;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }
    }

    public function show(int $id)
    {
        return $this->position::with('status','section.department.branch')->findOrFail($id);
    }
}
