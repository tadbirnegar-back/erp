<?php

namespace Modules\ACC\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use Modules\ACC\app\Http\Enums\AccCategoryEnum;
use Modules\ACC\app\Http\Enums\AccountLayerTypesEnum;
use Modules\ACC\app\Http\Enums\DocumentStatusEnum;
use Modules\ACC\app\Http\Enums\DocumentTypeEnum;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Http\Traits\ArticleTrait;
use Modules\ACC\app\Http\Traits\DocumentTrait;
use Modules\ACC\app\Jobs\ImportBudgetItemsJob;
use Modules\ACC\app\Jobs\ImportDocsJob;
use Modules\ACC\app\Models\Account;
use Modules\ACC\app\Models\Document;
use Modules\ACC\app\Models\GlAccount;
use Modules\ACC\app\Models\JobStatusTrack;
use Modules\ACC\app\Models\OunitAccImport;
use Modules\ACMS\app\Http\Enums\AccountantScriptTypeEnum;
use Modules\ACMS\app\Http\Trait\CircularSubjectsTrait;
use Modules\ACMS\app\Http\Trait\FiscalYearTrait;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\BNK\app\Http\Traits\BankTrait;
use Modules\BNK\app\Http\Traits\ChequeTrait;
use Modules\BNK\app\Http\Traits\TransactionTrait;
use Modules\FileMS\app\Models\File;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Morilog\Jalali\Jalalian;
use Validator;

class ACCController extends Controller
{
    use BankTrait, ChequeTrait, TransactionTrait, FiscalYearTrait, DocumentTrait, AccountTrait, ArticleTrait, CircularSubjectsTrait;

    public function importDocs(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'ounitID' => 'required',
            'fileID' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jobStatusTrack = JobStatusTrack::where('unique_id', $request->ounitID)->where('class_name', ImportDocsJob::class)->whereIn('status', ['pending', 'completed'])->exists();
        if ($jobStatusTrack) {
            return response()->json(['message' => 'Job already exists'], 400);
        }
        $user = Auth::user();
        ImportDocsJob::dispatch($request->ounitID, $request->fileID, $user->id);
        JobStatusTrack::create([
            'unique_id' => $request->ounitID,
            'class_name' => ImportDocsJob::class,
            'file_id', $request->fileID,
        ]);
        return response()->json(['message' => 'Job created successfully'], 200);

    }

    public function getConfirmationForOldData()
    {
        $user = Auth::user();
        $user->load('person');
        $ounits = $user->activeRecruitmentScripts()
            ->whereHas('scriptType', function ($query) {
                $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
            })->count();

        $videoId = 4787;
        $letterId = 17;

        $files = File::whereIntegerInRaw('id', [$videoId, $letterId])
            ->get()
            ->keyBy('id');

        $video = $files->get($videoId);
        $letter = $files->get($letterId);

        $response = [
            'name' => $user->person->display_name,
            'villageCount' => $ounits,
            'video' => [
                'slug' => $video?->slug,
            ],
            'file' => [
                'link' => $letter?->slug,
                'title' => $letter?->name,
            ],
        ];

        return response()->json(['data' => $response,
        ]);

    }

    public function getOunitsToImport(Request $request)
    {
        $user = Auth::user();
        $ounits = $user->activeRecruitmentScripts()
            ->whereHas('scriptType', function ($query) {
                $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
            })
            ->with(['ounit.importedResult', 'ounit.village', 'ounit.ancestors' => function ($query) {
                $query->where('unitable_type', '!=', StateOfc::class);

            }])
            ->get();

        $result = $ounits->pluck('ounit')->map(function ($ounit) {
            return [
                'ounit_id' => $ounit->id,
                'ounit_name' => $ounit->name,
                'abadi_code' => $ounit->village->abadi_code,
                'ancestors' => $ounit->ancestors->pluck('name'),
                'converted' => !is_null($ounit->importedResult),
            ];
        });

        return response()->json($result);
    }

