<?php

namespace Modules\PFM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\PFM\app\Http\Enums\PfmCircularStatusesEnum;
use Modules\PFM\app\Http\Traits\LevyItemTrait;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\LevyCircular;
use Modules\PFM\app\Models\PfmCirculars;

class LevyItemsController extends Controller
{

    use LevyItemTrait;

    public function store(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $data = $request->all();
            $this->storeItems($data['text'], $id);

            $data = $this->indexItems($data['circularID']);
            \DB::commit();

            return response()->json($data);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $this->deleteItems($id);
            return response()->json(['message' => 'اطلاعات با موفقیت حذف شد']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حذف اطلاعات با مشکل مواجه شد']);
        }
    }

    public function index($id)
    {
        $data = $this->indexItems($id);

        $circular = $this->findCircularID($id);


        $circularID = $circular->circular_id;


        $query = PfmCirculars::joinRelationship('fiscalYear')
            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join->whereRaw('pfm_circular_statuses.created_date = (SELECT MAX(created_date) FROM pfm_circular_statuses WHERE pfm_circular_id = pfm_circulars.id)');
            }])
            ->select([
                'statuses.name as status_name',
                'statuses.class_name as status_class',
                'fiscal_years.name as fiscal_year_name',
            ])
            ->where('pfm_circulars.id', $circularID)
            ->get();

        $status = $query->first()->status_name;
        if($status == PfmCircularStatusesEnum::DRAFT->value){
            $editable = true;
        }else{
            $editable = false;
        }
        $year = $query->first()->fiscal_year_name;



        $levyName = LevyCircular::join('pfm_levies as levies', 'pfm_levy_circular.levy_id', '=', 'levies.id')
            ->select([
                'levies.name as name',
            ])->find($id)->name;

        return response()->json(["data" => $data , 'editable' => $editable, 'year' => $year , 'levyName' => $levyName]);
    }

    public function update(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $data = $request->all();
            $this->updateItems($data['text'], $id);
            $data = $this->indexItems($data['circularID']);
            \DB::commit();
            return response()->json($data);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'ویرایش انجام نگرفت']);
        }
    }
}
