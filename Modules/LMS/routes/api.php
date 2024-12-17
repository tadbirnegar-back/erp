<?php

use Illuminate\Support\Facades\Route;
use Modules\LMS\app\Http\Controllers\CourseController;
use Modules\LMS\app\Http\Controllers\TeacherController;

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

//Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
//    Route::get('lms', fn (Request $request) => $request->user())->name('lms');
//});

Route::middleware([])->prefix('v1')->name('api.')->group(function () {
    Route::post('/teachers/list', [TeacherController::class, 'index']);
    Route::post('/teachers/search', [TeacherController::class, 'LiveSearchTeacher']);
    Route::post('/students/search', [\Modules\LMS\app\Http\Controllers\StudentController::class, 'isPersonStudent']);
    Route::post('/dehyari/add', [\Modules\LMS\app\Http\Controllers\StudentController::class, 'store']);
    Route::post('/students/list', [\Modules\LMS\app\Http\Controllers\StudentController::class, 'index']);
    Route::post('/students/{id}', [\Modules\LMS\app\Http\Controllers\StudentController::class, 'show']);
    Route::post('/students/update/{id}', [\Modules\LMS\app\Http\Controllers\StudentController::class, 'show']);
    Route::put('/students/update/{id}', [\Modules\LMS\app\Http\Controllers\StudentController::class, 'update']);
    Route::delete('/students/delete/{id}', [\Modules\LMS\app\Http\Controllers\StudentController::class, 'destroy']);

});
Route::middleware(['auth:api', 'route'])->prefix('v1')->group(function () {
    Route::post('/lms/teachers/add', [TeacherController::class, 'store']);
    Route::post('/lms/courses/questions/list', [\Modules\LMS\app\Http\Controllers\CourseController::class, 'courseList']);
    Route::post('/lms/courses/lesson/list', [CourseController::class, 'lesson_index']);
});
Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::post('/lms/teacher/check-national-code', [TeacherController::class, 'isTeacherExist']);
    Route::get('/lms/my-courses/{id}', [CourseController::class, 'show']);
});

