<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Http\Traits\PositionTrait;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\Position;
use Modules\OUnitMS\app\Models\OrganizationUnit;

class PositionController extends Controller
{
    use PositionTrait, UserTrait;

    public array $data = [];
//    protected PositionService $positionService;

//    /**
//     * @param PositionService $positionService
//     */
//    public function __construct(PositionService $positionService)
//    {
//        $this->positionService = $positionService;
//    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $result = $this->positionIndex();

        return response()->json($result);
    }

    public function getByOrganizationUnit(Request $request): JsonResponse
    {
        $ounit = OrganizationUnit::with('positions.levels')->findOr($request->ounitID, function () {
            return response()->json(['message' => 'واحد سازمانی یافت نشد'], 404);
        });

        return response()->json($ounit->positions);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {

        try {
            \DB::beginTransaction();


            $data = $request->all();

            $pos = $this->positionStore($data);

            \DB::commit();

            return response()->json($pos);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'خطا در ایجاد سمت', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $result = $this->positionShow($id);
        if (is_null($result)) {
            return response()->json(['message' => 'موزدی یافت نشد'], 404);

        }
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $result = Position::findOr($id, function () {
            return response()->json(['message' => 'موزدی یافت نشد'], 404);
        });


        try {
            $users = User::select('users.*')
                ->join('persons', 'users.person_id', '=', 'persons.id')
                ->join('work_forces', function ($join) {
                    $join->on('persons.id', '=', 'work_forces.person_id')
                        ->where('work_forces.workforceable_type', Employee::class);
                })
                ->join('employees', 'work_forces.workforceable_id', '=', 'employees.id')
                ->join('recruitment_scripts', 'employees.id', '=', 'recruitment_scripts.employee_id')
                ->join('recruitment_script_status as rss', 'recruitment_scripts.id', '=', 'rss.recruitment_script_id')
                ->join('statuses as s', 'rss.status_id', '=', 's.id')
                ->join('positions', 'recruitment_scripts.position_id', '=', 'positions.id')
                ->where('s.name', 'فعال')
                ->where('positions.id', $result->id)
                ->whereRaw('rss.create_date = (SELECT MAX(create_date) FROM recruitment_script_status as sub_rss WHERE sub_rss.recruitment_script_id = rss.recruitment_script_id)')
                ->get();

            \DB::beginTransaction();

            foreach ($users as $user) {
                $this->detachRolesByPosition($user, $result->id);
            }

            $data = $request->all();

            $pos = $this->positionUpdate($data, $result);

            $userIds = $users->pluck('id')->toArray();
            $roleIds = json_decode($data['roleIDs'], true);

            $data = [];
            foreach ($userIds as $userId) {
                foreach ($roleIds as $roleId) {
                    $data[] = ['user_id' => $userId, 'role_id' => $roleId];
                }
            }

            DB::table('user_role')->insert($data);

            \DB::commit();

            return response()->json($pos);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'خطا در بروزرسانی سمت', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $result = Position::findOr($id, function () {
            return response()->json(['message' => 'موزدی یافت نشد'], 404);
        });

        $status = $this->positionDelete($result);

        return response()->json(['message' => 'سمت با موفقیت حذف شد']);
    }
}
