<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskApiController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->has('api_token')) {
                $apiToken = $request->input('api_token');
                $this->user = User::where('api_token', $apiToken)->first();
                if (!$this->user) {
                    return response()->json(['error' => 'Unauthorized: Invalid API token'], 401);
                }
                Auth::setUser($this->user);
            } else {
                return response()->json(['error' => 'Unauthorized: Missing API token'], 401);
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $tasks = Task::with(['comments' => function ($query) {
            $query->with('replies');
        }])->latest()->get();

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'task_deadline_date' => 'required',
            'description' => 'required',
            'task_creator_user_id' => 'required|exists:users,id',
            'assigned_user_id' => 'required|exists:users,id',
            'assigned_tester_user_id' => 'required|exists:users,id',
            'task_type_id' => 'required|exists:task_types,id',
            'task_status_id' => 'required|exists:task_statuses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $task = Task::create($request->all());

        return response()->json(['message' => 'Task created successfully', 'data' => $task], 201);
    }

    public function show(Task $task)
    {
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'task_deadline_date' => 'required',
            'description' => 'required',
            'task_creator_user_id' => 'required|exists:users,id',
            'assigned_user_id' => 'required|exists:users,id',
            'assigned_tester_user_id' => 'required|exists:users,id',
            'task_type_id' => 'required|exists:task_types,id',
            'task_status_id' => 'required|exists:task_statuses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $task->update($request->all());

        return response()->json(['message' => 'Task updated successfully', 'data' => $task]);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
