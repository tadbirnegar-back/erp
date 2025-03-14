<?php

namespace Modules\ACC\app\Http\Traits;

use Modules\ACC\app\Http\Enums\AccountStatusEnum;
use Modules\ACC\app\Models\Account;
use Modules\ACC\app\Models\DetailAccount;
use Modules\ACC\app\Models\GlAccount;
use Modules\ACC\app\Models\SubAccount;
use Modules\ACMS\app\Models\CircularSubject;
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

        $preparationData = $this->accountDataPreparation($data, $accountable->id, $accountTypeToInsert, $parent, $status);
        $account = Account::create($preparationData->toArray()[0]);


        return $account;
    }

    public function firstOrStoreAccount(array $data, ?Account $parent = null, $status = null): Account
    {
        $accountTypeToInsert = self::$childTypeOfCurrentParent[$parent?->accountable_type];

        $account = Account::
        where('segment_code', $data['segmentCode'])
            ->where('chain_code', $data['chainCode'])
            ->where('ounit_id', $data['ounitID'])
            ->where('parent_id', $parent?->id)
            ->where('category_id', $data['categoryID'] ?? $parent?->category_id)
//            ->where('accountable_type', $accountTypeToInsert)
            ->withoutGlobalScopes()
            ->first();

        if ($account) {
            return $account;
        }

        $accountable = new $accountTypeToInsert();
        $accountable->save();

        $status = is_null($status) ? $this->inactiveAccountStatus() : $this->activeAccountStatus();

        $preparationData = $this->accountImportDataPreparation($data, $accountable->id, $accountTypeToInsert, $parent, $status);
        $account = Account::create($preparationData->toArray()[0]);


        return $account;
    }

    public function accountDataPreparation(array $data, int $accountableID, string $accountableType, ?Account $parent, Status $status)
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

            if (isset($item['entityType']) && $item['entityType'] == CircularSubject::class) {
                $item['chainCode'] = $item['segmentCode'];
            }

            if ($accountableType === DetailAccount::class) {
                $ounitID = $item['ounitID'];
            } else {
                $ounitID = null;
            }

            return [
                'name' => convertToDbFriendly($item['name']),
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

    public function accountImportDataPreparation(array $data, int $accountableID, string $accountableType, ?Account $parent, Status $status)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data)->map(function ($item) use ($parent, $accountableID, $accountableType, $status) {
//            if ($parent) {
//                $item['categoryID'] = $parent->category_id;
//                $item['chainCode'] = $parent->chain_code . $item['segmentCode'];
//            } else {
//                $item['chainCode'] = $item['categoryID'] . $item['segmentCode'];
//            }

            if (isset($item['entityType']) && $item['entityType'] == CircularSubject::class) {
                $item['chainCode'] = $item['segmentCode'];
            }

//            if ($accountableType === DetailAccount::class) {
            $ounitID = $item['ounitID'];
//            } else {
//                $ounitID = null;
//            }

            return [
                'name' => convertToDbFriendly($item['name']),
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
                'new_chain_code' => $item['newChainCode'] ?? null,

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

    public function getNewChainCode(string $code)
    {
        return match ($code) {
            '312007' => '311125',
            '312004' => '311123',
            '313001' => '311128',
            '313002' => '311127',
            '314001' => '311001',
            '316001' => '311105',
            '315001', '315002' => '311101',
            '313003' => '311130',
            '312003' => '311116',
            '312001' => '311117',
            '312002' => '311118',
            '314002' => '311002',
            '314003' => '311003',
            '510010' => '510001',
            default => null,

        };
    }
}

