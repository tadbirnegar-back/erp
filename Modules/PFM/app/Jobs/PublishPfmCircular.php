<?php

namespace Modules\PFM\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PublishPfmCircular implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

    }
}
