<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\HRMS\app\Http\Traits\EducationRecordTrait;
use Modules\LMS\app\Http\Traits\TeacherTrait;
use Modules\LMS\app\Resources\TeacherListResource;

class TeacherController extends Controller
{
    use TeacherTrait, EducationRecordTrait;

    public function index(Request $request)
    {
        $data = $request->all();
        $perPage = $data['perPage'] ?? 5;
        $pageNum = $data['pageNum'] ?? 1;

        $result = $this->teacherIndex($perPage, $pageNum, $data);
        $response = new TeacherListResource($result);

        return $response;
    }

    public function LiveSearchTeacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }
        $data = $request->all();
        $result = $this->teacherLiveSearch($data);
        return response()->json($result);
    }


    public function store(Request $request): JsonResponse
    {
        try {
            \DB::beginTransaction();
            $data = $request->all();
            $situation = $this->isPersonTeacher($data['nationalCode']);
            if (isset($data['bcIssueDate'])) {
                $data['bcIssueDate'] = convertJalaliPersianCharactersToGregorian($data['bcIssueDate']);
            }
            $data['dateOfBirth'] = convertJalaliPersianCharactersToGregorian($data['dateOfBirth']);
            if ($situation['message'] == "teacher") {
                return response()->json(["message" => "شخصی با این کد ملی قبلا به عنوان مدرس افزوده شده است"], 409);
            } elseif ($situation['message'] == "found") {
                $person = $this->personNaturalUpdate($data, $situation['data']['result']);
                $workforce = $this->storeTeacher($data, $person->id)->workForce;
            } else {
                $natural = $this->naturalStore($data);
                $person = $natural->person;
                $workforce = $this->storeTeacher($data, $person->id)->workForce;
            }

            if (isset($data['educations'])) {
                $edus = json_decode($data['educations'], true);
                $this->educationUpsert($edus, $workforce->id);
            }

            if (isset($data['deletedEducations'])) {
                $educations = json_decode($data['deletedEducations'], true);
                $this->EducationHardDelete($educations);
            }

            \DB::commit();
            return response()->json(["message" => "مدرس با موفقیت افزوده شد"], 200);

        } catch (\Exception $exception) {
            \DB::rollBack();
            return response()->json(['message' => $exception->getMessage(), 'trance' => $exception->getTrace()], 500);
        }

    }

    public function isTeacherExist(Request $request): JsonResponse
    {
        $situation = $this->isPersonTeacher($request->nationalCode);
        return response()->json($situation);
    }


}
