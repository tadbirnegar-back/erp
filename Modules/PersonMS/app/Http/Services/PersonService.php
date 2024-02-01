<?php

namespace Modules\PersonMS\App\Http\Services;

use Modules\PersonMS\app\Http\Repositories\PersonRepository;

class PersonService
{
    protected $personRepository;


    public function __construct(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
    }


    public function personExists(string $nationalCode)
    {
        return $this->personRepository->personExists($nationalCode);
    }
}
