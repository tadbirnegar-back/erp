<?php

namespace Modules\ODOC\app\Http\Enums;

use Modules\BDM\app\Models\BuildingDossier;

enum OdocDocumentComponentsTypeEnum: string
{
    case BONYADE_MASKAN = 'HousingFoundationPDF';

    case FORM_2 = "BuildingDossierIssuancePDF";
}
