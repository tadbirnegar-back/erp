<?php

namespace Modules\ACC\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\ACMS\app\Http\Enums\BudgetStatusEnum;
use Modules\ACMS\app\Http\Trait\BudgetTrait;
use Modules\ACMS\app\Models\Budget;
use Modules\ACMS\app\Models\BudgetItem;
use Modules\ACMS\app\Models\BudgetStatus;
use Modules\ACMS\app\Models\Circular;
use Modules\ACMS\app\Models\OunitFiscalYear;
use Spatie\SimpleExcel\SimpleExcelReader;

class BudgetImportSeederTest extends Seeder
{
    use BudgetTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();
            $pathToXlsx = realpath(__DIR__ . '/هفتوان_converted.xlsx');
            $ounitId = 5;
            $circular = Circular::joinRelationship('fiscalYear')->where('fiscal_years.name', 1403)
                ->addSelect('fiscal_years.name as fiscal_year_name')
                ->first();
            $ounitFiscalYear = OunitFiscalYear::where('ounit_id', $ounitId)
                ->where('fiscal_year_id', $circular->fiscalYear_id)->first();
            $budgetMain = Budget::where('ounitFiscalYear_id', $ounitFiscalYear->id)
                ->where('circular_id', $circular->id)
                ->where('isSupplementary', false)
                ->first();
            $userID = 1905;


            $minute = 1;
            do {
                $status = $budgetMain->load('latestStatus')->latestStatus->name;
                $nextStatus = match ($status) {
                    BudgetStatusEnum::PROPOSED->value => BudgetStatusEnum::PENDING_FOR_APPROVAL->value,
                    BudgetStatusEnum::PENDING_FOR_APPROVAL->value => BudgetStatusEnum::PENDING_FOR_HEYAAT_APPROVAL->value,
                    BudgetStatusEnum::PENDING_FOR_HEYAAT_APPROVAL->value => BudgetStatusEnum::FINALIZED->value,
                    default => null,
                };

                if ($nextStatus !== null) {
                    $status = Budget::GetAllStatuses()->where('name', $nextStatus)->first();
                    $budgetMain->statuses()->attach($status->id, [
                        'creator_id' => $userID,
                        'create_date' => now()->addMinutes($minute),
                    ]);
                    $minute++;
                }
            } while ($nextStatus !== null);
            if ($budgetMain->fiscal_year_name == 1403) {
                $newBudget = $budget->replicate();
                $newBudget->isSupplementary = true;
                $newBudget->parent_id = $budget->id;
                $newBudget->create_date = now();
                $newBudget->save();
                $status = $this->proposedBudgetStatus();
                BudgetStatus::create([
                    'budget_id' => $newBudget->id,
                    'status_id' => $status->id,
                    'creator_id' => $userID,
                    'create_date' => now(),
                ]);
                $items = $budget->budgetItems;
                $newItems = $items->map(function ($item) use ($newBudget) {
                    return [
                        'budget_id' => $newBudget->id,
                        'circular_item_id' => $item->circular_item_id,
                        'proposed_amount' => $item->proposed_amount,
                        'finalized_amount' => 0,
                        'percentage' => $item->percentage,
                    ];
                });
                BudgetItem::insert($newItems->toArray());
            }

            $excel = SimpleExcelReader::create($pathToXlsx);
            $rows = $excel
                ->headersToSnakeCase()
                ->fromSheetName('مصوب درآمدها')
                ->getRows();


            $rows->each(function ($row) use ($budgetMain) {
                $item = BudgetItem::where('budget_id', $budgetMain->id)
                    ->joinRelationship('circularItem.subject')
                    ->where('bgt_circular_subjects.code', $row['کد_حساب'])
                    ->where('bgt_circular_subjects.name', $row['نام_حساب'])
                    ->addSelect('bgt_circular_items.percentage as ci_percent')
                    ->first();

                $item->proposed_amount = $row['بودجه_مصوب_1403'];
                $item->percentage = $item->ci_percent;
                $item->save();


            });

