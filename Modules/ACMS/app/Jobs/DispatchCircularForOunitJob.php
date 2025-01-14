<?php

namespace Modules\ACMS\app\Jobs;

use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Modules\AAA\app\Models\User;
use Modules\ACMS\app\Http\Trait\BudgetTrait;
use Modules\ACMS\app\Http\Trait\OunitFiscalYearTrait;
use Modules\ACMS\app\Models\Circular;

class DispatchCircularForOunitJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, OunitFiscalYearTrait, BudgetTrait;

    /**
     * Create a new job instance.
     */

    private array $ounitIDs;
    private Circular $circular;
    private User $user;

    public function __construct(array $ounitIDs, Circular $circular, User $user)
    {
        $this->ounitIDs = $ounitIDs;
        $this->circular = $circular;
        $this->user = $user;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }
        \DB::beginTransaction();
        try {
            $ounitFiscalYears = $this->bulkStoreOunitFiscalYear($this->ounitIDs, $this->circular->fiscalYear, $this->user);

            $budgetName = 'بودجه سال ' . $this->circular->fiscalYear->name;
            $budgets = $this->bulkStoreBudget($ounitFiscalYears->toArray(), $budgetName, $this->user, $this->circular);
            $budgetItemsJobs = [];
            $budgets->each(function ($budget) use (&$budgetItemsJobs) {
                $budgetItemsJobs[] = new CalculateAndInsertBudgetItemsJob($budget, $this->circular->circularItems->toArray());
            });
            Bus::batch($budgetItemsJobs)
                ->then(function (Batch $batch) {
                    // All jobs completed successfully
                    \Log::info("All jobs in the batch have completed successfully.");
                })
                ->catch(function (Batch $batch, \Throwable $e) {
                    // Handle the exception
                    \Log::error("An error occurred in the batch: " . $e->getMessage());
                })
                ->finally(function (Batch $batch) {
                    // This block runs regardless of success or failure
                    \Log::info("Batch processing is complete.");
                })
                ->name('insertBudgetItems')
                ->onQueue('high')
                ->dispatch();
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->fail();
            \Log::error($e->getMessage());
        }

    }
}
