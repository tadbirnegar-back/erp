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
//        $branches = Branch::whereHas('status', function ($query) {
//            $query->where('name', 'فعال')
//                ->where('branch_status.create_date', function($subQuery) {
//                    $subQuery->selectRaw('MAX(create_date)')
//                        ->from('branch_status')
//                        ->whereColumn('branch_id', 'branches.id');
//                });
//        })->with('status')->get();
        $branches = Branch::with('statuses')->get();
//        $branches = Branch::with(['status' => function ($query) {
//            $query->orderBy('id', 'desc')
//                ->limit(1);
//        }])->get();

        return response()->json($branches);
    }

    /**
     * @authenticated
     */
    public function indexActive()
    {
        $branches = Branch::whereHas('status', function ($query) {
            $query->where('name', 'فعال')
                ->where('branch_status.create_date', function ($subQuery) {
                    $subQuery->selectRaw('MAX(create_date)')
                        ->from('branch_status')
                        ->whereColumn('branch_id', 'branches.id');
                });
        })->get();

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
     *
     *
     * @
     */
    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            if ($request->isNewAddress) {
                $address = new Address();
                $address->title = $request->title;
                $address->detail = $request->address;
                $address->postal_code = $request->postalCode ?? null;
                $address->longitude = $request->longitude ?? null;
                $address->latitude = $request->latitude ?? null;
                $address->map_link = $request->mapLink ?? null;
                $address->city_id = $request->cityID;
                $address->status_id = Address::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
                $address->creator_id = \Auth::user()->id;
                $address->save();
                $addressID = $address->id;
            } else {
                $addressID = $request->branchAddressID;
            }

            $branch = new Branch();
            $branch->name = $request->name;
            $branch->phone_number = $request->phone;
            $branch->address_id = $addressID;
            $branch->save();
            $status = Branch::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
            $branch->statuses()->attach($status);
            DB::commit();
            return response()->json(['message' => 'با موفقیت وارد شد',
                'branch' => $branch,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error'], 500);
//            return response()->json(['error' => 'خطا در ایجاد شعبه جدید'], 500);
        }

    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $branch = Branch::with('status', 'departments.status', 'sections.status', 'sections.department', 'address.city.state.country')->findOrFail($id);
        if (is_null($branch)) {
            return response()->json([
                'error' => 'شعبه ای با این مشخصات یافت نشد'
            ], 404);
        }
        if (\Str::contains(\request()->route()->uri(), 'branch/edit/{id}')) {
            $statuses = Branch::GetAllStatuses();

            return response()->json(['branch' => $branch, 'statuses' => $statuses]);

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
                $address->title = $request->title;
                $address->detail = $request->address;
                $address->latitude = $request->latitude ?? null;
                $address->longitude = $request->longitude ?? null;
                $address->postal_code = $request->postalCode ?? null;
                $address->city_id = $request->cityID;
                $address->status_id = $request->statusID;
                $address->creator_id = $user->id;
                $address->save();
            } else {
                $address = Address::findOrFail($request->addressID);
            }


            $branch->name = $request->branchName;
            $branch->phone_number = $request->branchPhone;
            $branch->address_id = $address->id;
            if ($branch->status[0]->id != $request->statusID) {

                $branch->statuses()->attach($request->statusID);
            }
            $branch->save();

            DB::commit();

            return response()->json([
                'message' => 'شعبه با موفقیت ویرایش شد',
                'branch' => $branch,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error'], 500);
//            return response()->json(['error' => 'خطا سمت سرور'], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $branch = Branch::findOrFail($id);
        if ($branch == null || $branch->status[0]->name === 'غیرفعال') {
            return response()->json(['message' => 'کسب و کاری با این مشخصات یافت نشد'], 404);
        }


        $status = Branch::GetAllStatuses()->where('name', '=', 'غیرفعال')->first()->id;
        $branch->statuses()->attach($status);

        return response()->json(['message' => 'با موفقیت حذف شد']);
    }
}
