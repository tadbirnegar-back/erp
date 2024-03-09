<?php

namespace Modules\ProductMS\app\Http\Services;

use Modules\ProductMS\app\Http\Repositories\VariantRepository;
use PhpParser\Node\Expr\Array_;

class VariantService
{

    protected VariantRepository $variantRepository;

    /**
     * @param VariantRepository $variantRepository
     */
    public function __construct(VariantRepository $variantRepository)
    {
        $this->variantRepository = $variantRepository;
    }


    public function store(array $data)
    {
        return $this->variantRepository->store($data);
    }

    public function update(array $data, int $id)
    {
        return $this->variantRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->variantRepository->destroy($id);
    }



}
