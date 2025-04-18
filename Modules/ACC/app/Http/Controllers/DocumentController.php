<?php

namespace Modules\ACC\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Cache;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;
use Modules\ACC\app\Http\Enums\AccCategoryEnum;
use Modules\ACC\app\Http\Enums\AccountCategoryTypeEnum;
use Modules\ACC\app\Http\Enums\AccountLayerTypesEnum;
use Modules\ACC\app\Http\Enums\DocumentStatusEnum;
use Modules\ACC\app\Http\Enums\DocumentTypeEnum;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Http\Traits\ArticleTrait;
use Modules\ACC\app\Http\Traits\DocumentTrait;
use Modules\ACC\app\Models\Account;
use Modules\ACC\app\Models\AccountCategory;
use Modules\ACC\app\Models\Article;
use Modules\ACC\app\Models\DetailAccount;
use Modules\ACC\app\Models\Document;
use Modules\ACC\app\Resources\ArticlesListResource;
use Modules\ACC\app\Resources\CurrentFiscalYearResource;
use Modules\ACC\app\Resources\DocumentListResource;
use Modules\ACC\app\Resources\DocumentShowResource;
use Modules\ACC\app\Resources\OunitWithBankAccounts;
use Modules\ACC\app\Resources\TarazLogResource;
use Modules\ACMS\app\Http\Enums\AccountantScriptTypeEnum;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\BNK\app\Http\Traits\ChequeTrait;
use Modules\BNK\app\Http\Traits\TransactionTrait;
use Modules\BNK\app\Models\BankAccount;
use Modules\BNK\app\Models\Cheque;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Morilog\Jalali\Jalalian;
use Validator;

class DocumentController extends Controller
{
    use DocumentTrait, ArticleTrait, ChequeTrait, TransactionTrait, AccountTrait;


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ounitID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $currentYear = Jalalian::now()->getYear();
        $fiscalYear = FiscalYear::where('name', $currentYear)->first();

        $docs = Document::leftJoinRelationship('articles')
            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join
                    ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                    ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value);
            }])
            ->where('acc_documents.ounit_id', $data['ounitID'])
            ->where('acc_documents.fiscal_year_id', $fiscalYear->id)
            ->select([
                'acc_documents.id as id',
                'acc_documents.description as document_description',
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                'acc_documents.document_date as document_date',
                'acc_documents.document_number as document_number',
                'acc_documents.create_date as create_date',
                'acc_documents.document_type_id as document_type_id',
                \DB::raw('SUM(acc_articles.debt_amount) as total_debt_amount'),
                \DB::raw('SUM(acc_articles.credit_amount) as total_credit_amount'),
            ])
            ->groupBy('acc_documents.id', 'acc_documents.description', 'acc_documents.document_date', 'acc_documents.document_number', 'acc_documents.create_date', 'acc_documents.document_type_id', 'statuses.name', 'statuses.class_name')
