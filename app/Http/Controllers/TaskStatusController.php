<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TaskStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\LogsController;

class TaskStatusController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $taskStatuses = TaskStatus::all();

        return view('task_statuses.index', compact('taskStatuses'));
    }

    public function create()
    {
        return view('task_statuses.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:task_statuses',
        ]);

        $taskStatus = TaskStatus::create($validated);

        LogsController::log(__('task_status_created') . ': ' . $taskStatus->name, $taskStatus->id, 'task_status');

        return redirect()->route('task-statuses.index')->with('success', __('status_created_success'));
    }

    public function show(TaskStatus $taskStatus)
    {
        return view('task_statuses.show', compact('taskStatus'));
    }

    public function edit(TaskStatus $taskStatus)
    {
        return view('task_statuses.form', compact('taskStatus'));
    }

    public function update(Request $request, TaskStatus $taskStatus)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:task_statuses,name,' . $taskStatus->id,
        ]);

        $oldName = $taskStatus->name;
        $taskStatus->update($validated);

        LogsController::log(__('task_status_updated') . ': ' . $oldName . ' -> ' . $taskStatus->name, $taskStatus->id, 'task_status');

        return redirect()->route('task-statuses.index')->with('success', __('status_updated_success'));
    }

    public function destroy(TaskStatus $taskStatus)
    {
        if ($taskStatus->tasks()->exists()) {
            return redirect()->route('task-statuses.index')
                ->with('error', __('cannot_delete_task_status_with_tasks'))
                ->withErrors(['delete' => __('task_status_has_associated_tasks')]);
        }

        $taskStatusName = $taskStatus->name;
        $taskStatusId = $taskStatus->id;
        $taskStatus->delete();

        LogsController::log(__('task_status_deleted') . ': ' . $taskStatusName, $taskStatusId, 'task_status');

        return redirect()->route('task-statuses.index')->with('success', __('task_status_deleted'));
    }
}
