<?php

namespace Modules\ACC\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ACC\app\Http\Enums\AccountCategoryTypeEnum;
use Modules\ACC\app\Http\Enums\AccountLayerTypesEnum;
use Modules\ACC\app\Http\Enums\DocumentStatusEnum;
use Modules\ACC\app\Http\Enums\DocumentTypeEnum;
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
use Modules\ACC\app\Resources\TarazLogResource;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\BNK\app\Http\Traits\ChequeTrait;
use Modules\BNK\app\Http\Traits\TransactionTrait;
use Modules\BNK\app\Models\BankAccount;
use Modules\BNK\app\Models\Cheque;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Morilog\Jalali\Jalalian;
use Validator;

class DocumentController extends Controller
{
    use DocumentTrait, ArticleTrait, ChequeTrait, TransactionTrait;


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
                \DB::raw('SUM(acc_articles.debt_amount) as total_debt_amount'),
                \DB::raw('SUM(acc_articles.credit_amount) as total_credit_amount'),
            ])
            ->groupBy('acc_documents.id', 'acc_documents.description', 'acc_documents.document_date', 'acc_documents.document_number', 'acc_documents.create_date', 'statuses.name', 'statuses.class_name')
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
                'acc_documents.create_date as create_date',
                \DB::raw('SUM(acc_articles.debt_amount) as total_debt_amount'),
                \DB::raw('SUM(acc_articles.credit_amount) as total_credit_amount'),
            ])
            ->groupBy('acc_documents.id', 'acc_documents.description', 'acc_documents.document_date', 'acc_documents.document_number', 'acc_documents.create_date', 'statuses.name', 'statuses.class_name')
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
            'fiscalYear' => [
                'required',
                'exists:fiscal_years,name'
            ],
            'ounitID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $data['userID'] = Auth::user()->id;
        $data['fiscalYearID'] = FiscalYear::where('name', $request->fiscalYear)->first()->id;

        $lastDocNumber = Document::where('fiscal_year_id', $data['fiscalYearID'])
            ->where('ounit_id', $data['ounitID'])
            ->latest('document_number')
            ->first();

        $data['documentNumber'] = $lastDocNumber ? $lastDocNumber->document_number + 1 : 1;

        try {
            DB::beginTransaction();

            $document = $this->storeDocument($data);
            $status = $this->draftDocumentStatus();
            $this->attachStatusToDocument($document, $status, $data['userID']);

            DB::commit();
            return response()->json($document);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
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
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                'village_ofcs.abadi_code as village_abadicode',
                'fiscal_years.name as fiscalYear_name'
            ])
            ->with(['ounit' => function ($query) {
                $query
                    ->with(['person', 'ancestors' => function ($query) {
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
                            ->with('accountCategory', 'ancestorsAndSelf')
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
                $transaction = $this->storeTransaction($cheque);
                $cheque['transactionID'] = $transaction->id;
                $newCheques[] = $cheque;
            }
            if (!empty($newCheques)) {
                $this->bulkStoreArticle($newCheques, $document);
            }


            if (isset($data['deletedID'])) {
                $article = Article::with('transaction.cheque')->find($data['deletedID']);
                if ($article->transaction) {
                    $transaction = $this->softDeleteTransaction($article->transaction);
                    $cheque = $article->transaction?->cheque;
                    $this->resetChequeAndFree($cheque);

                }

                $article->delete();
            }

            DB::commit();
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
            return response()->json([$e->getMessage(), $e->getTrace()], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
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
            return response()->json(['message' => 'با موفقیت انجام شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
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
            return response()->json($e->getMessage(), 500);
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
            DB::commit();
            return response()->json(['message' => 'با موفقیت انجام شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
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
            $tempCategory = AccountCategoryTypeEnum::BUDGETARY->getAccCategoryValues();

            $docs = Account::joinRelationship('articles.document.statuses', [
                'document' => function ($join) use ($data, $fiscalYear) {
                    $join
                        ->where('acc_documents.ounit_id', $data['ounitID'])
                        ->where('acc_documents.fiscal_year_id', $fiscalYear->id);
                },
                'statuses' => function ($join) {
                    $join
                        ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                        ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value);
                }
            ])
                ->whereIntegerInRaw('acc_accounts.category_id', $tempCategory)
                ->withoutGlobalScopes()
                ->select([
                    \DB::raw('(SELECT SUM(debt_amount) FROM acc_articles WHERE acc_articles.account_id = acc_accounts.id) as total_debt_amount'),
                    \DB::raw('(SELECT SUM(credit_amount) FROM acc_articles WHERE acc_articles.account_id = acc_accounts.id) as total_credit_amount'),
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

            if ($docs->isEmpty()) {
                return response()->json(['message' => 'No account found'], 404);
            }

            $doc['fiscalYearID'] = $fiscalYear->id;
            $lastDocNumber = Document::where('fiscal_year_id', $doc['fiscalYearID'])
                ->where('ounit_id', $data['ounitID'])
                ->latest('document_number')
                ->first();

            $doc['documentNumber'] = $lastDocNumber ? $lastDocNumber->document_number + 1 : 1;
            $doc['documentDate'] = Jalalian::now()->toDateString();
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

                $mazadAndKasriAccount = Account::where('name', 'مازاد و کسری')->where('chain_code', 5101)->first();

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
            return response()->json([$e->getMessage(), $e->getTrace()], 500);
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

            $balanceCategory = AccountCategoryTypeEnum::BALANCE_SHEET->getAccCategoryValues();
            $regularCategory = AccountCategoryTypeEnum::REGULATORY->getAccCategoryValues();

            $categories = array_merge($balanceCategory, $regularCategory);

            $docs = Account::joinRelationship('articles.document.statuses', [
                'document' => function ($join) use ($data, $fiscalYear) {
                    $join
                        ->where('acc_documents.ounit_id', $data['ounitID'])
                        ->where('acc_documents.fiscal_year_id', $fiscalYear->id);
                },
                'statuses' => function ($join) {
                    $join
                        ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                        ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value);
                }
            ])
                ->whereIntegerInRaw('acc_accounts.category_id', $categories)
                ->withoutGlobalScopes()
                ->select([
                    \DB::raw('(SELECT SUM(debt_amount) FROM acc_articles WHERE acc_articles.account_id = acc_accounts.id) as total_debt_amount'),
                    \DB::raw('(SELECT SUM(credit_amount) FROM acc_articles WHERE acc_articles.account_id = acc_accounts.id) as total_credit_amount'),
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

            if ($docs->isEmpty()) {
                return response()->json(['message' => 'No account found'], 404);
            }

            $doc['fiscalYearID'] = $fiscalYear->id;
            $lastDocNumber = Document::where('fiscal_year_id', $doc['fiscalYearID'])
                ->where('ounit_id', $data['ounitID'])
                ->latest('document_number')
                ->first();

            $doc['documentNumber'] = $lastDocNumber ? $lastDocNumber->document_number + 1 : 1;
            $doc['documentDate'] = Jalalian::now()->toDateString();
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

                $mazadAndKasriAccount = Account::where('name', 'مازاد و کسری')->where('chain_code', 5101)->first();

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
            return response()->json([$e->getMessage(), $e->getTrace()], 500);
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

            $lastYearClosingDoc = Account::joinRelationship('articles.document.statuses', [
                'document' => function ($join) use ($data, $lastYearFiscalYear) {
                    $join
                        ->where('acc_documents.ounit_id', $data['ounitID'])
                        ->where('acc_documents.document_type_id', DocumentTypeEnum::CLOSING->value)
                        ->where('acc_documents.fiscal_year_id', $lastYearFiscalYear->id);
                },
                'statuses' => function ($join) {
                    $join
                        ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                        ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value);
                }
            ])
                ->withoutGlobalScopes()
                ->select([
                    \DB::raw('SUM(DISTINCT acc_articles.debt_amount) as total_debt_amount'),
                    \DB::raw('SUM(DISTINCT acc_articles.credit_amount) as total_credit_amount'),
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
            $lastDocNumber = Document::where('fiscal_year_id', $doc['fiscalYearID'])
                ->where('ounit_id', $data['ounitID'])
                ->latest('document_number')
                ->first();

            $doc['documentNumber'] = $lastDocNumber ? $lastDocNumber->document_number + 1 : 1;
            $doc['documentDate'] = Jalalian::now()->toDateString();
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

                $mazadAndKasriAccount = Account::where('name', 'مازاد و کسری')->where('chain_code', 5101)->first();

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
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function insertClosingTemporaryDocument(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();

        try {
            DB::beginTransaction();
            $data['userID'] = $user->id;
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
            return response()->json([$e->getMessage(), $e->getTrace()], 500);
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
            ->where('fiscal_year_id', $data['fiscalYearID'])->where('ounit_id', $ounit->id)->with(['articles', 'person'])->first();

        $closeTempDoc = Document::where('document_type_id', DocumentTypeEnum::TEMPORARY->value)
            ->where('fiscal_year_id', $data['fiscalYearID'])->where('ounit_id', $ounit->id)->with(['articles', 'person'])->first();

        $closingDoc = Document::where('document_type_id', DocumentTypeEnum::CLOSING->value)
            ->where('fiscal_year_id', $data['fiscalYearID'])->where('ounit_id', $ounit->id)->with(['articles', 'person'])->first();


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


        $accountableType = AccountLayerTypesEnum::getLayerByID($data['balanceType']);

        $results = DB::table(DB::raw('(
                WITH RECURSIVE descendants AS (
                    SELECT id, id as root_id
                    FROM acc_accounts
                    WHERE accountable_type ="' . addslashes($accountableType) . '"
                    UNION ALL
                    SELECT a.id, d.root_id
                    FROM acc_accounts a
                    INNER JOIN descendants d ON a.parent_id = d.id
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
                $query->whereintegerInRaw('acc_documents.document_number', [$startDocNum, $endDocNum]);
            })
            ->select(
                [
                    'descendants.root_id',
                    'root_account.name',

                    // Sums for document_type_id = 1 & 2 & 4 (combined)
                    DB::raw("SUM(CASE WHEN acc_documents.document_type_id IN ({$periodTypesString}) THEN acc_articles.credit_amount ELSE 0 END) as period_credit"),
                    DB::raw("SUM(CASE WHEN acc_documents.document_type_id IN ({$periodTypesString}) THEN acc_articles.debt_amount ELSE 0 END) as period_debt"),

                    // Sums for document_type_id = 3, opening type
                    DB::raw("SUM(CASE WHEN acc_documents.document_type_id = {$openType} THEN acc_articles.credit_amount ELSE 0 END) as opening_credit"),
                    DB::raw("SUM(CASE WHEN acc_documents.document_type_id = {$openType} THEN acc_articles.debt_amount ELSE 0 END) as opening_debt"),

                ]
            )
            ->groupBy('descendants.root_id', 'root_account.name')
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


        return TarazLogResource::collection($results);
    }
}
