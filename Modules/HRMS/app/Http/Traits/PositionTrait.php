<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\Position;

trait PositionTrait
{
    public function positionIndex()
    {
        $result = Position::all();

        return $result;
    }

    public function positionStore(array $data)
    {

        $position = new Position();
        $position->name = $data['positionName'];
        $position->section_id = $data['sectionID'] ?? null;
        $status = $this->activePositionStatus();
        $position->status_id = $status->id;
        $position->save();

        // Attach levels
        if (isset($data['levelIDs']) && is_array($data['levelIDs'])) {
            $position->levels()->sync($data['levelIDs']);
        }

        return $position;


    }

    public function positionUpdate(array $data, Position $position)
    {

        $position->name = $data['positionName'];
        $position->section_id = $data['sectionID'] ?? null;
        $position->save();

        if (isset($data['levelIDs']) && is_array($data['levelIDs'])) {
            $position->levels()->sync($data['levelIDs']);
        }

        return $position;

    }

    public function positionShow(int $id)
    {
        return Position::with('status', 'section.department.branch')->findOrFail($id);
    }

    public function activePositionStatus()
    {
        return Position::GetAllStatuses()->firstWhere('name', '=', 'فعال');
    }

    public function inactivePositionStatus()
    {
        return Position::GetAllStatuses()->firstWhere('name', '=', 'غیرفعال');
    }
}
