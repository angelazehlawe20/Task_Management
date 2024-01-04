<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class TaskController extends Controller
{
    use GeneralTrait;

public function getSortedTasks(Request $request)
    {
        $sortBy = $request->input('sort_by');

        $validSortOptions = ['priority', 'date', 'name','status'];

        if (!in_array($sortBy, $validSortOptions)) {
            return $this->ResponseTasksErrors('Invalid sorting parameter', 400);
        }

        $tasks = Task::query();

        switch ($sortBy) {
            case 'priority':
                $tasks->orderBy('priority')->orderBy('due_date');
                break;
            case 'date':
                $tasks->orderBy('due_date')->orderBy('priority');
                break;
            case 'name':
                $tasks->orderBy('title');
                break;
            case 'status':
                $tasks->orderBy('status');
                break;
            default:
               return $this->ResponseTasksErrors('Invalid sorting parameter', 400);
                break;
        }

        $sortedTasks = $tasks->get();
        if($sortedTasks->isEmpty())
        {
            return $this->ResponseTasksErrors('There are no tasks',404);
        }

        return $this->ResponseTasks($sortedTasks,'All tasks sorted by ' . $sortBy, 200);
    }


protected function getColorForPriority(Request $request)
    {
        $priority=$request->input('priority');
        $priorityColors = [
            'high' => '#FF0000',
            'medium' => '#FFFF00',
            'low' => '#00FF00'
        ];

        if (array_key_exists($priority, $priorityColors)) {
            return $priorityColors[$priority];
        }
        return $this->ResponseTasksErrors('Color of '.$priority.' priority not found',404);
    }



public function createTask(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'priority' => 'required|in:high,medium,low',
                'title' => 'required|string',
                'description' => 'string',
                'due_date' => 'date_format:Y-m-d H:i',

            ]);
        } catch (ValidationException $e) {
            return $this->ResponseTasksErrors('Please ensure the accuracy of the provided information and fill in the required fields', 400);
        } catch (Exception $e) {
            return $this->ResponseTasksErrors('An error occurred while creating the task', 500);
        }

        $priority=$request->input('priority');
        $color=$this->getColorForPriority($request,$priority);
        $newTask = Task::create([
            'user_id' => $validatedData['user_id'],
            'priority' => $validatedData['priority'],
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'due_date' => $validatedData['due_date'],
            'color' => $color,

        ]);

        return $this->ResponseTasks($newTask,'Task created successfully', 201);
    }




public function showTask(Request $request,Task $task)
    {
        $one=Task::where('id',$request->id)->get();
        if($one->isEmpty())
        {
            return $this->ResponseTasksErrors('Task not found',404);
        }
        return $this->ResponseTasks($one,'Task retrieved successfully',200);
    }


public function updateTask(Request $request, Task $task)
    {

        try{
            $validatedData=$request->validate([
                'id' => 'integer',
                'priority' => 'string|in:high,medium,low',
                'title' => 'string',
                'description' => 'string',
                'due_date' => 'date_format:Y-m-d',
                'status'=> 'string|in:COMPLETED,IN_PROGRESS,PENDING'
            ]);
        }
        catch (ValidationException $e) {
            return $this->ResponseTasksErrors('Please ensure the accuracy of the provided information and fill in the required fields', 400);
        } catch (Exception $e) {
            return $this->ResponseTasksErrors('An error occurred while updating the task', 500);
        }
        try{
        $taskk=Task::findOrFail($validatedData['id']);
            $priority=$request->input('priority');
            $color=$this->getColorForPriority($request,$priority);
            $taskk->update([
            'id' => $validatedData['id'],
            'priority' => $validatedData['priority'],
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'due_date' => $validatedData['due_date'],
            'status' =>$validatedData['status'],
            'color' => $color,
            ]);

            return $this->ResponseTasks($taskk,'Task updated successfully',200);
        }
        catch (ModelNotFoundException $ex) {
            return $this->ResponseTasksErrors('Task not found', 404);
        }
    }




public function updateStatus(Request $request)
{
    try{
    $validatedData = $request->validate([
        'id' => 'required',
        'status' => 'required|string|in:COMPLETED,IN_PROGRESS,PENDING'
    ]);
}
catch (ValidationException $e) {
    return $this->ResponseTasksErrors('Please ensure the accuracy of the provided information and fill in the required fields', 400);
} catch (Exception $e) {
    return $this->ResponseTasksErrors('An error occurred while updating the task', 500);
}
    try{
    $task = Task::findOrFail($validatedData['id']);

    $task->update(['status' => $validatedData['status']]);

    return $this->ResponseTasks($task, 'Status changed successfully', 200);
}
catch(Exception $e){
    return $this->ResponseTasksErrors('Task not found',404);
}
}



public function searchTask(Request $request)
{
    $title=$request->input('title');
    $query=Task::query();
    if($title){
        $query->where('title','like','%'.$title.'%');
    }
    $seachData=$query->get();
    if($seachData->isEmpty()){
        return $this->ResponseTasksErrors('Tasks not found',404);
    }
    return $this->ResponseTasks($seachData,'Tasks matching the search criteria',200);
}



public function softDeleteTask(Request $request)
{
        try{
        $task_id = $request->id;

        $task = Task::findOrFail($task_id);

        Comment::where('task_id',$task_id)->delete();
        $tasks=$task->delete();
        return $this->ResponseTasks(null,'The task was successfully moved to the Recycle Bin',200);
    }
    catch(Exception $e){
        return $this->ResponseTasksErrors('Task not found',404);
    }
}




