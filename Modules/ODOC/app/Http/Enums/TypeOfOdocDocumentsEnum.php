<?php

namespace Modules\ODOC\app\Http\Enums;

use Modules\BDM\app\Models\BuildingDossier;

enum TypeOfOdocDocumentsEnum: string
{
    case VAREDAT ='و';
    case SADERAT ='ص';

    case DAKHELI ='د';

}
