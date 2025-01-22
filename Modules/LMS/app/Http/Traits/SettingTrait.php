<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\QuestionType;

trait SettingTrait
{
    public function showDropDowns()
    {
        $Q_type = QuestionType::select(['question_type.id']);
        return $Q_type;

    }
}
