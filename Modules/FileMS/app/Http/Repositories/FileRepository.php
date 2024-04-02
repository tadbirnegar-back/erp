<?php

namespace Modules\FileMS\app\Http\Repositories;

use Illuminate\Support\Carbon;
use Modules\FileMS\app\Models\Extension;
use Modules\FileMS\app\Models\File;
use Modules\StatusMS\app\Models\Status;

class FileRepository
{
    public static function store($uploadedFile, array $data)
    {

        // Get the current date
        $currentDate = Carbon::now();


        try {
            // Create a folder path based on the current year, month, and day
            $folderPath = 'uploads/' . $currentDate->year . '/' . $currentDate->month . '/' . $currentDate->day;
            $nameToSave = \Str::random(5) . $data['fileName'];
            $uploadedFile->move(public_path($folderPath), $nameToSave);

//            \DB::beginTransaction();
            $file = new File();
            $file->name = $data['fileName'];
            $file->size = $data['fileSize'];
            $file->description = $data['description'] ?? null;
            $file->creator_id = $data['userID'] ?? null;
            $file->extension_id = $data['extensionID'];
            $file->slug = 'public/' . $folderPath . '/' . $nameToSave;
            $file->save();

            $status = Status::where('name', '=', 'فعال')->where('model', '=', File::class)->first();

            $file->statuses()->attach($status->id);
//            \DB::commit();

            return $file;
        } catch (\Exception $e) {
//            \DB::rollBack();
            return $e;

        }
    }
}
