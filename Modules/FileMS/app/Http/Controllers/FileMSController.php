<?php

namespace Modules\FileMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Modules\FileMS\app\Models\Extension;
use Modules\FileMS\app\Models\File;
use Modules\StatusMS\app\Models\Status;

class FileMSController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * @Authenticated
     * @bodyparams image File required
     * @bodyparams description string The description for the uploaded file
     *
     * @response status=200 scenario=file uploaded successfully {"file" : "id"}
     * @response status=400 scenario=file trying to upload is not allowed {"message" : "فایل مجاز نمی باشد"}
     * @response status=500 scenario=server error {"message" : "خطا در بارگزاری فایل"}
     */
    public function store(Request $request): JsonResponse
    {
//        $request->validate([
//            'file' => 'required|file|max:2048' // Adjust the maximum file size as needed
//        ]);
        $uploadedFile = $request->file('file');
        $fileName = $uploadedFile->getClientOriginalName();
        $fileSize = $uploadedFile->getSize();
        $fileExtension = $uploadedFile->getClientOriginalExtension();
        $extension_id = Extension::where('name', '=', $fileExtension)->get(['id'])->first()->id;

        if (!$extension_id) {
            return response()->json(['message' => 'فایل مجاز نمی باشد'], 400);
        }
        // Get the current date
        $currentDate = Carbon::now();


        try {
            // Create a folder path based on the current year, month, and day
            $folderPath = 'uploads/' . $currentDate->year . '/' . $currentDate->month . '/' . $currentDate->day;
            $nameToSave = \Str::random(5) . $fileName;
            $uploadedFile->move(public_path($folderPath), $nameToSave);

            DB::beginTransaction();
            $file = new File();
            $file->name = $fileName;
            $file->size = $fileSize;
            $file->description = $request->description ?? null;
            $file->creator_id = \Auth::user()->id;
            $file->extension_id = $extension_id;
            $file->slug = 'public/' . $folderPath . '/' . $nameToSave;
            $file->save();

            $status = Status::where('name', '=', 'فعال')->where('model','=',File::class)->first();

            $file->statuses()->attach($status->id);
            DB::commit();

            return response()->json(['file' => $file->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در بارگزاری فایل'], 500);

        }



    }

    /**
     * @Authenticated
     * @bodyparams id int required the ID of the File
     *
     * @response status=200 scenario=file uploaded successfully {"file" : "id" , "slug" : "/path/to/file"}
     * @response status=404 scenario=file not found {"message" : "فایل مورد نظر یافت نشد"}
     */
    public function show($id): JsonResponse
    {
        $file = File::findOrFail($id);
        if ($file === null) {
            return response()->json('فایل مورد نظر یافت نشد', 404);
        }

        return response()->json([
            'id' => $file->id,
            'slug' => url('/') . $file->slug
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * @Authenticated
     * @bodyparams id int required the ID of the File
     *
     * @response status=200 scenario=file uploaded successfully {"message" : "با موفقیت حذف شد" }
     * @response status=404 scenario=file not found {"message" : "فایل مورد نظر یافت نشد"}
     */
    public function destroy($id): JsonResponse
    {
        $file = File::findOrFail($id);

        if ($file === null) {
            return response()->json('فایل مورد نظر یافت نشد', 404);
        }
        $status = Status::where('name', '=', 'غیرفعال')->where('model','=',File::class)->first();

        $file->statuses()->attach($status->id);

        return response()->json(['message' => 'با موفقیت حذف شد']);
    }
}
