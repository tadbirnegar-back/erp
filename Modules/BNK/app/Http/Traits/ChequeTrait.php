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

    public function updateChequeBook(array $data, ChequeBook $chequeBook)
    {
        $chequeBook->cheque_series = $data['series'];
        $chequeBook->cheque_count = $data['count'];
        $chequeBook->save();

        $chequeBook->statuses()->attach($data['cbStatusID']);

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

    public function updateCheque(array $data, Cheque $cheque)
    {
        $data['segmentNumber'] = $cheque->segment_number;
        $preparedData = $this->prepareChequeData($data, $cheque->chequeBook);
        $cheque->update($preparedData->toArray()[0]);
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

    public function resetChequeAndFree(Cheque $cheque)
    {
        $cheque->update([
            'payee_name' => null,
            'due_date' => null,
        ]);
        $cheque->statuses()->attach($this->whiteChequeStatus()->id);

        return $cheque;
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

    public function issuedChequeStatus()
    {
        return Cheque::GetAllStatuses()->where('name', ChequeStatusEnum::ISSUE->value)->first();
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