//            ->orderBy('acc_documents.document_number', 'desc')
            ->orderByRaw('CAST(acc_documents.document_number AS UNSIGNED) DESC')
            ->get();

        return DocumentListResource::collection($docs);
    }

    public function archiveIndex(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ounitID' => 'required',
            'fiscalYearID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $fiscalYear = FiscalYear::find($data['fiscalYearID']);

        $docs = Document::leftJoinRelationship('articles')
            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join
                    ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                    ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value);
            }])
            ->where('acc_documents.ounit_id', $data['ounitID'])
            ->where('acc_documents.fiscal_year_id', $fiscalYear->id)
            ->select([
                'acc_documents.id as id',
                'acc_documents.description as document_description',
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                'acc_documents.document_date as document_date',
                'acc_documents.document_number as document_number',
                'acc_documents.document_type_id as document_type_id',
                'acc_documents.create_date as create_date',
                \DB::raw('SUM(acc_articles.debt_amount) as total_debt_amount'),
                \DB::raw('SUM(acc_articles.credit_amount) as total_credit_amount'),
            ])
            ->groupBy('acc_documents.id', 'acc_documents.description', 'acc_documents.document_date', 'acc_documents.document_number', 'acc_documents.create_date', 'statuses.name', 'statuses.class_name', 'acc_documents.document_type_id')
            ->orderByRaw('CAST(acc_documents.document_number AS UNSIGNED) DESC')
            ->get();

        return DocumentListResource::collection($docs);
    }

    public function fiscalYearList()
    {
        return response()->json(FiscalYear::orderBy('name', 'asc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'fiscalYear' => 'required',
            'ounitID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $data['userID'] = Auth::user()->id;
        $data['fiscalYearID'] = FiscalYear::where('name', $request->fiscalYear)->first()->id;

        $lastDocNumber = Document::where('fiscal_year_id', $data['fiscalYearID'])
            ->where('ounit_id', $data['ounitID'])
            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join
                    ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                    ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value);
            }])
            ->orderByRaw('CAST(document_number AS UNSIGNED) DESC')
            ->first();
        $data['ounitHeadID'] = OrganizationUnit::where('id', $data['ounitID'])->first()?->head_id;
        $date = $lastDocNumber?->getRawOriginal('document_date');

        $data['documentNumber'] = $lastDocNumber ? $lastDocNumber->document_number + 1 : 2;
        $data['documentDate'] = (Jalalian::now()->getYear() > $request->fiscalYear
            ? $lastDocNumber?->document_date ?? (new Jalalian($request->fiscalYear, 1, 1))->getEndDayOfYear()->toDateString()
            : Jalalian::now()->toDateString());
        $data['ounitHeadID'] = OrganizationUnit::find($data['ounitID'])?->head_id;
        try {
            DB::beginTransaction();

            $document = $this->storeDocument($data);
            $status = $this->draftDocumentStatus();
            $this->attachStatusToDocument($document, $status, $data['userID']);

            DB::commit();
            return response()->json($document);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error', 'error'], 500);
        }

    }

    /**
     * Show the specified resource.
     */
    public function show($ounitid, $id)
    {
        $doc = Document::joinRelationship('village')
            ->joinRelationship('fiscalYear')
            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join
                    ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                    ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value);
            }])
            ->where('acc_documents.ounit_id', $ounitid)
            ->select([
                'acc_documents.id as id',
                'acc_documents.ounit_id',
                'acc_documents.description as document_description',
                'acc_documents.document_date as document_date',
                'acc_documents.document_number as document_number',
                'acc_documents.create_date as create_date',
                'acc_documents.ounit_head_id as ounit_head_id',
                'acc_documents.creator_id as creator_id',
                'acc_documents.read_only as read_only',
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                'village_ofcs.abadi_code as village_abadicode',
                'fiscal_years.name as fiscalYear_name',
                'fiscal_years.id as fiscal_year_id',
            ])
            ->with(['ounitHead', 'person', 'ounit' => function ($query) {
                $query
                    ->with(['ancestors' => function ($query) {
                        $query
                            ->where('unitable_type', '!=', StateOfc::class)
                            ->withoutGlobalScopes();
                    }])
                    ->withoutGlobalScopes();

            }, 'articles' => function ($query) {
                $query
                    ->orderBy('priority', 'asc')
                    ->with(['transaction.cheque' => function ($query) {
                        $query->with(['latestStatus', 'bankAccount.bankBranch.bank']);

                    }, 'account' => function ($query) {
                        $query
                            ->with(['accountCategory', 'ancestorsAndSelf' => function ($query) {
                                $query->withoutGlobalScopes();
                            }])
                            ->withoutGlobalScopes();
                    }])
                    ->withoutGlobalScopes();

            }])
            ->find($id);

        return DocumentShowResource::make($doc);
    }

    public function addDocumentBaseInfo(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ounitID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }


        $accs = AccountCategory::leftJoinRelationship('accounts', function ($join) use ($data) {
            $join
                ->where(function ($query) use ($data) {
                    $query->where('acc_accounts.ounit_id', $data['ounitID'])
                        ->orWhereNull('acc_accounts.ounit_id');
                })
                ->withGlobalScopes();
        })
            ->select([
                'acc_accounts.id as id',
                'acc_accounts.name as title',
                'acc_accounts.segment_code as code',
                'acc_accounts.chain_code as chainedCode',
                'acc_accounts.parent_id as parent_id',
                'acc_accounts.isFertile as isFertile',
                'acc_accounts.entity_type as entity_type',
                'acc_accounts.accountable_type as accountable_type',
                'acc_account_categories.id as categoryID',
                'acc_account_categories.name as accountCategory',
            ])
            ->get();
        $accs = $accs->groupBy('accountCategory')->map(function ($item) {
            $item->map(function ($item) {
                return $item->setAttribute('isBankAccount', $item->accountable_type == DetailAccount::class && $item->entity_type == BankAccount::class,);
            });
            return $item->toHierarchy();
        });
        return response()->json($accs);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['message' => 'سند یافت نشد'], 404);
        }
        try {
            $user = Auth::user();
            DB::beginTransaction();
            $this->updateDocument($document, $data);

            //chequeID
            //payeeName
            //transactionCode
            //dueDate
            //paymentType
            $articles = json_decode($data['articles'], true);
            $noPaymentType = array_values(array_filter($articles, fn($article) => !isset($article['paymentType'])));
            if (!empty($noPaymentType)) {
                $this->bulkStoreArticle($noPaymentType, $document);
            }

            $cheques = array_values(array_filter($articles, fn($article) => isset($article['paymentType']) && $article['paymentType'] == 1 && !isset($article['transactionID'])));

            $newCheques = [];
            foreach ($cheques as $cheque) {
                //chequeID
                //payeeName
                //transactionCode
                //dueDate
                //paymentType
                $whiteCheque = Cheque::with('chequeBook')->find($cheque['chequeID']);
                $whiteCheque = $this->updateCheque($cheque, $whiteCheque);
                $whiteCheque->statuses()->attach($this->issuedChequeStatus()->id);
                $cheque['withdrawal'] = $cheque['creditAmount'];
                $cheque['bankAccountID'] = $whiteCheque->chequeBook->bankAccount->id;
                $cheque['userID'] = $user->id;
                $cheque['chequeID'] = $whiteCheque->id;
                $cheque['trackingCode'] = $whiteCheque->segment_number;
                $transaction = $this->storeTransaction($cheque);
                $cheque['transactionID'] = $transaction->id;
                $newCheques[] = $cheque;
            }
            if (!empty($newCheques)) {
                $this->bulkStoreArticle($newCheques, $document);
            }

            $bills = array_values(array_filter($articles, fn($article) => isset($article['paymentType']) && $article['paymentType'] == 2 && !isset($article['transactionID']) && isset($article['billNumber'])));

            $newBills = [];
            foreach ($bills as $bill) {
                //chequeID
                //payeeName
                //transactionCode
                //dueDate
                //paymentType

                $bill['withdrawal'] = $bill['creditAmount'];
                $bill['bankAccountID'] = $bill['accountID'];
                $bill['userID'] = $user->id;
                $bill['trackingCode'] = $bill['billNumber'];
                $transaction = $this->storeTransaction($bill);
                $bill['transactionID'] = $transaction->id;
                $newBills[] = $bill;
            }
            if (!empty($newBills)) {
                $this->bulkStoreArticle($newBills, $document);
            }


            if (isset($data['deletedID'])) {
                $article = Article::with('transaction.cheque')->find($data['deletedID']);
                if ($article->transaction) {
                    $transaction = $this->softDeleteTransaction($article->transaction);
                    $cheque = $article->transaction?->cheque;
                    if ($cheque) {
                        $this->resetChequeAndFree($cheque);
                    }

                }

                $article->delete();
            }

            DB::commit();


            for ($i = 1; $i <= 3; $i++) {
                Cache::forget("last_year_confirmed_documents_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

                Cache::forget("three_months_two_years_ago_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

                Cache::forget("nine_month_last_year_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

            }


            $document->load(['articles' => function ($query) {
                $query
                    ->orderBy('priority', 'asc')
                    ->with(['transaction.cheque.latestStatus', 'account' => function ($query) {
                        $query
                            ->with('accountCategory', 'ancestorsAndSelf')
                            ->withoutGlobalScopes();
                    }])
                    ->withoutGlobalScopes();

            }]);
            return ArticlesListResource::collection($document->articles);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error', 'error'], 500);
        }

    }

    public function updateOldDocument(Request $request, $id)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ounitID' => 'required',
            'fiscalYearID' => 'required',
            'articles' => 'required',

        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['message' => 'سند یافت نشد'], 404);
        }
        try {
            $user = Auth::user();
            DB::beginTransaction();
            $this->updateDocument($document, $data);

            //chequeID
            //payeeName
            //transactionCode
            //dueDate
            //paymentType
            $articles = json_decode($data['articles'], true);

            $this->bulkStoreArticle($articles, $document);
            $status = $this->deleteDocumentStatus();

            $document = Document::joinRelationship('statuses', ['statuses' => function ($join) {
                $join
                    ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                    ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value);
            }])->where('description', 'سند تبدیل سرفصل حساب ها به کدینگ جدید')->where('ounit_id', $data['ounitID'])
                ->first();
            if ($document) {
                $this->attachStatusToDocument($document, $status, Auth::user()->id);
            }
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
                        ->where('ounit_id', '=', $data['ounitID'])->orWhereNull('ounit_id')//                    ->withoutGlobalScopes()
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
            DB::commit();
            for ($i = 1; $i <= 3; $i++) {
                Cache::forget("last_year_confirmed_documents_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

                Cache::forget("three_months_two_years_ago_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

                Cache::forget("nine_month_last_year_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

            }
            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error', 'error'], 500);
        }

    }

    public function resetChequeAndFreeByArticle(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'articleID' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }
        $article = Article::with('transaction.cheque')->find($data['articleID']);
        if (!$article) {
            return response()->json(['message' => 'رکورد مورد نظر یافت نشد'], 404);
        }
        try {
            DB::beginTransaction();
            if ($article->transaction) {
                $transaction = $this->softDeleteTransaction($article->transaction);
                $cheque = $article->transaction?->cheque;
                if ($cheque) {
                    $this->resetChequeAndFree($cheque);
                }
                $article->transaction_id = null;
                $article->save();
            } else {
                return response()->json(['message' => 'رکورد مورد نظر یافت نشد'], 404);
            }
            DB::commit();
            return response()->json(['message' => 'با موفقیت انجام شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json('error', 500);
        }
    }

    public function setConfirmedStatusTODocument(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'documentID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $document = Document::find($data['documentID']);

        if (!$document) {
            return response()->json(['message' => 'سند یافت نشد'], 404);
        }

        try {
            DB::beginTransaction();
            $status = $this->confirmedDocumentStatus();
            $this->attachStatusToDocument($document, $status, Auth::user()->id);
            DB::commit();
            for ($i = 1; $i <= 3; $i++) {
                Cache::forget("last_year_confirmed_documents_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

                Cache::forget("three_months_two_years_ago_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

                Cache::forget("nine_month_last_year_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

            }
            return response()->json(['message' => 'با موفقیت انجام شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json('error', 500);
        }
    }

    public function setDraftStatusTODocument(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'documentID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $document = Document::find($data['documentID']);

        if (!$document) {
            return response()->json(['message' => 'سند یافت نشد'], 404);
        }

        try {
            DB::beginTransaction();
            $status = $this->draftDocumentStatus();
            $this->attachStatusToDocument($document, $status, Auth::user()->id);
            DB::commit();
            return response()->json(['message' => 'با موفقیت انجام شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json('error', 500);
        }
    }

    public function setDeleteStatusTODocument(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'documentID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $document = Document::find($data['documentID']);

        if (!$document) {
            return response()->json(['message' => 'سند یافت نشد'], 404);
        }

        try {
            DB::beginTransaction();
            $status = $this->deleteDocumentStatus();
            $this->attachStatusToDocument($document, $status, Auth::user()->id);
            $articles = $document->articles()->whereNotNull('transaction_id')->get();
            $articles->each(function ($article) {
                $transaction = $article->transaction;
                $cheque = $article->transaction?->cheque;
                if ($cheque) {
                    $this->resetChequeAndFree($cheque);
                }

            });
            DB::commit();
            for ($i = 1; $i <= 3; $i++) {
                Cache::forget("last_year_confirmed_documents_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

                Cache::forget("three_months_two_years_ago_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

                Cache::forget("nine_month_last_year_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

            }
            return response()->json(['message' => 'با موفقیت انجام شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json('error', 500);
        }
    }

    public function createClosingTemporaryDocument(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ounitID' => 'required',
            'fiscalYearID' => 'required',

        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        try {
            DB::beginTransaction();
            $fiscalYear = FiscalYear::find($data['fiscalYearID']);

            $draftDocsExist = Document::where('fiscal_year_id', $fiscalYear->id)
                ->where('ounit_id', $data['ounitID'])
                ->joinRelationship('statuses', ['statuses' => function ($join) {
                    $join
                        ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                        ->where('statuses.name', '=', DocumentStatusEnum::DRAFT->value);
                }])
                ->exists();

            if ($draftDocsExist) {
                return response()->json(['message' => 'شما سند پیشنویس دارید، ابتدا به قطعی کردن این اسناد اقدام کنید'], 400);
            }

            $tempCategory = AccountCategoryTypeEnum::BUDGETARY->getAccCategoryValues();
            $subQuery = \DB::table('acc_articles')
                ->selectRaw('
        DISTINCT acc_articles.id,
        acc_articles.debt_amount,
        acc_articles.credit_amount,
        acc_articles.account_id
    ')
                ->join('acc_documents', 'acc_articles.document_id', '=', 'acc_documents.id')
                ->join('accDocument_status', 'accDocument_status.document_id', '=', 'acc_documents.id')
                ->join('statuses', 'accDocument_status.status_id', '=', 'statuses.id')
                ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value)
                ->where('acc_documents.fiscal_year_id', '=', $fiscalYear->id)
                ->where('acc_documents.document_type_id', '!=', DocumentTypeEnum::TEMPORARY->value)
                ->where('acc_documents.ounit_id', '=', $data['ounitID']);

            $docs = Account::joinSub($subQuery, 'distinct_articles', function ($join) {
                $join->on('acc_accounts.id', '=', 'distinct_articles.account_id');
            })
                ->whereIntegerInRaw('acc_accounts.category_id', $tempCategory)
                ->select([
                    'acc_accounts.id as id',
                    'acc_accounts.name as name',
                    'acc_accounts.segment_code as code',
                    'acc_accounts.chain_code as chainedCode',
                    \DB::raw('SUM(distinct_articles.debt_amount) as total_debt_amount'),
                    \DB::raw('SUM(distinct_articles.credit_amount) as total_credit_amount'),
                ])
                ->groupBy(
                    'acc_accounts.id',
                    'acc_accounts.name',
                    'acc_accounts.segment_code',
                    'acc_accounts.chain_code'
                )
                ->get();
//            $docs = Account::joinRelationship('articles.document.statuses', [
//                'document' => function ($join) use ($data, $fiscalYear) {
////                    $join->where('acc_documents.ounit_id', $data['ounitID']);
//                },
//                'statuses' => function ($join) {
//                    $join
//                        ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)');
//                }
//            ])
//                ->whereIntegerInRaw('acc_accounts.category_id', $tempCategory)
//                ->where('acc_documents.fiscal_year_id', $fiscalYear->id)
//                ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value)
//                ->where('acc_documents.ounit_id', $data['ounitID'])
//                ->withoutGlobalScopes()
//                ->select([
//                    \DB::raw('SUM(debt_amount) as total_debt_amount'),
//                    \DB::raw('SUM(credit_amount) as total_credit_amount'),
//                    'acc_accounts.id as id',
//                    'acc_accounts.name as name',
//                    'acc_accounts.segment_code as code',
//                    'acc_accounts.chain_code as chainedCode',
//
//                ])
//                ->groupBy(
//                    'acc_accounts.id',
//                    'acc_accounts.name',
//                    'acc_accounts.segment_code',
//                    'acc_accounts.chain_code'
//                )
//                ->get();

            if ($docs->isEmpty()) {
                return response()->json(['message' => 'No account found'], 404);
            }

            $doc['fiscalYearID'] = $fiscalYear->id;
            $lastDocNumber = Document::where('fiscal_year_id', $data['fiscalYearID'])
                ->where('ounit_id', $data['ounitID'])
                ->orderByRaw('CAST(document_number AS UNSIGNED) DESC')
                ->first();

            $doc['documentNumber'] = $lastDocNumber ? $lastDocNumber->document_number + 1 : 1;
            $doc['documentDate'] = (new Jalalian($fiscalYear->name, 1, 1))->getEndDayOfYear()->toDateString();
            $doc['ounitID'] = $data['ounitID'];
            $doc['description'] = 'سند بستن حساب موقت';
            $doc['documentTypeID'] = DocumentTypeEnum::TEMPORARY->value;


            $articles = $docs->filter(function ($doc) {
                return $doc->total_credit_amount - $doc->total_debt_amount != 0;
            })
                ->values()
                ->map(function ($doc, $key) {
                    $dif = $doc->total_credit_amount - $doc->total_debt_amount;
                    if ($dif > 0) {
                        $creditAmount = $dif;
                        $debtAmount = 0;
                    } else {
                        $creditAmount = 0;
                        $debtAmount = abs($dif);
                    }
                    return [
                        'description' => $doc->name,
                        'priority' => $key + 1,
                        'debtAmount' => $creditAmount,
                        'creditAmount' => $debtAmount,
                        'accountID' => $doc->id,
                    ];
                });

            $totalDebtAmount = $articles->sum('debtAmount');
            $totalCreditAmount = $articles->sum('creditAmount');

            $difference = $totalCreditAmount - $totalDebtAmount;

            if ($difference != 0) {

                $mazadAndKasriAccount = Account::where('name', AccCategoryEnum::SURPLUS_DEFICIT->getLabel())->where('chain_code', 51001)->first();

                $priority = $articles->count() + 1;
                $description = $mazadAndKasriAccount->name;
                if ($difference > 0) {
                    $debtAmount = abs($difference);
                    $creditAmount = 0;
                } else {
                    $debtAmount = 0;
                    $creditAmount = abs($difference);
                }
                $articles->push([
                    'description' => $description,
                    'priority' => $priority,
                    'debtAmount' => $debtAmount,
                    'creditAmount' => $creditAmount,
                    'accountID' => $mazadAndKasriAccount->id,
                ]);

            }
            $doc['articles'] = $articles;


            DB::commit();
            return response()->json($doc);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error', 'error'], 500);
        }

    }

    public function createClosingDocument(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ounitID' => 'required',
            'fiscalYearID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        try {
            DB::beginTransaction();
            $fiscalYear = FiscalYear::find($data['fiscalYearID']);
            $draftDocsExist = Document::where('fiscal_year_id', $fiscalYear->id)
                ->where('ounit_id', $data['ounitID'])
                ->joinRelationship('statuses', ['statuses' => function ($join) {
                    $join
                        ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                        ->where('statuses.name', '=', DocumentStatusEnum::DRAFT->value);
                }])
                ->exists();

            if ($draftDocsExist) {
                return response()->json(['message' => 'شما سند پیشنویس دارید، ابتدا به قطعی کردن این اسناد اقدام کنید'], 400);
            }
            $balanceCategory = AccountCategoryTypeEnum::BALANCE_SHEET->getAccCategoryValues();
            $regularCategory = AccountCategoryTypeEnum::REGULATORY->getAccCategoryValues();

            $categories = array_merge($balanceCategory, $regularCategory);
//            $docs = Account::joinRelationship('articles.document.statuses', [
//                'document' => function ($join) use ($data, $fiscalYear) {
//                    $join
//                        ->where('acc_documents.ounit_id', $data['ounitID']);
//                },
//                'statuses' => function ($join) {
//                    $join
//                        ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)');
//                }
//            ])
//                ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value)
//                ->where('acc_documents.fiscal_year_id', $fiscalYear->id)
//                ->whereIntegerInRaw('acc_accounts.category_id', $categories)
//                ->withoutGlobalScopes()
//                ->select([
//                    \DB::raw('SUM(debt_amount) as total_debt_amount'),
//                    \DB::raw('SUM(credit_amount) as total_credit_amount'),
//                    'acc_accounts.id as id',
//                    'acc_accounts.name as name',
//                    'acc_accounts.segment_code as code',
//                    'acc_accounts.chain_code as chainedCode',
//
//                ])
//                ->groupBy(
//                    'acc_accounts.id',
//                    'acc_accounts.name',
//                    'acc_accounts.segment_code',
//                    'acc_accounts.chain_code'
//                )
//                ->get();
            $subQuery = \DB::table('acc_articles')
                ->selectRaw('
        DISTINCT acc_articles.id,
        acc_articles.debt_amount,
        acc_articles.credit_amount,
        acc_articles.account_id
    ')
                ->join('acc_documents', 'acc_articles.document_id', '=', 'acc_documents.id')
                ->join('accDocument_status', 'accDocument_status.document_id', '=', 'acc_documents.id')
                ->join('statuses', 'accDocument_status.status_id', '=', 'statuses.id')
                ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value)
                ->where('acc_documents.fiscal_year_id', '=', $fiscalYear->id)
                ->where('acc_documents.document_type_id', '!=', DocumentTypeEnum::CLOSING->value)
                ->where('acc_documents.ounit_id', '=', $data['ounitID']);

            $docs = Account::joinSub($subQuery, 'distinct_articles', function ($join) {
                $join->on('acc_accounts.id', '=', 'distinct_articles.account_id');
            })
                ->whereIntegerInRaw('acc_accounts.category_id', $categories)
                ->select([
                    'acc_accounts.id as id',
                    'acc_accounts.name as name',
                    'acc_accounts.segment_code as code',
                    'acc_accounts.chain_code as chainedCode',
                    \DB::raw('SUM(distinct_articles.debt_amount) as total_debt_amount'),
                    \DB::raw('SUM(distinct_articles.credit_amount) as total_credit_amount'),
                ])
                ->groupBy(
                    'acc_accounts.id',
                    'acc_accounts.name',
                    'acc_accounts.segment_code',
                    'acc_accounts.chain_code'
                )
                ->get();

            if ($docs->isEmpty()) {
                return response()->json(['message' => 'No account found'], 404);
            }

            $doc['fiscalYearID'] = $fiscalYear->id;
            $lastDocNumber = Document::where('fiscal_year_id', $data['fiscalYearID'])
                ->where('ounit_id', $data['ounitID'])
                ->orderByRaw('CAST(document_number AS UNSIGNED) DESC')
                ->first();

            $doc['documentNumber'] = $lastDocNumber ? $lastDocNumber->document_number + 1 : 1;
            $doc['documentDate'] = (new Jalalian($fiscalYear->name, 1, 1))->getEndDayOfYear()->toDateString();
            $doc['ounitID'] = $data['ounitID'];
            $doc['description'] = 'سند اختتامیه';
            $doc['documentTypeID'] = DocumentTypeEnum::CLOSING->value;


//            $document = $this->storeDocument($doc);
//            $this->attachStatusToDocument($document, $this->confirmedDocumentStatus(), Auth::user()->id);

            $articles = $docs->filter(function ($doc) {
                return $doc->total_credit_amount - $doc->total_debt_amount != 0;
            })
                ->values()
                ->map(function ($doc, $key) {
                    $dif = $doc->total_credit_amount - $doc->total_debt_amount;
                    if ($dif > 0) {
                        $creditAmount = $dif;
                        $debtAmount = 0;
                    } else {
                        $creditAmount = 0;
                        $debtAmount = abs($dif);
                    }
                    return [
                        'description' => $doc->name,
                        'priority' => $key + 1,
                        'debtAmount' => $creditAmount,
                        'creditAmount' => $debtAmount,
                        'accountID' => $doc->id,
                    ];
                });
            $totalDebtAmount = $articles->sum('debtAmount');
            $totalCreditAmount = $articles->sum('creditAmount');

            $difference = $totalCreditAmount - $totalDebtAmount;

            if ($difference != 0) {

                $mazadAndKasriAccount = Account::where('name', AccCategoryEnum::SURPLUS_DEFICIT->getLabel())->where('chain_code', 51001)->first();

                $priority = $articles->count() + 1;
                $description = $mazadAndKasriAccount->name;
                if ($difference > 0) {
                    $debtAmount = abs($difference);
                    $creditAmount = 0;
                } else {
                    $debtAmount = 0;
                    $creditAmount = abs($difference);
                }
                $articles->push([
                    'description' => $description,
                    'priority' => $priority,
                    'debtAmount' => $debtAmount,
                    'creditAmount' => $creditAmount,
                    'accountID' => $mazadAndKasriAccount->id,
                ]);

            }
            $doc['articles'] = $articles;


            DB::commit();
            return response()->json($doc);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error', 'error'], 500);
        }

    }

    public function createOpeningDocument(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ounitID' => 'required',
            'fiscalYearID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        try {
            DB::beginTransaction();
            $fiscalYear = FiscalYear::find($data['fiscalYearID']);
            $lastYearFiscalYear = FiscalYear::where('name', $fiscalYear->name - 1)->first();

//            $lastYearClosingDoc = Account::joinRelationship('articles.document.statuses', [
//                'document' => function ($join) use ($data, $lastYearFiscalYear) {
//                    $join
//                        ->where('acc_documents.ounit_id', $data['ounitID']);
//                },
//                'statuses' => function ($join) {
//                    $join
//                        ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
//                        ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value);
//                }
//            ])
//                ->where('acc_documents.document_type_id', DocumentTypeEnum::CLOSING->value)
//                ->where('acc_documents.fiscal_year_id', $lastYearFiscalYear->id)
//                ->withoutGlobalScopes()
//                ->select([
//                    \DB::raw('SUM(acc_articles.debt_amount) as total_debt_amount'),
//                    \DB::raw('SUM(acc_articles.credit_amount) as total_credit_amount'),
//                    'acc_accounts.id as id',
//                    'acc_accounts.name as name',
//                    'acc_accounts.segment_code as code',
//                    'acc_accounts.chain_code as chainedCode',
//
//                ])
//                ->groupBy(
//                    'acc_accounts.id',
//                    'acc_accounts.name',
//                    'acc_accounts.segment_code',
//                    'acc_accounts.chain_code'
//                )
//                ->get();

            $lastYearClosingDoc = Document::joinRelationship('statuses', ['statuses' => function ($join) {
                $join
                    ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                    ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value);
            }])
                ->joinRelationship('articles.account')
                ->where('acc_documents.document_type_id', DocumentTypeEnum::CLOSING->value)
                ->where('acc_documents.fiscal_year_id', $lastYearFiscalYear->id)
                ->where('acc_documents.ounit_id', $data['ounitID'])
                ->withoutGlobalScopes()
                ->select([
                    \DB::raw('SUM(acc_articles.debt_amount) as total_debt_amount'),
                    \DB::raw('SUM(acc_articles.credit_amount) as total_credit_amount'),
//                    'acc_articles.debt_amount as total_debt_amount',
//                    'acc_articles.credit_amount as total_credit_amount',
                    'acc_accounts.id as id',
                    'acc_accounts.name as name',
                    'acc_accounts.segment_code as code',
                    'acc_accounts.chain_code as chainedCode',

                ])
                ->groupBy(
                    'acc_accounts.id',
                    'acc_accounts.name',
                    'acc_accounts.segment_code',
                    'acc_accounts.chain_code'
                )
                ->get();

            $doc['fiscalYearID'] = $fiscalYear->id;


            $doc['documentNumber'] = 1;
            $doc['documentDate'] = (new Jalalian($fiscalYear->name, 1, 1))->toDateString();
            $doc['ounitID'] = $data['ounitID'];
            $doc['description'] = 'سند افتتاحیه';
            $doc['documentTypeID'] = DocumentTypeEnum::OPENING->value;


//            $document = $this->storeDocument($doc);
//            $this->attachStatusToDocument($document, $this->confirmedDocumentStatus(), Auth::user()->id);

            $articles = $lastYearClosingDoc->filter(function ($doc) {
                return $doc->total_credit_amount - $doc->total_debt_amount != 0;
            })
                ->values()
                ->map(function ($doc, $key) {
                    $dif = $doc->total_credit_amount - $doc->total_debt_amount;
                    if ($dif > 0) {
                        $creditAmount = $dif;
                        $debtAmount = 0;
                    } else {
                        $creditAmount = 0;
                        $debtAmount = abs($dif);
                    }
                    return [
                        'description' => $doc->name,
                        'priority' => $key + 1,
                        'debtAmount' => $creditAmount,
                        'creditAmount' => $debtAmount,
                        'accountID' => $doc->id,
                    ];
                });
            $totalDebtAmount = $articles->sum('debtAmount');
            $totalCreditAmount = $articles->sum('creditAmount');

            $difference = $totalCreditAmount - $totalDebtAmount;

            if ($difference != 0) {

                $mazadAndKasriAccount = Account::where('name', AccCategoryEnum::SURPLUS_DEFICIT->getLabel())->where('chain_code', 51001)->first();

                $priority = $articles->count() + 1;
                $description = $mazadAndKasriAccount->name;
                if ($difference > 0) {
                    $debtAmount = abs($difference);
                    $creditAmount = 0;
                } else {
                    $debtAmount = 0;
                    $creditAmount = abs($difference);
                }
                $articles->push([
                    'description' => $description,
                    'priority' => $priority,
                    'debtAmount' => $debtAmount,
                    'creditAmount' => $creditAmount,
                    'accountID' => $mazadAndKasriAccount->id,
                ]);

            }
            $doc['articles'] = $articles;

            DB::commit();
            return response()->json($doc);

        } catch (\Exception $e) {
            return response()->json(['error' => 'error'], 422);
        }
    }

    public function insertClosingTemporaryDocument(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();

        try {
            DB::beginTransaction();
            $data['userID'] = $user->id;
            $data['ounitHeadID'] = OrganizationUnit::where('id', $data['ounitID'])->first()?->head_id;
            $document = $this->storeDocument($data);
            $this->attachStatusToDocument($document, $this->confirmedDocumentStatus(), $user->id);
            $arts = $data['articles'];

            $articles = json_decode($arts, true);
            $this->bulkStoreArticle($articles, $document);

            $document->load(['articles']);

            DB::commit();
            return response()->json($document);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error', 'error'], 500);
        }

    }

    public function currentFiscalYearSummary(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ounitID' => 'required',
            'fiscalYearID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }
        $fiscalYear = FiscalYear::find($data['fiscalYearID']);

        $ounit = OrganizationUnit::with(['ancestors', 'village'])->find($data['ounitID']);

        $openingDoc = Document::where('document_type_id', DocumentTypeEnum::OPENING->value)
            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join
                    ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                    ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value);
            }])
            ->where('fiscal_year_id', $data['fiscalYearID'])
            ->where('ounit_id', $ounit->id)->with(['articles', 'person'])->first();

        $closeTempDoc = Document::where('document_type_id', DocumentTypeEnum::TEMPORARY->value)
            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join
                    ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                    ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value);
            }])
            ->where('fiscal_year_id', $data['fiscalYearID'])
            ->where('ounit_id', $ounit->id)->with(['articles', 'person'])->first();

        $closingDoc = Document::where('document_type_id', DocumentTypeEnum::CLOSING->value)
            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join
                    ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                    ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value);
            }])
            ->where('fiscal_year_id', $data['fiscalYearID'])
            ->where('ounit_id', $ounit->id)->with(['articles', 'person'])->first();


        $result = [];
        $result['openingDoc'] = $openingDoc;
        $result['closeTempDoc'] = $closeTempDoc;
        $result['closingDoc'] = $closingDoc;
        $result['fiscalYear'] = $fiscalYear;
        $result['ounit'] = $ounit;

        return CurrentFiscalYearResource::make($result);
    }

    public function financialBalanceReport(Request $request)
    {
        try {
            $data = $request->all();
            $validate = Validator::make($data, [
                'ounitID' => 'required',
                'fiscalYearID' => 'required',
                'balanceType' => 'required',
                'status' => 'required',
                'showType' => 'required',
                'startDate' => 'sometimes',
                'endDate' => 'sometimes',
                'startDocNum' => 'sometimes',
                'endDocNum' => 'sometimes',
                'opening' => 'sometimes',
                'closing' => 'sometimes',
                'temporary' => 'sometimes',
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 422);
            }

            if (isset($data['startDate'])) {
                $startDate = convertJalaliPersianCharactersToGregorian($data['startDate']);
                $endDate = convertJalaliPersianCharactersToGregorian($data['endDate']);
                $searchByDate = true;
                $searchByDocNum = false;
                $startDocNum = null;
                $endDocNum = null;
            } else {
                $startDocNum = $data['startDocNum'];
                $endDocNum = $data['endDocNum'];
                $searchByDate = false;
                $searchByDocNum = true;
                $startDate = null;
                $endDate = null;

            }
            $statuses = [];
            $givenStatus = $data['status'];
            switch ($givenStatus) {
                case -1:
                    $statuses = collect(DocumentStatusEnum::cases())
                        ->reject(fn($item) => $item->value === DocumentStatusEnum::DELETED->value)
                        ->pluck('value')
                        ->toArray();
                    break;
                case 1:
                    $statuses[] = DocumentStatusEnum::CONFIRMED->value;
                    break;
                case 2:
                    $statuses[] = DocumentStatusEnum::DRAFT->value;
                    break;
            }
            $openType = isset($data['opening']) ? DocumentTypeEnum::OPENING->value : -1;
            $periodTypes = [
                DocumentTypeEnum::NORMAL->value,
                ...($data['temporary'] ?? false ? [DocumentTypeEnum::TEMPORARY->value] : []),
                ...($data['closing'] ?? false ? [DocumentTypeEnum::CLOSING->value] : []),
            ];
            $periodTypesString = implode(',', $periodTypes);
            $accountStatuses = [
                $this->activeAccountStatus()->id,
                $this->inactiveAccountStatus()->id,
                $this->importAccountStatus()->id,
            ];
            $accountStatusesString = implode(',', $accountStatuses);

            $accountableType = AccountLayerTypesEnum::getLayerByID($data['balanceType']);

            $results = DB::table(DB::raw('(
                WITH RECURSIVE descendants AS (
                    SELECT id, id as root_id
                    FROM acc_accounts
                    WHERE accountable_type ="' . addslashes($accountableType) . '"
                    AND (status_id IN (' . $accountStatusesString . '))
                    AND (ounit_id = ' . $data['ounitID'] . ' OR ounit_id IS NULL)
                    UNION ALL
                    SELECT a.id, d.root_id
                    FROM acc_accounts a
                    INNER JOIN descendants d ON a.parent_id = d.id
                    WHERE (a.ounit_id = ' . $data['ounitID'] . ' OR a.ounit_id IS NULL)
                        )
                SELECT * FROM descendants
            ) as descendants')
            )
                ->leftJoin('acc_articles', 'acc_articles.account_id', '=', 'descendants.id')
                ->leftJoin('acc_documents', 'acc_documents.id', '=', 'acc_articles.document_id')
                // Join the pivot table for statuses. This table contains the create_date and status_name.
                ->join('accDocument_status', 'accDocument_status.document_id', '=', 'acc_documents.id')
                ->join('statuses', 'accDocument_status.status_id', '=', 'statuses.id')
                ->join('acc_accounts as root_account', 'root_account.id', '=', 'descendants.root_id')
                // Ensure we only get the latest status per document
                ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                // And only if that latest status has the name "active"
                ->whereIn('statuses.name', $statuses)
                ->where('acc_documents.ounit_id', $data['ounitID'])
                ->where('acc_documents.fiscal_year_id', $data['fiscalYearID'])
                ->when($searchByDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('acc_documents.document_date', [$startDate, $endDate]);
                })
                ->when($searchByDocNum, function ($query) use ($startDocNum, $endDocNum) {
                    $query->whereBetween('acc_documents.document_number', [$startDocNum, $endDocNum]);
                })
                ->select(
                    [
                        'descendants.root_id',
                        'root_account.name',
                        'root_account.chain_code',

                        // Sums for document_type_id = 1 & 2 & 4 (combined)
                        DB::raw("SUM(CASE WHEN acc_documents.document_type_id IN ({$periodTypesString}) THEN acc_articles.credit_amount ELSE 0 END) as period_credit"),
                        DB::raw("SUM(CASE WHEN acc_documents.document_type_id IN ({$periodTypesString}) THEN acc_articles.debt_amount ELSE 0 END) as period_debt"),

                        // Sums for document_type_id = 3, opening type
                        DB::raw("SUM(CASE WHEN acc_documents.document_type_id = {$openType} THEN acc_articles.credit_amount ELSE 0 END) as opening_credit"),
                        DB::raw("SUM(CASE WHEN acc_documents.document_type_id = {$openType} THEN acc_articles.debt_amount ELSE 0 END) as opening_debt"),

                    ]
                )
                ->groupBy('descendants.root_id', 'root_account.name', 'root_account.chain_code')
                ->orderBy('root_account.category_id', 'asc')
                ->get();
            $showType = $data['showType'];
            if ($showType == 1) {
                $results = $results->reject(function ($item) {
                    return $item->opening_credit == 0 && $item->opening_debt == 0 && $item->period_credit == 0 && $item->period_debt == 0;
                });
            } elseif ($showType == 2) {
                $results = $results->reject(function ($item) {
                    $untilNowCredit = $item->opening_credit + $item->period_credit;
                    $untilNowDebt = $item->opening_debt + $item->period_debt;

                    return $untilNowCredit - $untilNowDebt == 0;
                });
            } elseif ($showType == 3) {
                $results = $results->reject(function ($item) {
                    $untilNowCredit = $item->opening_credit + $item->period_credit;
                    $untilNowDebt = $item->opening_debt + $item->period_debt;

                    return $untilNowCredit - $untilNowDebt != 0;
                });
            }

            $ounit = OrganizationUnit::joinRelationship('head.person')->select('persons.display_name as head_name')->find($data['ounitID']);

            $rc = RecruitmentScript::where('organization_unit_id', $data['ounitID'])
                ->whereHas('latestStatus', function ($query) {
                    $query->where('statuses.name', '=', 'فعال');
                })
                ->joinRelationship('scriptType', function ($join) {
                    $join->where('script_types.title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
                })->with('person')->first();


            return TarazLogResource::collection($results)->additional([
                'head_name' => $ounit->head_name,
                'financial_manager' => $rc->person->display_name
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function ounitsWithBankAccounts(Request $request)
    {
        $user = Auth::user();
        $recruitmentScripts = $user
            ->activeRecruitmentScripts()
            ->whereHas('scriptType', function ($query) {
                $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
            })
            ->with(['organizationUnit' => function ($query) {
                $query
                    ->with(['village', 'ancestors' => function ($query) {
                        $query->where('unitable_type', '!=', StateOfc::class);
                    },
                        'accounts' => function ($q) {
                            $q->where('entity_type', BankAccount::class);

                        }
                    ]);
            }])
            ->get();

        return OunitWithBankAccounts::collection($recruitmentScripts->pluck('organizationUnit'));
    }

    public function bulkInsertDocsForOunits(Request $request)
    {

        $data = $request->all();
        $validate = Validator::make($data, [
            'docs' => 'required',
            'docDate' => 'required',
            'docDescription' => 'required',
            'fiscalYearID' => 'required',
            'budgetAccountID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $docs = json_decode($data['docs'], true);

            $user = Auth::user();

            $docs = collect($docs)->map(function ($doc) use ($data, $user) {
                $lastDocNumber = $this->getLatestDoc($doc['ounitID'], $data['fiscalYearID']);

                $docData['documentNumber'] = $lastDocNumber ? $lastDocNumber->document_number + 1 : 2;
                $docData['documentDate'] = $data['docDate'];
                $docData['description'] = $data['docDescription'];
                $docData['fiscalYearID'] = $data['fiscalYearID'];
                $docData['ounitID'] = $doc['ounitID'];
                $docData['ounitHeadID'] = OrganizationUnit::where('id', $doc['ounitID'])->first()?->head_id;
                $docData['userID'] = $user->id;
                $docData['articles'] = [
                    [
                        'description' => $data['docDescription'],
                        'priority' => 1,
                        'debtAmount' => 0,
                        'creditAmount' => $doc['amount'],
                        'accountID' => $data['budgetAccountID'],
                    ],
                    [
                        'description' => $doc['artDescription'],
                        'priority' => 2,
                        'debtAmount' => $doc['amount'],
                        'creditAmount' => 0,
                        'accountID' => $doc['bankAccountID'],
                    ]
                ];
                return $docData;
            });

            $docs->each(function ($doc) use ($data) {
                $document = $this->storeDocument($doc);
                $status = $this->draftDocumentStatus();
                $this->attachStatusToDocument($document, $status, $doc['userID']);
                $this->bulkStoreArticle($doc['articles'], $document);
            });
            DB::commit();
            return response()->json(['message' => 'اسناد با موفقیت ایجاد شدند'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteSwapDocument(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ounitID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $document = Document::where('description', 'سند تبدیل سرفصل حساب ها به کدینگ جدید')->where('ounit_id', $data['ounitID'])->first();

        if (!$document) {
            return response()->json(['message' => 'سند یافت نشد'], 200);
        }

        try {
            DB::beginTransaction();
            $status = $this->deleteDocumentStatus();
            $this->attachStatusToDocument($document, $status, Auth::user()->id);

            DB::commit();
            return response()->json(['message' => 'با موفقیت انجام شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json('error', 500);
        }
    }

    public function rearrangeDocuments(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ounitID' => 'required',
            'fiscalYearID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        try {
            DB::beginTransaction();
            $docs = Document::where('fiscal_year_id', $data['fiscalYearID'])
                ->where('ounit_id', $data['ounitID'])
                ->joinRelationship('statuses', ['statuses' => function ($join) {
                    $join
                        ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                        ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value);
                }])
                ->orderBy('document_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();
            $docNum = 1;
            $docs->each(function ($doc) use (&$docNum) {
                $doc->document_number = $docNum++;
                $doc->save();
            });
            DB::commit();
            return response()->json(['message' => 'با موفقیت انجام شد',]);


        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('error', 500);
        }
    }

    public function purgeDocuments(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ounitID' => 'required',
            'fiscalYearID' => 'required',
            'documentID' => 'required',

        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        try {
            DB::beginTransaction();
            $document = Document::find($data['documentID']);

            $status = $this->deleteDocumentStatus();
            $this->attachStatusToDocument($document, $status, Auth::user()->id);

            //============================
            $document = Document::joinRelationship('statuses', ['statuses' => function ($join) {
                $join
                    ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                    ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value);
            }])->where('description', 'سند تبدیل سرفصل حساب ها به کدینگ جدید')->where('ounit_id', $data['ounitID'])
                ->first();
            if ($document) {
                $this->attachStatusToDocument($document, $status, Auth::user()->id);
                //==============================
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
                            ->where('ounit_id', '=', $data['ounitID'])->orWhereNull('ounit_id')//                    ->withoutGlobalScopes()
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
            } else {
                $response = [];
            }


            DB::commit();
            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function duplicateDocument(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'targetOunits' => ['required', 'json'],
            'documentID' => ['required', 'integer'],
        ]);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $document = Document::with(['articles.account' => function ($query) {
                $query->withoutGlobalScopes();

            }])->find($data['documentID']);
            $draft = $this->draftDocumentStatus();

            $ounits = OrganizationUnit::with(['documents' => function ($query) use ($document) {
                $query->where('fiscal_year_id', $document->fiscal_year_id)
                    ->where(function ($query) {
                        $query->where('document_type_id', DocumentTypeEnum::CLOSING->value)
                            ->orWhere('document_type_id', DocumentTypeEnum::TEMPORARY->value);
                    })
                    ->with(['articles.account' => function ($query) {
                        $query
                            ->withoutGlobalScopes()
                            ->with(['subject.account']);

                    }]);

            }])->whereIntegerInRaw('id', json_decode($data['targetOunits'], true))
                ->get();

            $closedOunits = [];
            $importStatus = $this->importAccountStatus();
            $ounits->each(function ($ounit) use ($document, $draft, &$closedOunits, $importStatus) {
                if ($ounit->documents->isEmpty()) {
                    $newDocument = $document->replicate();
                    $newDocument->ounit_id = $ounit->id;
                    $newDocument->ounit_head_id = $ounit?->head_id;
                    $newDocument->create_date = now();
                    $newDocument->read_only = false;
                    $lastDocNumber = $this->getLatestDoc($ounit->id, $document->fiscal_year_id);
                    $newDocument->document_number = $lastDocNumber ? $lastDocNumber->document_number + 1 : 2;
                    $newDocument->save();

                    $this->attachStatusToDocument($newDocument, $draft, Auth::user()->id);

                    $document->articles->each(function ($article) use ($newDocument, $importStatus) {
                        $newArticle = $article->replicate();
                        $newArticle->document_id = $newDocument->id;
                        $newArticle->transaction_id = null;
                        if ($article->account->ounit_id == $newDocument->ounit_id) {
                            return;
                        } elseif ($article->account->ounit_id != null) {
                            $newArticle->account_id = null;
                        } elseif (is_null($article->account->ounit_id) && ($article->account->category_id == AccCategoryEnum::INCOME->value || $article->account->category_id == AccCategoryEnum::EXPENSE->value) && $article->account->status_id == $importStatus->id) {

                            $newArticle->account_id = $article->account?->subject?->account?->id;
                        }
                        $newArticle->save();
                    });
                } else {
                    $closedOunits[] = $ounit->name;
                }
            });


            DB::commit();
            for ($i = 1; $i <= 3; $i++) {
                Cache::forget("last_year_confirmed_documents_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

                Cache::forget("three_months_two_years_ago_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

                Cache::forget("nine_month_last_year_ounit_{$document->ounit_id}_year_{$document->fiscal_year_id}_subject_type_{$i}");

            }
            return response()->json(['message' => 'با موفقیت انجام شد', 'closedOunits' => $closedOunits]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function bulkChangeDocStatusToConfirmed(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'documentIDs' => ['required', 'json'],
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $docIDs = json_decode($data['documentIDs'], true);
            $documents = Document::whereIntegerInRaw('id', $docIDs)->get();
            $user = Auth::user();
            $confStatus = $this->confirmedDocumentStatus();
            $documents->each(function ($document) use ($confStatus, $user) {
                $this->attachStatusToDocument($document, $confStatus, $user->id);
            });

            DB::commit();

            return response()->json(['message' => 'با موفقیت انجام شد']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
