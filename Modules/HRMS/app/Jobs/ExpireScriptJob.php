<?php

namespace Modules\HRMS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpireScriptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $scriptid;

    /**
     * Create a new job instance.
     */
    public function __construct($scriptid)
    {
        $this->scriptid = $scriptid;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
