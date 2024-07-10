<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\HRMS\app\Models\Position;

trait PositionTrait
{
    private string $activePositionStatus = 'فعال';
    private string $inactivePositionStatus = 'غیرفعال';
    public function positionIndex()
    {
        $result = Position::whereHas('status', function ($query) {
            $query->where('name', '=', $this->activePositionStatus);
        })->with('levels')->get();

        return $result;
    }

    public function positionStore(array $data)
    {

        $position = new Position();
        $position->name = $data['title'];
        $position->ounit_cat = $data['ounitCatID'];
        $status = $this->activePositionStatus();
        $position->status_id = $status->id;

        $position->save();

        // Attach levels
        if (isset($data['levelIDs'])) {
            $levelIDs = json_decode($data['levelIDs'], true);
            $position->levels()->sync($levelIDs);
        }
        $position->load('levels');
        return $position;


    }

    public function positionUpdate(array $data, Position $position)
    {
        $position->name = $data['title'];
        $position->ounit_cat = $data['ounitCatID'];
        $position->save();

        if (isset($data['levelIDs'])) {
            $levelIDs = json_decode($data['levelIDs'], true);
            $position->levels()->sync($levelIDs);
        }
        $position->load('levels');
        return $position;

    }

    public function positionDelete(Position $position)
    {
        $position->status_id = $this->inactivePositionStatus()->id;
        $position->save();
        return $position;
    }

    public function positionShow(int $id)
    {
        return Position::with('status', 'section.department.branch')->findOrFail($id);
    }

    public function activePositionStatus()
    {
        return Position::GetAllStatuses()->firstWhere('name', '=', $this->activePositionStatus);
    }

    public function inactivePositionStatus()
    {
        return Position::GetAllStatuses()->firstWhere('name', '=', $this->inactivePositionStatus);
    }


}
