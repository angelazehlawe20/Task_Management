<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\Comment;
use Dotenv\Exception\ValidationException;
use Exception;
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




}
