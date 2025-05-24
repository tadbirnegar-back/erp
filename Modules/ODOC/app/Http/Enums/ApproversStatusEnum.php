<?php

namespace Modules\ODOC\app\Http\Enums;

use Modules\BDM\app\Models\BuildingDossier;

enum ApproversStatusEnum: string
{
    case ASSIGNED = 'امضا شده';
    case PENDING = 'در انتظار امضا';
    case DECLINED = 'منسوخ شده';

}
