<?php

namespace Modules\PFM\app\Http\Enums;

enum BookletStatusEnum: string
{
    case MOSAVAB = 'مصوب شده';

    case DAR_ENTEZAR = 'در انتظار تأیید';

    case PISHNAHAD_SHODE = 'پیشنهاد شده';


}
