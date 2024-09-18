<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentApiController extends Controller
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

    public function show(Task $task)
    {
        $task->load(['comments', 'comments.replies']);

        return response()->json($task);
    }

    public function store(Request $request, Task $task)
    {
        $apiToken = $request->api_token;
        $user = User::where('api_token', $apiToken)->first();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'body' => 'required',
            'parent_id' => [
                'nullable',
                'exists:comments,id',
                function ($attribute, $value, $fail) use ($task) {
                    if (!Comment::where('id', $value)->where('task_id', $task->id)->exists()) {
                        $fail('The selected parent comment is invalid.');
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $attributes = [
            'body' => $request->body,
            'task_id' => $task->id,
            'user_id' => $user->id,
        ];

        if ($request->filled('parent_id')) {
            $attributes['parent_id'] = $request->parent_id;
        }

        $comment = Comment::create($attributes);

        return response()->json(['message' => 'Comment was added successfully', 'comment' => $comment], 201);
    }



}

