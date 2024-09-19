<?php

namespace App\Http\Controllers;

use App\Models\TaskType;
use App\Services\LogService;
use Illuminate\Http\Request;

class TaskTypeController extends Controller
{
    public function index()
    {
        $taskTypes = TaskType::orderBy('name')->get();
        return view('task_types.index', compact('taskTypes'));
    }

    public function create()
    {
        return view('task_types.create');
    }

    public function store(Request $request)
    {
        $taskType = TaskType::create($request->validate([
            'name' => 'required|string|max:255',
        ]));
        LogService::logAction(__('action_created'), $taskType->id, 'task_type');

        return redirect()->route('task-types.index')->with('success', __('task_type_created'));
    }

    public function edit(TaskType $taskType)
    {
        return view('task_types.edit', compact('taskType'));
    }

    public function update(Request $request, TaskType $taskType)
    {
        $taskType->update($request->validate([
            'name' => 'required|string|max:255',
        ]));
        LogService::logAction(__('action_updated'), $taskType->id, 'task_type');

        return redirect()->route('task-types.index')->with('success', __('task_type_updated'));
    }

    public function destroy(TaskType $taskType)
    {
        $taskType->delete();

        LogService::logAction(__('action_deleted'), $taskType->id, 'task_type');

        return redirect()->route('task-types.index')->with('success', __('task_type_deleted'));
    }
}
