<?php

namespace Modules\ProductMS\app\Http\Services;

use Modules\ProductMS\app\Http\Repositories\CategoryRepository;

class CategoryService
{
    protected CategoryRepository $categoryRepository;

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }


    public function store(array $data)
    {
        return $this->categoryRepository->store($data);
    }

    public function update(array $data, int $id)
    {
        return $this->categoryRepository->update($data,$id);
    }


}
