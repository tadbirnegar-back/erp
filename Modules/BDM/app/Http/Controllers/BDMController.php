<?php

namespace Modules\BDM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\BDM\app\Models\Estate;

class BDMController extends Controller
{
    public function updateEstate(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $estate = Estate::where('dossier_id', $id)->first();
            $estate->ounit_id = $request->input('ounitID') ?? $estate->ounit_id;
            $estate->ownership_type_id = $request->input('ownershipTypeID') ?? $estate->ownership_type_id;
            $estate->part = $request->input('part') ?? $estate->part;
            $estate->transfer_type_id = $request->input('transferTypeID') ?? $estate->transfer_type_id;
            $estate->postal_code = $request->input('postalCode') ?? $estate->postal_code;
            $estate->address = $request->input('address') ?? $estate->address;
            $estate->ounit_number = $request->input('ounitNumber') ?? $estate->ounit_number;
            $estate->main = $request->input('main') ?? $estate->main;
            $estate->minor = $request->input('minor') ?? $estate->minor;
            $estate->deal_number = $request->input('dealNumber') ?? $estate->deal_number;
            $estate->building_number = $request->input('buildingNumber') ?? $estate->building_number;
            $estate->save();
            \DB::commit();
            return response()->json(['message' => 'با موفقیت به روز شد']);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
