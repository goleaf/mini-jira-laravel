<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;

class UserController extends Controller
{

    public function userDashboard(User $user)
    {
        return view('user.dashboard',[
            'user' => $user,
            'tasks' =>   Task::where('task_creator_user_id', $user->id)->orWhere('assigned_user_id', $user->id)->paginate(10)
        ]);
    }

}
