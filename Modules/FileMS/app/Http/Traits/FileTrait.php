<?php

namespace Modules\FileMS\app\Http\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Modules\FileMS\app\Models\Extension;
use Modules\FileMS\app\Models\File;

trait FileTrait
{
    private function putFileIntoStorage(UploadedFile $file, bool $isPrivate = false)
    {
        $method = $isPrivate ? 'putFileAsPrivate' : 'putFileAsPublic';
        $currentDate = Carbon::now();

        $path = $currentDate->year . '/' . $currentDate->month . '/' . $currentDate->day;

        return $this->$method($file->getClientOriginalName(), $file, $path);

    }

    public function putFileAsPrivate(string $name, UploadedFile $file, string $path)
    {
        return Storage::disk('private')->putFileAs($path, $file, $name);

    }

    public function putFileAsPublic(string $name, UploadedFile $file, string $path)
    {
        return Storage::disk('public')->putFileAs($path, $file, $name);

    }

    public function uploadPrivateFileWithTempUrl(UploadedFile $file, array $data, Extension $extension)
    {
        $path = $this->putFileIntoStorage($file, true);
        $storedFile = $this->storeFile($data, $extension, $path);
        $tempUrl = $this->getTemporaryUrl($path);

        return [
            'file' => $storedFile,
            'temporary_url' => $tempUrl,
        ];
    }

    public function getTemporaryUrl(string $path, int $expirationMinutes = 60)
    {
        return Storage::disk('private')->temporaryUrl($path, now()->addMinutes($expirationMinutes));
    }

    public function storeFile(array $data, Extension $extension, string $path, bool $isPrivate = false)
    {

        $file = new File();
        $file->name = $data['fileName'];
        $file->size = $data['fileSize'];
        $file->description = $data['description'] ?? null;
        $file->creator_id = $data['userID'] ?? null;

        $file->extension_id = $extension->id;
        $file->slug = $isPrivate ? $path : 'uploads/' . $path;
        $file->isPrivate = $isPrivate;
        $file->save();
        return $file;
    }
}
