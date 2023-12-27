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


}
