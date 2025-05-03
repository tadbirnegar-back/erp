<?php

namespace Modules\BDM\app\Http\Traits;


use Carbon\Carbon;
use Modules\AAA\app\Http\Enums\PermissionTypesEnum;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\VCM\app\Models\VcmVersions;

trait DossierTrait
{
    public function makeDossier( $ounitID , $ownershipTypeID)
    {
        $dossier = BuildingDossier::create([
            'tracking_code' => null,
            'created_date' => now(),
        ]);

        //Year , Month , Day , OunitID , OwnershipTypeID , dossierID
        $date = convertGregorianToJalali(Carbon::now()->format('Y/m/d'));

        $date = explode('/' , $date);


        $trackingCode = convertToEnglishNumbersWithoutZeros($date[0]).convertToEnglishNumbersWithoutZeros($date[1]).convertToEnglishNumbersWithoutZeros($date[2]).$ounitID.$ownershipTypeID.$dossier->id;

        $dossier->tracking_code = $trackingCode;
        $dossier->save();
        return $dossier;
    }

}
