<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\Comment;
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

    public function FunctionName() : Returntype {

    }
}
