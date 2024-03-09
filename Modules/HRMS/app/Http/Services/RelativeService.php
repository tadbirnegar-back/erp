<?php

namespace Modules\HRMS\app\Http\Services;

use Modules\HRMS\app\Http\Repositories\RelativeRepository;

class RelativeService
{
    protected RelativeRepository $relativeRepository;

    /**
     * @param RelativeRepository $relativeRepository
     */
    public function __construct(RelativeRepository $relativeRepository)
    {
        $this->relativeRepository = $relativeRepository;
    }

    public function store(array $data)
    {
        return $this->relativeRepository->store($data);
    }


}
