<?php

namespace Modules\AAA\app\Http\Services;

use Modules\AAA\app\Http\Repositories\UserRepository;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function isPersonUser(int $personID)
    {
        return $this->userRepository->isPersonUser($personID);
    }

    public function store(array $data)
    {
        return $this->userRepository->store($data);
    }

    public function show(int $id)
    {
        return $this->userRepository->show($id);
    }

    public function update(array $data, int $id)
    {
        return $this->userRepository->update($data, $id);
    }

}
