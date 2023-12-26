<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use GeneralTrait;

    public function getAll()
    {
        $get=Comment::all();
        return $this->ResponseTasks($get,'All existing comments',200);
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
