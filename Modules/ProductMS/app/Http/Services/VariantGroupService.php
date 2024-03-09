<?php

namespace Modules\ProductMS\app\Http\Services;

use Modules\ProductMS\app\Http\Repositories\VariantGroupRepository;

class VariantGroupService
{
    protected VariantGroupRepository $variantGroupRepository;

    /**
     * @param VariantGroupRepository $variantGroupRepository
     */
    public function __construct(VariantGroupRepository $variantGroupRepository)
    {
        $this->variantGroupRepository = $variantGroupRepository;
    }

    public function index()
    {
        return $this->variantGroupRepository->index();
}
    public function store(array $data)
    {
        return $this->variantGroupRepository->store($data);
    }

    public function show(int $id)
    {
        return $this->variantGroupRepository->show($id);
    }
    public function update(array $data, int $id)
    {
        return $this->variantGroupRepository->update($data, $id);
    }


}
