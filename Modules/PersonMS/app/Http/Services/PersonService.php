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
        $result = $this->personRepository->naturalExists($nationalCode);

        if (!is_null($result)) {
//            $result->avatar->slug = url('/') . '/' . $result->avatar->slug;
            $result->personable_type = 'natural';

        }

        return $result;
    }

    public function naturalStore(array $data)
    {
        $result = $this->personRepository->naturalStore($data);
//        if (!$result instanceof \Exception) {
//            $result->person->avatar->slug = url('/') . '/' . $result->person->avatar->slug;
//            $result->person->personable_type = 'natural';
//
//        }
        return $result;
    }

    public function legalExists(string $name)
    {
        return $this->personRepository->legalExists($name);
    }

    public function legalStore(array $data)
    {
        return $this->personRepository->legalStore($data);
    }

    public function naturalUpdate(array $data, int $id)
    {
        return $this->personRepository->naturalUpdate($data, $id);
    }

    public function legalUpdate(array $data, $id)
    {
        return $this->personRepository->legalUpdate($data, $id);
    }
}
