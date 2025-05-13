<?php

namespace Modules\BDM\app\Http\Traits;


use Modules\AAA\app\Http\Enums\PermissionTypesEnum;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Models\DossierLawyer;
use Modules\BDM\app\Models\Lawyers;
use Modules\VCM\app\Models\VcmVersions;

trait LawyersTrait
{
    public function insertLawyers($personId, $dossierID)
    {
        $lawyer = Lawyers::create([
            'person_id' => $personId,
        ]);

        DossierLawyer::create([
            'dossier_id' => $dossierID,
            'lawyer_id' => $lawyer->id,
        ]);

    }
}
