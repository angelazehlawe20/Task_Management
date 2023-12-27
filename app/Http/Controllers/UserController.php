<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use GeneralTrait;

    public function getAll()
{
    $users = User::all();
    return $this->ResponseTasks($users,'All users',200);
}

public function getOneUser(Request $request,User $user)
{
    $u_id=$request->input('id');
    $oneUser=User::where('id',$u_id)->get();
    if(!$oneUser)
    {
        return $this->ResponseTasksErrors('User not found',404);
    }
    return $this->ResponseTasks($oneUser,'User id '.$u_id,200);
}
}
