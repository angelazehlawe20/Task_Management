<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Models\Priority;
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

               ////comment
Route::post('/getSortedComments', [CommentController::class,'getAllSorted']);
Route::post('/getComment', [CommentController::class,'getOne']);
Route::post('/addComm',[CommentController::class,'newComment']);
Route::post('/commOfTask',[CommentController::class,'getCommOfTask']);
Route::post('/updateComment',[CommentController::class,'updateComment']);
Route::post('/getUserComments',[CommentController::class,'getUserComments']);
Route::post('/deleteComment',[CommentController::class,'deletComm']);

               ///priority
Route::post('/getSotedPriority',[PriorityController::class,'getAll']);
Route::post('/getColorForOrder',[PriorityController::class,'getColorForOrder']);
Route::post('/createPriority',[PriorityController::class,'createPriority']);
Route::post('/updatePriority',[PriorityController::class,'updatePriority']);
Route::post('/deletPriority',[PriorityController::class,'deletPriority']);

              ////user
Route::post('/getAllUsers',[UserController::class,'getAll']);
Route::post('/getOneUser',[UserController::class,'getOneUser']);
Route::post('/createUser',[UserController::class,'createUser']);
Route::post('/updateUser',[UserController::class,'updateUser']);
