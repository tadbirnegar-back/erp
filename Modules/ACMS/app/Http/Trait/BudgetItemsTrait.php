<?php

namespace Modules\ACMS\app\Http\Trait;

use Modules\ACMS\app\Models\BudgetItem;
use Modules\ACMS\app\Models\Circular;

trait BudgetItemsTrait
{
    public function bulkStoreBudgetItems(array $data, Circular $circular)
    {
        $preparadoData = $this->budgetItemsDataPreparation($data, $circular);

        $circularItems = BudgetItem::insert($preparadoData->toArray());

        return $circularItems;
    }

    public function budgetItemsDataPreparation(array $data, Circular $circular)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data)->map(function ($item) use ($circular) {

            return [
                'budget_id' => $item['id'],
                'circular_item_id' => $circular->id,
                'proposed_amount' => $item['proposed_amount'] ?? 0,
                'finalized_amount' => $item['approved_amount'] ?? 0,
            ];
        });

        return $data;

    }
}
