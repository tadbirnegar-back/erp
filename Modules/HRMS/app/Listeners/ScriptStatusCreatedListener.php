<?php

namespace Modules\HRMS\app\Listeners;

use Auth;
use Modules\HRMS\app\Events\ScriptStatusCreatedEvent;
use Modules\HRMS\app\Http\Enums\RecruitmentScriptStatusEnum;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\RecruitmentScriptStatus\ActiveHandler;
use Modules\HRMS\app\RecruitmentScriptStatus\CancelHandler;
use Modules\HRMS\app\RecruitmentScriptStatus\ExpireHandler;
use Modules\HRMS\app\RecruitmentScriptStatus\PendingApproveHandler;
use Modules\HRMS\app\RecruitmentScriptStatus\ServiceEndedHandler;
use Modules\HRMS\app\RecruitmentScriptStatus\TerminateHandler;
use Modules\StatusMS\app\Models\Status;

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

        $recruitmentScript = RecruitmentScript::with('user.person', 'position.roles')->find($recstatus->recruitment_script_id);
        $status = Status::find($recstatus->status_id);

        $relatedClass = $this->getRelatedClassByStatusName($status->name);
        $currentUser = Auth::user();
        if ($relatedClass) {
            $handler = new $relatedClass($recruitmentScript, $currentUser);
            $handler->execute();
        }
    }

    public function getRelatedClassByStatusName(string $statusName): ?string
    {
        $statuses = [
            RecruitmentScriptStatusEnum::PENDING_APPROVAL->value => PendingApproveHandler::class,
//            RecruitmentScriptStatusEnum::REJECTED->value => RejectedHandler::class,
            RecruitmentScriptStatusEnum::ACTIVE->value => ActiveHandler::class,
            RecruitmentScriptStatusEnum::TERMINATED->value => TerminateHandler::class,
            RecruitmentScriptStatusEnum::SERVICE_ENDED->value => ServiceEndedHandler::class,
            RecruitmentScriptStatusEnum::CANCELED->value => CancelHandler::class,
            RecruitmentScriptStatusEnum::EXPIRED->value => ExpireHandler::class,

        ];

        return $statuses[$statusName] ?? null;
    }

}
