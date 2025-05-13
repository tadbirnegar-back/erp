<?php

namespace Modules\PFM\app\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\PFM\app\Http\Enums\LeviesListEnum;
use Modules\PFM\app\Http\Traits\BookletTrait;
use Modules\PFM\app\Models\Booklet;
use Modules\PFM\app\Models\BookletStatus;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\LevyCircular;
use Modules\PFM\app\Models\LevyItem;
use Modules\PFM\app\Models\Tarrifs;

class PublishPfmCircularJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels , BookletTrait;

    protected $circualrId;
    protected $userId;
    protected $ounitId;


    /**
     * Create a new job instance.
     */
    public function __construct($circualrId, $userId, $ounitId)
    {
        $this->circualrId = $circualrId;
        $this->userId = $userId;
        $this->ounitId = $ounitId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $booklet = Booklet::create([
            'pfm_circular_id' => $this->circualrId,
            'ounit_id' => $this->ounitId,
            'created_date' => now(),
        ]);

        $leviesToInsertToTariff = [
            LeviesListEnum::SUDURE_PARVANEH_SAKHTEMAN->value,
            LeviesListEnum::ARZESHE_AFZODEH_HADI->value,
            LeviesListEnum::CHESHME_MADANI->value,
        ];

        foreach ($leviesToInsertToTariff as $levy) {
            $levyID = Levy::where('name' , $levy)->first()->id;
            $levyCircular = LevyCircular::where('levy_id' , $levyID)
                ->where('circular_id' , $this->circualrId)
                ->first();

            if($levyCircular){
                $levyItem = LevyItem::where('circular_levy_id' , $levyCircular->id)->first();
                if($levyItem){
                    Tarrifs::create([
                        'item_id' => $levyItem->id,
                        'booklet_id' => $booklet->id,
                        'value' => 1,
                        'app_id' => null,
                        'created_date'=>now(),
                        'creator_id' => $this->userId,
                    ]);
                }else{
                    \Log::info('Levy Item not found');
                }
            }else{
                \Log::info('Levy Circular not found');
            }
        }

        $this->attachDarEntazarStatus($booklet->id, $this->userId);
    }
}
