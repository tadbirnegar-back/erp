<?php

namespace Modules\PersonMS\app\Http\Services;

use Modules\PersonMS\app\Http\Repositories\PersonRepository;

class PersonService
{
    protected $personRepository;


    public function __construct(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
    }


    public function naturalExists(string $nationalCode)
    {
        return $this->personRepository->naturalExists($nationalCode);
    }

    public function naturalStore(array $data){
        return $this->personRepository->naturalStore($data);
    }

    public function legalExists(string $name)
    {
        return $this->personRepository->legalExists($name);
    }

    public function legalStore(array $data)
    {
        return $this->personRepository->legalStore($data);
    }

    public function naturalUpdate(array $data,int $id)
    {
        return $this->personRepository->naturalUpdate($data, $id);
    }

    public function legalUpdate(array $data,$id)
    {
        return $this->personRepository->legalUpdate($data, $id);
    }
}
