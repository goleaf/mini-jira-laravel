<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;

class UserController extends Controller
{

    public function adminDashboard($searchValue)
    {
        $userCount = User::latest()->count();
        $users = User::latest()->filter(['search' => $searchValue])->paginate(10);
        $tasks = Task::latest()->get();
        $taskCompleted = Task::where('completed', 1)->count();
        $taskDue = Task::where('completed', 0)->count();

        return view('user.admin-dashboard', compact('userCount', 'users', 'tasks', 'taskCompleted', 'taskDue'));
    }


    public function userDashboard(User $user)
    {
        $userTasks = Task::where('taskcreator_id', $user->id)->orWhere('assigneduser_id', $user->id)->paginate(10);

        return view('user.dashboard', compact('user', 'userTasks'));
    }

}
