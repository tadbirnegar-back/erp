<?php

namespace Modules\PFM\app\Http\Traits;


use Modules\PFM\app\Http\Enums\BookletStatusEnum;
use Modules\PFM\app\Http\Enums\LevyStatusEnum;
use Modules\PFM\app\Models\Booklet;
use Modules\PFM\app\Models\BookletStatus;
use Modules\PFM\app\Models\Levy;

trait BookletTrait
{
    public function bookletsWithStatuses($circularId)
    {

        $MosavabStatus = $this->MosavabStatus();
        $DarEntazarStatus = $this->DarEntazarStatus();
        $PishnahadShodeStatus = $this->PishnahadShodeStatus();

        $mosavabStatus = Booklet::joinRelationship('statuses', ['statuses' => function ($join) {
            $join->whereRaw('pfm_booklet_status.created_date = (SELECT MAX(created_date) FROM pfm_booklet_status WHERE booklet_id = pfm_circular_booklets.id)');
        }])
            ->select([
                'pfm_circular_booklets.id',
            ])
            ->distinct('pfm_circular_booklets.id')
            ->where('pfm_booklet_status.status_id', $MosavabStatus->id)
            ->where('pfm_circular_booklets.pfm_circular_id', $circularId)
            ->get();

        $countOfMosavabStatus = count($mosavabStatus);

        $darEntazarStatus = Booklet::joinRelationship('statuses', ['statuses' => function ($join) {
            $join->whereRaw('pfm_booklet_status.created_date = (SELECT MAX(created_date) FROM pfm_booklet_status WHERE booklet_id = pfm_circular_booklets.id)');
        }])
            ->select([
                'pfm_circular_booklets.id',
            ])
            ->distinct('pfm_circular_booklets.id')
            ->where('pfm_booklet_status.status_id', $DarEntazarStatus->id)
            ->where('pfm_circular_booklets.pfm_circular_id', $circularId)
            ->get();

        $countOfDarEntazarStatus = count($darEntazarStatus);

        $pishnahadShodeStatus = Booklet::joinRelationship('statuses', ['statuses' => function ($join) {
            $join->whereRaw('pfm_booklet_status.created_date = (SELECT MAX(created_date) FROM pfm_booklet_status WHERE booklet_id = pfm_circular_booklets.id)');
        }])
            ->select([
                'pfm_circular_booklets.id',
            ])
            ->distinct('pfm_circular_booklets.id')
            ->where('pfm_booklet_status.status_id', $PishnahadShodeStatus->id)
            ->where('pfm_circular_booklets.pfm_circular_id', $circularId)
            ->get();

        $countOfPishnahadShodeStatus = count($pishnahadShodeStatus);

        return [
            'countOfMosavabStatus' => $countOfMosavabStatus,
            'countOfDarEntazarStatus' => $countOfDarEntazarStatus,
            'countOfPishnahadShodeStatus' => $countOfPishnahadShodeStatus,
        ];
    }
    public function attachPishnahadShodeStatus($id, $user)
    {
        BookletStatus::create([
            'booklet_id' => $id,
            'status_id' => $this->PishnahadShodeStatus()->id,
            'created_date' => now(),
            'creator_id' => $user,
        ]);
    }

    public function attachMosavabStatus($id, $user)
    {
        BookletStatus::create([
            'booklet_id' => $id,
            'status_id' => $this->MosavabStatus()->id,
            'created_date' => now(),
            'creator_id' => $user,
        ]);
    }

    public function attachDarEntazarStatus($id, $user)
    {
        BookletStatus::create([
            'booklet_id' => $id,
            'status_id' => $this->DarEntazarStatus()->id,
            'created_date' => now(),
            'creator_id' => $user,
        ]);
    }



    public function MosavabStatus()
    {
        return Booklet::GetAllStatuses()->firstWhere('name', BookletStatusEnum::MOSAVAB->value);
    }

    public function DarEntazarStatus()
    {
        return Booklet::GetAllStatuses()->firstWhere('name', BookletStatusEnum::DAR_ENTEZAR->value);
    }

    public function PishnahadShodeStatus()
    {
        return Booklet::GetAllStatuses()->firstWhere('name', BookletStatusEnum::PISHNAHAD_SHODE->value);
    }
}
