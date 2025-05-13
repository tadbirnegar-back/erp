<?php

namespace Modules\BDM\app\Http\Traits;


use Modules\AAA\app\Http\Enums\PermissionTypesEnum;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Models\Owner;
use Modules\VCM\app\Models\VcmVersions;

trait OwnersTrait
{
    public function makeOwners($dossierID , $personID , $isMainOwner)
    {
        Owner::create([
            'dossier_id' => $dossierID,
            'person_id' => $personID,
            'is_main_owner' => $isMainOwner,
        ]);
    }
}
