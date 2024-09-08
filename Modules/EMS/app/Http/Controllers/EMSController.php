<?php

namespace Modules\EMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\EMS\app\Models\EnactmentTitle;
use Modules\OUnitMS\app\Models\VillageOfc;

class EMSController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    public function addBaseInfo()
    {
        $user = Auth::user();
        $titles = EnactmentTitle::all();
        $ounits = $user->activeRecruitmentScripts()
            ->whereHas('ounit', function ($query) {
                $query->where('unitable_type', VillageOfc::class)->with('ancestors');
            })
            ->whereHas('issueTime', function ($query) {
                $query->where('issue_times.title', 'شروع به همکاری');
            })
            ->with('ounit')
            ->get();


        $result = [
            'enactmentTitles' => $titles,
            'ounits' => $ounits->pluck('ounit'),
        ];

        return response()->json($result);


    }
}
