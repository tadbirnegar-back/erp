<?php

namespace Modules\BNK\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Modules\BNK\app\Http\Enums\ChequeStatusEnum;
use Modules\BNK\app\Http\Traits\ChequeTrait;
use Modules\BNK\app\Models\Cheque;
use Modules\BNK\app\Models\ChequeBook;
use Validator;

class ChequeController extends Controller
{
    use ChequeTrait;

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'series' => 'required',
            'count' => 'required',
            'accountID' => 'required',
            'startNumber' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $data['userID'] = auth()->user()->id;
        try {
            DB::beginTransaction();
            $chequeBook = $this->storeChequeBook($data);
            $chequeData = [];

            for ($i = 0; $i < $data['count']; $i++) {
                $chequeData[] = [
                    'segmentNumber' => $data['startNumber'] + $i,
                ];
            }

            $this->bulkInsertCheque($chequeData, $chequeBook);

            DB::commit();
            $chequeBook->load('cheques.latestStatus', 'latestStatus');
            return response()->json(['data' => $chequeBook]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error'], 500);
        }

    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'series' => 'required',
            'count' => 'required',
            'startNumber' => 'required',
            'cbStatusID' => ['required', 'exists:statuses,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $chequeBook = ChequeBook::with('cheques')->find($id);

            $blankCheques = Cheque::where('cheque_book_id', $chequeBook->id)
                ->whereHas('latestStatus', function ($query) {
                    $query->where('statuses.name', ChequeStatusEnum::BLANK->value);
                })
                ->count();

            if ($chequeBook->cheques->count() != $blankCheques) {
                return response()->json(['error' => 'چک استفاده شده دارید و دسترسی ویرایش دسته چک مجاز نیست'], 403);
            }
            $chequeBook->statuses()->attach($data['cbStatusID']);

//            $deleteStatus = $this->deletedChequeBook();
            $chequeBook->cheques->each(function ($cheque) {
                $cheque->delete();
            });
            $chequeData = [];

            for ($i = 0; $i < $data['count']; $i++) {
                $chequeData[] = [
                    'segmentNumber' => $data['startNumber'] + $i,
                ];
            }
            $chequeBook = $this->updateChequeBook($data, $chequeBook);

            $this->bulkInsertCheque($chequeData, $chequeBook);
            DB::commit();
            $chequeBook->load('cheques.latestStatus', 'latestStatus');
            return response()->json(['data' => $chequeBook]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error'], 500);
        }

    }
}
