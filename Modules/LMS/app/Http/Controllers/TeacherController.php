<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Modules\AAA\app\Models\Module;
use Modules\HRMS\app\Http\Traits\EducationRecordTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Http\Traits\RelativeTrait;
use Modules\HRMS\app\Http\Traits\ResumeTrait;
use Modules\LMS\app\Http\Trait\TeacherTrait;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Natural;


class TeacherController extends Controller
{
    use TeacherTrait , EducationRecordTrait,RecruitmentScriptTrait,RelativeTrait,ResumeTrait,PersonTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('lms::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lms::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        try {
            DB::beginTransaction();
            $personResult = isset($request->personID) ?
                $this->naturalStore($data):
            $this->naturalUpdate($data,Natural::find( $data['personID']));


//
//            $personResult = isset($request->personID) ?
//                $this->naturalUpdate($data, Natural::find($data['personID'])) :
//                $this->naturalStore($data);



            $data['personID'] = $personResult->person->id;
            $data['password'] = $data['nationalCode'];
            $personAsTeacher = $this->isTeacher($data['personID']);
            $teacher = !is_null($personAsTeacher) ? $this->teacherUpdate($data, $personAsTeacher) : $this->storeteacher($data);
            $workForce = $teacher->workForce;

            if (isset($data['educations'])) {
                $edus = json_decode($data['educations'], true);

                $educations = $this->EducationalRecordStore($edus, $workForce->id);
            }

            if (isset($data['relatives'])) {
                $rels = json_decode($data['relatives'], true);

                $relatives = $this->RelativeStore($rels, $workForce->id);

            }


            if (isset($data['resumes'])) {
                $resumes = json_decode($data['resumes'], true);

                $resume = $this->resumeStore($resumes, $workForce->id);

            }


            if (isset($data['recruitmentRecords'])) {
                $rs = json_decode($data['recruitmentRecords'], true);

                $pendingStatus = $this->pendingRsStatus();
                $rsRes = $this->rsStore($rs, $teacher->id, $pendingStatus);
//                $rsRes = collect($rsRes);
//                $rsRes->each(function ($rs) use ($user) {
//                    $this->approvingStore($rs);
//                });

            }
            DB::commit();

            return response()->json($teacher);



        } catch ( Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در افزودن مدرس', 'error' => $e->getMessage()], 500);

        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('lms::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('lms::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        return redirect()->route('lms.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
