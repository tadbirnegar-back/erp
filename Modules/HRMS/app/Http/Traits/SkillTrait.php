<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\Skill;

trait SkillTrait
{
    private string $skillActiveName = 'فعال';
    private string $skillInactiveName = 'غیرفعال';
    public function skillIndex()
    {
        $result = Skill::whereHas('status', function ($query) {
            $query->where('name', '=', $this->skillActiveName);
        })->get();

        return $result;
    }

    public function skillStore(array $data)
    {


        $skill = new Skill();
        $skill->name = $data['title'];
        $status = $this->activeSkillStatus();
        $skill->status_id = $status->id;;
        $skill->save();
        return $skill;


    }

    public function skillShow(int $id)
    {
        return Skill::findOrFail($id);
    }

    public function skillUpdate(array $data, Skill $skill)
    {

            $skill->name = $data['title'];
            $skill->save();
            return $skill;


    }

    public function activeSkillStatus()
    {
        return Skill::GetAllStatuses()->firstWhere('name', '=', $this->skillActiveName);
    }

    public function inactiveSkillStatus()
    {
        return Skill::GetAllStatuses()->firstWhere('name', '=', $this->skillInactiveName);
    }

}
