<?php

namespace Modules\ACC\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ACC\app\Http\Enums\AccountCategoryTypeEnum;
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
use Modules\ACMS\app\Models\FiscalYear;
use Modules\BNK\app\Models\BankAccount;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Morilog\Jalali\Jalalian;
use Validator;

class DocumentController extends Controller
{
    use DocumentTrait, ArticleTrait;


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
                    ->with(['ancestors' => function ($query) {
                        $query
                            ->where('unitable_type', '!=', StateOfc::class)
                            ->withoutGlobalScopes();
                    }])
                    ->withoutGlobalScopes();

            }, 'articles' => function ($query) {
                $query
                    ->orderBy('priority', 'asc')
                    ->with(['account' => function ($query) {
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
            DB::beginTransaction();
            $this->updateDocument($document, $data);

            $articles = json_decode($data['articles'], true);
            $this->bulkStoreArticle($articles, $document);

            if (isset($data['deletedID'])) {
                Article::find($data['deletedID'])->delete();
            }

            DB::commit();
            $document->load(['articles' => function ($query) {
                $query
                    ->orderBy('priority', 'asc')
                    ->with(['account' => function ($query) {
                        $query
                            ->with('accountCategory', 'ancestorsAndSelf')
                            ->withoutGlobalScopes();
                    }])
                    ->withoutGlobalScopes();

            }]);
            return ArticlesListResource::collection($document->articles);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
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

                $mazadAndKasriAccount = Account::where('name', 'مازاد و کسری')->where('chain_code', 51010)->first();

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

                $mazadAndKasriAccount = Account::where('name', 'مازاد و کسری')->where('chain_code', 51010)->first();

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

                $mazadAndKasriAccount = Account::where('name', 'مازاد و کسری')->where('chain_code', 51010)->first();

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
}
