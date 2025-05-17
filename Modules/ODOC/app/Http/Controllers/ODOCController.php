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
use Modules\ODOC\app\Http\Traits\OdocDocumentTrait;
use Modules\ODOC\app\Models\Document;

class ODOCController extends Controller
{
    use OdocDocumentTrait;

    public function createOdocDocument(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = Auth::user();
            $this->storeOdocDocument($data, $user);
            DB::commit();
            return response()->json(['message' => 'اطلاعات امضا با موفقیت ثبت شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function listOfOdocDocuments(Request $request)
    {
        try {
            $data = $request->all();
            $user = Auth::user();
            $perPage = $data['perPage'] ?? 10;
            $pageNum = $data['pageNum'] ?? 1;
            $data = $this->fetchAllRelatedDocuments($data, $user, $perPage, $pageNum);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function showOdocDocument($id)
    {
        try {
            $data = Document::find($id);

            $data->data = decrypt_json($data->data);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function approveOdocDocument(Request $request,$id)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $data = $request->all();
            $data['mobile'] = $user->mobile;
            $data['person_id'] = $user->person_id;
            $result = $this->documentApproval($id, $data);
            DB::commit();
            return response()->json(['message' => $result['message']], $result['status']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function declineOdocDocument(Request $request,$id)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $data = $request->all();
            $data['mobile'] = $user->mobile;
            $data['person_id'] = $user->person_id;
            $result = $this->documentDecline($id, $data);
            DB::commit();
            return response()->json(['message' => $result['message']], $result['status']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
