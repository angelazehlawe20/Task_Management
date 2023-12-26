<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use GeneralTrait;
public function getAllSorted(Request $request){
    $sortBy=$request->input('sort_by','addition_date');
        $comments=Comment::with(['addition_date']);
        switch ($sortBy) {
            case 'addition_date':
                $comments->orderBy('addition_date');
                break;
            case 'content':
                $comments->orderBy('content');
                break;
            default:
                return $this->ResponseTasksErrors('Invalid sorting parameter', 400);
                break;
        }
        $sortedComments=$comments->get();
        return $this->ResponseTasks($sortedComments,null,200);
    }

    public function getOne(Request $request,Comment $comment)
    {
        $one=Comment::where('id',$request->id)->get();
        if($one->isEmpty()){
            return $this->ResponseTasksErrors('Comment not found',404);
        }
        return $this->ResponseTasks($one,'Task retrieved successfully',200);
    }
}
