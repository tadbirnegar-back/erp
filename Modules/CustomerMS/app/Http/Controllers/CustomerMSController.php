<?php

namespace Modules\CustomerMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;
use Modules\AddressMS\app\Traits\AddressTrait;
use Modules\CustomerMS\app\Http\Traits\CustomerTrait;
use Modules\CustomerMS\app\Models\Customer;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Natural;

class CustomerMSController extends Controller
{
    use PersonTrait, AddressTrait, CustomerTrait;


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $data = $request->all();
        $result = $this->customerIndex($data);

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $data['userID'] = Auth::user()->id;
        try {
            DB::beginTransaction();
            if ($request->personableType == 'natural') {

                if ($request->isNewNatural) {

                    if ($request->isNewAddress) {
                        $address = $this->addressStore($data);

                        $data['homeAddressID'] = $address->id;
                    }

                    $naturalResult = $this->naturalStore($data);

                    $data['personID'] = $naturalResult->person->id;
                }


            } else {
                if ($request->isNewLegal) {

                    if ($request->isNewAddress) {
                        $address = $this->addressStore($data);
                        $data['businessAddressID'] = $address->id;
                    }

                    $legalPerson = $this->legalStore($data);


                    $data['personID'] = $legalPerson->person->id;

                } else {
                    if ($this->isPersonCustomer($data['personID'])) {
                        $message = 'customer';

                        return response()->json(['message' => $message]);
                    }
                }
            }

            $insertedCustomer = $this->customerStore($data);

            DB::commit();

            return response()->json($insertedCustomer);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ثبت مشتری'], 500);

        }

    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $customer = Customer::findOr($id, function () {
            return response()->json(['message' => 'مشتری ای با این مشخصات یافت نشد'], 404);
        });

        $customer = $this->customerShow($id);


//        if (!is_null($customer->person->avatar)) {
//            $customer->person->avatar->slug = $customer->person->avatar->slug;
//
//        }

        if (\Str::contains(\request()->route()->uri(), 'customers/edit/{id}')) {
            $statuses = $this->allCustomerStats();

            $customer->statuses = $statuses;
        }

        return response()->json($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $customer = Customer::findOr($id, function () {
            return response()->json(['message' => 'مشتری ای با این مشخصات یافت نشد'], 404);
        });


        $data = $request->all();
        $data['userID'] = Auth::user()->id;
        try {
            DB::beginTransaction();
            $this->customerUpdate($data, $customer);

            if ($request->isNewAddress) {
                $address = $this->addressStore($data);

            }

            if ($customer->person->personable_type == Natural::class) {
                if (isset($address)) {
                    $data['homeAddressID'] = $address->id;
                }
                $personResult = $this->naturalUpdate($data, $customer->person->personable_id);

            } else {
                if (isset($address)) {
                    $data['businessAddressID'] = $address->id;
                }

                $personResult = $this->legalUpdate($data, $customer->person->personable_id);

            }
            DB::commit();

            return response()->json($personResult);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در بروزرسانی مشتری'], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $customer = Customer::findOr($id, function () {
            return response()->json(['message' => 'مشتری ای با این مشخصات یافت نشد'], 404);
        });


        try {
            DB::beginTransaction();
            $result = $this->customerDestroy($customer);
            DB::commit();

            return response()->json(['message' => 'مشتری با موفقیت حذف شد']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'مشتری با موفقیت حذف شد'], 500);
        }


    }

    public function naturalIsCustomer(Request $request)
    {
        $result = $this->naturalPersonExists($request->nationalCode);
        if ($result == null) {
            $message = 'notFound';
            $data = null;
        } elseif ($this->isPersonCustomer($result->id)) {
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
        $result = $this->legalExists($request->businessName);


        return response()->json($result);
    }
}
