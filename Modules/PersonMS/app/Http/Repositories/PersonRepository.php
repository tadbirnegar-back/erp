<?php

namespace Modules\PersonMS\app\Http\Repositories;

use Modules\PersonMS\app\Models\Person;

class PersonRepository
{
    protected $person;

    public function __construct(Person $person)
    {
        $this->person = $person;
    }

    public function personExists(string $nationalCode)
    {
        $result = $this->person::where('national_code' , '=', $nationalCode)->first();

        return $result;
    }

}