            $rows = $excel
                ->headersToSnakeCase()
                ->fromSheetName('مصوب هزینه‌ها')
                ->getRows();


            $rows->each(function ($row) use ($budgetMain) {
                $item = BudgetItem::where('budget_id', $budgetMain->id)
                    ->joinRelationship('circularItem.subject')
                    ->where('bgt_circular_subjects.code', $row['کد_حساب'])
                    ->where('bgt_circular_subjects.name', $row['نام_حساب'])
                    ->addSelect('bgt_circular_items.percentage as ci_percent')
                    ->first();

                $item->proposed_amount = $row['بودجه_مصوب_1403'];
                $item->percentage = $item->ci_percent;
                $item->save();


            });

            $rows = $excel
                ->headersToSnakeCase()
                ->fromSheetName('عمرانی مصوب (Merged)')
                ->getRows();


            $rows->each(function ($row) use ($budgetMain) {
                $item = BudgetItem::where('budget_id', $budgetMain->id)
                    ->joinRelationship('circularItem.subject')
                    ->where('bgt_circular_subjects.code', $row['کد_حساب'])
                    ->where('bgt_circular_subjects.name', $row['نام_حساب'])
                    ->addSelect('bgt_circular_items.percentage as ci_percent')
                    ->first();

                $item->proposed_amount = $row['بودجه_مصوب_1403'];
                $item->percentage = $item->ci_percent;
                $item->save();


            });

            if ($excel->hasSheet('متمم مصوب درآمدها')) {

                $rows = $excel
                    ->headersToSnakeCase()
                    ->fromSheetName('متمم مصوب درآمدها')
                    ->getRows();


                $rows->each(function ($row) use ($budgetMain) {
                    $item = BudgetItem::where('budget_id', $budgetMain->id)
                        ->joinRelationship('circularItem.subject')
                        ->where('bgt_circular_subjects.code', $row['کد_حساب'])
                        ->where('bgt_circular_subjects.name', $row['نام_حساب'])
                        ->addSelect('bgt_circular_items.percentage as ci_percent')
                        ->first();

                    $item->proposed_amount = $row['بودجه_مصوب_1403'];
                    $item->percentage = $item->ci_percent;
                    $item->save();


                });
            }

            if ($excel->hasSheet('متمم مصوب هزینه‌ها')) {

                $rows = $excel
                    ->headersToSnakeCase()
                    ->fromSheetName('متمم مصوب هزینه‌ها')
                    ->getRows();


                $rows->each(function ($row) use ($budgetMain) {
                    $item = BudgetItem::where('budget_id', $budgetMain->id)
                        ->joinRelationship('circularItem.subject')
                        ->where('bgt_circular_subjects.code', $row['کد_حساب'])
                        ->where('bgt_circular_subjects.name', $row['نام_حساب'])
                        ->addSelect('bgt_circular_items.percentage as ci_percent')
                        ->first();

                    $item->proposed_amount = $row['بودجه_مصوب_1403'];
                    $item->percentage = $item->ci_percent;
                    $item->save();


                });
            }

            if ($excel->hasSheet('عمرانی متمم مصوب (Merged)')) {

                $rows = $excel
                    ->headersToSnakeCase()
                    ->fromSheetName('عمرانی متمم مصوب (Merged)')
                    ->getRows();


                $rows->each(function ($row) use ($budgetMain) {
                    $item = BudgetItem::where('budget_id', $budgetMain->id)
                        ->joinRelationship('circularItem.subject')
                        ->where('bgt_circular_subjects.code', $row['کد_حساب'])
                        ->where('bgt_circular_subjects.name', $row['نام_حساب'])
                        ->addSelect('bgt_circular_items.percentage as ci_percent')
                        ->first();

                    $item->proposed_amount = $row['بودجه_مصوب_1403'];
                    $item->percentage = $item->ci_percent;
                    $item->save();


                });
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

    }
}
