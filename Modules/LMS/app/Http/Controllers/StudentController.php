<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AAA\app\Http\Repositories\UserRepository;
use Modules\AddressMS\app\Repositories\AddressRepository;
use Modules\CustomerMS\app\Http\Repositories\CustomerRepository;
use Modules\HRMS\app\Http\Repositories\EmployeeRepository;
use Modules\LMS\app\Http\Repository\StudentRepository;
use Modules\PersonMS\app\Http\Repositories\PersonRepository;

class StudentController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();

        try {
            \DB::beginTransaction();
            $personService = new PersonRepository();

            $personResult = isset($request->personID)
                ? $personService->naturalUpdate($data, $data['personID'])
                : $personService->naturalStore($data);


            if ($personResult instanceof \Exception) {
                return response()->json($personResult->getMessage());
            }

            $data['personID'] = $personResult->person->id;

            $data['userID'] = \Auth::user()->id ?? null;

            if ($request->isNewAddress) {
                $addressService = new AddressRepository();
                $address = $addressService->store($data);

//                if ($address instanceof \Exception) {
//                    return response()->json(['message' => 'خطا در وارد کردن آدرس'], 500);
//                }

                $data['homeAddressID'] = $address->id;

            }


            $userService = new UserRepository();
            $userResult = $userService->store($data);
            $data['userID'] = $userResult->id;

            //check if person is employee and insert it if not
            $employeeService = new EmployeeRepository();
            $personEmployee = $employeeService->isPersonEmployee($personResult->id) ?? $employeeService->store($data);


            $studentService = new StudentRepository();
            $customerResult = $studentService->isPersonStudent($data['personID']) ?? $studentService->store($data);
            \DB::commit();

            return response()->json([$customerResult]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
            return response()->json(['message' => 'خطا در ایجاد فراگیر جدید'], 500);

        }


    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    public function isPersonStudent(Request $request)
    {
        $personService = new PersonRepository();
        $studentService = new StudentRepository();
        $result = $personService->naturalExists($request->nationalCode);
        if ($result == null) {
            $message = 'notFound';
            $data = null;
        } elseif ($studentService->isPersonStudent($result->id)) {
            $message = 'student';
            $data = $result;
        } else {
            $message = 'found';
            $data = $result;
        }

        return response()->json(['data' => $data, 'message' => $message]);

    }

}
