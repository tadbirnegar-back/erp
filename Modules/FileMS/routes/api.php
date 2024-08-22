<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::middleware([])->prefix('v1')->name('api.')->group(function () {
    Route::post('/files/add', [\Modules\FileMS\app\Http\Controllers\FileMSController::class, 'store']);

    Route::post('/download/{year}/{month}/{day}/{filename}', function ($year, $month, $day, $filename) {
        $path = "$year/$month/$day/$filename";
        return response()->json([
            'temporary_url' => Storage::disk('private')->temporaryUrl($path, now()->addMinutes(60)),
        ]);
    })->name('file.temp');

    Route::get('/download/{year}/{month}/{day}/{filename}', function ($year, $month, $day, $filename) {
        // Construct the path based on the URL parameters
        $path = "$year/$month/$day/$filename";

        // Check if the file exists
        if (!Storage::disk('private')->exists($path)) {
            abort(404, 'File not found.');
        }

        // Return the file as a download response
        return Storage::disk('private')->download($path);
    })->middleware('signed');

});

Route::middleware(['auth:api', 'route'])->prefix('v1')->name('api.')->group(function () {

    Route::post('/files/list', [\Modules\FileMS\app\Http\Controllers\FileMSController::class, 'index']);
    Route::delete('/files/delete/{id}', [\Modules\FileMS\app\Http\Controllers\FileMSController::class, 'destroy']);
    Route::put('/files/update/{id}', [\Modules\FileMS\app\Http\Controllers\FileMSController::class, 'update']);
    Route::post('/files/{id}', [\Modules\FileMS\app\Http\Controllers\FileMSController::class, 'show']);
});
