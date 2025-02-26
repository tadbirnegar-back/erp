<?php

namespace Modules\ACC\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\LazyCollection;
use Modules\ACC\app\Http\Enums\DocumentTypeEnum;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Http\Traits\ArticleTrait;
use Modules\ACC\app\Http\Traits\DocumentTrait;
use Modules\ACC\app\Models\AccountCategory;
use Modules\ACMS\app\Http\Trait\CircularSubjectsTrait;
use Modules\ACMS\app\Http\Trait\FiscalYearTrait;
use Modules\BNK\app\Http\Traits\BankTrait;
use Modules\BNK\app\Http\Traits\ChequeTrait;
use Modules\BNK\app\Http\Traits\TransactionTrait;
use Modules\FileMS\app\Models\File;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportDocsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, BankTrait, ChequeTrait, TransactionTrait, FiscalYearTrait, DocumentTrait, AccountTrait, ArticleTrait, CircularSubjectsTrait;

    /**
     * Create a new job instance.
     */

    private int $ounitID;
    private int $fileID;

    public function __construct(int $ounitID, int $fileID)
    {
        $this->ounitID = $ounitID;
        $this->fileID = $fileID;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $pathToXlsx = File::find($this->fileID)->getRawOriginal('slug');
//            dd($pathToXlsx->getRealPath());
            $rows = SimpleExcelReader::create($pathToXlsx)
                ->getRows();
            $a = $rows->groupBy('year')
                ->map(function ($yearGroup) {
                    return $yearGroup->groupBy('Doc ID');
                });
            /**
             * @var LazyCollection $a
             */
            $ounitID = $this->ounitID;
            $docStatus = $this->confirmedDocumentStatus();
            \DB::beginTransaction();
            $a->each(function ($yearGroup, $key) use ($ounitID, $docStatus) {

//            dd($row[5], $key);
                $fiscalYearData = [
                    'startDate' => $key . '/01/01',
                    'finishDate' => $key . '/12/29',
                    'fiscalYearName' => $key,
                ];
                $fy = $this->createFiscalYear($fiscalYearData);
                $firstItem = $yearGroup->slice(0, 1)->first(); // Get first item
                $lastItem = $yearGroup->slice(-1)->first(); // Get last item
                $secondLastItem = $yearGroup->slice(-2, 1)->first(); // Get second last item

//            dump($firstItem, $secondLastItem, $lastItem);

                $yearGroup->each(function ($doc, $key) use ($firstItem, $secondLastItem, $lastItem, $ounitID, $fy, $docStatus) {

                    $docType = match ($key) {
                        $firstItem[0]['Doc ID'] => DocumentTypeEnum::OPENING->value,
                        $secondLastItem[0]['Doc ID'] => DocumentTypeEnum::TEMPORARY->value,
                        $lastItem[0]['Doc ID'] => DocumentTypeEnum::CLOSING->value,
                        default => DocumentTypeEnum::NORMAL->value,
                    };


//                dd($doc, $docType);
                    $docData = [
                        'fiscalYearID' => $fy->id,
                        'documentNumber' => $key,
                        'documentDate' => $doc[0]['Doc Date'],
                        'documentTypeID' => $docType,
                        'description' => $doc[0]['Doc Description'],
                        'ounitID' => $ounitID,
                        'userID' => 1905,

                    ];

                    $docObj = $this->storeDocument($docData);
                    $this->attachStatusToDocument($docObj, $docStatus, 1905);
                    $doc->each(function ($article) use ($doc, $ounitID, $docObj) {
                        if ($article['Account Name'] != $article['Bestankari']) {
                            $cat = AccountCategory::where('name', convertToDbFriendly($article['Ancestor_name_0']))->where('id', $article['Ancestor_code_0'])->first();


                            // 1. Filter only the keys matching "Ancestor_code_*"
                            $chainCodes = [];
                            foreach ($article as $key => $value) {
                                if (preg_match('/^Ancestor_code_(\d+)$/', $key, $matches)) {
                                    $index = (int)$matches[1];
                                    $chainCodes[$index] = (string)$value;
                                }
                            }

// 2. Ensure the chain codes are in order (by index)
                            ksort($chainCodes);

// 3. Calculate segment codes by "removing" the last non-empty parent's code
                            $segmentCodes = [];
                            $lastValidCode = null;

                            foreach ($chainCodes as $index => $code) {
                                if ($index === 0) {
                                    // Level 0 is the root; set last valid code (even if it might be empty)
                                    $lastValidCode = $code;
                                    continue;
                                }

                                // If the current chain code is empty, we assign an empty segment and do not update the last valid code.
                                if ($code === '') {
                                    $segmentCodes["segment_code_$index"] = '';
                                    continue;
                                }

                                // If there's no valid parent (or parent's code is empty), we can't remove a prefix.
                                if ($lastValidCode === null || $lastValidCode === '') {
                                    $segmentCodes["segment_code_$index"] = $code;
                                } else {
                                    // Remove the last valid (non-empty) parent's chain code from the beginning.
                                    if (strpos($code, $lastValidCode) === 0) {
                                        $segmentCodes["segment_code_$index"] = substr($code, strlen($lastValidCode));
                                    } else {
                                        // If the expected pattern isn't met, you can handle it as needed.
                                        $segmentCodes["segment_code_$index"] = null;
                                    }
                                }

                                // Update last valid code since the current one is non-empty.
                                $lastValidCode = $code;
                            }
                            $maxLayer = 0;
                            foreach ($chainCodes as $index => $code) {
                                if ($index === 0) continue; // skip the root if you don't need to insert it
                                if ($code === '' || $code === '0') {
                                    break;  // stop when you encounter an empty or "0" chain code
                                }
                                $maxLayer = $index;
                            }

// Step 4: Insert each valid layer into the database.
                            $parentAccount = null;
                            $usedAccount = null;
                            $usedCodeInArticle = $article["Account Code"];
                            $accs = [];
                            for ($layer = 1; $layer <= $maxLayer; $layer++) {
//                            $data = [];
                                $data = [
                                    'name' => $article["Ancestor_name_$layer"],
                                    'categoryID' => $cat->id,
                                    'ounitID' => $ounitID,
                                    'segmentCode' => $segmentCodes["segment_code_$layer"] ?? null,
                                    'chainCode' => $article["Ancestor_code_$layer"],
                                    'layer' => $layer,
                                ];

                                $childAccount = $this->firstOrStoreAccount($data, $parentAccount);
                                $accs[] = $childAccount;
//                            if ($layer == $maxLayer) {
////                                dump($usedCodeInArticle, $childAccount, $article["Ancestor_code_$layer"], $data);
//
//                            }
                                if ($usedCodeInArticle == $childAccount->chain_code) {
                                    $usedAccount = $childAccount;
//                                dd('dd', $usedAccount, $childAccount);
                                }
                                $parentAccount = $childAccount;

                                // For demonstration, we print the data array.
                                // In a Laravel project you might do:
                                // DB::table('your_table')->insert($data);
                                //print_r($data);
                            }
                            if (is_null($usedAccount)) {
                                $allowedEndings = ['81', '82', '83'];
                                foreach ($allowedEndings as $ending) {

                                    if (str_ends_with($usedCodeInArticle, $ending)) {
                                        $coreCode = substr($usedCodeInArticle, 0, -strlen($ending));

                                        foreach ($accs as $account) {
                                            // Adjust the property name (here assumed as 'code') as needed
                                            if ($account->chain_code == $coreCode) {
                                                $usedAccount = $account;
                                                break 2; // Break out of both loops once a match is found
                                            }
                                        }
                                    }
                                }
                            }
                            if (is_null($usedAccount)) {
                                dd($usedCodeInArticle, $accs, $article);
                            }

                            $transaction = null;
                            if (!empty($article['Cheque Number'])) {
                                $transactionData = [
                                    'trackingCode' => $article['Cheque Number'],
                                    'withdrawal' => $article['Bestankari'],
                                    'userID' => 1905,
                                    'createDate' => now(),
                                ];
                                $transaction = $this->storeTransaction($transactionData);
                            }

                            $articleData = [
                                'description' => $article['Article Description'],
                                'debtAmount' => $article['Bedehkari'],
                                'creditAmount' => $article['Bestankari'],
                                'accountID' => $usedAccount->id,
                                'transactionID' => $transaction?->id,
                            ];
                            $this->storeArticle($articleData, $docObj);


//                    $this->storeTransaction($docData);
//                    $this->storeBank($docData);
//                    $this->storeCheque($docData);
                        }
                    });
                });
//Account::firstOrCreate()

            });
            \DB::commit();

        } catch (\Exception $e) {
            \DB::rollBack();
            $this->fail($e);
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        $ounit = OrganizationUnit::find($this->ounitID);
        return ['render', 'ounit:' . $ounit->name, 'ounitID:' . $this->ounitID];
    }

}
