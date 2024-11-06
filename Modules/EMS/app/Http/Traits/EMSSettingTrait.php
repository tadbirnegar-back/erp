<?php

namespace Modules\EMS\app\Http\Traits;

use Modules\EMS\app\Http\Enums\SettingsEnum;
use Modules\SettingsMS\app\Models\Setting;

trait EMSSettingTrait
{

    public function updateConsultingAutoMoghayerat($value)
    {
        $setting = Setting::updateOrCreate(
            ['key' => SettingsEnum::CONSULTING_AUTO_MOGHAYERAT->value],
            ['value' => $value]
        );
        return $setting;
    }

    public function getConsultingAutoMoghayerat()
    {
        return Setting::where('key', SettingsEnum::CONSULTING_AUTO_MOGHAYERAT->value)->first();
    }

    public function updateBoardAutoMoghayerat($value)
    {
        $setting = Setting::updateOrCreate(
            ['key' => SettingsEnum::BOARD_AUTO_MOGHAYERAT->value],
            ['value' => $value]
        );
        return $setting;
    }

    public function getBoardAutoMoghayerat()
    {
        return Setting::where('key', SettingsEnum::BOARD_AUTO_MOGHAYERAT->value)->first();
    }

    public function updateEnactmentLimitPerMeeting($value)
    {
        $setting = Setting::updateOrCreate(
            ['key' => SettingsEnum::ENACTMENT_LIMIT_PER_MEETING->value],
            ['value' => $value]
        );

        return $setting;
    }

    public function getEnactmentLimitPerMeeting()
    {
        return Setting::where('key', SettingsEnum::ENACTMENT_LIMIT_PER_MEETING->value)->first();
    }

    public function updateShouraMaxMeetingDateDaysAgo($value)
    {
        $setting = Setting::updateOrCreate(
            ['key' => SettingsEnum::SHOURA_MAX_MEETING_DATE_DAYS_AGO->value],
            ['value' => $value]
        );

        return $setting;
    }

    public function getShouraMaxMeetingDateDaysAgo()
    {
        return Setting::where('key', SettingsEnum::SHOURA_MAX_MEETING_DATE_DAYS_AGO->value)->first();
    }


}
