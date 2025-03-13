<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Facades\DB;
use Modules\HRMS\app\Models\Level;
use Modules\HRMS\app\Models\Position;

trait LevelTrait
{
    private string $activeLevelName = 'فعال';
    private string $inactiveLevelName = 'غیرفعال';

    public function levelIndex()
    {
        $result = Level::whereHas('status', function ($query) {
            $query->where('name', '=', $this->activeLevelName);
        })->get();

        return $result;
    }

    public function storeLevel(array $data)
    {

        $level = new Level;
        $level->name = $data['title'];

        $status = $this->activeLevelStatus();

        $level->status_id = $status->id;
        $level->save();
        return $level;


    }

    public function updateLevel(Level $level, array $data)
    {

        $level->name = $data['title'];
        $level->save();
        return $level;

    }

    public function showLevel(int $ID)
    {
        return Level::findOrFail($ID);
    }

    public function showLevelsBasedOnCatId($ounitCats)
    {
        $ounits = str_split($ounitCats);
        return Position::query()
            ->join('level_position as lvl_pos'  , 'lvl_pos.position_id', '=', 'positions.id')
            ->join('levels as lvl_alias' , 'lvl_alias.id', '=', 'lvl_pos.level_id')
            ->join('statuses as status_alias' , 'status_alias.id', '=', 'lvl_alias.status_id')
            ->where('status_alias.name', '=', $this->activeLevelName)
            ->join('statuses as status_pos_alias' , 'status_pos_alias.id', '=', 'positions.status_id')
            ->where('status_pos_alias.name', '=', 'فعال')
            ->select([
                'lvl_alias.id as level_alias_id',
                'lvl_alias.name as level_name',
            ])
            ->whereIn('positions.ounit_cat', $ounits)
            ->distinct()
            ->get();
    }

    public function activeLevelStatus()
    {
        return Level::GetAllStatuses()->firstWhere('name', '=', $this->activeLevelName);
    }

    public function inactiveLevelStatus()
    {
        return Level::GetAllStatuses()->firstWhere('name', '=', $this->inactiveLevelName);
    }
}
