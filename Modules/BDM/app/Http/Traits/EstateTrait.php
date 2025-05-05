<?php

namespace Modules\BDM\app\Http\Traits;


use Modules\AAA\app\Http\Enums\PermissionTypesEnum;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Http\Enums\BdmTypesEnum;
use Modules\BDM\app\Http\Enums\DossierStatusesEnum;
use Modules\BDM\app\Http\Enums\GeographicalCordinatesTypesEnum;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\Estate;
use Modules\BDM\app\Models\EstateAppSet;
use Modules\BDM\app\Models\EstateAppSuggest;
use Modules\BDM\app\Models\EstateUtm;
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

    public function getGeoLocationList()
    {
        return GeographicalCordinatesTypesEnum::listWithIds();
    }

    public function getArea($dossierID)
    {
        $query = Estate::where('dossier_id' , $dossierID)->first();
        return $query->area;
    }

    public function getBdmType($dossierID)
    {
        $dossier = BuildingDossier::find($dossierID);
        $bdmType = BdmTypesEnum::getNameById($dossier->bdm_type_id);

        return $bdmType;
    }

    public function insertEstateDatas($dossierID , $data)
    {
        $estate = Estate::where('dossier_id' , $dossierID)->first();
        $estate->update([
            'allow_floor' => $data['allow_floor'],
            'allow_floor_height' => $data['allow_floor_height'],
            'allow_height' => $data['allow_height'],
            'area_after_observe' => $data['area_after_observe'],
            'area_before_observe' => $data['area_before_observe'],
            'density_percent' => $data['density_percent'],
            'floor_area' => $data['floor_area'],
            'form_date' => convertPersianToGregorianBothHaveTimeAndDont($data['form_date']),
            'form_number' => $data['form_number'],
            'form_trace_code' => $data['form_trace_code'],
            'occupation_amount' => $data['occupation_amount'],
            'occupation_percent' => $data['occupation_percent'],
            'tree_count' => $data['tree_count'] ?? null,
            'propery_sketch_file_id' => $data['propery_sketch_file_id'],
        ]);
        $this->insertUMTDatas($estate->id , $data);
        $this->insertAppSets($estate->id , $data);
        $this->insertGeoLocations($dossierID , $data);
    }

    public function insertUMTDatas($estateID , $data)
    {
        $utms = json_decode($data['utms']);
        foreach ($utms as $utm) {
            EstateUtm::create([
                'estate_id' => $estateID,
                'x' => $utm->x,
                'y' => $utm->y,
                'zone' => $utm->zone,
            ]);
        }
    }

    public function insertAppSets($estateID , $data)
    {
        $appIds = json_decode($data['apps']);
        foreach ($appIds as $appId) {
            EstateAppSet::create([
                'estate_id' => $estateID,
                'app_id' => $appId,
            ]);
        }
    }

    public function insertGeoLocations($dossierID , $data)
    {
        $geoLocations = json_decode($data['geoLocations']);
        foreach ($geoLocations as $geoLocation) {
            GeographicalCordinate::create([
                'west' => $geoLocation->west,
                'east' => $geoLocation->east,
                'north' => $geoLocation->north,
                'south' => $geoLocation->south,
                'type_id' => $geoLocation->type_id,
                'dossier_id' => $dossierID,
            ]);
        }
    }
}
