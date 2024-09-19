<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LogService;
use Illuminate\Http\Request;
use App\Models\TaskStatus;
use Illuminate\Support\Facades\Auth;

class TaskStatusController extends Controller
{
    protected $user;

    public function index()
    {
        $taskStatuses = TaskStatus::all();

        return view('task_statuses.index', compact('taskStatuses'));
    }

    public function create()
    {
        return view('task_statuses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $task_status = TaskStatus::create([
            'name' => $request->name,
        ]);

        LogService::logAction('created', $task_status->id, 'task_status');

        return redirect()->route('task-statuses.index')->with('success', __('task_status_controller.status_created'));
    }

    public function show(TaskStatus $taskStatus)
    {
        return view('task_statuses.show', compact('taskStatus'));
    }

    public function edit(TaskStatus $taskStatus)
    {
        return view('task_statuses.edit', compact('taskStatus'));
    }

    public function update(Request $request, TaskStatus $taskStatus)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $taskStatus->update([
            'name' => $request->name,
        ]);

        LogService::logAction('updated', $taskStatus->id, 'task_status');

        return redirect()->route('task-statuses.index')->with('success', __('task_status_controller.status_updated'));
    }

    public function destroy(TaskStatus $taskStatus)
    {
        $taskStatus->delete();

        LogService::logAction('deleted', $taskStatus->id, 'task_status');

        return redirect()->route('task-statuses.index')->with('success', __('task_status_controller.status_deleted'));
    }
}
