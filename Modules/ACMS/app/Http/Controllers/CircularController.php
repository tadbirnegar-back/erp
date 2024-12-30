<?php

namespace Modules\ACMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;
use Modules\ACMS\app\Http\Trait\CircularTrait;
use Modules\ACMS\app\Http\Trait\FiscalYearTrait;
use Modules\ACMS\app\Models\Circular;
use Modules\ACMS\app\Models\CircularSubject;
use Modules\ACMS\app\Resources\CircularListResource;
use Modules\ACMS\app\Resources\CircularShowResource;
use Validator;

class CircularController extends Controller
{
    use CircularTrait, FiscalYearTrait;

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $user = Auth::user();
        $validate = Validator::make($data, [
            'fiscalYearName' => 'required',
            'circularName' => 'required',
            'fileID' => 'required',
            'startDate' => 'required',
            'finishDate' => 'required',
        ]);


        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }

        $data['userID'] = $user->id;

        try {
            $fiscalYear = $this->createFiscalYear($data);
            $circular = $this->createCircular($data, $fiscalYear);
            $circularSubjects = CircularSubject::get(['id']);
            $circular->statuses()->attach($circularSubjects->toArray());

            return response()->json(['data' => $circular]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function index(Request $request)
    {
        $data = $request->all();

        $circulars = $this->indexCircular($data);

        return CircularListResource::collection($circulars);

    }

    public function show($id)
    {
        $circular = Circular::with('circularSubjects', 'latestStatus:name,class_name', 'file:id,slug')->find($id);

        if (is_null($circular)) {
            return response()->json(['message' => 'بخشنامه مورد نظر یافت نشد'], 404);
        }

        return CircularShowResource::make($circular);

    }

}
