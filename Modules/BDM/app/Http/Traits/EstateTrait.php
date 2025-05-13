<?php

namespace Modules\BDM\app\Http\Traits;


use Illuminate\Support\Number;
use Modules\AAA\app\Http\Enums\PermissionTypesEnum;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Http\Enums\BdmTypesEnum;
use Modules\BDM\app\Http\Enums\DossierStatusesEnum;
use Modules\BDM\app\Http\Enums\EstateConditionsEnum;
use Modules\BDM\app\Http\Enums\FieldConditionsEnum;
use Modules\BDM\app\Http\Enums\FloorNumbersEnum;
use Modules\BDM\app\Http\Enums\GeographicalCordinatesTypesEnum;
use Modules\BDM\app\Http\Enums\PlaceTypesEnum;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\Estate;
use Modules\BDM\app\Models\EstateAppSet;
use Modules\BDM\app\Models\EstateAppSuggest;
use Modules\BDM\app\Models\EstateUtm;
use Modules\BDM\app\Models\GeographicalCordinate;
use Modules\FileMS\app\Models\File;
use Modules\PFM\app\Models\Application;
use Modules\VCM\app\Models\VcmVersions;

trait EstateTrait
{
    public function makeEstate($data, $dossierID)
    {
        $estate = Estate::create([
            'ounit_id' => $data['ounitID'],
            'ownership_type_id' => $data['ownershipTypeID'],
            'part' => $data['part'] ?? null,
            'transfer_type_id' => $data['transferTypeID'] ?? null,
            'postal_code' => $data['postalCode'] ?? null,
            'address' => $data['address'] ?? null,
            'ounit_number' => $data['ounitNumber'] ?? null,
            'main' => $data['main'] ?? null,
            'minor' => $data['minor'] ?? null,
            'deal_number' => $data['dealNumber'] ?? null,
            'building_number' => $data['buildingNumber'] ?? null,
            'dossier_id' => $dossierID,
            'area' => $data['area'] ?? null,
            'created_date' => isset($data['created_date']) ? convertPersianToGregorianBothHaveTimeAndDont($data['created_date']) : now(),
            'request_date' => isset($data['request_date']) ? convertPersianToGregorianBothHaveTimeAndDont($data['request_date']) : now(),
        ]);


        EstateAppSuggest::create([
            'estate_id' => $estate->id,
            'app_id' => $data['appID'],
        ]);


        $typeID = GeographicalCordinatesTypesEnum::SUBMITTED->id();

        GeographicalCordinate::create([
            'west' => $data['west'],
            'east' => $data['east'],
            'north' => $data['north'],
            'south' => $data['south'],
            'type_id' => $typeID,
            'dossier_id' => $dossierID,
        ]);
    }

    public function getGeoLocations($dossierID)
    {
        $query = GeographicalCordinate::where('dossier_id', $dossierID)->first();
        return $query;
    }

    public function getGeoLocationList()
    {
        return GeographicalCordinatesTypesEnum::listWithIds();
    }

    public function getAppsList()
    {
        return Application::select('id', 'name')->get();
    }

    public function getFloorNumbers($dossierID)
    {
        $query = FloorNumbersEnum::listWithIds();
        return $query;
    }


    public function getArea($dossierID)
    {
        $query = Estate::where('dossier_id', $dossierID)->first();
        return $query->area;
    }

    public function getBdmType($dossierID)
    {
        $dossier = BuildingDossier::find($dossierID);
        $bdmType = BdmTypesEnum::getNameById($dossier->bdm_type_id);

        return $bdmType;
    }

