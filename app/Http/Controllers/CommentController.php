<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\Comment;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use GeneralTrait;
public function getAllSorted(Request $request){
    $sortBy=$request->input('sort_by','date');
        $comments=Comment::with(['date']);
        switch ($sortBy) {
            case 'date':
                $comments->orderBy('addition_date');
                break;
            case 'comment':
                $comments->orderBy('content');
                break;
            default:
                return $this->ResponseTasksErrors('Invalid sorting parameter', 400);
                break;
        }
        $sortedComments=$comments->get();
        return $this->ResponseTasks($sortedComments,'All comments by '.$sortBy.':',200);
    }

    public function getOne(Request $request,Comment $comment)
    {

        $one=Comment::where('id',$request->id)->get();
        if($one->isEmpty()){
            return $this->ResponseTasksErrors('Comment not found',404);
        }
        return $this->ResponseTasks($one,'Task retrieved successfully',200);
    }

    public function getCommOfTask(Request $request,Comment $comment){
        $task_id=$request->input('task_id');
        $commTask=Comment::where('task_id',$task_id)->get();
        if($commTask->isEmpty()){
            return $this->ResponseTasksErrors('Task not found',404);
        }
        return $this->ResponseTasks($commTask,'Comments related to task #'.$task_id,200);
    }


    public function newComment(Request $request)
    {
        try{
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'task_id' => 'required|exists:tasks,id',
            'content' => 'required|string',
        ]);

    $newComment = Comment::create($validatedData);

    return $this->ResponseTasks($newComment,'Comment created successfully',201);
    }
    catch(ValidationException $e){
        return $this->ResponseTasksErrors('Please ensure the accuracy of the provided information and fill in the required fields',400);
    }
    catch(Exception $e){
        return $this->ResponseTasksErrors('An error occurred while creating the comment',500);
    }
    }

    public function updateComm(Request $request,Comment $comment)
    {
        try{
            $validatedData = $request->validate([
                'id' => 'integer|required|exists:comments,id',
                'content' => 'string',
            ]);
        }
            catch(ValidationException $e){

                return $this->ResponseTasksErrors('Please ensure the accuracy of the provided information and fill in the required fields',400);
            }
            $commentId = Comment::findOrFail($validatedData['id']);
            if(!$commentId) {
                return $this->ResponseTasksErrors('Comment not found', 404);
            }

            $commentId->update($request->except('id'));

            $commentId->setVisible([
                'id',
                'content',
            ]);

            return $this->ResponseTasks($commentId,'Comment updated successfully',200);
    }

    public function deletComm(Request $request)
    {
        $comm_id=$request->input('id');
        $commDel=Comment::findOrFail($comm_id);
        if(!$commDel){
            return $this->ResponseTasksErrors('Comment not found');
        }
        $commDel->delete();
        $comments=Comment::all();
        return $this->ResponseTasks($comments,'Comment deleted successfully',200);
    }


}
