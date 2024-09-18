<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskStatusApiController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Check if the request contains the api_token
            if ($request->has('api_token')) {
                // Validate the API token
                $apiToken = $request->input('api_token');
                $this->user = User::where('api_token', $apiToken)->first();
                if (!$this->user) {
                    return response()->json(['error' => 'Unauthorized: Invalid API token'], 401);
                }
                // Set the authenticated user
                Auth::setUser($this->user);
            } else {
                return response()->json(['error' => 'Unauthorized: Missing API token'], 401);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $taskStatuses = TaskStatus::all();

        return response()->json(['task_statuses' => $taskStatuses]);
    }

    public function store(Request $request)
    {
        $request->validate([
                               'name' => 'required|string|max:255',
                           ]);

        $taskStatus = TaskStatus::create([
                                             'name' => $request->name,
                                         ]);

        return response()->json(['message' => 'Task status created successfully.', 'task_status' => $taskStatus], 201);
    }

    public function show(TaskStatus $taskStatus)
    {
        return response()->json(['task_status' => $taskStatus]);
    }

    public function update(Request $request, TaskStatus $taskStatus)
    {
        $request->validate([
                               'name' => 'required|string|max:255',
                           ]);

        $taskStatus->update([
                                'name' => $request->name,
                            ]);

        return response()->json(['message' => 'Task status updated successfully.', 'task_status' => $taskStatus]);
    }

    public function destroy(TaskStatus $taskStatus)
    {
        $taskStatus->delete();

        return response()->json(['message' => 'Task status deleted successfully.']);
    }

}
