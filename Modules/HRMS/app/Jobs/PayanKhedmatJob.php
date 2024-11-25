<?php

namespace Modules\HRMS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\Position;
use Modules\HRMS\app\Notifications\PayanKhedmatRsNotification;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\PersonMS\app\Models\Person;

class PayanKhedmatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public object $rs;

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
        $this->rs->finish_date = $this->rs->expire_date;
        $this->rs->save();
        $emp = Employee::find($this->rs->employee_id);
        $emp->load('user');

        $username = Person::find($emp->user->person_id)->display_name;
        $ounitName = OrganizationUnit::find($this->rs->organization_unit_id)->name;
        $position = Position::find($this->rs->position_id)->name;

        $emp->user->notify(new PayanKhedmatRsNotification($username, $ounitName, $position));

    }
}
