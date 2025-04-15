<?php

namespace Modules\PFM\app\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\PFM\app\Http\Traits\BookletTrait;
use Modules\PFM\app\Models\Booklet;
use Modules\PFM\app\Models\BookletStatus;

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

        $this->attachDarEntazarStatus($booklet->id, $this->userId);
    }
}
