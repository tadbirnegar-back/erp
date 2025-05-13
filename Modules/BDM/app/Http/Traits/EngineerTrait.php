<?php

namespace Modules\BDM\app\Http\Traits;


use Carbon\Carbon;
use Modules\AAA\app\Http\Enums\PermissionTypesEnum;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Http\Enums\DocumentsNameEnum;
use Modules\BDM\app\Http\Enums\DossierStatusesEnum;
use Modules\BDM\app\Http\Enums\PermitStatusesEnum;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\DossierStatus;
use Modules\BDM\app\Models\Engineer;
use Modules\BDM\app\Models\EngineerBuilding;
use Modules\BDM\app\Models\Form;
use Modules\BDM\app\Models\LicenseDocument;
use Modules\BDM\app\Models\PermitStatus;
use Modules\PersonMS\app\Http\Enums\PersonLicensesEnums;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;
use Modules\VCM\app\Models\VcmVersions;

trait EngineerTrait
{
    public function storeEngineer($data , $personId)
    {
        $engineer = Engineer::where('person_id' , $personId)->first();
        if(!$engineer){
            Engineer::create([
                'person_id' => $personId,
                'dossier_number' => $data['dossierNumber'],
                'registration_number' => $data['registrationNumber'],
                'working_file_id' => $data['workingFileID'],
            ]);
        }

    }
}
