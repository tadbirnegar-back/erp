<?php

namespace Modules\BDM\app\Http\Enums;

enum DossierStatusesEnum : string
{
    case WAIT_TO_DONE = 'در حال تکمیل';
    case DONE = 'تکمیل شده';
    case EXPIRED = 'منقضی شده';


}
