<?php

use Illuminate\Support\Facades\Route;
use Modules\LMS\app\Http\Controllers\ChapterController;
use Modules\LMS\app\Http\Controllers\ContentController;
use Modules\LMS\app\Http\Controllers\CourseController;
use Modules\LMS\app\Http\Controllers\CourseCourseController;
use Modules\LMS\app\Http\Controllers\CourseReportController;
use Modules\LMS\app\Http\Controllers\ExamResultController;
use Modules\LMS\app\Http\Controllers\ExamsController;
use Modules\LMS\app\Http\Controllers\LessonController;
use Modules\LMS\app\Http\Controllers\OucPropertyController;
use Modules\LMS\app\Http\Controllers\OucPropertyValueController;
use Modules\LMS\app\Http\Controllers\PriviciesController;
use Modules\LMS\app\Http\Controllers\QuestionsController;
use Modules\LMS\app\Http\Controllers\ReportingController;
use Modules\LMS\app\Http\Controllers\SettingController;
use Modules\LMS\app\Http\Controllers\StudentController;
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
    Route::post('/students/{id}', [StudentController::class, 'show']);
    Route::post('/students/update/{id}', [StudentController::class, 'show']);
    Route::put('/students/update/{id}', [StudentController::class, 'update']);
    Route::delete('/students/delete/{id}', [StudentController::class, 'destroy']);
});
Route::middleware(['auth:api', 'route'])->prefix('v1')->group(function () {
    Route::post('/lms/teachers/add', [TeacherController::class, 'store']);
    Route::post('/lms/courses/questions/list', [CourseController::class, 'courseList']);
    Route::post('/lms/courses/lesson/list', [CourseController::class, 'lessonList']);
    Route::post('/lms/lesson/add', [LessonController::class, 'addLesson']);
    Route::post('/lms/chapter/edit/{id}', [ChapterController::class, 'update']);
    Route::get('/lms/chapter/delete/{id}', [ChapterController::class, 'delete']);
    Route::get('/lms/lesson/show/{id}', [LessonController::class, 'show']);
    Route::post('/lms/lesson/update/{id}', [LessonController::class, 'update']);
    Route::post('/lms/course/add', [CourseController::class, 'store']);
    Route::post('/lms/course/update/{id}', [CourseController::class, 'update']);
    Route::get('/lms/publish/course/{id}', [CourseController::class, 'publishCourseDataShow']);
    Route::get('/lms/course/make-publish/{id}', [CourseController::class, 'makeCoursePublish']);
    Route::get('/lms/course/delete/{id}', [CourseController::class, 'deleteCourse']);
    Route::post('/lms/course/cancel/{id}', [CourseController::class, 'cancelCourse']);
    Route::get('/lms/lesson/delete/{id}', [LessonController::class, 'deleteLesson']);
    Route::get('/lms/course/my-enrolled-courses', [CourseController::class, 'myEnrolledCourses']);
    Route::post('/lms/exam/store-ansSheet/{id}', [ExamResultController::class, 'storeAnsS']);
    Route::post('/lms/exam/show/{id}', [ExamResultController::class, 'showAns']);
    Route::get('/lms/exam-result/list', [ExamsController::class, 'index']);
    Route::get('/lms/pre-view/{id}', [ExamsController::class, 'previewExam']);
    Route::get('/lms/generated-exam/{id}', [ExamsController::class, 'generateExam']);
    Route::get('/lms/show-exam/{id}', [ExamsController::class, 'showExamQuestions']);
    Route::get('/lms/view-course/{id}', [CourseController::class, 'learningShow']);
    Route::post('/lms/lesson/data', [LessonController::class, 'sendLessonDatas']);
    Route::post('/lms/content-log/set', [ContentController::class, 'setLog']);
    Route::post('/lms/add/question/{id}', [QuestionsController::class, 'storeQuestionAndOptions']);
    Route::post('/lms/last/changed-setting/show', [SettingController::class, 'LastShow']);
    Route::get('/lms/questions/dropdown/{id}', [QuestionsController::class, 'showDropDowns']);
    Route::get('/lms/question/list/{id}', [QuestionsController::class, 'questionsManagement']);
    Route::get('/lms/questions/delete/{id}', [QuestionsController::class, 'deleteQuestionAndRelatedOptions']);
    Route::post('/lms/questions/update/{id}', [QuestionsController::class, 'update']);
    Route::get('/lms/questions/update/show/{id}', [QuestionsController::class, 'showQuestion']);
    Route::post('/lms/show/setting', [SettingController::class, 'index']);
    Route::post('/lms/store/setting', [SettingController::class, 'store']);
    Route::post('/lms/teachers/list', [TeacherController::class, 'index']);
    Route::post('/lms/teacher/search', [TeacherController::class, 'LiveSearchTeacher']);
    Route::get('/lms/examPreperation/{id}', [ExamsController::class, 'isExamReady']);
    Route::get('/lms/reporting/data/{id}', [ReportingController::class, 'index']);

});
Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::post('/lms/teacher/check-national-code', [TeacherController::class, 'isTeacherExist']);
    Route::get('/lms/my-courses/{id}', [CourseController::class, 'show']);
    Route::post('/lms/register/course/{id}', [CourseController::class, 'registerCourse']);
    Route::post('/lms/course/check-payment', [CourseController::class, 'checkPayment']);
    Route::post('/lms/lesson/comment', [LessonController::class, 'storeComment']);
    Route::get('/lms/lesson/adding-requirements/{id}', [LessonController::class, 'addLessonRequirements']);
    Route::get('/lms/ounit/list/course-all', [CourseController::class, 'courseListAll']);
    Route::get('/lms/privicies/index', [PriviciesController::class, 'index']);
    Route::post('/lms/ounit/live-search', [CourseController::class, 'liveSearchOunit']);
    Route::post('/lms/ouc-properties/list', [OucPropertyController::class, 'listing']);
    Route::post('/lms/ouc-property-values/list', [OucPropertyValueController::class, 'listing']);
    Route::get('/lms/course-course/list/{id}', [CourseCourseController::class, 'listing']);
    Route::get('/lms/course/update-show/{id}', [CourseController::class, 'updateDataShow']);
    Route::post('/lms/course/related-courses-list', [CourseController::class, 'relatedCoursesList']);
});
Route::get('/lms/course/report/{id}', [CourseReportController::class, 'index']);
