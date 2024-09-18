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
        LogService::logAction('updated', $taskType->id, 'task_type');

        return redirect()->route('task-types.index')->with('success', 'Task type created successfully!');
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
        LogService::logAction('updated', $taskType->id, 'task_type');

        return redirect()->route('task-types.index')->with('success', 'Task type updated successfully!');
    }

    public function destroy(TaskType $taskType)
    {

        $taskType->delete();

        LogService::logAction('updated', $taskType->id, 'task_type');

        return redirect()->route('task-types.index')->with('success', 'Task type deleted successfully!');
    }
}
