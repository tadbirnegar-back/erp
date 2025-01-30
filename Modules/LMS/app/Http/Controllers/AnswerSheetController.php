<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnswerSheetController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        //

        return response()->json($this->data);
    }


}
