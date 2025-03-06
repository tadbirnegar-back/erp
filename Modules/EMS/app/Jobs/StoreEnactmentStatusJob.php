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
            \DB::beginTransaction();
            $enactment = Enactment::with([
                'status',
                'members' => function ($query) {
                    $query->whereDoesntHave('enactmentReviews', function ($subQuery) {
                        $subQuery->where('enactment_id', $this->encId);
                    })->whereHas('roles', function ($q) {
                        $q->whereIn('name', [RolesEnum::OZV_HEYAAT->value , RolesEnum::OZV_HEYAT_FREEZONE]);
                    });

                },])->find($this->encId);

            $memebers = $enactment->members;

            $AllMainPersons = $enactment->load(['members' => function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', [RolesEnum::OZV_HEYAAT->value , RolesEnum::OZV_HEYAT_FREEZONE]);
                });
            }]);
            $AllMainCount = $AllMainPersons->members->count();

            if (is_null($enactment)) {
                $this->delete();
                return;
            }

            if ($enactment->status->id != $this->enactmentCancelStatus()->id) {
                if ($enactment->members->isNotEmpty()) {
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

                    $reviewStatuses = $enactment->enactmentReviews()
                        ->whereHas('user.roles', function ($query) {
                            $query->whereIn('name', [RolesEnum::OZV_HEYAAT->value , RolesEnum::OZV_HEYAT_FREEZONE]);
                        })->with('status')->get();

                    if ($reviewStatuses->count() == $AllMainCount) {
                        $result = $reviewStatuses->groupBy('status.id')
                            ->map(fn($statusGroup) => [
                                'status' => $statusGroup->first(),
                                'count' => $statusGroup->count()
                            ])
                            ->sortByDesc('count')
                            ->values();

                        if ($result->count() == 2 && isset($result[0]) && isset($result[1]) && $result[0]['count'] == $result[1]['count']) {
                            $finalStatus = null;
                        } else {
                            $finalStatus = $result[0]['status']->status;
                        }

                        if (!is_null($finalStatus)) {
                            $enactment->final_status_id = $finalStatus->id;
                            $enactment->save();
                        }

                    }

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
