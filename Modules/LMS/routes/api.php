<?php

use Illuminate\Support\Facades\Route;
use Modules\LMS\app\Http\Controllers\ChapterController;
use Modules\LMS\app\Http\Controllers\ContentController;
use Modules\LMS\app\Http\Controllers\CourseController;
use Modules\LMS\app\Http\Controllers\CourseCourseController;
use Modules\LMS\app\Http\Controllers\CourseOunitFeatureController;
use Modules\LMS\app\Http\Controllers\LessonController;
use Modules\LMS\app\Http\Controllers\OucPropertyController;
use Modules\LMS\app\Http\Controllers\OucPropertyValueController;
use Modules\LMS\app\Http\Controllers\PriviciesController;
use Modules\LMS\app\Http\Controllers\QuestionsController;
use Modules\LMS\app\Http\Controllers\SettingController;
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
    Route::post('/teacher/search', [TeacherController::class, 'LiveSearchTeacher']);
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
    Route::get('/lms/course/cancel/{id}', [CourseController::class, 'cancelCourse']);
    Route::get('/lms/lesson/delete/{id}', [LessonController::class, 'deleteLesson']);
    Route::get('/lms/course/my-enrolled-courses', [CourseController::class, 'myEnrolledCourses']);
    Route::post('/lms/exam/store-ansSheet/{id}', [\Modules\LMS\app\Http\Controllers\ExamResultController::class, 'storeAnsS']);
    Route::post('/lms/exam/show/{id}', [\Modules\LMS\app\Http\Controllers\ExamResultController::class, 'showAns']);
    Route::get('/lms/exam-result/list', [\Modules\LMS\app\Http\Controllers\ExamsController::class, 'index']);
    Route::get('/lms/pre-view/{id}', [\Modules\LMS\app\Http\Controllers\ExamsController::class, 'previewExam']);
    Route::get('/lms/generated-exam/{id}', [\Modules\LMS\app\Http\Controllers\ExamsController::class, 'generateExam']);
    Route::get('/lms/show-exam/{id}', [\Modules\LMS\app\Http\Controllers\ExamsController::class, 'showExamQuestions']);
    Route::get('/lms/view-course/{id}', [CourseController::class, 'learningShow']);


});
Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::post('/lms/teacher/check-national-code', [TeacherController::class, 'isTeacherExist']);
    Route::get('/lms/my-courses/{id}', [CourseController::class, 'show']);
    Route::post('/lms/register/course/{id}', [CourseController::class, 'registerCourse']);
    Route::post('/lms/course/check-payment', [CourseController::class, 'checkPayment']);
    Route::get('/lms/view-course/{id}', [CourseController::class, 'learningShow']);
    Route::post('/lms/lesson/comment', [LessonController::class, 'storeComment']);
    Route::get('/lms/view-course/{id}', [CourseController::class, 'learningShow']);
    Route::post('/lms/lesson/comment', [LessonController::class, 'storeComment']);
    Route::get('/lms/lesson/adding-requirements/{id}', [LessonController::class, 'addLessonRequirements']);
    Route::get('/lms/pre-view/{id}', [\Modules\LMS\app\Http\Controllers\ExamsController::class, 'previewExam']);
    Route::get('/lms/generated-exam/{id}', [\Modules\LMS\app\Http\Controllers\ExamsController::class, 'generateExam']);
    Route::get('/lms/show-exam/{id}', [\Modules\LMS\app\Http\Controllers\ExamsController::class, 'showExamQuestions']);
    Route::get('/lms/generated-exam/{id}', [\Modules\LMS\app\Http\Controllers\ExamsController::class, 'generateExam']);
    Route::get('/lms/show-exam/{id}', [\Modules\LMS\app\Http\Controllers\ExamsController::class, 'showExamQuestions']);
    Route::post('/lms/lesson/data', [LessonController::class, 'sendLessonDatas']);
    Route::post('/lms/content-log/set', [ContentController::class, 'setLog']);
    Route::get('/lms/ounit/list/course-all', [CourseController::class, 'courseListAll']);
    Route::get('/lms/privicies/index', [PriviciesController::class, 'index']);
    Route::post('/lms/ounit/live-search', [CourseController::class, 'liveSearchOunit']);
    Route::post('/lms/ouc-properties/list', [OucPropertyController::class, 'listing']);
    Route::post('/lms/ouc-property-values/list', [OucPropertyValueController::class, 'listing']);
    Route::get('/lms/course-course/list/{id}', [CourseCourseController::class, 'listing']);
    Route::get('/lms/course/update-show/{id}', [CourseController::class, 'updateDataShow']);
    Route::post('/lms/course/related-courses-list', [CourseController::class, 'relatedCoursesList']);
    Route::get('/lms/course/my-enrolled-courses', [CourseController::class, 'myEnrolledCourses']);
    Route::post('/lms/exam/store-ansSheet/{id}', [\Modules\LMS\app\Http\Controllers\ExamResultController::class, 'storeAnsS']);
    Route::post('/lms/exam/show/{id}', [\Modules\LMS\app\Http\Controllers\ExamResultController::class, 'showAns']);
    Route::post('/lms/exams/list', [\Modules\LMS\app\Http\Controllers\ExamsController::class, 'index']);
    Route::post('/lms/add/question/{id}', [QuestionsController::class, 'storeQuestionAndOptions']);
    Route::get('/lms/show/{id}', [QuestionsController::class, 'showDropDowns']);
    Route::get('/lms/question/list/{id}', [QuestionsController::class, 'questionsManagement']);
    Route::get('/lms/questions/delete/{id}', [QuestionsController::class, 'deleteQuestionAndRelatedOptions']);
    Route::post('/lms/questions/update/{id}', [QuestionsController::class, 'update']);
    Route::get('/lms/questions/update/show/{id}', [QuestionsController::class, 'showQuestion']);
    Route::post('/lms/show/setting', [SettingController::class, 'index']);
    Route::post('/lms/store/setting', [SettingController::class, 'store']);
    Route::post('/lms/course/related-courses-list', [CourseController::class, 'relatedCoursesList']);
    Route::get('/lms/my-courses/{id}', [CourseController::class, 'show']);


});
