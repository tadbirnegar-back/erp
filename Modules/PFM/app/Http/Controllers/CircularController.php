<?php

namespace Modules\PFM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\User;
use Modules\PFM\app\Http\Traits\PfmCircularTrait;
use Modules\PFM\app\Resources\IndexCircularsResource;
use Modules\PFM\app\Resources\ShowCircularForUpdate;
use Modules\PFM\app\Resources\ShowCircularResource;

class CircularController extends Controller


{
    use PfmCircularTrait;

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $user = Auth::user();
            $this->storeCircular($data, $user);

            Db::commit();
            return response()->json(['message' => 'بخشنامه با موفقیت ساخته شد'], 200);
        } catch (\Exception $e) {
            Db::rollBack();
            return response()->json(['message' => 'ایجاد بخشنامه با مشکل مواجه شد'], 400);
        }

    }

    public function index(Request $request)
    {
        $data = $request->all();
        $data = $this->indexCirculars($data);
        return IndexCircularsResource::collection($data);
    }

    public function show($id)
    {
        $data = $this->showCircular($id);


        if (!$data) {
            return response()->json(['message' => 'بخشنامه یافت نشد'], 404);
        }

        return new ShowCircularResource($data);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->updateCircular($request, $id);
            Db::commit();
            return response()->json(['message' => 'بخشنامه با موفقیت بروزرسانی شد'], 200);
        }catch (\Exception $e) {
            Db::rollBack();
            return response()->json(['message' => 'متاسفانه تغییرات بخشنامه اعمال نگردید'], 400);
        }

    }

    public function showForUpdate($id)
    {
        $data = $this->showForUpdating($id);
        return new ShowCircularForUpdate($data);
    }

    public function generateBooklets($id)
    {
        $user = Auth::user();
        try {
            Artisan::call('pfm:dispatch-circular', [
                'circularId' => $id,
                'userId' => $user->id
            ]);
//            $this->publishCircular($id);
            return response()->json(['message' => 'بخشنامه با موفقیت ابلاغ گردید'], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function delete($id)
    {
        try {
            $this->deleteCircular($id);
            return response()->json(['message' => 'بخشنامه با موفقیت حذف شد'], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => 'حذف بخشنامه با مشکل مواجه شد'], 500);
        }
    }


}
