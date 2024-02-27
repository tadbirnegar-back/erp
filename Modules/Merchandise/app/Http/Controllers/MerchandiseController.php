<?php

namespace Modules\Merchandise\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Merchandise\app\Http\Services\MerchandiseService;
use Modules\Merchandise\app\Models\MerchandiseProduct;
use Modules\ProductMS\app\Models\Product;

class MerchandiseController extends Controller
{
    public array $data = [];
    protected MerchandiseService $merchandiseService;


    public function __construct(MerchandiseService $merchandiseService)
    {
        $this->merchandiseService = $merchandiseService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();

        /**
         * @var MerchandiseProduct $parent
         */
        $parent = $this->merchandiseService->store($data['parent']);

        if ($parent instanceof \Exception) {
            return response()->json(['message' => 'خطا در ایجاد محصول'], 500);
        }


        if (isset($data['variants'])) {
            //generate combos of variations
            $combos = $this->uniqueCombinations($data['variants']);


            foreach ($combos as $combo) {

                $childProduct['parentID'] = $parent->product->id;
                $childProduct['variant'] = $combo;
                $childProduct['statusID'] = $parent->product->status_id;


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
        $merch = MerchandiseProduct::with('product.children.coverFile','product.children.variants','product.status','product.productCategory','product.coverFile','product.unit')->findOrFail($id);
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

        $update = $this->merchandiseService->update($data['parent'],$id);
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
                    $result = searchArray($subArr, $searchArr); // Recursive call
                    if ($result) {
                        return $result;
                    }
                }
            }
        }
        return null; // No match found
    }
}
