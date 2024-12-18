<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Http\Traits\QuestionsTrait;

class QuestionController extends Controller
{
    use QuestionsTrait;

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
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'creatorID' => 'required|integer|exists:users,id',
            'difficultyID' => 'required|integer|exists:difficulties,id',
            'lessonID' => 'required|integer|exists:lessons,id',
            'questionTypeID' => 'required|integer|exists:question_types,id',
            'repositoryID' => 'nullable|integer|exists:repositories,id',
            'createDate' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $question = $this->storeQuestion($validatedData);

            DB::commit();

            return response()->json('success', 'سوال با موفقیت ثبت شد.');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json('error', 'خطایی در ثبت سوال رخ داده است.');
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
