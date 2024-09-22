<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\TaskStatus;
use App\Models\TaskType;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function userDashboard(User $user, Request $request)
    {
        $createdTasks = $this->getCreatedTasksSorted($user, $request);
        $assignedTasks = $this->getAssignedTasksSorted($user, $request);
        $lastUpdatedTasks = $this->getLastUpdatedTasksSorted($user, $request);

        $dashboardData = new Collection([
            'user' => $user,
            'createdTasks' => $createdTasks,
            'assignedTasks' => $assignedTasks,
            'lastUpdatedTasks' => $lastUpdatedTasks,
            'createdTasksCount' => $createdTasks->sum(fn($tasks) => $tasks->count()),
            'assignedTasksCount' => $assignedTasks->sum(fn($tasks) => $tasks->count()),
        ]);

        // Add filter data
        $dashboardData['taskStatuses'] = TaskStatus::all();
        $dashboardData['taskTypes'] = TaskType::all();
        $dashboardData['taskCreators'] = User::has('tasksCreated')->get();
        $dashboardData['assignedUsers'] = User::has('tasksAssigned')->get();
        $dashboardData['assignedTesters'] = User::has('tasksAssignedAsTester')->get();

        return view('dashboard.index', compact('dashboardData'));
    }

    private function getCreatedTasksSorted(User $user, Request $request)
    {
        return $this->getTasksSorted('task_creator_user_id', $user->id, $request);
    }

    private function getAssignedTasksSorted(User $user, Request $request)
    {
        return $this->getTasksSorted('assigned_user_id', $user->id, $request);
    }

    private function getLastUpdatedTasksSorted(User $user, Request $request)
    {
        return $this->getTasksSorted('id', $user->id, $request)
            ->map(function ($tasks) {
                return $tasks->sortByDesc('updated_at');
            });
    }

    private function getTasksSorted($column, $userId, Request $request)
    {
        $now = Carbon::now()->startOfDay();

        $query = Task::where($column, $userId)
            ->where('task_deadline_date', '>=', $now)
            ->with(['taskType', 'taskStatus', 'taskCreator', 'assignedUser', 'assignedTester']);

        // Apply filters
        if ($request->filled('task_status_id')) {
            $query->where('task_status_id', $request->task_status_id);
        }
        if ($request->filled('task_type_id')) {
            $query->where('task_type_id', $request->task_type_id);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('task_creator_user_id')) {
            $query->where('task_creator_user_id', $request->task_creator_user_id);
        }
        if ($request->filled('assigned_user_id')) {
            $query->where('assigned_user_id', $request->assigned_user_id);
        }
        if ($request->filled('assigned_tester_user_id')) {
            $query->where('assigned_tester_user_id', $request->assigned_tester_user_id);
        }

        $tasks = $query->get();

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
