<?php

namespace Modules\ACC\app\Http\Traits;

use Modules\ACC\app\Http\Enums\AccountStatusEnum;
use Modules\ACC\app\Models\Account;
use Modules\ACC\app\Models\DetailAccount;
use Modules\ACC\app\Models\GlAccount;
use Modules\ACC\app\Models\SubAccount;
use Modules\StatusMS\app\Models\Status;

trait AccountTrait
{
    public static array $childTypeOfCurrentParent = [
        null => GlAccount::class,
        GlAccount::class => SubAccount::class,
        SubAccount::class => DetailAccount::class,
        DetailAccount::class => DetailAccount::class,
    ];

    public function storeAccount(array $data, ?Account $parent = null): Account
    {
        $accountTypeToInsert = self::$childTypeOfCurrentParent[$parent?->accountable_type];

        $accountable = new $accountTypeToInsert();
        $accountable->save();

        $status = $this->activeAccountStatus();

        $preparationData = $this->dataPreparation($data, $accountable->id, $accountTypeToInsert, $parent, $status);
        $account = Account::create($preparationData->toArray()[0]);


        return $account;
    }

    public function dataPreparation(array $data, int $accountableID, string $accountableType, ?Account $parent, Status $status)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data)->map(function ($item) use ($parent, $accountableID, $accountableType, $status) {
            if ($parent) {
                $item['categoryID'] = $parent->category_id;
                $item['chainCode'] = $parent->chain_code . $item['segmentCode'];
            } else {
                $item['chainCode'] = $item['categoryID'] . $item['segmentCode'];
            }

            if ($accountableType === DetailAccount::class) {
                $ounitID = $item['ounitID'];
            } else {
                $ounitID = null;
            }

            return [
                'name' => $item['name'],
                'segment_code' => $item['segmentCode'],
                'chain_code' => $item['chainCode'],
                'accountable_id' => $accountableID,
                'accountable_type' => $accountableType,
                'parent_id' => $parent?->id ?? null,
                'ounit_id' => $ounitID,
                'category_id' => $item['categoryID'],
                'subject_id' => $item['subjectID'] ?? null,
                'status_id' => $status->id,
                'entity_type' => $item['entityType'] ?? null,
                'entity_id' => $item['entityID'] ?? null,
            ];
        });

        return $data;
    }

    public function activeAccountStatus()
    {
        return Account::GetAllStatuses()->where('name', AccountStatusEnum::ACTIVE->value)->first();
    }

    public function inactiveAccountStatus()
    {
        return Account::GetAllStatuses()->where('name', AccountStatusEnum::INACTIVE->value)->first();
    }
}

