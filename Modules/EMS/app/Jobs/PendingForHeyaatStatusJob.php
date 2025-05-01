<?php

namespace Modules\EMS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Models\Enactment;
use Modules\EMS\app\Models\EnactmentStatus;

class PendingForHeyaatStatusJob implements ShouldQueue
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
            $enactment = Enactment::with('status')->find($this->encId);
//            if (is_null($enactment)) {
//                $this->delete();
//                return;
//            }

            if ($enactment->status->id != $this->enactmentCancelStatus()->id) {
                $takmilshodeStatus = $this->enactmentHeyaatStatus()->id;
                EnactmentStatus::create([
                    'status_id' => $takmilshodeStatus,
                    'enactment_id' => $this->encId,
                ]);
            }
            // If the condition fails, we manually delete the job to stop retries and mark it as "done"
            \DB::commit();
            $this->delete();
            return; // Return to stop further execution

        } catch (\Exception $e) {
            \DB::rollBack();
            $this->fail($e);
        }

    }

    public function tags(): array
    {

        return ['enactmentID:' . $this->encId,];
    }
}
