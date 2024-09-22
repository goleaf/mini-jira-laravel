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
        $createdTasks = $this->getCreatedTasksSorted($user);
        $assignedTasks = $this->getAssignedTasksSorted($user);
        $lastUpdatedTasks = $this->getLastUpdatedTasksSorted($user);

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

    private function getCreatedTasksSorted(User $user)
    {
        return $this->getTasksSorted('task_creator_user_id', $user->id);
    }

    private function getAssignedTasksSorted(User $user)
    {
        return $this->getTasksSorted('assigned_user_id', $user->id);
    }

    private function getLastUpdatedTasksSorted(User $user)
    {
        return $this->getTasksSorted('id', $user->id)
            ->map(function ($tasks) {
                return $tasks->sortByDesc('updated_at');
            });
    }
    private function getTasksSorted($column, $userId)
    {
        $now = Carbon::now()->startOfDay();

        $tasks = Task::where($column, $userId)
            ->where('task_deadline_date', '>=', $now)
            ->with(['taskType', 'taskStatus', 'taskCreator', 'assignedUser', 'assignedTester'])
            ->get();

        $groupedTasks = $tasks->groupBy(function ($task) {
            return Carbon::parse($task->task_deadline_date)->format('Y-m');
        })->sortKeys();

        $formattedTasks = $groupedTasks->map(function ($tasks, $yearMonth) {
            $carbonDate = Carbon::createFromFormat('Y-m', $yearMonth);
            $formattedDate = $carbonDate->format('F Y');
            return [
                'date' => $formattedDate,
                'tasks' => $tasks->sortBy('task_deadline_date')
            ];
        });

        $currentMonthKey = Carbon::now()->format('Y-m');
        $currentMonthTasks = $formattedTasks->pull($currentMonthKey);

        if ($currentMonthTasks) {
            return collect([__('this_month') => $currentMonthTasks['tasks']])->merge($formattedTasks->pluck('tasks', 'date'));
        }

        return $formattedTasks->pluck('tasks', 'date');
    }
}
