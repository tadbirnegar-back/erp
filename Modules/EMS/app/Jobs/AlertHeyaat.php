<?php

namespace Modules\EMS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\EMS\app\Http\Enums\RolesEnum;
use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Models\Enactment;
use Modules\EMS\app\Notifications\AlertMMLastDayNotification;
use Modules\PersonMS\app\Models\Person;

class AlertHeyaat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, EnactmentTrait;

    public int $encId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $encId)
    {
        $this->encId = $encId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            \DB::beginTransaction();
            $enactment = Enactment::with([
                'status',
                'meeting.meetingMembers' => function ($query) {
                    $query->whereDoesntHave('enactmentReviews', function ($subQuery) {
                        $subQuery->where('enactment_id', $this->encId);
                    })->whereHas('roles', function ($q) {
                        $q->where('name', RolesEnum::OZV_HEYAAT->value);
                    })->with(['user']);
                },
            ])->find($this->encId);

            if (is_null($enactment)) {
                $this->delete();
                return;
            }

            if ($enactment->status->id != $this->enactmentCancelStatus()->id) {
                foreach ($enactment->meeting->meetingMembers as $meetingMember) {
                    $user = $meetingMember->user; // Access the User model associated with the meeting member

                    $username = Person::find($user->person_id)->display_name;

                    $user->notify(new AlertMMLastDayNotification($username));

                }
            }
            \DB::commit();
                $this->delete();
                return;



        } catch (\Exception $e) {
            \DB::rollBack();
            $this->fail($e);
        }
    }
}
