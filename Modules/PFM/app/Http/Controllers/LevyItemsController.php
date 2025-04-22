<?php

namespace Modules\PFM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\PFM\app\Http\Traits\LevyItemTrait;

class LevyItemsController extends Controller
{

    use LevyItemTrait;

    public function store(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $data = $request->all();
            $this->storeItems($data['text'], $id);
            \DB::commit();
            return response()->json(['message' => 'اطلاعات با موفقیت ثبت شد']);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $this->deleteItems($id);
            return response()->json(['message' => 'اطلاعات با موفقیت حذف شد']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حذف اطلاعات با مشکل مواجه شد']);
        }
    }

    public function index($id)
    {
        $data = $this->indexItems($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $data = $request->all();
            $this->updateItems($data['text'], $id);
            \DB::commit();
            return response()->json(['message' => 'اطلاعات با موفقیت ثبت شد']);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'ویرایش انجام نگرفت']);
        }
    }
}
