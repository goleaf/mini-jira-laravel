<?php

namespace App\Http\Controllers;

use App\Models\TaskType;
use Illuminate\Http\Request;

class TaskTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $taskTypes = TaskType::orderBy('name')->get();
        return view('task_types.index', compact('taskTypes'));
    }

    public function create()
    {
        return view('task_types.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:task_types',
        ]);

        $taskType = TaskType::create($validated);
        
        LogsController::log(__('action_created'), $taskType->id, 'task_type');

        return redirect()->route('task-types.index')->with('success', __('task_type_created'));
    }

    public function edit(TaskType $taskType)
    {
        return view('task_types.form', compact('taskType'));
    }

    public function update(Request $request, TaskType $taskType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:task_types,name,' . $taskType->id,
        ]);

        $taskType->update($validated);
        
        LogsController::log(__('action_updated'), $taskType->id, 'task_type');

        return redirect()->route('task-types.index')->with('success', __('task_type_updated'));
    }

    public function destroy(TaskType $taskType)
    {
        if ($taskType->tasks()->exists()) {
            return redirect()->route('task-types.index')
                ->with('error', __('cannot_delete_task_type_with_tasks'))
                ->withErrors(['delete' => __('task_type_has_associated_tasks')]);
        }

        $taskTypeName = $taskType->name; // Store the name before deletion
        $taskTypeId = $taskType->id;
        $taskType->delete();

        LogsController::log(__('action_deleted') . ': ' . $taskTypeName, $taskTypeId, 'task_type');

        return redirect()->route('task-types.index')->with('success', __('task_type_deleted'));
    }
}
