<?php

namespace Modules\BranchMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\AddressMS\app\Models\Address;
use Modules\BranchMS\app\Models\Branch;

class BranchMSController extends Controller
{
    public array $data = [];

    /**
     * @authenticated
     */
    public function index(): JsonResponse
    {
        $branches = Branch::with(['status:name'])->select(['name']);

        return response()->json($branches);
    }

    /**
     * @authenticated
     * @bodyparams cityID int required the id of city that returned previously by cities of state api
     * @bodyparams addressTitle string required the title of address. Example: home
     * @bodyparams branchName string required the title of branch. Example: urmia
     * @bodyparams branchPhone string required the phone number of branch. Example: 04435231234
     * @bodyparams addressDetail string required the detail of the address to insert. Example: Imam Street
     * @bodyparams postalCode string the postal code of the address default is null
     * @bodyparams statusID int the status of the address, default is active
     *
     * @
     */
    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            $address = new Address();
            $address->title = $request->addressTitle;
            $address->detail = $request->addressDetail;
            $address->latitude = $request->latitude ?? null;
            $address->longitude = $request->longitude ?? null;
            $address->postal_code = $request->postalCode ?? null;
            $address->status_id = $request->statusID;
            $address->creator_id = $user->id;
            $address->save();


            $branch = new Branch();
            $branch->name = $request->branchName;
            $branch->phone_number = $request->branchPhone;
            $branch->address_id = $address->id;
            $branch->save();

            DB::commit();
            return response()->json(['message' => 'با موفقیت وارد شد',
                'branchID' => $branch->id,
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'خطا سرور'], 500);
        }

    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
//        $branch = Branch::findOrFail($id);
        $branch = Branch::with('departments')->findOrFail($id);
        if (is_null($branch)) {
            return response()->json([
                'error' => 'شعبه ای با این مشخصات یافت نشد'
            ], 404);
        }

        return response()->json($branch);
    }

    /**
     * @authenticated
     * @bodyparams cityID int required the id of city that returned previously by cities of state api
     * @bodyparams addressTitle string required the title of address. Example: home
     * @bodyparams branchName string required the title of branch. Example: urmia
     * @bodyparams branchPhone string required the phone number of branch. Example: 04435231234
     * @bodyparams addressDetail string required the detail of the address to insert. Example: Imam Street
     * @bodyparams postalCode string the postal code of the address default is null
     * @bodyparams statusID int the status of the address, default is active
     * @bodyparams isNewAddress bool true if the address is new to insert otherwise you should provide addressID
     * @bodyparams addressID int if trying to use and existing address
     *
     *
     * @response scenario="success" {message : 'شعبه با موفقیت ویرایش شد',branchID : 'id'}
     * @response status=404 scenario="branch not found"  {error : 'شعبه ای با این مشخصات یافت نشد'}
     * @response status=500 scenario="server error"  {error : 'خطا سمت سرور'}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $branch = Branch::findOrFail($id);
        if (is_null($branch)) {
            return response()->json([
                'error' => 'شعبه ای با این مشخصات یافت نشد'
            ], 404);
        }
        try {

            DB::beginTransaction();

            if ($request->isNewAddress) {
                $user = Auth::user();

                $address = new Address();
                $address->title = $request->addressTitle;
                $address->detail = $request->addressDetail;
                $address->latitude = $request->latitude ?? null;
                $address->longitude = $request->longitude ?? null;
                $address->postal_code = $request->postalCode ?? null;
                $address->status_id = $request->statusID;
                $address->creator_id = $user->id;
                $address->save();
            }else{
                $address = Address::findOrFail($request->addressID);
            }


            $branch = new Branch();
            $branch->name = $request->branchName;
            $branch->phone_number = $request->branchPhone;
            $branch->address_id = $address->id;
            $branch->save();

            DB::commit();

            return response()->json(['message' => 'شعبه با موفقیت ویرایش شد',
                'branchID' => $branch->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'خطا سمت سرور'], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }
}
