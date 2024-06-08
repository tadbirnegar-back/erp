<?php

namespace Modules\HRMS\App\Http\Traits;

use Modules\HRMS\app\Models\Skill;

trait SkillTrait
{
    public function skillIndex()
    {
        $result = Skill::all();

        return $result;
    }

    public function skillStore(array $data)
    {


        $skill = new Skill();
        $skill->name = $data['skillName'];
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

            $skill->name = $data['skillName'];
            $skill->save();
            return $skill;


    }

    public function activeSkillStatus()
    {
        return Skill::GetAllStatuses()->firstWhere('name', '=', 'فعال');
    }

    public function inactiveSkillStatus()
    {
        return Skill::GetAllStatuses()->firstWhere('name', '=', 'غیرفعال');
    }

}
