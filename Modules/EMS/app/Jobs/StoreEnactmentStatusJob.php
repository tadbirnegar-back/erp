<?php

namespace Modules\EMS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\EMS\app\Http\Enums\RolesEnum;
use Modules\EMS\app\Http\Traits\EnactmentReviewTrait;
use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Models\Enactment;
use Modules\EMS\app\Models\EnactmentReview;
use Modules\EMS\app\Models\EnactmentStatus;
use function PHPUnit\Framework\isEmpty;

class StoreEnactmentStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, EnactmentReviewTrait, EnactmentTrait;

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
                'members' => function ($query) {
                    $query->whereDoesntHave('enactmentReviews', function ($subQuery) {
                        $subQuery->where('enactment_id', $this->encId);
                    })->whereHas('roles', function ($q) {
                        $q->where('name', RolesEnum::OZV_HEYAAT->value);
                    });

                },])->find($this->encId);
            if ($enactment->status->id != $this->enactmentCancelStatus()->id) {
                if ($enactment->members->isNotEmpty() || !isEmpty($enactment->members)) {
                    $noMoghayeratAutoStatus = $this->reviewNoSystemInconsistencyStatus();
                    $data = $enactment->members->map(function ($member) use ($noMoghayeratAutoStatus) {
                        return [
                            'user_id' => $member->employee_id,
                            'description' => null,
                            'status_id' => $noMoghayeratAutoStatus->id,
                            'enactment_id' => $this->encId,
                        ];
                    })->toArray();

                    EnactmentReview::insert($data);
                    $takmilshodeStatus = $this->enactmentCompleteStatus()->id;
                    EnactmentStatus::create([
                        'status_id' => $takmilshodeStatus,
                        'enactment_id' => $this->encId,
                    ]);
                    if ($enactment->members->count() > 1) {
                        $enactment->final_status_id = $noMoghayeratAutoStatus->id;
                        $enactment->save();
                    }
                    \DB::commit();
                }
            } else {
                $this->delete();
                return;
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->fail($e);
        }

    }
}