    public function getOldDataToConvert(Request $request)
    {
        $data = $request->all();

        $docs = Account::withoutGlobalScopes()
            ->whereIntegerNotInRaw('category_id', [AccCategoryEnum::INCOME->value, AccCategoryEnum::EXPENSE->value])
            ->where('acc_accounts.status_id', '=', 155)
            ->where('acc_accounts.ounit_id', $data['ounitID'])
            ->join('acc_articles', 'acc_articles.account_id', '=', 'acc_accounts.id')
            ->join('acc_documents', 'acc_documents.id', '=', 'acc_articles.document_id')
            ->join('accDocument_status', 'accDocument_status.document_id', '=', 'acc_documents.id')
            ->join('statuses', 'accDocument_status.status_id', '=', 'statuses.id')
            ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
            ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value)
            ->where('acc_documents.ounit_id', $data['ounitID'])
            ->where('acc_documents.fiscal_year_id', 1)
            ->with(['newCode' => function ($query) use ($data) {
                $query->with(['ancestors' => function ($query) use ($data) {
                    $query->withoutGlobalScopes();

                }])
                    ->where('ounit_id', '=', $data['ounitID'])->orWhereNull('ounit_id')
                    ->orderByDesc('ounit_id')
                    //                    ->withoutGlobalScopes()
                ;
            }, 'ancestorsAndSelf' => function ($query) {
                $query->withoutGlobalScopes();
            }, 'accountCategory'])
            ->select(
                [
                    'acc_accounts.id',
                    'acc_accounts.name',
                    'acc_accounts.chain_code',
                    'acc_accounts.new_chain_code',
                    'acc_accounts.parent_id',
                    'acc_accounts.category_id',

                    DB::raw('SUM(acc_articles.credit_amount) - SUM(acc_articles.debt_amount) AS total'),
                ]
            )
            ->groupBy(
                'acc_accounts.id',
                'acc_accounts.name',
                'acc_accounts.chain_code',
                'acc_accounts.new_chain_code',
                'acc_accounts.parent_id',
                'acc_accounts.category_id',


            )
            ->having('total', '!=', 0)
            ->get();


        $response = $docs->map(function ($item) {
            return [
                'id' => $item->id,
                'ancestors' => $item->ancestorsAndSelf->isNotEmpty() ? $item->ancestorsAndSelf->map(function ($ancestor) {
                    return [
                        'id' => $ancestor->id,
                        'name' => $ancestor->name,
                        'chain_code' => $ancestor->chain_code,
                        'type' => AccountLayerTypesEnum::from($ancestor->accountable_type)->getLabel(),
                    ];
                }) : [],
                'newCode' => $item->newCode,
                'category' => $item->accountCategory?->name,
                'total' => $item->total,
            ];
        });

