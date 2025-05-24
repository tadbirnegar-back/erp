<?php

namespace Modules\WBM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\User;
use Modules\WBM\app\Http\Traits\DossierWBMTrait;

class DossierWBMController extends Controller
{
    use DossierWBMTrait;

    public function listOfWorksForEngineers(Request $request)
    {
        $data = $request->all();
        $pageNum = $data['pageNum'] ?? 1;
        $perPage = $data['perPage'] ?? 10;
        $user = Auth::user();
        $tasks = $this->TasksOfEngineers($data,$pageNum, $perPage, $user->person_id);
        return response()->json($tasks);
    }

    public function listOfItemsForEngineers($id)
    {
        $data = $this->ItemsForEngineers($id);
        return response()->json($data);
    }

    public function storeItemReport(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user =Auth::user();
            $this->makeBDMReportItems($data, $id , $user);
            DB::commit();
            return response()->json(['message' => 'اطلاعات با موفقیت ذخیره شد']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
