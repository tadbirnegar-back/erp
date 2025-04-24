<?php

namespace Modules\PFM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\AAA\app\Models\User;
use Modules\PFM\app\Http\Traits\BookletTrait;
use Modules\PFM\app\Models\Booklet;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\Tarrifs;
use Modules\PFM\app\Resources\ListOfBookletsResource;
use Modules\PFM\app\Resources\ShowBookletResource;

class BookletController extends Controller
{
    use BookletTrait;

    public function index(Request $request)
    {
        $data = $request->all();

        $data['isThisYear'] = false;
        $pageNum = $data['pageNum'] ?? 1;
        $perPage = $data['perPage'] ?? 10;

        $user = Auth::user();
        $data = $this->listOfBooklets($data, $user, $pageNum, $perPage);

        return ListOfBookletsResource::collection($data);
    }

    public function indexThisYear(Request $request)
    {
        $data = $request->all();

        $data['isThisYear'] = true;
        $pageNum = $data['pageNum'] ?? 1;
        $perPage = $data['perPage'] ?? 10;

        $user = Auth::user();
        $data = $this->listOfBooklets($data, $user, $pageNum, $perPage);

        return ListOfBookletsResource::collection($data);
    }

    public function show($id)
    {
        $user = Auth::user();
        $query = $this->showBooklet($id, $user);
        if ($query['status'] == 200) {
            return new ShowBookletResource($query);
        } else {
            return response()->json(['message' => $query['message']], $query['status']);
        }
    }

    public function showItems(Request $request, $levyId)
    {
        $data = $request->all();
        $bookletId = $data['booklet_id'];


        $query = Booklet::joinRelationship('statuses', ['statuses' => function ($join) {
            $join->whereRaw('pfm_booklet_statuses.created_date = (SELECT MAX(created_date) FROM pfm_booklet_statuses WHERE booklet_id = pfm_circular_booklets.id)');
        }])
            ->select([
                'statuses.name as status_name',
            ])
            ->where('pfm_circular_booklets.id', $bookletId)
            ->get();
        $status = $query->first()->status_name;
        $res = $this->showTable($levyId, $bookletId, $status);
        return response()->json($res);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $bookletId = $data['bookletID'];
        $value = $data['value'];
        $appIds = json_decode($data['applicationIDs']);

        $itemId = $data['itemID'];

        $user = Auth::user();


        collect($appIds)->each(function ($appId) use ($itemId, $bookletId, $value, $user) {
            Tarrifs::create([
                'item_id' => $itemId,
                'booklet_id' => $bookletId,
                'app_id' => $appId,
                'value' => $value,
                'creator_id' => $user->id,
                'created_date' => now(),
            ]);
        });

    }

    public function showPrices($id)
    {
        $data = Booklet::select(['pfm_circular_booklets.p_residential', 'pfm_circular_booklets.p_commercial', 'pfm_circular_booklets.p_administrative'])->find($id);
        return response()->json($data);
    }

    public function storePrices(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $booklet = Booklet::find($id);
            $booklet->p_residential = $data['p_residential'];
            $booklet->p_commercial = $data['p_commercial'];
            $booklet->p_administrative = $data['p_administrative'];
            $booklet->save();
            DB::commit();
            return response()->json(['message' => 'با موفقیت ثبت شد']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function submitBooklet($id)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $this->submitting($id, $user);
            DB::commit();
            return response()->json(['message' => 'ثبت دفترچه با موفقیت انجام گردید.']);
        } catch (Exception $e) {
            return response()->json(['message' => 'خطا در ثبت دفترچه'], 500);
        }

    }

    public function declineBooklet(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $data = $request->all();
            $this->attachRadShodeStatus($id, $user->id, $data['description'] ?? null, $data['fileID'] ?? null);
            $this->douplicateBooklet($id, $user->id);
            DB::commit();
            return response()->json(['message' => 'رد دفترچه با موفقیت انجام گردید.']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
