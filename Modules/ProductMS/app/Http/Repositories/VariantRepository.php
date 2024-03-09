<?php

namespace Modules\ProductMS\app\Http\Repositories;

use Modules\ProductMS\app\Models\Variant;

class VariantRepository
{
    protected Variant $variant;

    /**
     * @param Variant $variant
     */
    public function __construct(Variant $variant)
    {
        $this->variant = $variant;
    }


    public function store(array $data)
    {

        try {
            \DB::beginTransaction();
            /**
             * @var Variant $variant
             */
            $variant = new $this->variant();
            $variant->name = $data['variantName'];
            $variant->status_id = $data['variantStatusID'];
            $variant->variant_group_id = $data['variantGroupID'];
            $variant->save();
            \DB::commit();
            return $variant;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }

    }

    public function update(array $data, int $id)
    {
        try {
            \DB::beginTransaction();
            /**
             * @var Variant $variant
             */
            $variant = $this->variant::findOrFail($id);
            $variant->name = $data['variantName'];
            $variant->status_id = $data['variantStatusID'];
            $variant->variant_group_id = $data['variantGroupID'];
            $variant->save();
            \DB::commit();
            return $variant;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }
    }


    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $vDeleteStatus = Variant::GetAllStatuses()->where('name', '=', 'غیرفعال')->first();

            $variant = $this->variant::findOrFail($id);
            $variant->status_id = $vDeleteStatus->id;
            $variant->save();
            \DB::commit();
            return $variant;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }

    }


}
