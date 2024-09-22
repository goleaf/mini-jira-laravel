<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function userDashboard(User $user)
    {
        if (Auth::id() !== $user->id && !Auth::user()->can('view', $user)) {
            abort(403, 'Unauthorized action.');
        }

        $createdTasks = $this->getCreatedTasksSortedByWeek($user);
        $assignedTasks = $this->getAssignedTasksSortedByWeek($user);
        $lastUpdatedTasks = $this->getLastUpdatedTasksSortedByWeek($user);

        $dashboardData = new Collection([
            'user' => $user,
            'createdTasks' => $createdTasks,
            'assignedTasks' => $assignedTasks,
            'lastUpdatedTasks' => $lastUpdatedTasks,
            'createdTasksCount' => $createdTasks->sum(function ($week) {
                return $week->count();
            }),
            'assignedTasksCount' => $assignedTasks->sum(function ($week) {
                return $week->count();
            }),
        ]);

        return view('user.dashboard', compact('dashboardData'));
    }

    private function getCreatedTasksSortedByWeek(User $user)
    {
        return Task::where('task_creator_user_id', $user->id)
            ->with(['taskType', 'taskStatus'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($task) {
                return $task->created_at->startOfWeek()->format('Y-m-d');
            });
    }

    private function getAssignedTasksSortedByWeek(User $user)
    {
        return Task::where('assigned_user_id', $user->id)
            ->with(['taskType', 'taskStatus'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($task) {
                return $task->created_at->startOfWeek()->format('Y-m-d');
            });
    }

    private function getLastUpdatedTasksSortedByWeek(User $user)
    {
        return Task::where(function ($query) use ($user) {
                $query->where('task_creator_user_id', $user->id)
                      ->orWhere('assigned_user_id', $user->id);
            })
            ->with(['taskType', 'taskStatus'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->groupBy(function ($task) {
                return $task->updated_at->startOfWeek()->format('Y-m-d');
            });
    }
}
