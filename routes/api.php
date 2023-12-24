<?php

use App\Http\Controllers\TaskController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

             /////Task
Route::post('/orderByAll',[TaskController::class,'getSortedTasks']);
Route::post('/addNew',[TaskController::class,'newTask']);
Route::post('/showTask',[TaskController::class,'show']);
Route::post('/updateTask',[TaskController::class,'update']);
Route::post('/deleteTask',[TaskController::class,'delete']);
Route::post('/statusTask',[TaskController::class,'showStatus']);
