<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Exception;
use GuzzleHttp\Psr7\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
    try{
    $validatedData=$request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ]);
}
catch(ValidationException $e){
    return $this->ResponseTasksErrors('Please ensure the accuracy of the provided information and fill in the required fields',400);
}
catch(Exception $e){
    return $this->ResponseTasksErrors('An error occurred while creating the task',500);

}
    $us=User::create($validatedData);
    return $this->ResponseTasks($us,'User created successfully',201);
}

public function updateUser(Request $request,User $user)
{
    try{
        $validatedData=$request->validate([
            'id' => 'integer|required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
    }
    catch(ValidationException $e){
        return $this->ResponseTasksErrors('Please ensure the accuracy of the provided information and fill in the required fields',400);
    }
    $userUpd=User::find($validatedData['id']);
    if(!$userUpd){
        return $this->ResponseTasksErrors('User not found',404);
    }
    $userUpd->update($request->except('id'));
    return $this->ResponseTasks($userUpd,'User updated successfully',200);
}

public function searchUsers(Request $request)
{
    $name = $request->input('name');
    $email = $request->input('email');

    if (empty($name) && empty($email)) {
        return $this->ResponseTasksErrors('Please provide search criteria', 400);
    }

    $query = User::query();

    if ($name) {
        $query->where('name', 'like', '%' . $name . '%');
    }

    if ($email) {
        $query->where('email', 'like', '%' . $email . '%');
    }

    $searchResult = $query->get();
    if($searchResult->isEmpty()){
        return $this->ResponseTasksErrors('No users found',404);
    }

    return $this->ResponseTasks($searchResult, 'Users matching the search criteria', 200);
}

public function updatePassword(Request $request, User $user)
{
    $validatedData = $request->validate([
        'user_id' => 'required|integer|exists:users,id',
        'old_password' => 'required|string|min:8',
        'new_password' => 'required|string|min:8',
    ]);

    $user = User::find($validatedData['user_id']);

    if (!$user) {
        return $this->ResponseTasksErrors('User not found', 404);
    }

    if (!Hash::check($validatedData['old_password'], $user->password)) {
        return $this->ResponseTasksErrors('Invalid old password', 400);
    }
    $user->password = bcrypt($validatedData['new_password']);
    $user->save();

    return $this->ResponseTasks($user, 'Password updated successfully', 200);
}



    public function deleteUser(Request $request)
    {
        $userId = $request->input('user_id');

        $user = User::find($userId);

        if (!$user) {
            return $this->ResponseTasksErrors('User not found', 404);
        }

            Task::where('user_id', $userId)->delete();
            Comment::where('user_id', $userId)->delete();
            $user->delete();
            $users=User::all();
            return $this->ResponseTasks($users, 'User and associated records deleted successfully', 200);


}
}
