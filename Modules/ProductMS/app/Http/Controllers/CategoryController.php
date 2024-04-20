<?php

namespace Modules\ProductMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ProductMS\app\Http\Services\CategoryService;
use Modules\ProductMS\app\Models\ProductCategory;

class CategoryController extends Controller
{

    protected CategoryService $categoryService;
    public array $data = [];

    /**
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = ProductCategory::with('status')->whereHas('status', function ($query) {
            $query->where('name', 'فعال');
        })->get();

        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $status = ProductCategory::GetAllStatuses()->where('name', '=', 'فعال')->first();
        $data['statusID']=$status->id;
        $category = $this->categoryService->store($data);

        if ($category instanceof \Exception) {
            return response()->json($category->getMessage());

        }

        return response()->json($category);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $category = ProductCategory::with('children','parent')->findOrFail($id);

        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $data = $request->all();

        $category = $this->categoryService->update($data, $id);

        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $category = ProductCategory::findOrFail($id);
        $status = ProductCategory::GetAllStatuses()->where('name', '=', 'غیرفعال')->first();

        $category->status_id = $status->id;
        $category->save();

        return response()->json($this->data);
    }
}
