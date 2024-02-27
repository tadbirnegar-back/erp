<?php

namespace Modules\Merchandise\app\Http\Repositories;

use Mockery\Exception;
use Modules\Merchandise\app\Models\MerchandiseProduct;
use Modules\ProductMS\app\Models\Product;

class MerchandiseRepository
{

    protected MerchandiseProduct $merchandise;
    protected Product $product;

    public function __construct(MerchandiseProduct $merchandise, Product $product)
    {
        $this->merchandise = $merchandise;
        $this->product = $product;

    }


    public function store(array $data)
    {

        try {
            \DB::beginTransaction();

            /**
             * @var MerchandiseProduct $merch
             * @var Product $product
             */
            $merch = new $this->merchandise();


            $merch->package_breadth = $data['packageBreadth'] ?? null;
            $merch->package_height = $data['packageHeight'] ?? null;
            $merch->package_weight = $data['packageWeight'] ?? null;
            $merch->package_width = $data['packageWidth'] ?? null;
            $merch->save();

            $product = new $this->product();
            $product->name = $data['name'];
            $product->description = $data['description'] ?? null;
            $product->sale_price = $data['salePrice'] ?? null;
            $product->sku = $data['sku'] ?? null;
            $product->cover_file_id = $data['coverFileID'] ?? null;
            $product->creator_id = $data['userID'] ?? null;
            $product->parent_id = $data['parentID'] ?? null;
            $product->unit_id = $data['unitID'] ?? null;
            $product->product_category_id = $data['productCategoryID'] ?? null;

//            $status = $this->product::GetAllStatuses()->where('name', '=', 'فعال')->first();

            $product->status_id = $data['statusID'];


            $merch->product()->save($product);

            if (isset($data['variant'])) {
                $product->variants()->sync($data['variant']);
            }

            \DB::commit();
            return $merch;
        } catch (Exception $e) {
            \DB::rollBack();
            return $e;

        }


    }

    public function update(array $data,int $id)
    {

        try {
            \DB::beginTransaction();

            /**
             * @var MerchandiseProduct $merch
             * @var Product $product
             */
            $merch = $this->merchandise::findOrFail($id);


            $merch->package_breadth = $data['packageBreadth'] ?? null;
            $merch->package_height = $data['packageHeight'] ?? null;
            $merch->package_weight = $data['packageWeight'] ?? null;
            $merch->package_width = $data['packageWidth'] ?? null;
            $merch->save();

            $product = $merch->product;
            $product->name = $data['name'];
            $product->description = $data['description'] ?? null;
            $product->sale_price = $data['salePrice'] ?? null;
            $product->sku = $data['sku'] ?? null;
            $product->cover_file_id = $data['coverFileID'] ?? null;
            $product->creator_id = $data['userID'] ?? null;
            $product->parent_id = $data['parentID'] ?? null;
            $product->unit_id = $data['unitID'] ?? null;
            $product->product_category_id = $data['productCategoryID'] ?? null;

//            $status = $this->product::GetAllStatuses()->where('name', '=', 'فعال')->first();

            $product->status_id = $data['statusID'];


            $merch->product()->save($product);

//            if (isset($data['variant'])) {
//                $product->variants()->sync($data['variant']);
//            }

            \DB::commit();
            return $merch;
        } catch (Exception $e) {
            \DB::rollBack();
            return $e;

        }
    }
}
