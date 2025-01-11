<?php

namespace Modules\ACC\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ACC\app\Http\Traits\ArticleTrait;
use Modules\ACC\app\Http\Traits\DocumentTrait;
use Modules\ACC\app\Models\AccountCategory;
use Modules\ACC\app\Models\Article;
use Modules\ACC\app\Models\Document;
use Modules\ACC\app\Resources\ArticlesListResource;
use Modules\ACC\app\Resources\DocumentListResource;
use Modules\ACC\app\Resources\DocumentShowResource;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\OUnitMS\app\Models\StateOfc;
use Morilog\Jalali\Jalalian;
use Validator;

class DocumentController extends Controller
{
    use DocumentTrait, ArticleTrait;

    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'ounitID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $currentYear = Jalalian::now()->getYear();
        $fiscalYear = FiscalYear::where('name', $currentYear)->first();

        $docs = Document::leftJoinRelationship('articles')
            ->joinRelationship('statuses', function ($join) {
                $join->on('accDocument_status.id', '=', \DB::raw('(
                                SELECT id
                                FROM accDocument_status AS ps
                                WHERE ps.document_id = acc_documents.id
                                ORDER BY ps.create_date DESC
                                LIMIT 1
                            )'));
            })
            ->where('acc_documents.ounit_id', $request->ounitID)
            ->where('acc_documents.fiscal_year_id', $fiscalYear->id)
            ->select([
                'acc_documents.id as document_id',
                'acc_documents.description as document_description',
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                'acc_documents.document_date as document_date',
                'acc_documents.document_number as document_number',
                'acc_documents.create_date as create_date',
                \DB::raw('SUM(acc_articles.debt_amount) as total_debt_amount'),
                \DB::raw('SUM(acc_articles.credit_amount) as total_credit_amount'),
            ])
            ->groupBy('acc_documents.id', 'acc_documents.description', 'statuses.name', 'statuses.class_name', 'acc_documents.document_date', 'acc_documents.document_number', 'acc_documents.create_date')
            ->get();

        return DocumentListResource::collection($docs);
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
            ->joinRelationship('statuses', function ($join) {
                $join->on('accDocument_status.id', '=', \DB::raw('(
                                SELECT id
                                FROM accDocument_status AS ps
                                WHERE ps.document_id = acc_documents.id
                                ORDER BY ps.create_date DESC
                                LIMIT 1
                            )'));
            })
            ->joinRelationship('fiscalYear')
            ->where('acc_documents.ounit_id', $ounitid)
            ->select([
                'acc_documents.id as id',
                'acc_documents.ounit_id',
                'acc_documents.description as document_description',
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                'acc_documents.document_date as document_date',
                'acc_documents.document_number as document_number',
                'acc_documents.create_date as create_date',
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
                    ->with(['account.ancestorsAndSelf' => function ($query) {
                        $query
                            ->with('accountCategory')
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
            $join->where('ounit_id', $data['ounitID'])
                ->withGlobalScopes();
        })
            ->select([
                'acc_accounts.id as id',
                'acc_accounts.name as title',
                'acc_accounts.segment_code as code',
                'acc_accounts.chain_code as chainedCode',
                'acc_accounts.parent_id as parent_id',
                'acc_account_categories.id as categoryID',
                'acc_account_categories.name as accountCategory',
            ])
            ->get();
        $accs = $accs->groupBy('accountCategory')->map(function ($item) {
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
                    ->with(['account.ancestorsAndSelf' => function ($query) {
                        $query
                            ->with('accountCategory')
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
}
