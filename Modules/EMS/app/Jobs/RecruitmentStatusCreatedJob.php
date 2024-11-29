<?php

namespace Modules\EMS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\EMS\app\Http\Traits\MeetingMemberTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;

class RecruitmentStatusCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MeetingMemberTrait, RecruitmentScriptTrait, MeetingTrait;

    public int $rs;


    /**
     * Create a new job instance.
     */
    public function __construct(int $rs)
    {
        $this->rs = $rs;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {


    }
}
