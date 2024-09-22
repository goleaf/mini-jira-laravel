<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function userDashboard(User $user)
    {

        $createdTasks = $this->getCreatedTasksSortedByWeek($user);
        $assignedTasks = $this->getAssignedTasksSortedByWeek($user);
        $lastUpdatedTasks = $this->getLastUpdatedTasksSortedByWeek($user);

        $dashboardData = new Collection([
            'user' => $user,
            'createdTasks' => $createdTasks,
            'assignedTasks' => $assignedTasks,
            'lastUpdatedTasks' => $lastUpdatedTasks,
            'createdTasksCount' => $createdTasks->sum(fn($tasks) => $tasks->count()),
            'assignedTasksCount' => $assignedTasks->sum(fn($tasks) => $tasks->count()),
        ]);

        return view('dashboard.index', compact('dashboardData'));
    }

    private function getCreatedTasksSortedByWeek(User $user)
    {
        return $this->getTasksSortedByWeek('task_creator_user_id', $user->id);
    }

    private function getAssignedTasksSortedByWeek(User $user)
    {
        return $this->getTasksSortedByWeek('assigned_user_id', $user->id);
    }

    private function getLastUpdatedTasksSortedByWeek(User $user)
    {
        return Task::where(function ($query) use ($user) {
                $query->where('task_creator_user_id', $user->id)
                      ->orWhere('assigned_user_id', $user->id);
            })
            ->with(['taskType', 'taskStatus', 'taskCreator', 'assignedUser', 'assignedTester'])
            ->get()
            ->groupBy(function ($task) {
                return $task->updated_at->format('YW');
            })
            ->map(function ($tasks) {
                return $tasks->sortByDesc('updated_at');
            });
    }

    private function getTasksSortedByWeek($column, $userId)
    {
        return Task::where($column, $userId)
            ->with(['taskType', 'taskStatus', 'taskCreator', 'assignedUser', 'assignedTester'])
            ->get()
            ->groupBy(function ($task) {
                return $task->created_at->format('YW');
            })
            ->map(function ($tasks) {
                return $tasks->sortByDesc('created_at');
            });
    }
}
