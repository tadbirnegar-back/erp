<?php

namespace Modules\BNK\app\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\BNK\app\Http\Enums\CardStatusEnum;
use Modules\BNK\app\Models\BankAccountCard;

trait BankAccountCardTrait
{
    public function storeBankAccountCard(array $data)
    {
        $prepareData = $this->prepareBankAccountCardData($data);
        $bankAccountCard = BankAccountCard::create($prepareData->toArray()[0]);

        $bankAccountCard->statuses()->attach($this->activeBankAccountCardStatus()->id);

        return $bankAccountCard;
    }

    public function updateBankAccountCard(array $data, BankAccountCard $bankAccountCard)
    {
        $bankAccountCard->expire_date = $data['expireDate'];
        $bankAccountCard->card_number = $data['cardNumber'];
        $bankAccountCard->save();

        $bankAccountCard->statuses()->attach($data['statusID']);

        return $bankAccountCard;
    }


//    public function bulkInsertBankAccountCard(array $data)
//    {
//        $prepareData = $this->prepareBankAccountCardData($data);
//        $bankAccountCard = BankAccountCard::insert($prepareData->toArray());
//
//        $latestBankAccountCards = BankAccountCard::take($prepareData->count())->orderBy('id', 'desc')->get();
//        $status = $this->activeBankAccountCardStatus();
//
//        $latestBankAccountCards->each(function ($bankAccountCard) use ($status) {
//            $bankAccountCard->statuses()->attach($status->id);
//        });
//
//        return $bankAccountCard;
//    }

    public function prepareBankAccountCardData(array $data)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }

        $data = collect($data)->map(function ($item) {
            return [
                'card_number' => $item['cardNumber'],
                'expire_date' => $item['expireDate'],
                'account_id' => $item['accountID'],
            ];
        });

        return $data;
    }

    public function activeBankAccountCardStatus()
    {
        return Cache::rememberForever('bank_account_card_active_status', function () {
            return BankAccountCard::GetAllStatuses()
                ->where('name', CardStatusEnum::ACTIVE->value)
                ->first();
        });
    }

    public function cancellBankAccountCardStatus()
    {
        return Cache::rememberForever('bank_account_card_canceled_status', function () {
            return BankAccountCard::GetAllStatuses()
                ->where('name', CardStatusEnum::CANCELED->value)
                ->first();
        });
    }
}