    public function insertEstateDatas($dossierID, $data)
    {
        $estate = Estate::where('dossier_id', $dossierID)->first();
        $estate->update([
            'allow_floor' => $data['allow_floor'] ?? $estate->allow_floor,
            'allow_floor_height' => $data['allow_floor_height'] ?? $estate->allow_floor_height,
            'allow_height' => $data['allow_height'] ?? $estate->allow_height,
            'area_after_observe' => $data['area_after_observe'] ?? $estate->area_after_observe,
            'area_before_observe' => $data['area_before_observe'] ?? $estate->area_before_observe,
            'density_percent' => $data['density_percent'] ?? $estate->density_percent,
            'floor_area' => $data['floor_area'] ?? $estate->floor_area,
            'form_date' => convertPersianToGregorianBothHaveTimeAndDont($data['form_date']),
            'form_number' => $data['form_number'] ?? $estate->form_number,
            'form_trace_code' => $data['form_trace_code'] ?? $estate->form_trace_code,
            'occupation_amount' => $data['occupation_amount'] ?? $estate->occupation_amount,
            'occupation_percent' => $data['occupation_percent'] ?? $estate->occupation_percent,
            'tree_count' => $data['tree_count'] ?? null,
            'propery_sketch_file_id' => $data['propery_sketch_file_id'] ?? $estate->propery_sketch_file_id,
            'place_type_id' => $data['place_type_id'] ?? $estate->place_type_id,
            'building_status_id' => $data['estate_status_id'] ?? $estate->building_status_id,
            'field_status_id' => $data['field_status_id'] ?? $estate->field_status_id,
        ]);
        $this->insertUMTDatas($estate->id, $data);
        $this->insertAppSets($estate->id, $data);
        $this->insertGeoLocations($dossierID, $data);
        if (isset($data['deleted_utms'])) {
            $this->deleteUTMs($data['deleted_utms']);
        }
    }

    public function deleteUTMs($utms)
    {
        $utms = json_decode($utms);
        foreach ($utms as $utm) {
            EstateUtm::find($utm)->delete();
        }
    }

    public function insertUMTDatas($estateID, $data)
    {
        $utms = json_decode($data['utms']);
        foreach ($utms as $utm) {
            if (isset($utm->id)) {
                EstateUtm::updateOrCreate(
                    ['id' => $utm->id, 'estate_id' => $estateID],
                    [
                        'x' => $utm->x,
                        'y' => $utm->y,
                        'zone' => $utm->zone,
                        'is_center' => $utm->is_center,
                    ]
                );

            }else{
                EstateUtm::create([
                    'estate_id' => $estateID,
                    'x' => $utm->x,
                    'y' => $utm->y,
                    'zone' => $utm->zone,
                    'is_center' => $utm->is_center,
                ]);
            }
        }
        }

        public
        function insertAppSets($estateID, $data)
        {
            $appIds = json_decode($data['apps']);
            $appID = $appIds[0];
            $app = EstateAppSet::where('estate_id', $estateID)->first();
            if ($app) {
                $app->app_id = $appID;
                $app->save();
            } else {
                EstateAppSet::create([
                    'estate_id' => $estateID,
                    'app_id' => $appID,
                ]);
            }
        }

        public
        function insertGeoLocations($dossierID, $data)
        {
            $geoLocations = json_decode($data['geoLocations']);
            foreach ($geoLocations as $geoLocation) {
                GeographicalCordinate::updateOrCreate(
                    [
                        'type_id' => $geoLocation->type_id,
                        'dossier_id' => $dossierID,
                    ],
                    [
                        'west' => $geoLocation->west,
                        'east' => $geoLocation->east,
                        'north' => $geoLocation->north,
                        'south' => $geoLocation->south,
                    ]
                );

            }
        }

        public
        function getPreviousData($dossierID)
        {
            $estate = Estate::where('dossier_id', $dossierID)->first();
            if ($estate->form_number != null) {
                $estate->estate_status_name = EstateConditionsEnum::getNameById($estate->building_status_id);
                $estate->field_status_name = FieldConditionsEnum::getNameById($estate->field_status_id);
                $estate->file = File::with('extension')->find($estate->propery_sketch_file_id);
                $estate->file->size_humanReadable = $estate->file->size;

                $settedApp = EstateAppSet::where('estate_id', $estate->id)->first();

                $settedApp->name = Application::find($settedApp->app_id)->name;

                $estate->app_data = $settedApp;

                $estate->place_type_name = PlaceTypesEnum::getNameById($estate->place_type_id);

                $estate->form_date = convertDateTimeGregorianToJalaliDateTime($estate->form_date);

                $utms = EstateUtm::where('estate_id', $estate->id)->get();
                $geoLocations = GeographicalCordinate::where('dossier_id', $dossierID)
                    ->orderBy('type_id')
                    ->get();
                $geoLocations->map(function ($item) {
                    $item->type_name = GeographicalCordinatesTypesEnum::getNameById($item->type_id);
                });

                return ['estate' => $estate, 'utms' => $utms, 'geoLocations' => $geoLocations];
            }
            return null;

        }
    }
