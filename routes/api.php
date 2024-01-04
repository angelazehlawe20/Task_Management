<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
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
Route::post('/addNew',[TaskController::class,'createTask']);
Route::post('/getColorForPriority',[TaskController::class,'getColorForPriority']);
Route::post('/showTask',[TaskController::class,'showTask']);
Route::post('/updateStatus',[TaskController::class,'updateStatus']);
Route::post('/updateTask',[TaskController::class,'updateTask']);
Route::post('/softDeleteTask',[TaskController::class,'softDeleteTask']);
Route::post('/statusTask',[TaskController::class,'showStatus']);
Route::post('/searchTask',[TaskController::class,'searchTask']);
Route::post('/todayTask',[TaskController::class,'todayTask']);
Route::post('/remindTasks',[TaskController::class,'remindTasks']);
Route::post('/taskNow',[TaskController::class,'taskNow']);
Route::post('/restoreTask',[TaskController::class,'restoreTask']);
Route::post('/showDeletedTasks',[TaskController::class,'showDeletedTasks']);
Route::post('/forceDeleteTask',[TaskController::class,'forceDeleteTask']);
Route::post('/sharedTask',[TaskController::class,'sharedTask']);
Route::post('/getSharedTasks',[TaskController::class,'getSharedTasks']);



               ////comment
Route::post('/getSortedComments', [CommentController::class,'getAllSorted']);
Route::post('/getComment', [CommentController::class,'getOne']);
Route::post('/addComm',[CommentController::class,'newComment']);
Route::post('/commOfTask',[CommentController::class,'getCommOfTask']);
Route::post('/updateComment',[CommentController::class,'updateComment']);
Route::post('/getUserComments',[CommentController::class,'getUserComments']);
Route::post('/deleteComment',[CommentController::class,'deletComm']);

              ////user
Route::post('/getAllUsers',[UserController::class,'getAll']);
Route::post('/getOneUser',[UserController::class,'getOneUser']);
Route::post('/createUser',[UserController::class,'createUser']);
Route::post('/updateUser',[UserController::class,'updateUser']);
Route::post('/updatePassword',[UserController::class,'updatePassword']);
Route::post('/deleteUser',[UserController::class,'deleteUser']);
Route::post('/searchUsers',[UserController::class,'searchUsers']);
Route::post('/getTasksAndComments',[UserController::class,'getTasksAndComments']);
