<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use GeneralTrait;

    public function getSortedTasks(Request $request)
    {
        $sortBy=$request->input('sort_by','priority');
        $tasks=Task::with(['priority']);
        switch ($sortBy) {
            case 'priority':
                $tasks->orderBy('priority_id')->orderBy('due_date');
                break;
            case 'date':
                $tasks->orderBy('due_date')->orderBy('priority_id');
                break;
            case 'name':
                $tasks->orderBy('title');
                break;
            default:
                return $this->ResponseTasks(null,'Invalid sorting parameter', 400);
                break;
        }
        $sortedTasks=$tasks->get();
        return $this->ResponseTasks($sortedTasks,null,200);
    }


    public function newTask(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'priority_id' => 'required|exists:priorities,id',
            'title' => 'required|string',
            'description' => 'string',
            'due_date' => 'required|date_format:Y-m-d'
        ]);

        $newTask = Task::create([
            'user_id' => $validatedData['user_id'],
            'priority_id' => $validatedData['priority_id'],
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'due_date' => $validatedData['due_date'],
        ]);

        return $this->ResponseTasks($newTask,'Task created successfully',201);
    }


    public function show(Request $request,Task $task)
    {
        $one=Task::where('title',$request->title)->get();
        if(!$one)
        {
            return $this->ResponseTasks(null,'Task not found',404);
        }
        return $this->ResponseTasks($one,'Task retrieved successfully',200);
    }


    public function update(Request $request, Task $task)
{
    $validatedData = $request->validate([
        'id' => 'integer|required|exists:tasks,id',
        'priority_id' => 'integer|exists:priorities,id',
        'title' => 'string',
        'description' => 'string',
        'status' => 'integer|in:1,2,3',
        'due_date' => 'date_format:Y-m-d'
    ]);

    $task = Task::findOrFail($validatedData['id']);
    $task->status = $request->input('status');
    $task->update($request->except('id'));

    $task->setVisible([
        'id',
        'user_id',
        'priority_id',
        'title',
        'description',
        'due_date',
        'status'
    ]);

    return $this->ResponseTasks($task,'Task updated successfully',200);
}

public function delete(Request $request) {
    $validatedData = $request->validate([
        'id' => 'required|exists:tasks,id',
    ]);

    $task = Task::find($validatedData['id']);

    if (!$task) {
        return $this->ResponseTasks(['message' => 'Task not found', 404]);
    }

    $task->delete();
    $tasks=Task::all();

    return $this->ResponseTasks(['message' => 'Task deleted successfully','Tasks'=>$tasks]);
}


   public function showStatus(Request $request)
    {
            $stat = $request->input('status');

            $statusMap = [
                'COMPLETED' => 1,
                'IN_PROGRESS' => 2,
                'PENDING' => 3
            ];

            if (array_key_exists($stat, $statusMap)) {
                $taskStat = Task::where('status', $statusMap[$stat])->get();
                if ($taskStat->count() > 0)
                {
                    return $this->ResponseTasks($taskStat);
                }
                return $this->ResponseTasks(['message' => 'No tasks found for the selected status']);
            }
            else
             {
                return $this->ResponseTasks(['message' => 'Status not found']);
            }
        }

}



