<?php

namespace Modules\ACC\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Models\Account;
use Modules\ACC\app\Models\AccountCategory;
use Validator;

class AccountsController extends Controller
{
    use AccountTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ounitID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        $accs = AccountCategory::leftJoinRelationship('accounts', function ($join) use ($data) {
            $join->where('ounit_id', $data['ounitID'])
                ->withGlobalScopes();
        })
            ->select([
                'acc_accounts.id as id',
                'acc_accounts.name as title',
                'acc_accounts.segment_code as code',
                'acc_accounts.chain_code as chainedCode',
                'acc_accounts.parent_id as parent_id',
                'acc_account_categories.id as categoryID',
                'acc_account_categories.name as accountCategory',
            ])
            ->get();
        $accs = $accs->groupBy('accountCategory')->map(function ($item) {
            return $item->toHierarchy();
        });

        return response()->json(['data' => $accs]);
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }
        $account = Account::find($id);
        if (!$account) {
            return response()->json(['message' => 'رکورد مورد نظر یافت نشد'], 404);
        }
        try {

            DB::beginTransaction();
            $account->name = $request->name;
            $account->save();
            DB::commit();
            return response()->json(['data' => $account]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();

        $validate = Validator::make($data, [
            'ounitID' => 'required',
            'name' => 'required',
            'segmentCode' => 'required',
            'categoryID' => 'sometimes',
            'parentID' => 'sometimes',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        try {
            DB::beginTransaction();
            if (isset($data['parentID'])) {
                $parent = Account::find($data['parentID']);
            } else {
                $parent = null;
            }
            $account = $this->storeAccount($data, $parent);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($account);
    }

    public function getAddAccountBaseInfo()
    {
        $result['categories'] = AccountCategory::all();

        return response()->json(['data' => $result]);
    }

    public function deleteAccount($id)
    {


        try {
            DB::beginTransaction();
            $status = $this->inactiveAccountStatus();
            $acc = Account::with('descendantsAndSelf')->find($id);
            if (!$acc) {
                return response()->json(['message' => 'رکورد مورد نظر یافت نشد'], 404);
            }
            $descendants = $acc->descendantsAndSelf;

            $descendants->each(function ($item) use ($status) {
                $item->status_id = $status->id;
                $item->save();
            });

            DB::commit();
            return response()->json(['message' => 'با موفقیت حذف شد'], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
