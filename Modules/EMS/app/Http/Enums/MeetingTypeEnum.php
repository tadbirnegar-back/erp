<?php

namespace Modules\EMS\app\Http\Enums;

enum MeetingTypeEnum: string
{
    case SHURA_MEETING = 'جلسه شورا روستا';
    case HEYAAT_MEETING = 'جلسه هیئت تطبیق';
    case OLGOO = 'الگو';
}
