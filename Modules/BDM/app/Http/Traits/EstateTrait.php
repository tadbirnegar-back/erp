<?php

namespace Modules\BDM\app\Http\Traits;


use Modules\AAA\app\Http\Enums\PermissionTypesEnum;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Http\Enums\GeographicalCordinatesTypesEnum;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\Estate;
use Modules\BDM\app\Models\EstateAppSuggest;
use Modules\BDM\app\Models\GeographicalCordinate;
use Modules\VCM\app\Models\VcmVersions;

trait EstateTrait
{
    public function makeEstate($data , $dossierID)
    {
        Estate::create([
            'ounit_id' => $data['ounitID'],
            'ownership_type_id' => $data['ownershipTypeID'],
            'part' => $data['part'],
            'transfer_type_id' => $data['transferTypeID'] ?? null,
            'postal_code' => $data['postalCode'],
            'address' => $data['address'],
            'ounit_number' => $data['ounitNumber'],
            'main' => $data['main'],
            'minor' => $data['minor'],
            'deal_number' => $data['dealNumber'] ?? null,
            'building_number' => $data['buildingNumber'],
            'dossier_id' => $dossierID,
            'area' => $data['area'],
            'created_date' => isset($data['created_date']) ? convertPersianToGregorianBothHaveTimeAndDont($data['created_date']) : null,
            'request_date' => convertPersianToGregorianBothHaveTimeAndDont($data['request_date']),
        ]);

        $appIds = json_decode($data['apps']);
        foreach ($appIds as $appId) {
            EstateAppSuggest::create([
                'estate_id' => $dossierID,
                'app_id' => $appId,
            ]);
        }


        $typeID = GeographicalCordinatesTypesEnum::SUBMITTED->id();

        GeographicalCordinate::create([
            'west' => $data['west'],
            'east' => $data['east'],
            'north' => $data['north'],
            'south' => $data['south'],
            'type_id' => $typeID,
        ]);
    }

    public function getGeoLocations($dossierID)
    {
        $query = GeographicalCordinate::where('dossier_id' , $dossierID)->first();
        return $query;
    }

    public function getArea($dossierID)
    {
        $query = Estate::where('dossier_id' , $dossierID)->first();
        return $query->area;
    }
}
