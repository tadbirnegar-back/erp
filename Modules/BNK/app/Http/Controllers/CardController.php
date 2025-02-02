<?php

namespace Modules\BNK\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\BNK\app\Http\Traits\BankAccountCardTrait;
use Modules\BNK\app\Models\BankAccountCard;
use Validator;

class CardController extends Controller
{
    use BankAccountCardTrait;

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
        $data = $request->all();
        $validate = Validator::make($data, [
            'accountID' => 'required',
            'cardNumber' => 'required',
            'expireDate' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }
        try {
            DB::beginTransaction();
            $bankAccountCard = $this->storeBankAccountCard($data);
            DB::commit();
            return response()->json(['data' => $bankAccountCard->load('latestStatus')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }


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
        $data = $request->all();
        $validate = Validator::make($data, [
            'statusID' => 'required',
            'cardNumber' => 'required',
            'expireDate' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }
        try {
            DB::beginTransaction();
            $card = BankAccountCard::find($id);
            $data['accountID'] = $card->account_id;
            $bankAccountCard = $this->updateBankAccountCard($data, $card);
            DB::commit();
            return response()->json(['data' => $bankAccountCard->load('latestStatus')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }
}
