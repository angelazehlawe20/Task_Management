<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\User;
use GuzzleHttp\Psr7\Query;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use GeneralTrait;

    public function getAll(Request $request,User $user)
{
    $sortBy=$request->input('sort_by','name');
    $usr=User::query();
    switch($sortBy){
    case 'name':
        $usr->orderBy('name');
        break;
    case 'id':
        $usr->orderBy('id');
        break;
    default:
        return $this->ResponseTasksErrors('Invalid sorting parameter', 400);
        break;
    }
    $usersSorted = $usr->get();
    return $this->ResponseTasks($usersSorted,'All users',200);
}

public function getOneUser(Request $request,User $user)
{

    $oneUser=User::where('id',$request->id)->get();
    if($oneUser->isEmpty())
    {
        return $this->ResponseTasksErrors('User not found',404);
    }
    return $this->ResponseTasks($oneUser,'User id '.$request->id,200);
}

public function createUser(Request $request,User $user)
{
    $validatedData=$request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ]);
    $us=User::create($validatedData);
    return $this->ResponseTasks($us,'User created successfully');
}
}
