<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskTypeApiController extends Controller
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
        $taskTypes = TaskType::orderBy('name')->get();
        return response()->json(['taskTypes' => $taskTypes]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
                                                'name' => 'required|string|max:255',
                                            ]);

        $taskType = TaskType::create($validatedData);
        return response()->json(['message' => 'Task type created successfully!', 'taskType' => $taskType], 201);
    }

    public function update(Request $request, TaskType $taskType)
    {
        $validatedData = $request->validate([
                                                'name' => 'required|string|max:255',
                                            ]);

        $taskType->update($validatedData);
        return response()->json(['message' => 'Task type updated successfully!', 'taskType' => $taskType]);
    }

    public function destroy(TaskType $taskType)
    {
        $taskType->delete();
        return response()->json(['message' => 'Task type deleted successfully!']);
    }
}
