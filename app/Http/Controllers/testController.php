<?php

namespace App\Http\Controllers;


use Modules\PersonMS\app\Models\Person;

class testController extends Controller
{

    public function run()
    {
        $searchTerm = 'کاوه مولودی';
        $words = explode(' ', $searchTerm);

        $lastWord = end($words);

        $lastWordWithWildcard = $lastWord . '*';

        $data = Person::whereRaw('MATCH(display_name) AGAINST(? IN BOOLEAN MODE) > 0.5', [$searchTerm])
            ->orderByRaw('MATCH(display_name) AGAINST(? IN BOOLEAN MODE) DESC', [$lastWordWithWildcard])
            ->get();

        return response()->json($data);




    }
}