public function showStatus(Request $request)
    {
            $stat = $request->input('status');

            $validStatuses = [
                'COMPLETED',
                'IN_PROGRESS',
                'PENDING'
            ];

            if (!in_array($stat, $validStatuses)) {
                return $this->ResponseTasksErrors('Status '.$stat.' not found',404);
            }
                $taskStat = Task::where('status', $stat)->get();
                if ($taskStat->isNotEmpty())
                {
                    return $this->ResponseTasks($taskStat,'Tasks with status '.$stat.' retrieved successfully',200);
                }
                return $this->ResponseTasksErrors('No tasks found for '.$stat.' status',404);
            }


public function todayTask(Request $request){
    $user=$request->input('user_id');
    $today=now()->toDateString();
    $tasks = Task::where('user_id',$user)->whereDate('due_date', '=', $today)->get();
    $us = Task::where('user_id', $user)->exists();
    if(!$us)
    {
        return $this->ResponseTasksErrors('User not found',404);
    }
    if($tasks->isEmpty())
    {
        return $this->ResponseTasksErrors('Tasks expected to be completed today not found', 404);
    }
        return $this->ResponseTasks($tasks,'Tasks expected to be completed today',200);
    }



public function remindTasks(Request $request)
{
    $user=$request->input('user_id');
    $currentTime = now();
    $reminderTime = $currentTime->copy()->addMinutes(15);
    $userExistsInTasks = Task::where('user_id', $user)->exists();
    if(!$userExistsInTasks){
        return $this->ResponseTasksErrors('User not found',404);
    }
try{
    $tasks = Task::where('user_id', $user)
    ->where('status','=','IN_PROGRESS')
    ->whereDate('due_date', $currentTime->toDateString())
    ->whereTime('due_date', '>=', $currentTime->toTimeString())
    ->whereTime('due_date', '=', $reminderTime->toTimeString())
    ->get();

    if($tasks->isEmpty()){
        return $this->ResponseTasksErrors('No tasks found within the expected time',404);
    }
    return $this->ResponseTasks($tasks,'Only a 15 minutes remains to complete the task',200);
}
catch(Exception $e){
    return $this->ResponseTasksErrors('Task not found',404);
}

}


public function taskNow(Request $request){
    try {
        $id = $request->input('user_id');

        $userExistsInTasks = Task::where('user_id', $id)->exists();

        if (!$userExistsInTasks) {
            return $this->ResponseTasksErrors('User not found', 404);
        }

        $currentTime = \Carbon\Carbon::now();

        $tasks = Task::where('user_id', $id)
            ->where('status', '=', 'IN_PROGRESS')
            ->where('due_date', '>=', $currentTime->subMinute())
            ->where('due_date', '<=', $currentTime->addMinute())
            ->get();

        if ($tasks->isEmpty()) {
            return $this->ResponseTasksErrors('No tasks found within the expected time', 404);
        }

        return $this->ResponseTasks($tasks, 'Tasks found within the expected time', 200);
    } catch (Exception $e) {
        return $this->ResponseTasksErrors('An error occurred while processing tasks', 500);
    }
}


public function restoreTask(Request $request){

    $task_id=$request->input('id');
    $delTask=Task::withTrashed()->where('id',$task_id)->first();
    if(!$delTask)
    {
        return $this->ResponseTasksErrors('Task not found',404);
    }
    $delTask->restore();
    return $this->ResponseTasks($delTask,'Task restored successfully',200);

}


public function showDeletedTasks(Request $request)
{
    $sortBy=$request->input('sort_by');
    $validateSort=['name','Deletion_time'];
    if(!in_array($sortBy,$validateSort)){
        return $this->ResponseTasksErrors('Invalid sorting parameter',400);
    }
    $deletedTasks=Task::onlyTrashed();

    switch($sortBy){
        case 'name':
            $deletedTasks->orderBy('title');
            break;
        case 'Deletion_time':
            $deletedTasks->orderBy('deleted_at');
            break;
        default:
            return $this->ResponseTasksErrors('Invalid sorting parameter',400);
            break;
    }
    $sortedTasks=$deletedTasks->get();
    return $this->ResponseTasks($sortedTasks,'All deleted tasks sorted by ' . $sortBy,200);
}



public function forceDeleteTask(Request $request){

    $task_id=$request->input('id');
    $task=Task::withTrashed()->find($task_id);
    if(!$task)
    {
        return $this->ResponseTasksErrors('Task not found',404);
    }
    $task->forceDelete();
    return $this->ResponseTasks(null,'Task deleted successfully',200);
}



public function sharedTask(Request $request)
{
    $validatedData = $request->validate([
        'task_id' => 'required|exists:tasks,id',
        'user_id' => 'required|exists:users,id'
    ]);

    try {
        $task = Task::find($validatedData['task_id']);
        if (!$task) {
            return $this->ResponseTasksErrors('Task not found', 404);
        }

        $sharedUsers = $task->sharedUsers()->pluck('users.id')->toArray();

        if (in_array($validatedData['user_id'], $sharedUsers)) {
            return $this->ResponseTasksErrors('The task has already been shared with this user', 500);
        } else {
            $attached = $task->sharedUsers()->attach($validatedData['user_id']);
            if (!$attached) {
                return $this->ResponseTasksErrors('Task sharing failed', 500);
            }
            return $this->ResponseTasks($task, 'Task shared successfully', 200);
        }
    } catch (ValidationException $e) {
        return $this->ResponseTasksErrors('Please ensure the accuracy of the provided information and fill in the required fields', 400);
    } catch (QueryException $e) {
        return $this->ResponseTasksErrors('Task sharing failed', 500);
    }
}


public function getSharedTasks(Request $request)
{
    $user_id=$request->input('user_id');
    $checkUser=User::find($user_id);
    if(!$checkUser){
        return $this->ResponseTasksErrors('User not found',404);
    }
    $checkUserr=$checkUser->sharedTasks;
    return $this->ResponseTasks($checkUserr,'all tasks of user',200);
}

}
