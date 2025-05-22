<?php

namespace Modules\ODOC\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\Otp;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\Estate;
use Modules\BDM\app\Models\Form;
use Modules\FileMS\app\Models\File;
use Modules\ODOC\app\Http\Traits\OdocDocumentTrait;
use Modules\ODOC\app\Models\Document;
use Modules\PersonMS\app\Models\Person;
use Modules\PersonMS\app\Models\Signature;

class ODOCController extends Controller
{
    use OdocDocumentTrait;

    public function createOdocDocument(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = Auth::user();
            $json = json_decode($data['json']);
            $data['json'] = encrypt_json($json);
            $data['model_id'] = $id;
            $data['model'] = BuildingDossier::class;
            $data['version'] = '1';
            $dossier = Estate::find($id);
            $data['ounit_id'] = $dossier->ounit_id;
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

    public function showOdocDocument(Request $request,$id)
    {
        try {
            $data = Document::find($id);
            $user = Auth::user();


            $data->data = decrypt_json($data->data);

            $user = Auth::user();
            $signatures = Signature::where('person_id', $user->person_id)
                ->where('status_id', $this->ActiveSignatureStatus()->id)
                ->first();
            if (!$signatures) {
                return response()->json(['message' => 'شما امضای ثبت شده در سیستم ندارید'], 202);
            }
            $file = File::where('id', $signatures->signature_file_id)->first();
            if(isset($request->code)){
                $result = Otp::where('mobile', $user->mobile)
                    ->where('code', $request->code)
                    ->where('expire_date', '>', now())->first();
                if(!$result){
                    return response()->json(['data' => $data, 'file' => $file] , 201);
                }else{
                    return response()->json(['data' => $data, 'file' => $file] , 200);
                }

            }else{
                return response()->json(['data' => $data, 'file' => $file] , 201);
            }

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function approveOdocDocument(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $data = $request->all();
            $data['mobile'] = $user->mobile;
            $data['person_id'] = $user->person_id;
            $this->documentApproval($id, $data);
            DB::commit();
            return response()->json(['message' => 'سند با موفقیت تایید شد']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function declineOdocDocument(Request $request, $id)
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
