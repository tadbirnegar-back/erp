<?php

namespace Modules\SettingsMS\app\Http\Traits;

use Modules\SettingsMS\app\Models\Setting;

trait SettingsTrait
{
    public function examNumbers()
    {
        $examNumberSetting = Setting::where('key', 'question_numbers_perExam')->first();
        $examNumber = $examNumberSetting ? $examNumberSetting->value : 0;
        return $examNumber;

    }
}