        return response()->json($response);
    }

    public function newActiveAccounts(Request $request)
    {
        $accounts = Account::where('accountable_type', '!=', GlAccount::class)
            ->whereIntegerNotInRaw('category_id', [AccCategoryEnum::INCOME->value, AccCategoryEnum::EXPENSE->value])
            ->where(function ($query) use ($request) {
                $query->where('ounit_id', $request->ounitID)
                    ->orWhereNull('ounit_id');
            })
            ->where(function ($query) {
                $query->where('isFertile', false)
                    ->orWhereNull('isFertile');
            })
            ->with('ancestors')
            ->get([
                'id',
                'chain_code',
                'name',
                'parent_id',
                'ounit_id',
                'isFertile',

            ]);

        return response()->json($accounts);
    }

    public function convertToNewAccount(Request $request)
    {
        $data = $request->all();
        $validation = Validator::make($data, [
            'articles' => 'required',
            'ounitID' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 422);
        }

        try {
            DB::beginTransaction();
            $articles = json_decode($data['articles'], true);
            $doc['fiscalYearID'] = FiscalYear::where('name', 1403)->first()->id;
            $lastDocNumber = Document::where('fiscal_year_id', $doc['fiscalYearID'])
                ->where('ounit_id', $data['ounitID'])
                ->joinRelationship('statuses', ['statuses' => function ($join) {
                    $join
                        ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                        ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value);
                }])
                ->orderByRaw('CAST(document_number AS UNSIGNED) DESC')
                ->first();

            $date = $lastDocNumber->getRawOriginal('document_date');


            $doc['documentNumber'] = $lastDocNumber ? $lastDocNumber->document_number + 1 : 1;
            $doc['documentDate'] = Jalalian::fromDateTime($date)->toDateString();;
            $doc['ounitID'] = $data['ounitID'];
            $doc['description'] = 'سند تبدیل سرفصل حساب ها به کدینگ جدید';
            $doc['documentTypeID'] = DocumentTypeEnum::NORMAL->value;
            $doc['readOnly'] = true;
            $doc['userID'] = Auth::user()->id;
            $doc['ounitHeadID'] = OrganizationUnit::where('id', $doc['ounitID'])->first()?->head_id;
            $doc = $this->storeDocument($doc);
            $status = $this->confirmedDocumentStatus();
            $this->attachStatusToDocument($doc, $status, Auth::user()->id);
            $artsToInsert = [];
            $articles = collect($articles);

            $p = 1;
//            $articles->each(function ($art) use (&$artsToInsert, &$p) {
//                $artsToInsert[] = [
//                    'description' => $art['name'],
//                    'priority' => $p++,
//                    'debtAmount' => $art['creditAmount'],
//                    'creditAmount' => $art['debtAmount'],
//                    'accountID' => $art['accountID'],
//                ];
//
//                $artsToInsert[] = [
//                    'description' => $art['newName'],
//                    'priority' => $p++,
//                    'debtAmount' => $art['debtAmount'],
//                    'creditAmount' => $art['creditAmount'],
//                    'accountID' => $art['newAccountID'],
//                ];
//            });
// Process articles with new keys
            // Articles with both 'newName' and 'newAccountID'
            $articlesWithNew = $articles->filter(function ($art) {
                return isset($art['newName']) && isset($art['newAccountID']);
            });
            $articlesWithNew->each(function ($art) use (&$artsToInsert, &$p) {
                // Insert the original entry
                $artsToInsert[] = [
                    'description' => $art['name'],
                    'priority' => $p++,
                    'debtAmount' => $art['creditAmount'],
                    'creditAmount' => $art['debtAmount'],
                    'accountID' => $art['accountID'],
                ];
                // Insert the new entry with new values
                $artsToInsert[] = [
                    'description' => $art['newName'],
                    'priority' => $p++,
                    'debtAmount' => $art['debtAmount'],
                    'creditAmount' => $art['creditAmount'],
                    'accountID' => $art['newAccountID'],
                ];
            });


            // Articles without 'newName' or 'newAccountID'
            $articlesWithoutNew = $articles->filter(function ($art) {
                return !isset($art['newName']) || !isset($art['newAccountID']);
            });
//
            if ($articlesWithoutNew->isNotEmpty()) {

                $incomeAcc = Account::where('chain_code', 16290)->where('category_id', AccCategoryEnum::INCOME->value)->first();

                $haziAcc = Account::where('chain_code', 140210)->where('category_id', AccCategoryEnum::EXPENSE->value)->first();

// Process articles without new keys
                $articlesWithoutNew->each(function ($art) use (&$artsToInsert, &$p, $incomeAcc, $haziAcc) {
                    $toName = $art['creditAmount'] > 0 ? $haziAcc : $incomeAcc;
                    // Insert the original entry
                    $artsToInsert[] = [
                        'description' => $art['name'],
                        'priority' => $p++,
                        'debtAmount' => $art['creditAmount'],
                        'creditAmount' => $art['debtAmount'],
                        'accountID' => $art['accountID'],
                    ];
                    $artsToInsert[] = [
                        'description' => $toName->name . ' - ' . $art['name'],
                        'priority' => $p++,
                        'debtAmount' => $art['debtAmount'],
                        'creditAmount' => $art['creditAmount'],
                        'accountID' => $toName->id,
                    ];
                });
            }

//            $creditNoCodes = $articlesWithoutNew->filter(function ($art) {
//                return $art['creditAmount'] > 0;
//            });
//
//            if ($creditNoCodes->isNotEmpty()) {
//                $incomeAcc = Account::where('chain_code', 16290)->where('category_id', AccCategoryEnum::INCOME->value)->first();
//
//                $artsToInsert[] = [
//                    'description' => $incomeAcc->name,
//                    'priority' => $p++,
//                    'debtAmount' => $creditNoCodes->sum('creditAmount'),
//                    'creditAmount' => 0,
//                    'accountID' => $incomeAcc->id,
//                ];
//
////                $creditNoCodes->each(function ($art) use (&$artsToInsert, &$p, $incomeAcc) {
////                    $artsToInsert[] = [
////                        'description' => $incomeAcc->name,
////                        'priority' => $p++,
////                        'debtAmount' => $art['creditAmount'],
////                        'creditAmount' => $art['debtAmount'],
////                        'accountID' => $incomeAcc->id,
////                    ];
////                });
//            }

//            $debtNoCodes = $articlesWithoutNew->filter(function ($art) {
//                return $art['debtAmount'] > 0;
//            });
//
//            if ($debtNoCodes->isNotEmpty()) {
//                $haziAcc = Account::where('chain_code', 140210)->where('category_id', AccCategoryEnum::EXPENSE->value)->first();
//
//                $artsToInsert[] = [
//                    'description' => $haziAcc->name,
//                    'priority' => $p++,
//                    'debtAmount' => 0,
//                    'creditAmount' => $debtNoCodes->sum('debtAmount'),
//                    'accountID' => $haziAcc->id,
//                ];
//
////                $creditNoCodes->each(function ($art) use (&$artsToInsert, &$p, $haziAcc) {
////                    $artsToInsert[] = [
////                        'description' => $haziAcc->name,
////                        'priority' => $p++,
////                        'debtAmount' => $art['creditAmount'],
////                        'creditAmount' => $art['debtAmount'],
////                        'accountID' => $haziAcc->id,
////                    ];
////                });
//            }

            $this->bulkStoreArticle($artsToInsert, $doc);
            OunitAccImport::create([
                'ounit_id' => $data['ounitID'],
                'creator_id' => Auth::user()->id,
            ]);
            DB::commit();
            return response()->json($doc);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error', 'error'], 500);
        }


    }

    public function setNewChainCodeToAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accounts' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $accs = $request->accounts;
            $accs = json_decode($accs, true);
            foreach ($accs as $account) {
                $acc = Account::withoutGlobalScopes()->find($account['id']);
                $acc->new_chain_code = $account['chainCode'] ?? null;
                $acc->save();
            }
            DB::commit();
            return response()->json(['message' => 'با موفقیت بروزرسانی شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage(), $e->getTrace()], 500);
        }
    }

    public function importDocChecker(Request $request)
    {
        $jobStatusTrack = JobStatusTrack::where('unique_id', $request->ounitID)->where('class_name', ImportDocsJob::class)->latest('id')->first();

        return response()->json(['data' => $jobStatusTrack]);
    }

    public function importBudgets(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'ounitID' => 'required',
            'fileID' => 'required',
            'fiscalYear' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

//        $jobStatusTrack = JobStatusTrack::where('unique_id', $request->ounitID)->where('class_name', ImportBudgetItemsJob::class)->whereIn('status', ['pending', 'completed'])->exists();
//        if ($jobStatusTrack) {
//            return response()->json(['message' => 'Job already exists'], 400);
//        }
        $user = Auth::user();
        ImportBudgetItemsJob::dispatch($request->fileID, $request->ounitID, $request->fiscalYear, $user->id);
        JobStatusTrack::create([
            'unique_id' => $request->ounitID,
            'class_name' => ImportBudgetItemsJob::class,
        ]);
        return response()->json(['message' => 'Job created successfully'], 200);

    }
}
