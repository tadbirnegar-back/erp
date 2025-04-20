<?php

namespace Modules\ACC\app\Jobs;

use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Modules\AAA\app\Models\User;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACMS\app\Http\Enums\BudgetStatusEnum;
use Modules\ACMS\app\Http\Trait\BudgetTrait;
use Modules\ACMS\app\Models\Budget;
use Modules\ACMS\app\Models\BudgetItem;
use Modules\ACMS\app\Models\BudgetStatus;
use Modules\ACMS\app\Models\Circular;
use Modules\ACMS\app\Models\OunitFiscalYear;
use Modules\FileMS\app\Models\File;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportBudgetItemsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use BudgetTrait, AccountTrait;

    private int $fileID;
    private int $ounitID;
    private string $fiscalYear;
    private int $userID;

    /**
     * Create a new job instance.
     */
    public function __construct(int $fileID, int $ounitID, string $fiscalYear, int $userID)
    {
        $this->fileID = $fileID;
        $this->ounitID = $ounitID;
        $this->fiscalYear = $fiscalYear;
        $this->userID = $userID;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();
            $pathToXlsx = File::find($this->fileID)->getRawOriginal('slug');
            $pathToXlsx = str_replace('uploads/', 'storage/app/public/', $pathToXlsx);

//            $excel = SimpleExcelReader::create($pathToXlsx);

            $ounitId = $this->ounitID;
            $circular = Circular::joinRelationship('fiscalYear')->where('fiscal_years.name', $this->fiscalYear)
                ->addSelect(
                    'fiscal_years.name as fiscal_year_name',
                )
                ->first();
            $ounitFiscalYear = OunitFiscalYear::where('ounit_id', $ounitId)
                ->where('fiscal_year_id', $circular->fiscal_year_id)->first();
            $budgetMain = Budget::where('ounitFiscalYear_id', $ounitFiscalYear->id)
                ->where('circular_id', $circular->id)
                ->where('isSupplementary', false)
                ->first();
            $userID = $this->userID;

            $minute = 1;
            if ($circular->fiscal_year_name == 1403) {
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
            }


            if (SimpleExcelReader::create($pathToXlsx)->hasSheet('3') && SimpleExcelReader::create($pathToXlsx)->fromSheetName('3')->headersToSnakeCase()->getRows()->first()['کد_حساب'] != '') {
                $newBudget = $budgetMain->replicate();
                $newBudget->isSupplementary = true;
                $newBudget->parent_id = $budgetMain->id;
                $newBudget->create_date = now();
                $newBudget->save();
                $status = $this->proposedBudgetStatus();
                BudgetStatus::create([
                    'budget_id' => $newBudget->id,
                    'status_id' => $status->id,
                    'creator_id' => $userID,
                    'create_date' => now(),
                ]);
                $items = $budgetMain->budgetItems;
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

                do {
                    $status = $newBudget->load('latestStatus')->latestStatus->name;
                    $nextStatus = match ($status) {
                        BudgetStatusEnum::PROPOSED->value => BudgetStatusEnum::PENDING_FOR_APPROVAL->value,
                        BudgetStatusEnum::PENDING_FOR_APPROVAL->value => BudgetStatusEnum::PENDING_FOR_HEYAAT_APPROVAL->value,
                        BudgetStatusEnum::PENDING_FOR_HEYAAT_APPROVAL->value => BudgetStatusEnum::FINALIZED->value,
                        default => null,
                    };

                    if ($nextStatus !== null) {
                        $status = Budget::GetAllStatuses()->where('name', $nextStatus)->first();
                        $newBudget->statuses()->attach($status->id, [
                            'creator_id' => $userID,
                            'create_date' => now()->addMinutes($minute),
                        ]);
                        $minute++;
                    }
                } while ($nextStatus !== null);
            }
//            else {
//                dd('sub is false', $budgetMain, SimpleExcelReader::create($pathToXlsx)->hasSheet('3'));
//            }
            if (SimpleExcelReader::create($pathToXlsx)
                ->hasSheet('1')
            ) {
                $rows = SimpleExcelReader::create($pathToXlsx)
                    ->headersToSnakeCase()
                    ->fromSheetName('1')
                    ->getRows();


                $rows->each(function ($row) use ($budgetMain,) {
                    if ($row['کد_حساب'] != 'جمع' && $row['نام_حساب'] != '') {
                        $item = BudgetItem::where('budget_id', $budgetMain->id)
                            ->joinRelationship('circularItem.subject')
                            ->where('bgt_circular_subjects.code', $row['کد_حساب'])
//                        ->where('bgt_circular_subjects.name', $row['نام_حساب'])
                            ->addSelect('bgt_circular_items.percentage as ci_percent')
                            ->first();
                        if (is_null($item)) {
                            Log::error('error: 153, sheet: 1', [$row]);
                        } else {
                            $item->proposed_amount = abs($row['بودجه_مصوب_' . $this->fiscalYear] ?? $row['پیشنهادی_' . $this->fiscalYear]);
                            $item->percentage = $row['درصد_جاری'] ?? $row['سهم_جاری'];
                            $item->save();
                        }

                    }


                });
            }
            if (SimpleExcelReader::create($pathToXlsx)
                ->hasSheet('2')
            ) {
                $rows = SimpleExcelReader::create($pathToXlsx)
                    ->headersToSnakeCase()
                    ->fromSheetName('2')
                    ->getRows();

//                $a = array('210100', '210200', '210300', '210900', '210400', '220100', '220200', '220900', '230100', '240100', '250100', '250200', '260100', '310000', '320000', '330000', '340000');
                $rows->each(function ($row) use ($budgetMain) {
                    if ($row['کد_حساب'] != 'جمع' && $row['نام_حساب'] != '') {

                        $item = BudgetItem::where('budget_id', $budgetMain->id)
                            ->joinRelationship('circularItem.subject')
                            ->where('bgt_circular_subjects.code', $row['کد_حساب'])
//                        ->where('bgt_circular_subjects.name', $this->normalizeName($row['نام_حساب'],['،']))
                            ->addSelect('bgt_circular_items.percentage as ci_percent')
                            ->first();

                        if (is_null($item)) {
                            Log::error('error: 180, sheet: 2', [$row]);
                        } else {
//                            if (in_array($row['کد_حساب'], $a)) {
//                                $item->proposed_amount = 0;
//                            } else {
                                $item->proposed_amount = abs($row['بودجه_مصوب_' . $this->fiscalYear] ?? $row['پیشنهادی_' . $this->fiscalYear]);
//                            }

//                    $item->percentage = $row['درصد_جاری'];
                            $item->save();
                        }


                    }
                });
            }

            $rows = SimpleExcelReader::create($pathToXlsx)
                ->headersToSnakeCase()
                ->fromSheetName('5')
                ->getRows();


            $rows->each(function ($row) use ($budgetMain) {
                if ($row['کد_حساب'] != 'جمع' || $row['کد_حساب'] != 'جمع_مالی') {

                    $new_str = substr($row['کد_حساب'], 0, -2);
                    $item = BudgetItem::where('budget_id', $budgetMain->id)
                        ->joinRelationship('circularItem.subject')
                        ->where('bgt_circular_subjects.code', $new_str)
//                        ->where('bgt_circular_subjects.name', $row['نام_حساب'])
                        ->addSelect('bgt_circular_items.percentage as ci_percent')
                        ->first();
                    if (is_null($item)) {
                        Log::error('error: 207,sheet: 5', [$row]);
                    } else {
                        $item->proposed_amount = abs($row['بودجه_مصوب_' . $this->fiscalYear] ?? $row['پیشنهادی_' . $this->fiscalYear]);;
//                    $item->percentage = $row['درصد_جاری'];
                        $item->save();
                    }

                }

            });

            if (SimpleExcelReader::create($pathToXlsx)->hasSheet('3') && SimpleExcelReader::create($pathToXlsx)->fromSheetName('3')->headersToSnakeCase()->getRows()->first()['کد_حساب'] != '') {

                $rows = SimpleExcelReader::create($pathToXlsx)
                    ->headersToSnakeCase()
                    ->fromSheetName('3')
                    ->getRows();


                $rows->each(function ($row) use ($newBudget) {
                    if ($row['کد_حساب'] != 'جمع') {

                        $item = BudgetItem::where('budget_id', $newBudget->id)
                            ->joinRelationship('circularItem.subject')
                            ->where('bgt_circular_subjects.code', $row['کد_حساب'])
//                            ->where('bgt_circular_subjects.name', $row['نام_حساب'])
                            ->addSelect('bgt_circular_items.percentage as ci_percent')
                            ->first();

                        if (is_null($item)) {
                            Log::info('Item not found');
                        } else {
                            $item->proposed_amount = abs($row['بودجه_اصلاح_و_متمم_مصوب_' . $this->fiscalYear] ?? $row['بودجه_اصلاح_و_متمم_پیشنهادی_' . $this->fiscalYear]);
                            $item->percentage = $row['درصد_جاری'] ?? $row['سهم_جاری'];
                            $item->save();
                        }
                    }

                });
            }

            if (SimpleExcelReader::create($pathToXlsx)->hasSheet('4') && SimpleExcelReader::create($pathToXlsx)
                    ->fromSheetName('4')
                    ->headersToSnakeCase()
                    ->getRows()->first()['کد_حساب'] != '') {

                $rows = SimpleExcelReader::create($pathToXlsx)
                    ->headersToSnakeCase()
                    ->fromSheetName('4')
                    ->getRows();


                $rows->each(function ($row) use ($newBudget) {
                    if ($row['کد_حساب'] != 'جمع' && $row['کد_حساب'] != '') {

                        $item = BudgetItem::where('budget_id', $newBudget->id)
                            ->joinRelationship('circularItem.subject')
                            ->where('bgt_circular_subjects.code', $row['کد_حساب'])
//                            ->where('bgt_circular_subjects.name', $row['نام_حساب'])
                            ->addSelect('bgt_circular_items.percentage as ci_percent')
                            ->first();
                        if (is_null($item)) {
                            Log::error('error: 266, sheet:4', [$row]);
                        } else {

                            $item->proposed_amount = abs($row['بودجه_اصلاح_و_متمم_مصوب_' . $this->fiscalYear] ?? $row['بودجه_اصلاح_و_متمم_پیشنهادی_' . $this->fiscalYear]);
//                        $item->percentage = $row['درصد_جاری'];
                            $item->save();
                        }
                    }


                });
            }

            $row = SimpleExcelReader::create($pathToXlsx)
                ->fromSheetName('6')
                ->headersToSnakeCase()
                ->getRows()
                ->first();

            if ($row !== null && isset($row['کد_حساب']) && $row['کد_حساب'] !== '') {

                $rows = SimpleExcelReader::create($pathToXlsx)
                    ->headersToSnakeCase()
                    ->fromSheetName('6')
                    ->getRows();


                $rows->each(function ($row) use ($newBudget) {
                    if ($row['کد_حساب'] != 'جمع') {
                        $new_str = substr($row['کد_حساب'], 0, -2);

                        $item = BudgetItem::where('budget_id', $newBudget->id)
                            ->joinRelationship('circularItem.subject')
                            ->where('bgt_circular_subjects.code', $new_str)
//                            ->where('bgt_circular_subjects.name', $row['نام_حساب'])
                            ->addSelect('bgt_circular_items.percentage as ci_percent')
                            ->first();

                        if (is_null($item)) {
                            Log::error('error: 299', [$row]);
                        } else {
                            //                        $item->proposed_amount = $row['بودجه_اصلاح_و_متمم_مصوب_1403'];
                            $item->proposed_amount = abs($row['بودجه_اصلاح_و_متمم_مصوب_' . $this->fiscalYear] ?? $row['بودجه_اصلاح_و_متمم_پیشنهادی_' . $this->fiscalYear]);
//                        $item->percentage = $row['درصد_جاری'];
                            $item->save();
                        }


                    }

                });
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error: 298', [$e->getMessage(), $e->getLine(), $e->getTrace()]);
            $this->fail();
//            dd($e->getMessage(), $e->getTrace());
        }
    }

    public function tags(): array
    {
        $person = User::with('person')->find($this->userID);
        $ounit = OrganizationUnit::find($this->ounitID);
        return ['ounit:' . $ounit->name, 'ounitID:' . $this->ounitID, 'financeManager:' . $person->person->display_name, 'fiscal year:' . $this->fiscalYear];
    }
}
