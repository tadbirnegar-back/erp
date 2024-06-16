<?php

namespace Modules\FileMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Modules\AAA\app\Models\User;
use Modules\FileMS\app\Http\Traits\FileTrait;
use Modules\FileMS\app\Models\Extension;
use Modules\FileMS\app\Models\File;
use Modules\FileMS\app\Models\MimeType;
use Modules\StatusMS\app\Models\Status;
use Number;

class FileMSController extends Controller
{
    use FileTrait;
    public array $data = [];

    /**
     * @Authenticated
     * @bodyparams pageNumber int default is 1
     * @bodyparams perPage int default is 10
     * @bodyparams mimeTypeID int default is null
     * @bodyparams startDate string timestamp of start date to filter default is null
     * @bodyparams endDate string timestamp of end date to filter default is null
     * @bodyparams fileName string the specific file name default is null
     * @response status=200 scenario=success {"data": [{"id": 4,"link": "https://tgbot.zbbo.net/public/uploads/2024/1/17/abOeH_27e50034-f346-4f0c-bfd8-8720c17a166d.jpg","title": "_27e50034-f346-4f0c-bfd8-8720c17a166d.jpg","date": "2024-01-17 11:05:57","size": "171 KB","type": "image"}],"current_page": 1,"last_page": 15,"mimeTypes": [{"label": "document","value": 1},{"label": "audio","value": 2},{"label": "image","value": 3},{"label": "video","value": 4}]}
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->input('pageNumber', 1); // Default to page 1 if not provided
        $perPage = $request->input('perPage', 10); // Default to 10 items per page if not provided

        /**
         * @var User $user
         */
        $user = \Auth::user();

        $filesQuery = $user->files();

        // Filter based on mime type ID
        if (isset($request->mimeTypeID)) {
            $mimeTypeID = $request->mimeTypeID;
            $filesQuery->whereHas('mimeType', function ($query) use ($mimeTypeID) {
                $query->where('mime_types.id', '=', $mimeTypeID);
            });
        }

        // Filter based on date
        if (isset($request->startDate) && isset($request->endDate)) {

            $startDate = Carbon::createFromTimestamp($request->startDate, 'Asia/Tehran')->format('Y-m-d') . ' 00:00:00';
            $endDate = Carbon::createFromTimestamp($request->endDate, 'Asia/Tehran')->format('Y-m-d') . ' 23:59:59';

            $filesQuery->whereBetween('create_date', [$startDate, $endDate]);
        }
        $searchTerm = $request->input('fileName', ''); // Or however you get your search term

        $filesQuery->when($searchTerm, function ($query) use ($searchTerm) {
            $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
        });

        $files = $filesQuery
            ->with('mimeType')
            ->orderBy('create_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        //generate response of the query
        $response = $files->getCollection()->transform(function ($file) {
            return [
                'id' => $file->id,
                'link' => $file->slug,
                'title' => $file->name,
                'date' => $file->create_date,
                'size' => Number::fileSize($file->size),
                'type' => $file->mimeType->name ?? null, // Include mime type name
            ];
        });

        return response()->json([
            'data' => $response,
            'current_page' => $files->currentPage(),
            'last_page' => $files->lastPage(),
            'mimeTypes' => MimeType::all()->map(function ($item) {
                return [
                    'label' => $item->name,
                    'value' => $item->id,
                ];
            }),
        ]);


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
        $fileExtension = $uploadedFile->getClientOriginalExtension();
        $extension_id = Extension::where('name', '=', $fileExtension)->get(['id'])->first();

        if (!$extension_id) {
            return response()->json(['message' => 'فایل مجاز نمی باشد'], 400);
        }
        // Get the current date
        $currentDate = Carbon::now();


        try {
            DB::beginTransaction();
            $uploadedFile = $request->file('file');
            $data['fileName'] = $uploadedFile->getClientOriginalName();
            $data['fileSize'] = $uploadedFile->getSize();
            $fileExtension = $uploadedFile->getClientOriginalExtension();
            $extension = Extension::where('name', '=', $fileExtension)->get(['id'])->first();

            if (!$extension) {
                return response()->json(['message' => 'فایل مجاز نمی باشد'], 400);
            }
            $filePath= $this->putFileIntoStorage($uploadedFile);

            $file= $this->storeFile($data, $extension,$filePath);

            $status = Status::where('name', '=', 'فعال')->where('model', '=', File::class)->first();

            $file->statuses()->attach($status->id);
            DB::commit();

            return response()->json(['file' => $file]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در بارگزاری فایل'], 500);

        }


    }

    /**
     * @Authenticated
     * @bodyparams id int required the ID of the File
     *
     * @response status=200 scenario=successful {"id": 147,"link": "https://tgbot.zbbo.net/public/uploads/2024/1/18/l3loH635855-aurora-borealis-wallpaper-desktop-desktop-background.jpg","title": "635855-aurora-borealis-wallpaper-desktop-desktop-background.jpg","description": null,"date": "2024-01-18 09:28:58","size": "162 KB","type": "image"}
     * @response status=404 scenario=file not found {"message" : "فایل مورد نظر یافت نشد"}
     */
    public function show($id): JsonResponse
    {
        $file = File::with('mimeType')->findOrFail($id);
        if ($file === null) {
            return response()->json('فایل مورد نظر یافت نشد', 404);
        }
        $responseArray = [
            'id' => $file->id,
            'link' => URL::to('/') . '/' . $file->slug,
            'title' => $file->name,
            'description' => $file->description,
            'date' => $file->create_date,
            'size' => Number::fileSize($file->size),
            'type' => $file->mimeType->name ?? null,
        ];

        return response()->json($responseArray);

    }

    /**
     * @Authenticated
     * @bodyparams description string The description for the uploaded file
     *
     * @response status=200 scenario=file uploaded successfully {"message" : "ویرایش فایل با موفقیت انجام شد"}
     * @response status=404 scenario=file not found {"message" : "فایل مورد نظر یافت نشد"}
     */
    public function update(Request $request, $id): JsonResponse
    {

        $file = File::with('mimeType')->findOrFail($id);
        if ($file === null) {
            return response()->json('فایل مورد نظر یافت نشد', 404);
        }
        $file->description = $request->input('description') ?? null;

        $file->save();
        return response()->json(['message'=>'ویرایش فایل با موفقیت انجام شد']);
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
        $status = Status::where('name', '=', 'غیرفعال')->where('model', '=', File::class)->first();

        $file->statuses()->attach($status->id);

        return response()->json(['message' => 'با موفقیت حذف شد']);
    }

    public function testUpload(Request $request)
    {

    }
}
