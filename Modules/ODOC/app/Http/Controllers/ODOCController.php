<?php

namespace Modules\ODOC\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\Form;
use Modules\ODOC\app\Http\Traits\OdocDocemntTrait;
use Modules\ODOC\app\Models\Document;

class ODOCController extends Controller
{
    use OdocDocemntTrait;
    public function createOdocDocument(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = Auth::user();
            $this->storeOdocDocument($data , $user);
            DB::commit();
            return response()->json(['message' => 'اطلاعات امضا با موفقیت ثبت شد']);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
