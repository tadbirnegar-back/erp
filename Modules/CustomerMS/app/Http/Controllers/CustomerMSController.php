<?php

namespace Modules\CustomerMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AddressMS\app\services\AddressService;
use Modules\CustomerMS\app\Http\Services\CustomerService;
use Modules\PersonMS\app\Http\Services\PersonService;

class CustomerMSController extends Controller
{
    public array $data = [];
    protected $personService;
    protected $customerService;
    protected $addressService;

    public function __construct(CustomerService $customerService, PersonService $personService, AddressService $addressService)
    {
        $this->personService = $personService;
        $this->customerService = $customerService;
        $this->addressService = $addressService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $data = $request->all();
        $result = $this->customerService->index($data);

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $data['userID'] = \Auth::user()->id;

        if ($request->personableType == 'natural') {

            if ($request->isNewNatural) {

                if ($request->isNewAddress) {
                    $address = $this->addressService->store($data);
                    if ($address instanceof \Exception) {
//                        return response()->json(['message' => $address->getMessage()], 500);
                        return response()->json(['message' => 'خطا در ایجاد مشتری'], 500);
                    }
                    $data['homeAddressID'] = $address->id;
                }

                $naturalResult = $this->personService->naturalStore($data);
                if ($naturalResult instanceof \Exception) {
//                    return response()->json(['message' => $naturalResult->getMessage()], 500);
                    return response()->json(['message' => 'خطا در ایجاد مشتری'], 500);
                }
                $data['personID'] = $naturalResult->person->id;
            }


        } else {
            if ($request->isNewLegal) {

                if ($request->isNewAddress) {
                    $address = $this->addressService->store($data);
                    if ($address instanceof \Exception) {

                        return response()->json(['message' => 'خطا در ایجاد مشتری'], 500);
                    }
                    $data['businessAddressID'] = $address->id;
                }

                $legalPerson = $this->personService->legalStore($data);

                if ($legalPerson instanceof \Exception) {
                    return response()->json(['message' => 'خطا در ایجاد مشتری'], 500);
                }

                $data['personID'] = $legalPerson->person->id;

            } else {
                if ($this->customerService->isPersonCustomer($data['personID'])) {
                    $message = 'customer';

                    return response()->json(['message' => $message]);
//                    $data = $result;
                }
            }
        }

        $insertedCustomer = $this->customerService->store($data);

        if ($insertedCustomer instanceof \Exception) {
            return response()->json(['message' => 'خطا در ایجاد مشتری'], 500);
        }

        return response()->json($insertedCustomer);
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

    public function naturalIsCustomer(Request $request)
    {
        $result = $this->personService->naturalExists($request->nationalCode);
        if ($result == null) {
            $message = 'notFound';
            $data = null;
        } elseif ($this->customerService->isPersonCustomer($result->id)) {
            $message = 'customer';
            $data = $result;
        } else {
            $message = 'found';
            $data = $result;
        }

        return response()->json(['data' => $data, 'message' => $message]);
    }

    public function legalIsCustomer(Request $request)
    {
        $result = $this->personService->legalExists($request->businessName);


        return response()->json($result);
    }
}
