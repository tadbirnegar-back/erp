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
            $enactment = Enactment::with(['members' => function ($query) {
                $query->whereDoesntHave('enactmentReviews', function ($subQuery) {
                    $subQuery->where('enactment_id', $this->encId);
                })->whereHas('roles', function ($q) {
                    $q->where('name', RolesEnum::OZV_HEYAAT->value);
                });

            },])->find($this->encId);

            if ($enactment->members->isNotEmpty()) {
                $noMoghayeratAutoStatus = $this->reviewNoSystemInconsistencyStatus();
                $data = $enactment->members->map(function ($member) use ($noMoghayeratAutoStatus) {
                    return [
                        'user_id' => $member->employee_id,
                        'description' => "تایید توسط سیستم",
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

                $enactment->final_status_id = $noMoghayeratAutoStatus->id;
                $enactment->save();
            }
        } catch (\Exception $e) {
            $this->fail($e);
        }

    }
}
