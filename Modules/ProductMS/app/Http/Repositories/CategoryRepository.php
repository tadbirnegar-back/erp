<?php

namespace Modules\ProductMS\app\Http\Repositories;

use Modules\ProductMS\app\Models\ProductCategory;

class CategoryRepository
{
    protected ProductCategory $category;

    /**
     * @param ProductCategory $category
     */
    public function __construct(ProductCategory $category)
    {
        $this->category = $category;
    }

    public function store(array $data)
    {

        /**
         * @var ProductCategory $category
         */
        try {
            \DB::beginTransaction();
            $category = new $this->category();
            $category->name = $data['categoryName'];
            $category->parent_id = $data['categoryParentID'] ?? null;
            $category->status_id = $data['statusID'];
            $category->save();
            \DB::commit();
            return $category;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }
    }

    public function update(array $data, int $id)
    {
        /**
         * @var ProductCategory $category
         */
        try {
            \DB::beginTransaction();
            $category = $this->category::find($id);
            $category->name = $data['categoryName'];
            $category->parent_id = $data['categoryParentID'] ?? null;
            $category->save();
            \DB::commit();
            return $category;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }
    }


}
