<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\HRMS\app\Models\Position;
use Modules\OUnitMS\app\Models\OrganizationUnit;

trait PositionTrait
{
    private string $activePositionStatus = 'فعال';
    private string $inactivePositionStatus = 'غیرفعال';

    public function positionIndex()
    {
        $result = Position::whereHas('status', function ($query) {
            $query->where('name', '=', $this->activePositionStatus);
        })->with('levels', 'roles')->get();

        return $result;
    }

    public function positionStore(array $data)
    {

        $ounitCatEnum = OunitCategoryEnum::from($data['ounitCatID']);
        $position = new Position();
        $position->name = $data['title'];
        $position->ounit_cat = $ounitCatEnum->value;
        $status = $this->activePositionStatus();
        $position->status_id = $status->id;

        $position->save();

        //add position to related organizationUnits

        $ounitIDs = OrganizationUnit::where('unitable_type', $ounitCatEnum->getUnitableType())->get(['id']);

        $position->organizationUnits()->attach($ounitIDs->pluck('id')->toArray());

        if (isset($data['roleIDs'])) {
            $roleIDs = json_decode($data['roleIDs'], true);
            $position->roles()->sync($roleIDs);
        }

        // Attach levels
        if (isset($data['levelIDs'])) {
            $levelIDs = json_decode($data['levelIDs'], true);
            $position->levels()->sync($levelIDs);
        }
        $position->load('levels', 'roles');
        return $position;


    }

    public function positionUpdate(array $data, Position $position)
    {
        $ounitCatEnum = OunitCategoryEnum::from($data['ounitCatID']);

        $ounitCatToDelete = $position->ounit_cat;


        if (!is_null($ounitCatToDelete)) {
            $ounitToDeleteIDs = OrganizationUnit::where('unitable_type', $ounitCatEnum->getUnitableType())->get(['id']);

            $position->organizationUnits()->detach($ounitToDeleteIDs->pluck('id')->toArray());

        }


        $ounitIDs = OrganizationUnit::where('unitable_type', $ounitCatEnum->getUnitableType())->get(['id']);


        $position->name = $data['title'];
        $position->ounit_cat = $data['ounitCatID'];
        $position->save();

        $position->organizationUnits()->attach($ounitIDs->pluck('id')->toArray());

        if (isset($data['roleIDs'])) {
            $roleIDs = json_decode($data['roleIDs'], true);
            $position->roles()->sync($roleIDs);
        }

        if (isset($data['levelIDs'])) {
            $levelIDs = json_decode($data['levelIDs'], true);
            $position->levels()->sync($levelIDs);
        }
        $position->load('levels', 'roles');
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
