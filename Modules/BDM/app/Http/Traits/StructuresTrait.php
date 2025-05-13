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

        if(isset($data['buildings'])){
            $buildings = json_decode($data['buildings']);
            foreach ($buildings as $building) {
                $building = Building::create([
                    'dossier_id' => $dossierID,
                    'app_id' => $building->app_id,
                    'floor_type_id' => $building->floor_type_id,
                    'floor_number_id' => $building->floor_number_id,
                    'all_corbelling_area' => $building->all_corbelling_area,
                    'floor_height' => $building->floor_height,
                    'building_area' => $building->building_area,
                    'storage_area' => $building->storage_area,
                    'stairs_area' => $building->stairs_area,
                    'elevator_shaft' => $building->elevator_shaft,
                    'parking_area' => $building->parking_area,
                    'corbelling_area' => $building->corbelling_area,
                    'duct_area' => $building->duct_area,
                    'other_parts_area' => $building->other_parts_area,
                    'is_existed' => $building->is_existed,
                ]);

                Structure::create([
                    'dossier_id' => $dossierID,
                    'structureable_id' => $building->id,
                    'structureable_type' => Building::class,
                ]);
            }
        }


        if (isset($data['Partitionings'])) {
            $partitionings = json_decode($data['Partitionings']);
            foreach ($partitionings as $partitioning) {
                $partitioning = Partitioning::create([
                    'height' => $partitioning->height,
                    'partitioning_type_id' => $partitioning->partitioning_type_id,
                    'app_id' => $partitioning->app_id,
                ]);
                $structure = Structure::where('dossier_id' , $dossierID)
                    ->where('structureable_type' , Partitioning::class)
                    ->first();
                if($structure){
                    $structure->structureable_id = $partitioning->id;
                    $structure->save();
                }else{
                    Structure::create([
                        'dossier_id' => $dossierID,
                        'structureable_id' => $partitioning->id,
                        'structureable_type' => Partitioning::class,
                    ]);
                }

            }
        }

        if (isset($data['Parkings'])) {
            $parkings = json_decode($data['Parkings']);
            foreach ($parkings as $parking) {
                $parking = Parking::create([
                    'height' => $parking->height,
                    'length' => $parking->length,
                    'width' => $parking->width,
                    'app_id' => $parking->app_id,
                ]);
                $structure = Structure::where('dossier_id' , $dossierID)
                    ->where('structureable_type' , Parking::class)
                    ->first();

                if(!$structure){
                    Structure::create([
                        'dossier_id' => $dossierID,
                        'structureable_id' => $parking->id,
                        'structureable_type' => Parking::class,
                        'app_id' => $parking->app_id,
                    ]);
                }else{
                    $structure->structureable_id = $parking->id;
                    $structure->save();
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
                    'app_id' => $pool->app_id,
                ]);
                $structure = Structure::where('dossier_id' , $dossierID)
                    ->where('structureable_type' , Pool::class)
                    ->first();

                if(!$structure){
                    Structure::create([
                        'dossier_id' => $dossierID,
                        'structureable_id' => $pool->id,
                        'structureable_type' => Pool::class,
                    ]);
                }else{
                    $structure->structureable_id = $pool->id;
                    $structure->save();
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
                    'app_id' => $pavilion->app_id,
                ]);
                $structure = Structure::where('dossier_id' , $dossierID)
                    ->where('structureable_type' , Pavilion::class)
                    ->first();

                if(!$structure){
                    Structure::create([
                        'dossier_id' => $dossierID,
                        'structureable_id' => $pavilion->id,
                        'structureable_type' => Pavilion::class,
                    ]);
                }else{
                    $structure->structureable_id = $pavilion->id;
                    $structure->save();
                }
            }
        }

        if(isset($data['BuildingDeleteIds']))
        {
            $deletesIds = json_decode($data['BuildingDeleteIds']);
            foreach ($deletesIds as $deleteId) {
                $structure = Structure::where('id' , $deleteId)->first();
                $bilding = Building::where('id' , $structure->structureable_id)->delete();
                $structure->delete();

            }
        }

        if(isset($data['ParkingDeleteIds']))
        {
            $deletesIds = json_decode($data['ParkingDeleteIds']);
            foreach ($deletesIds as $deleteId) {

                $structure = Structure::where('id' , $deleteId)->first();
                $parking = Parking::where('id' , $structure->structureable_id)->delete();
                $structure->delete();
            }
        }

        if(isset($data['PartitioningDeleteIds']))
        {
            $deletesIds = json_decode($data['PartitioningDeleteIds']);
            foreach ($deletesIds as $deleteId) {
                $structure = Structure::where('id' , $deleteId)->first();
                $partitioning = Partitioning::where('id' , $structure->structureable_id)->delete();
                $structure->delete();
            }
        }

        if(isset($data['PoolDeleteIds']))
        {
            $deletesIds = json_decode($data['PoolDeleteIds']);
            foreach ($deletesIds as $deleteId) {
                $structure = Structure::where('id' , $deleteId)->first();
                $pool = Pool::where('id' , $structure->structureable_id)->delete();
                $structure->delete();
            }
        }

        if(isset($data['PavilionDeleteIds']))
        {
            $deletesIds = json_decode($data['PavilionDeleteIds']);
            foreach ($deletesIds as $deleteId) {
                $structure = Structure::where('id' , $deleteId)->first();
                $pavilion = Pavilion::where('id' , $structure->structureable_id)->delete();
                $structure->delete();
            }
        }
    }
}
