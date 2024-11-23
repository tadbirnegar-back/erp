<?php

namespace Modules\EMS\app\Http\Enums;

enum SettingsEnum: string
{
    case CONSULTING_AUTO_MOGHAYERAT = 'ems_consulting_auto_moghayerat_count_down';
    case BOARD_AUTO_MOGHAYERAT = 'ems_board_auto_moghayerat_count_down';
    case ENACTMENT_LIMIT_PER_MEETING = 'ems_enactment_limit_per_meeting';
    case SHOURA_MAX_MEETING_DATE_DAYS_AGO = 'ems_shoura_max_meeting_date_days_ago';
    case MAX_DAY_FOR_RECEPTION = 'ems_max_day_for_reception';
}
