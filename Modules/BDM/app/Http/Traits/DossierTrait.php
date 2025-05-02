<?php

namespace Modules\BDM\app\Http\Traits;


use Modules\AAA\app\Http\Enums\PermissionTypesEnum;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\VCM\app\Models\VcmVersions;

trait DossierTrait
{
    public function makeDossier()
    {
        $dossier = BuildingDossier::create([
            'created_date' => now(),
        ]);
        return $dossier;
    }

}
