<?php

namespace Modules\BDM\app\Http\Traits;


use Modules\BDM\app\Http\Enums\PermitStatusesEnum;
use Modules\BDM\app\Models\Building;
use Modules\BDM\app\Models\Parking;
use Modules\BDM\app\Models\Partitioning;
use Modules\BDM\app\Models\Pavilion;
use Modules\BDM\app\Models\PermitStatus;
use Modules\BDM\app\Models\Pool;
use Modules\BDM\app\Models\Structure;

trait StructuresTrait
{
    public function insertStructures($dossierID, $data)
    {

        $buildings = json_decode($data['buildings']);
        foreach ($buildings as $building) {
            $building = Building::create([
                'dossier_id' => $dossierID,
                'app_id' => $building->appID,
                'floor_type_id' => $building->floorTypeID,
                'floor_number_id' => $building->floorNumberID,
                'all_corbelling_area' => $building->allCorbellingArea,
                'floor_height' => $building->floorHeight,
                'building_area' => $building->buildingArea,
                'storage_area' => $building->storageArea,
                'stairs_area' => $building->stairsArea,
                'elevator_shaft' => $building->elevatorShaft,
                'parking_area' => $building->parkingArea,
                'corbelling_area' => $building->corbellingArea,
                'duct_area' => $building->ductArea,
                'other_parts_area' => $building->otherPartsArea,
                'is_existed' => $building->isExisted,
            ]);

            Structure::create([
                'dossier_id' => $dossierID,
                'structureable_id' => $building->id,
                'structureable_type' => Building::class,
            ]);
        }

        if (isset($data['Partitionings'])) {
            $partitionings = json_decode($data['Partitionings']);
            foreach ($partitionings as $partitioning) {
                $partitioning = Partitioning::create([
                    'height' => $partitioning->height,
                    'partitioning_type_id' => $partitioning->partitioning_type_id,
                ]);
                Structure::create([
                    'dossier_id' => $dossierID,
                    'structureable_id' => $partitioning->id,
                    'structureable_type' => Partitioning::class,
                ]);
            }
        }

        if (isset($data['Parkings'])) {
            $parkings = json_decode($data['Parkings']);
            foreach ($parkings as $parking) {
                $parking = Parking::create([
                    'height' => $parking->height,
                    'length' => $parking->length,
                    'width' => $parking->width,
                ]);
                $structure = Structure::where('structureable_id' , $parking->id)
                    ->where('structureable_type' , Parking::class)
                    ->first();

                if(!$structure){
                    Structure::create([
                        'dossier_id' => $dossierID,
                        'structureable_id' => $parking->id,
                        'structureable_type' => Parking::class,
                    ]);
                }else{
                    $structure->update([
                        'dossier_id' => $dossierID,
                        'structureable_id' => $parking->id,
                        'structureable_type' => Parking::class,
                    ]);
                }
            }
        }

        if(isset($data['Pools'])){
            $pools = json_decode($data['Pools']);
            foreach ($pools as $pool) {
                $pool = Pool::create([
                    'height' => $pool->height,
                    'width' => $pool->width,
                    'length' => $pool->length,
                ]);
                $structure = Structure::where('structureable_id' , $pool->id)
                    ->where('structureable_type' , Pool::class)
                    ->first();

                if(!$structure){
                    Structure::create([
                        'dossier_id' => $dossierID,
                        'structureable_id' => $pool->id,
                        'structureable_type' => Pool::class,
                    ]);
                }else{
                    $structure->update([
                        'dossier_id' => $dossierID,
                        'structureable_id' => $pool->id,
                        'structureable_type' => Pool::class,
                    ]);
                }
            }
        }


        if(isset($data['Pavilions'])){
            $pavilions = json_decode($data['Pavilions']);
            foreach ($pavilions as $pavilion) {
                $pavilion = Pavilion::create([
                    'height' => $pavilion->height,
                    'width' => $pavilion->width,
                    'length' => $pavilion->length,
                ]);
                $structure = Structure::where('structureable_id' , $pavilion->id)
                    ->where('structureable_type' , Pavilion::class)
                    ->first();

                if(!$structure){
                    Structure::create([
                        'dossier_id' => $dossierID,
                        'structureable_id' => $pavilion->id,
                        'structureable_type' => Pavilion::class,
                    ]);
                }else{
                    $structure->update([
                        'dossier_id' => $dossierID,
                        'structureable_id' => $pavilion->id,
                        'structureable_type' => Pavilion::class,
                    ]);
                }
            }
        }

        if(isset($data['BuildingDeleteIds']))
        {
            $deletesIds = json_decode($data['BuildingDeleteIds']);
            foreach ($deletesIds as $deleteId) {
                Building::where('id' , $deleteId)->delete();
                Structure::where('structureable_id' , $deleteId)
                    ->where('structureable_type' , Building::class)
                    ->delete();
            }
        }

        if(isset($data['ParkingDeleteIds']))
        {
            $deletesIds = json_decode($data['ParkingDeleteIds']);
            foreach ($deletesIds as $deleteId) {
                Parking::where('id' , $deleteId)->delete();
                Structure::where('structureable_id' , $deleteId)
                    ->where('structureable_type' , Parking::class)
                    ->delete();
            }
        }

        if(isset($data['PoolDeleteIds']))
        {
            $deletesIds = json_decode($data['PoolDeleteIds']);
            foreach ($deletesIds as $deleteId) {
                Pool::where('id' , $deleteId)->delete();
                Structure::where('structureable_id' , $deleteId)
                    ->where('structureable_type' , Pool::class)
                    ->delete();
            }
        }

        if(isset($data['PavilionDeleteIds']))
        {
            $deletesIds = json_decode($data['PavilionDeleteIds']);
            foreach ($deletesIds as $deleteId) {
                Pavilion::where('id' , $deleteId)->delete();
                Structure::where('structureable_id' , $deleteId)
                    ->where('structureable_type' , Pavilion::class)
                    ->delete();
            }
        }
    }
}
