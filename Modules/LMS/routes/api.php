<?php

use Illuminate\Support\Facades\Route;
use Modules\LMS\app\Http\Controllers\ChapterController;
use Modules\LMS\app\Http\Controllers\CourseController;
use Modules\LMS\app\Http\Controllers\LessonController;
use Modules\LMS\app\Http\Controllers\OptionController;
use Modules\LMS\app\Http\Controllers\QuestionController;
use Modules\LMS\app\Http\Controllers\TeacherController;
use Modules\LMS\app\Http\Controllers\ContentController;

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
    Route::post('/teacher/search', [TeacherController::class, 'LiveSearchTeacher']);
    Route::post('/students/search', [\Modules\LMS\app\Http\Controllers\StudentController::class, 'isPersonStudent']);
    Route::post('/dehyari/add', [\Modules\LMS\app\Http\Controllers\StudentController::class, 'store']);
    Route::post('/students/list', [\Modules\LMS\app\Http\Controllers\StudentController::class, 'index']);
    Route::post('/students/{id}', [\Modules\LMS\app\Http\Controllers\StudentController::class, 'show']);
    Route::post('/students/update/{id}', [\Modules\LMS\app\Http\Controllers\StudentController::class, 'show']);
    Route::put('/students/update/{id}', [\Modules\LMS\app\Http\Controllers\StudentController::class, 'update']);
    Route::delete('/students/delete/{id}', [\Modules\LMS\app\Http\Controllers\StudentController::class, 'destroy']);
    Route::post('/lms/edit/questions/{id}', [QuestionController::class, 'editQuestion']);
    Route::post('/lms/delete/questions/{id}', [QuestionController::class, 'destroyQuestion']);
    Route::post('/lms/edit/options/{id}', [OptionController::class, 'editOption']);
    Route::post('/lms/delete/options/{id}', [OptionController::class, 'destroyOption']);
    Route::post('/lms/exams/list', [\Modules\LMS\app\Http\Controllers\ExamsController::class, 'index']);

});
Route::middleware(['auth:api', 'route'])->prefix('v1')->group(function () {
    Route::post('/lms/teachers/add', [TeacherController::class, 'store']);
    Route::post('/lms/courses/questions/list', [\Modules\LMS\app\Http\Controllers\CourseController::class, 'courseList']);
    Route::post('/lms/courses/lesson/list', [CourseController::class, 'lessonList']);
    Route::post('/lms/add/questions', [QuestionController::class, 'store']);
    Route::post('/lms/add/options', [OptionController::class, 'store']);
    Route::post('/lms/lesson/add', [LessonController::class, 'addLesson']);
    Route::post('/lms/chapter/edit/{id}', [ChapterController::class, 'update']);
    Route::get('/lms/chapter/delete/{id}', [ChapterController::class, 'delete']);
    Route::get('/lms/lesson/show/{id}' , [LessonController::class, 'show']);
    Route::post('/lms/lesson/update/{id}' , [LessonController::class, 'update']);
});
Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::post('/lms/teacher/check-national-code', [TeacherController::class, 'isTeacherExist']);
    Route::get('/lms/my-courses/{id}', [CourseController::class, 'show']);
    Route::post('/lms/register/course/{id}', [CourseController::class, 'registerCourse']);
    Route::post('/lms/course/check-payment', [CourseController::class, 'checkPayment']);
    Route::get('/lms/view-course/{id}', [CourseController::class, 'learningShow']);
    Route::post('/lms/lesson/comment', [LessonController::class, 'storeComment']);
    Route::get('/lms/lesson/adding-requirements/{id}', [LessonController::class, 'addLessonRequirements']);
    Route::post('/lms/lesson/data' , [LessonController::class, 'sendLessonDatas']);
    Route::post('/lms/content-log/set' , [ContentController::class , 'setLog']);
});
