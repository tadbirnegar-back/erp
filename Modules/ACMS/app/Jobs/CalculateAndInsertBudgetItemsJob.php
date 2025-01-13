<?php

namespace Modules\ACMS\app\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\ACMS\app\Http\Trait\BudgetItemsTrait;
use Modules\ACMS\app\Models\Budget;

class CalculateAndInsertBudgetItemsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, BudgetItemsTrait;

    private Budget $budget;
    private array $circularSubjects;

    /**
     * Create a new job instance.
     */
    public function __construct(Budget $budget, array $circularSubjects)
    {

        $this->budget = $budget;
        $this->circularSubjects = $circularSubjects;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        try {
            \DB::beginTransaction();

            $this->bulkStoreBudgetItems($this->budget, $this->circularSubjects);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->fail();
            \Log::error($e->getMessage());
        }
    }
}
