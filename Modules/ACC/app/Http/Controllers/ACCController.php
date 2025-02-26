<?php

namespace Modules\ACC\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Http\Traits\ArticleTrait;
use Modules\ACC\app\Http\Traits\DocumentTrait;
use Modules\ACC\app\Jobs\ImportDocsJob;
use Modules\ACMS\app\Http\Trait\CircularSubjectsTrait;
use Modules\ACMS\app\Http\Trait\FiscalYearTrait;
use Modules\BNK\app\Http\Traits\BankTrait;
use Modules\BNK\app\Http\Traits\ChequeTrait;
use Modules\BNK\app\Http\Traits\TransactionTrait;

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
        ImportDocsJob::dispatch($request->ounitID, $request->fileID);

        return response()->json(['message' => 'Job created successfully'], 200);

    }
}
