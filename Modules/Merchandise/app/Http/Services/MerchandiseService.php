<?php

namespace Modules\Merchandise\app\Http\Services;

use Modules\Merchandise\app\Http\Repositories\MerchandiseRepository;

class MerchandiseService
{
    protected MerchandiseRepository $merchandiseRepository;


    public function __construct(MerchandiseRepository $merchandiseRepository)
    {
        $this->merchandiseRepository = $merchandiseRepository;
    }


    public function store(array $data)
    {
        return $this->merchandiseRepository->store($data);
    }

    public function update(array $data,int $id)
    {
        return $this->merchandiseRepository->update($data, $id);
    }

    public function index(int $pageNumber = 1, int $perPage = 10)
    {
        return $this->merchandiseRepository->index($pageNumber, $perPage);
    }

}
