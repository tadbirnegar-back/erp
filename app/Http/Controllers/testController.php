<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;


class testController extends Controller
{
    use ApprovingListTrait;

    public function run()
    {
$user=User::with(['organizationUnits'])->find(1955);


$a=$user->organizationUnits[0]->descendants()->whereDepth(2)->with(['person','ancestors','payments'=>function ($query) {
    $query->where('status_id', '=', '46');
},'evaluators'])->get();

        dd($a->pluck('payments')->flatten());
    }

}
