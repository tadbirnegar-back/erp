<?php

namespace Modules\HRMS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Notifications\RegisterNotification;

class ExpireScriptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $rs;

    /**
     * Create a new job instance.
     */
    public function __construct($rs)
    {
        $this->rs = $rs;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Retrieve the recruitment script along with the employee, person, and user data
        $recruitmentScript = RecruitmentScript::with(['employee.person.user', 'scriptType', 'ounit'])->find($this->rs);

        //Todo :
        //make Script's status to become GheyreFaal in here

        if ($recruitmentScript && $recruitmentScript->employee && $recruitmentScript->employee->person && $recruitmentScript->employee->person->user) {
            $user = $recruitmentScript->employee->person->user;
            $person = $recruitmentScript->employee->person;
            $scriptTypeName = $recruitmentScript->scriptType ? $recruitmentScript->scriptType->title : 'نامعلوم';
            $ounit = $recruitmentScript->ounit;

            $ExpDateEng = \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($recruitmentScript->expire_date)); // 1395-02-19
            $ExpDateFarsi = \Morilog\Jalali\CalendarUtils::convertNumbers($ExpDateEng); // ۱۳۹۵-۰۲-۱۹

            Log::info($user->id);

            $user->notify(new RegisterNotification($person->display_name, $ExpDateFarsi, $scriptTypeName, $ounit->name));
        } else {
            Log::warning('User not found for RecruitmentScript ID: ' . $this->rs);
        }
    }
}
