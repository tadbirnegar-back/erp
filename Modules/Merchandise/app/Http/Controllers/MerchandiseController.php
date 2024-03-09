<?php

namespace Modules\Merchandise\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Merchandise\app\Http\Services\MerchandiseService;
use Modules\Merchandise\app\Models\MerchandiseProduct;
use Modules\ProductMS\app\Http\Services\CategoryService;
use Modules\ProductMS\app\Models\Product;
use Modules\ProductMS\app\Models\ProductCategory;
use Modules\ProductMS\app\Models\Unit;
use Modules\ProductMS\app\Models\Variant;

class MerchandiseController extends Controller
{
    public array $data = [];
    protected MerchandiseService $merchandiseService;
    protected CategoryService $categoryService;


    public function __construct(MerchandiseService $merchandiseService)
    {
        $this->merchandiseService = $merchandiseService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $pageNumber = $request->input('pageNumber', 1);
        $perPage = $request->input('perPage', 10);

        $result = $this->merchandiseService->index($pageNumber,$perPage);

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $parentData = json_decode($data['parent'], true);

        /**
         * @var MerchandiseProduct $parent
         */
        $parentData['userID'] = \Auth::user()->id;
        $parent = $this->merchandiseService->store($parentData);

        if ($parent instanceof \Exception) {
            return response()->json(['message' => 'خطا در ایجاد محصول'], 500);
        }


        if (isset($data['variants'])) {
            $variantData = json_decode($data['variants'], true);
            //generate combos of variations
            $combos = $this->uniqueCombinations($variantData);


            foreach ($combos as $combo) {
                $childProduct = [];
                $childProduct['parentID'] = $parent->product->id;
                $childProduct['variant'] = $combo;
                $childProduct['statusID'] = $parent->product->status_id;
                $variants = Variant::find($combo, ['name']);
                $childProduct['name'] = $parent->product->name;
                $childProduct['userID'] = \Auth::user()->id;
                $childProduct['salePrice'] = $parent->product->sale_price;

                /**
                 * @var Variant $item
                 */

                foreach ($variants as $key => $item) {
                    if (!(end($variants) === $key)) {
                        $childProduct['name'] .= ' - ' . $item->name;
                    } else {
                        $childProduct['name'] .= ' ' . $item->name;
                    }
                }

                if (isset($data['exceptions'])) {
                    $variantData = json_decode($data['exceptions'], true);

                    $comboAsText = implode(',', $combo);
                    if (array_key_exists($comboAsText, $variantData)) {
                        $childProduct['salePrice'] = $variantData[$comboAsText]['salePrice'] ?? null;
                        $childProduct['coverFileID'] = $variantData[$comboAsText]['coverFileID'] ?? null;
//                        $childProduct['name'] = $variantData[$comboAsText]['name'] ?? null;

                    }
                }

                $childProductResult = $this->merchandiseService->store($childProduct);
                if ($childProductResult instanceof \Exception) {
                    return response()->json(['message' => 'خطا در ایجاد محصول'], 500);
                }
            }
        }

        return response()->json($parent->product);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $merch = MerchandiseProduct::with('product.children.coverFile', 'product.children.variants', 'product.status', 'product.productCategory', 'product.coverFile', 'product.unit')->findOrFail($id);
        if (is_null($merch)) {
            return response()->json(['message' => 'محصولی با این مشخصات یافت نشد'], 404);
        }
        return response()->json($merch);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $data = $request->all();
        $merch = MerchandiseProduct::findOrFail($id);
        $parent = $merch->product;

        $update = $this->merchandiseService->update($data['parent'], $id);
        if ($update instanceof \Exception) {
            return response()->json(['message' => 'خطا در بروزرسانی محصول'], 500);
        }
        $children = $parent->children;
        if (!is_null($children)) {
            foreach ($children as $child) {
                $child->variants()->detach();
                $child->forceDelete();
            }
        }
        if (isset($data['variants'])) {

            $combos = $this->uniqueCombinations($data['variants']);


            foreach ($combos as $combo) {

                $childProduct['parentID'] = $parent->id;
                $childProduct['variant'] = $combo;
                $childProduct['statusID'] = $parent->status_id;


                if (isset($data['exceptions'])) {
                    $comboAsText = implode(',', $combo);
                    if (array_key_exists($comboAsText, $data['exceptions'])) {
                        $childProduct['salePrice'] = $data['exceptions'][$comboAsText]['price'] ?? null;
                        $childProduct['coverFileID'] = $data['exceptions'][$comboAsText]['avatar'] ?? null;
                        $childProduct['name'] = $data['exceptions'][$comboAsText]['name'] ?? null;

                    }
                }

                $childProduct = $this->merchandiseService->store($childProduct);
                if ($childProduct instanceof \Exception) {
                    return response()->json(['message' => 'خطا در بروزرسانی محصول'], 500);
                }
            }
        }

        return response()->json($this->data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    function uniqueCombinations($arrays)
    {

        $combinations = [];

        // Helper function for recursive generation
        function generateCombinations($current, $remaining, &$combinations)
        {
            if (count($remaining) == 1) {
                foreach ($remaining[0] as $element) {
                    $sortedCombination = array_merge($current, [$element]);
                    sort($sortedCombination); // Sort in ascending order
                    $combinations[] = $sortedCombination;
                }
                return;
            }

            foreach ($remaining[0] as $i => $element) {
                $current[] = $element;
                generateCombinations($current, array_slice($remaining, 1), $combinations);
                array_pop($current); // Backtrack
            }
        }

        generateCombinations([], $arrays, $combinations);

        return array_unique($combinations, SORT_REGULAR); // Ensure unique combinations
    }

    function searchArray($arr, $searchArr)
    {
        foreach ($arr as $subArr) {
            if (is_array($subArr) && count($subArr) === count($searchArr)) { // Check for same length
                // Sort both arrays before comparison
                $sortedSubArr = $subArr;
                sort($sortedSubArr);
                $sortedSearchArr = $searchArr;
                sort($sortedSearchArr);


                if ($sortedSubArr === $sortedSearchArr) { // Match after sorting
                    return $subArr;
                } else {
                    $result = $this->searchArray($subArr, $searchArr); // Recursive call
                    if ($result) {
                        return $result;
                    }
                }
            }
        }
        return null; // No match found
    }

    public function addBaseInfo()
    {
        $data['status'] = MerchandiseProduct::GetAllStatuses();
        $data['units'] = Unit::all();
        $data['category'] = ProductCategory::whereHas('status', function ($query) {
            $query->where('name', 'فعال');
        })->get();

        return response()->json($data);
    }
}
