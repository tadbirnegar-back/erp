<?php

namespace Modules\ProductMS\app\Http\Repositories;

use Mockery\Exception;
use Modules\ProductMS\app\Models\VariantGroup;

class VariantGroupRepository
{

    protected VariantGroup $variantGroup;

    /**
     * @param VariantGroup $variantGroup
     */
    public function __construct(VariantGroup $variantGroup)
    {
        $this->variantGroup = $variantGroup;
    }


    public function index()
    {
        $variantGroups = $this->variantGroup::whereHas('status', function ($query) {
            $query->where('name', 'فعال');
        })->with(['variants' => function ($query) {
            $query->whereHas('status', function ($query) {
                $query->where('name', 'فعال');
            });
        }])->get();

        return $variantGroups;
    }

    public function store(array $data)
    {

        try {
            \DB::beginTransaction();
            /**
             * @var VariantGroup $vGroup
             */
            $vGroup = new $this->variantGroup();
            $vGroup->name = $data['variantGroupName'];
            $vGroup->status_id = $data['groupStatusID'];
            $vGroup->save();
            \DB::commit();
            return $vGroup;
        } catch (Exception $e) {
            \DB::rollBack();
            return $e;
        }

    }

    public function show(int $id)
    {
        $variantGroup = VariantGroup::whereHas('status', function ($query) {
            $query->where('name', 'فعال');
        })->with(['variants' => function ($query) {
            $query->whereHas('status', function ($query) {
                $query->where('name', 'فعال');
            });
        }])->findOrFail($id);

        return $variantGroup;
    }

    public function update(array $data, int $id)
    {
        try {
            \DB::beginTransaction();
            $vGroup = $this->variantGroup::findOrFail($id);

            $vGroup->name = $data['variantGroupName'];
//            $vGroup->status_id = $data['groupStatusID'];
            $vGroup->save();
            \DB::commit();
            return $vGroup;
        } catch (Exception $e) {
            \DB::rollBack();
            return $e;
        }
    }


}
