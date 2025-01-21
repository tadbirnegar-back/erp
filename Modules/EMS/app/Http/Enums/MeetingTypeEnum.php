<?php

namespace Modules\EMS\app\Http\Enums;

enum MeetingTypeEnum: string
{
    case SHURA_MEETING = 'جلسه شورا روستا';
    case SHURA_DISTRICT_MEETING = 'جلسه شورا بخش';
    case HEYAAT_MEETING = 'جلسه هیئت تطبیق';
    case OLGOO = 'الگو';

    case FREE_ZONE = 'جلسه منطقه آزاد';
}
