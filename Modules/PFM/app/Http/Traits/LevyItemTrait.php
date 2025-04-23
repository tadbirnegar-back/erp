<?php

namespace Modules\PFM\app\Http\Traits;

use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\LevyCircular;
use Modules\PFM\app\Models\LevyItem;

trait LevyItemTrait
{
    public function storeItems($text, $circularLevyId)
    {
        LevyItem::create([
            'name' => $text,
            'circular_levy_id' => $circularLevyId,
            'created_date' => now(),
        ]);

    }

    public function deleteItems($id)
    {
        LevyItem::find($id)->delete();
    }

    public function indexItems($id)
    {
        $data = LevyCircular::join('pfm_levy_items as levy_items' , 'pfm_levy_circular.id' , '=' , 'levy_items.circular_levy_id')
            ->select([
                'levy_items.id as item_id',
                'levy_items.name as item_name',
                'pfm_levy_circular.circular_id as circularID',
            ])
            ->distinct('levy_items.id')
            ->where('pfm_levy_circular.id', $id)
            ->get();
        return $data;
    }

    public function updateItems($text, $itemID)
    {
        LevyItem::find($itemID)->update(['name' => $text]);
    }

    public function findCircularID($id)
    {
        return LevyCircular::find($id);
    }
}
