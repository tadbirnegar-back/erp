<?php

namespace Modules\ODOC\app\Http\Enums;

use Modules\BDM\app\Models\BuildingDossier;

enum DocumentStatusEnum: string
{
    case COMPLETED = 'امضا شده';
    case PENDING = 'در انتظار تایید';
    case DECLINED = 'منسوخ شده';

}
