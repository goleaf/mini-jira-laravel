<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LogService;
use Illuminate\Http\Request;
use App\Models\TaskStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ], [
            'name.required' => __('validation.required', ['attribute' => __('status')]),
            'name.string' => __('validation.string', ['attribute' => __('status')]),
            'name.max' => __('validation.max.string', ['attribute' => __('status'), 'max' => 255]),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $task_status = TaskStatus::create([
            'name' => $request->name,
        ]);

        LogService::logAction(__('action_created'), $task_status->id, 'task_status');

        return redirect()->route('task-statuses.index')->with('success', __('status_created_success'));
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ], [
            'name.required' => __('validation.required', ['attribute' => __('status')]),
            'name.string' => __('validation.string', ['attribute' => __('status')]),
            'name.max' => __('validation.max.string', ['attribute' => __('status'), 'max' => 255]),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $taskStatus->update([
            'name' => $request->name,
        ]);

        LogService::logAction(__('action_updated'), $taskStatus->id, 'task_status');

        return redirect()->route('task-statuses.index')->with('success', __('status_updated_success'));
    }

    public function destroy(TaskStatus $taskStatus)
    {
        $taskStatus->delete();

        LogService::logAction(__('action_deleted'), $taskStatus->id, 'task_status');

        return redirect()->route('task-statuses.index')->with('success', __('status_deleted'));
    }
}
