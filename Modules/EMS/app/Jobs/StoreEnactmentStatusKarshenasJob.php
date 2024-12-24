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
use function PHPUnit\Framework\isEmpty;

class StoreEnactmentStatusKarshenasJob implements ShouldQueue
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
                        $q->where('name', RolesEnum::KARSHENAS_MASHVARATI->value);
                    });

                },])->find($this->encId);

            if (is_null($enactment)) {
                $this->delete();
                return;
            }

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

                    // Insert the data into EnactmentReview only if the data array is not empty
                    if (!empty($data)) {
                        EnactmentReview::insert($data);
                    }
                }
            } else {
                $this->delete();
                return;
            }
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->fail($e);
        }
    }
}
