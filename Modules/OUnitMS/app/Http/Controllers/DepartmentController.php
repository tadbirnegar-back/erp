<?php

namespace Modules\OUnitMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Modules\OUnitMS\app\Http\Traits\OrganizationUnitTrait;

class DepartmentController extends Controller
{
    use OrganizationUnitTrait;

    public function index(Request $request)
    {
        $searchTerm = $request->name ?? null;
        $perPage = $request->perPage ?? 10;
        $page = $request->pageNum ?? 1;
        $ounitID = $request->orginizationUnit ?? null;


        $departemans = $this->departmentIndex($searchTerm, $ounitID, $perPage, $page);

        return response()->json($departemans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();
            $state = $this->departmentStore($data);
            DB::commit();

            return response()->json($state);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ایجاد استانداری جدید'], 500);
        }
    }

    public function show($id)
    {
        return view('ounitms::show');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
