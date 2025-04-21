<?php

namespace Modules\PFM\app\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Batch;
use Illuminate\Support\Carbon;
use Modules\AAA\app\Models\User;
use Modules\PFM\app\Http\Traits\PfmCircularTrait;
use Modules\PFM\app\Jobs\PublishPfmCircularJob;
use Throwable;
use Modules\PFM\app\Services\YourService; // replace with the actual service or method provider

class DispatchPfmCircularCommand extends Command
{
    use PfmCircularTrait;
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'pfm:dispatch-circular {circularId} {userId}';

    /**
     * The console command description.
     */
    protected $description = 'Dispatch PFM circular jobs in batches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $circularId = $this->argument('circularId');
        $userId = $this->argument('userId');

        $this->info("Starting dispatch for circular: {$circularId}");

        try {
            $includedOunitsForBooklet = $this->ounitsIncludedForPublish($circularId)->chunk(150)->values();

            foreach ($includedOunitsForBooklet as $ounitChunk) {
                $jobs = [];

                foreach ($ounitChunk->values() as $item) {
                    $delayInSeconds = rand(1, 45);
                    $jobs[] = (new PublishPfmCircularJob($circularId, $userId, $item['ounitID']))
                        ->delay(Carbon::now()->addSeconds($delayInSeconds));
                }

                Bus::batch($jobs)
                    ->then(function (Batch $batch) {
                        Log::info("Batch {$batch->id} completed successfully.");
                    })
                    ->catch(function (Batch $batch, Throwable $e) {
                        Log::error("Batch {$batch->id} failed: " . $e->getMessage());
                    })
                    ->finally(function (Batch $batch) {
                        Log::info("Batch {$batch->id} finished.");
                    })
                    ->name('PublishPfmCircularJob')
                    ->onQueue('default')
                    ->dispatch();
            }

            $this->attachPublishedStatus($circularId, $userId);
        } catch (Throwable $e) {
            Log::error('DispatchPfmCircularCommand failed: ' . $e->getMessage());
            $this->error('Error dispatching circular jobs.');
        }
    }
}
