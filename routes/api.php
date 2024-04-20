<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

/**
 * @bodyParam user_id int required The id of the user. Example: 9
 * @bodyParam room_id string The id of the room.
 * @bodyParam forever boolean Whether to ban the user forever. Example: false
 * @bodyParam another_one number This won't be added to the examples. No-example
 *
 * @group testGroup
 *
 * @subgroup testSubGroup
 */

