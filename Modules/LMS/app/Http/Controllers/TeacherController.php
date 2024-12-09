<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Traits\EducationRecordTrait;
use Modules\LMS\app\Http\Traits\TeacherTrait;

class TeacherController extends Controller
{
    use TeacherTrait, EducationRecordTrait;

    public function index(Request $request)
    {
        $data = $request->all();
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;

        $result = $this->teacherIndex($perPage, $pageNum, $data);

        return response()->json($result);
    }
    public function LiveSearchTeacher(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
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

            if ($situation['message'] == "teacher") {
                return response()->json(["message" => "شخصی با این کد ملی قبلا به عنوان مدرس افزوده شده است"], 409);
            } elseif ($situation['message'] == "found") {
                $person = $this->personNaturalUpdate($data, $situation['data']);
                $workForce = $this->storeTeacher($data, $person->id)->workForce->id;
            } else {
                $natural = $this->naturalStore($data);
                $person = $natural->person;
                $workForce = $this->storeTeacher($data, $person->id)->workForce->id;
            }

            $edu = $this->EducationalRecordStore([$person], $workForce);
            \DB::commit();
            return response()->json(["message" => "مدرس با موفقیت افزوده شد"], 200);

        } catch (\Exception $exception) {
            \DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], 500);
        }

    }

    public function isTeacherExist(Request $request): JsonResponse
    {
        $situation = $this->isPersonTeacher($request->nationalCode);
        return response()->json($situation);
    }
}
