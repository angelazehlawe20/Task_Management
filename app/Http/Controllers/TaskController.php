<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
                $tasks->orderBy('due_date')->orderBy('task_time')->orderBy('priority');
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


public function deleteTask(Request $request)
{
        try{
        $task_id = $request->id;

        $task = Task::findOrFail($task_id);

        Comment::where('task_id',$task_id)->delete();
        $tasks=$task->delete();
        return $this->ResponseTasks('Task deleted successfully', 200);
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
                $us=User::find($request->user_id);
                if(!$us){
                    return $this->ResponseTasksErrors('User not found',404);
                }
                if($tasks->isEmpty()){
                    return $this->ResponseTasksErrors('Tasks expected to be completed today not found', 404);
                }
                return $this->ResponseTasks($tasks,'Tasks expected to be completed today',200);
            }


    public function remindTasks(Request $request)
{
    $user=$request->input('user_id');
    $currentTime = now();
    $reminderTime = $currentTime->copy()->addMinutes(15);


    $us=User::find($user);
    if(!$us){
        return $this->ResponseTasksErrors('User not found',404);
    }
try{
    $tasks = Task::where('user_id', $user)
    ->where('status','=','IN_PROGRESS')
    ->whereDate('due_date', $currentTime->toDateString())
    ->whereTime('due_date', '>', $currentTime->toTimeString())
    ->whereTime('due_date', '<', $reminderTime->toTimeString())
    ->get();

    return $this->ResponseTasks($tasks,'Only a 15 minutes remains to complete the task',200);
}
catch(Exception $e){
    return $this->ResponseTasksErrors('Task not found',404);
}

}

public function fetchTasksToBeCompletedNow()
{
    $currentTime = now();

    $tasks = Task::where('status', '=', 'IN_PROGRESS')
        ->where('due_date', '=', $currentTime)
        ->get();

    return $tasks;
}


public function gh(Request $request){
    $currentTime = Carbon::now()->setTimezone('Asia/Damascus');
    return $currentTime;
}


}


