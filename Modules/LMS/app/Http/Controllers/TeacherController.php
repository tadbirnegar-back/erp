<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Modules\LMS\app\Http\Trait\TeacherTrait;


class TeacherController extends Controller
{
    use TeacherTrait;
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
    public function store(Request $request): RedirectResponse
    {
        $data = $request->all();
        try {
            DB::beginTransaction();
            $personResult = isset($request->personID) ?
                $this->naturalUpdate($data, $data['personID']) :
                $this->naturalStore($data);

            $data['personID'] = $personResult->person->id;
            $data['password'] = $data['nationalCode'];
            $personAsTeacher = $this->isTeacher($data['personID']);

        } catch (Exception $e) {
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
