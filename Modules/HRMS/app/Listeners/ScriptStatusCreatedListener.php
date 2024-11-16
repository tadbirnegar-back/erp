<?php

namespace Modules\HRMS\app\Listeners;

use Carbon\Carbon;
use Modules\HRMS\app\Events\ScriptStatusCreatedEvent;
use Modules\HRMS\app\Http\Enums\RecruitmentScriptStatusEnum;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Jobs\ExpireScriptJob;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\RecruitmentScriptStatus\ActiveHandler;
use Modules\HRMS\app\RecruitmentScriptStatus\PendingApproveHandler;

class ScriptStatusCreatedListener
{
    use RecruitmentScriptTrait;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ScriptStatusCreatedEvent $event): void
    {
        $recstatus = $event->recStatus;

        if ($recstatus->status_id == $this->pendingRsStatus()->id) {
            $expireDate = RecruitmentScript::find($recstatus->recruitment_script_id)->expire_date;

            // Ensure consistent timezone
            $expireDateCarbon = Carbon::parse($expireDate)->setTimezone(config('app.timezone'));
            $now = Carbon::now();

            // Calculate the delay
            $delayInSeconds = $expireDateCarbon->timestamp - $now->timestamp;

            // Dispatch the job only if delay is positive
            if ($delayInSeconds > 0) {
                ExpireScriptJob::dispatch($recstatus->recruitment_script_id)->delay($delayInSeconds);
            }
        }
    }

    public function getRelatedClassByStatusName(string $statusName): ?string
    {
        $statuses = [
            RecruitmentScriptStatusEnum::PENDING_APPROVAL->value => PendingApproveHandler::class,
//            RecruitmentScriptStatusEnum::REJECTED->value => RejectedHandler::class,
            RecruitmentScriptStatusEnum::ACTIVE->value => ActiveHandler::class,
            RecruitmentScriptStatusEnum::TERMINATED->value => TerminatedHandler::class,
            RecruitmentScriptStatusEnum::SERVICE_ENDED->value => ServiceEndedHandler::class,
            RecruitmentScriptStatusEnum::CANCELED->value => CanceledHandler::class,
            RecruitmentScriptStatusEnum::PENDING_FOR_TERMINATE->value => PendingForTerminateHandler::class,
            RecruitmentScriptStatusEnum::EXPIRED->value => PendingForTerminateHandler::class,

        ];

        return $statuses[$statusName] ?? null;
    }

}
