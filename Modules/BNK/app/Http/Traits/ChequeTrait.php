<?php

namespace Modules\BNK\app\Http\Traits;

use Modules\BNK\app\Http\Enums\ChequeBookStatusEnum;
use Modules\BNK\app\Http\Enums\ChequeStatusEnum;
use Modules\BNK\app\Models\Cheque;
use Modules\BNK\app\Models\ChequeBook;

trait ChequeTrait
{

    public function storeChequeBook(array $data)
    {
        $prepareData = $this->prepareChequeBookData($data);
        $chequeBook = ChequeBook::create($prepareData->toArray()[0]);

        $chequeBook->statuses()->attach($this->activeChequeBook()->id);

        return $chequeBook;

    }

    public function bulkInsertCheque(array $data, ChequeBook $book)
    {
        $prepareData = $this->prepareChequeData($data, $book);
        $cheque = Cheque::insert($prepareData->toArray());

        $latestCheques = Cheque::take($prepareData->count())->orderBy('id', 'desc')->get();
        $status = $this->whiteChequeStatus();

        $latestCheques->each(function ($cheque) use ($status) {
            $cheque->statuses()->attach($status->id);
        });

        return $cheque;
    }


    public function prepareChequeBookData(array $data)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data)->map(function ($item) {
            return [
                'cheque_series' => $item['series'],
                'cheque_count' => $item['count'],
                'account_id' => $item['accountID'],
                'creator_id' => $item['userID'],
            ];
        });

        return $data;
    }

    public function prepareChequeData(array $data, ChequeBook $chequeBook)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }

        $data = collect($data)->map(function ($item) use ($chequeBook) {
            return [
                'payee_name' => $item['payeeName'] ?? null,
                'segment_number' => $item['segmentNumber'],
                'amount' => $item['amount'] ?? null,
                'cheque_book_id' => $chequeBook->id,
                'due_date' => $item['dueDate'] ?? null,
                'signed_date' => $item['signedDate'] ?? null,
            ];
        });

        return $data;
    }

    public function whiteChequeStatus()
    {
        return Cheque::GetAllStatuses()->where('name', ChequeStatusEnum::BLANK->value)->first();
    }

    public function activeChequeBook()
    {
        return ChequeBook::GetAllStatuses()->where('name', ChequeBookStatusEnum::ACTIVE->value)->first();
    }

    public function deletedChequeStatus()
    {
        return ChequeBook::GetAllStatuses()->where('name', ChequeStatusEnum::DELETED->value)->first();
    }


}
