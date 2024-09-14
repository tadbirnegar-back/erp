<?php

namespace App\Http\Controllers;


use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Models\Enactment;

class testController extends Controller
{
    use EnactmentTrait;


    public function run()
    {


        $userRoles = ['admin', 'بخشدار'];
        $enactmentStatus = 'در انتظار وصول';

        $componentsToRender = $this->getComponentsToRender($userRoles, $enactmentStatus);
        dd($componentsToRender->all());
        $a = Enactment::with('statuses.pivot.operator.person')->find(2);

        dd($a);

        $componentsToRender = collect([
            'MainEnactment' => ['reviewStatuses', 'meeting', 'attachments', 'creator'],
            'MembersBeforeReview' => ['meeting.persons.pivot.mr'],
            'DenyCard' => ['creator'],
        ]);

        $myPermissions = collect(['myProfile', 'MainEnactment', 'DenyCard', 'xyz']);

        $uniqueValues = $componentsToRender->only($myPermissions->intersect($componentsToRender->keys()))
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        $enactment = Enactment::with($uniqueValues)->find(2);

        $componentsWithData = $componentsToRender->map(fn($relations, $component) => [
            'name' => $component,
            'data' => $enactment->only($relations)
        ])->values();

        $enactment->setAttribute('componentsToRender', $componentsWithData);

//        return response()->json($a);

        dd($componentsWithData, $enactment);
    }

}

