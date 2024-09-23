<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LogsController;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['login']);
    }

    /**
     * Authentication Methods
     */

    /**
     * Login user and create token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @example
     * curl -X POST http://your-domain.com/api/v1/login \
     *   -H "Content-Type: application/json" \
     *   -d '{"email":"user@example.com","password":"password123"}'
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;

            LogsController::log(__('user_api_login'), $user->id, 'user');

            return response()->json(['token' => $token], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @example
     * curl -X POST http://your-domain.com/api/v1/logout \
     *   -H "Authorization: Bearer {your_token}"
     */
    public function logout(Request $request)
    {
        LogsController::log(__('user_api_logout'), $request->user()->id, 'user');

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    /**
     * Task Methods
     */

    /**
     * Display a listing of the tasks with comments and replies
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @example
     * curl -X GET http://your-domain.com/api/v1/tasks \
     *   -H "Authorization: Bearer {your_token}"
     */
    public function indexTasks()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized. Please log in.'], 401);
        }

        try {
            $tasks = Task::with(['taskStatus', 'taskType', 'taskCreator', 'assignedUser', 'assignedTester', 'comments.replies', 'comments.user', 'comments.replies.user'])
                ->latest()
                ->take(10)
                ->get();

            // Check if tasks are empty
            if ($tasks->isEmpty()) {
                return response()->json(['message' => 'No tasks found'], 200);
            }

            // Transform tasks to include all required fields
            $transformedTasks = $tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'task_deadline_date' => $task->task_deadline_date,
                    'task_creator_user_id' => $task->taskCreator ? $task->taskCreator->name : null,
                    'assigned_user_id' => $task->assignedUser ? $task->assignedUser->name : null,
                    'assigned_tester_user_id' => $task->assignedTester ? $task->assignedTester->name : null,
                    'task_type_id' => $task->taskType ? $task->taskType->name : null,
                    'task_status_id' => $task->taskStatus ? $task->taskStatus->name : null,
                    'status' => $task->taskStatus ? $task->taskStatus->name : null,
                    'type' => $task->taskType ? $task->taskType->name : null,
                    'task_creator' => $task->taskCreator ? $task->taskCreator->name : null,
                    'assigned_user' => $task->assignedUser ? $task->assignedUser->name : null,
                    'assigned_tester' => $task->assignedTester ? $task->assignedTester->name : null,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at,
                    'deleted_at' => $task->deleted_at,
                    'comments' => $task->comments->map(function ($comment) {
                        return [
                            'id' => $comment->id,
                            'body' => $comment->body,
                            'user' => $comment->user->name,
                            'created_at' => $comment->created_at,
                            'replies' => $comment->replies->map(function ($reply) {
                                return [
                                    'id' => $reply->id,
                                    'body' => $reply->body,
                                    'user' => $reply->user->name,
                                    'created_at' => $reply->created_at,
                                ];
                            }),
                        ];
                    }),
                ];
            });

            return response()->json(['tasks' => $transformedTasks]);
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve tasks: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while retrieving tasks. Please try again later.',
                'details' => [
                    'status_code' => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Store a newly created task in storage
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @example
     * curl -X POST http://your-domain.com/api/v1/tasks \
     *   -H "Authorization: Bearer {your_token}" \
     *   -H "Content-Type: application/json" \
     *   -d '{"title":"New Task","description":"Task description","status_id":1,"type_id":1,"due_date":"2023-06-30"}'
     */
    public function storeTask(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status_id' => 'required|exists:task_statuses,id',
                'type_id' => 'required|exists:task_types,id',
                'due_date' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $task = Task::create(array_merge(
                $validator->validated(),
                ['user_id' => Auth::id()]
            ));

            // Eager load the status and type relationships
            $task->load(['taskStatus', 'taskType']);

            LogsController::log(__('task_created_api') . ': ' . $task->title, $task->id, 'task');

            return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
        } catch (\Exception $e) {
            \Log::error('Failed to create task: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while creating the task. Please try again later.',
                'details' => [
                    'status_code' => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Display the specified task
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @example
     * curl -X GET http://your-domain.com/api/v1/tasks/1 \
     *   -H "Authorization: Bearer {your_token}"
     */
    public function showTask($id)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized. Please log in.'], 401);
        }

        try {
            $task = Task::with(['taskStatus', 'taskType', 'taskCreator', 'assignedUser', 'assignedTester', 'comments.replies', 'comments.user', 'comments.replies.user'])
                        ->findOrFail($id);

            $taskData = [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'task_deadline_date' => $task->task_deadline_date,
                'task_creator_user_id' => $task->taskCreator ? $task->taskCreator->name : null,
                'assigned_user_id' => $task->assignedUser ? $task->assignedUser->name : null,
                'assigned_tester_user_id' => $task->assignedTester ? $task->assignedTester->name : null,
                'task_type_id' => $task->taskType ? $task->taskType->name : null,
                'task_status_id' => $task->taskStatus ? $task->taskStatus->name : null,
                'status' => $task->taskStatus ? $task->taskStatus->name : null,
                'type' => $task->taskType ? $task->taskType->name : null,
                'task_creator' => $task->taskCreator ? $task->taskCreator->name : null,
                'assigned_user' => $task->assignedUser ? $task->assignedUser->name : null,
                'assigned_tester' => $task->assignedTester ? $task->assignedTester->name : null,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
                'deleted_at' => $task->deleted_at,
                'comments' => $task->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'body' => $comment->body,
                        'user' => $comment->user ? $comment->user->name : null,
                        'created_at' => $comment->created_at,
                        'replies' => $comment->replies->map(function ($reply) {
                            return [
                                'id' => $reply->id,
                                'body' => $reply->body,
                                'user' => $reply->user ? $reply->user->name : null,
                                'created_at' => $reply->created_at,
                            ];
                        }),
                    ];
                }),
            ];

            LogsController::log(__('task_viewed_api') . ': ' . $task->title, $task->id, 'task');

            return response()->json(['task' => $taskData]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Task not found'], 404);
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve task: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while retrieving the task. Please try again later.',
                'details' => [
                    'status_code' => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Update the specified task in storage
     *
     * @param Request $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     *
     * @example
     * curl -X PUT http://your-domain.com/api/v1/tasks/1 \
     *   -H "Authorization: Bearer {your_token}" \
     *   -H "Content-Type: application/json" \
     *   -d '{"title":"Updated Task","description":"Updated description","status_id":2,"type_id":2,"due_date":"2023-07-15"}'
     */
    public function updateTask(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status_id' => 'required|exists:task_statuses,id',
            'type_id' => 'required|exists:task_types,id',
            'due_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $oldTitle = $task->title;
        $task->update($validator->validated());

        LogsController::log(__('task_updated_api') . ': ' . $oldTitle . ' -> ' . $task->title, $task->id, 'task');

        return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
    }

    /**
     * Remove the specified task from storage
     *
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     *
     * @example
     * curl -X DELETE http://your-domain.com/api/v1/tasks/1 \
     *   -H "Authorization: Bearer {your_token}"
     */
    public function destroyTask(Task $task)
    {
        $taskTitle = $task->title;
        $taskId = $task->id;

        $task->delete();

        LogsController::log(__('task_deleted_api') . ': ' . $taskTitle, $taskId, 'task');

        return response()->json(['message' => 'Task deleted successfully']);
    }

    /**
     * Comment Methods
     */

    /**
     * Display the comments for a specific task
     *
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     *
     * @example
     * curl -X GET http://your-domain.com/api/v1/tasks/1/comments \
     *   -H "Authorization: Bearer {your_token}"
     */
    public function showComments(Task $task)
    {
        $task->load(['comments', 'comments.replies', 'comments.user', 'comments.replies.user']);
        return response()->json(['comments' => $task->comments]);
    }

    /**
     * Store a newly created comment in storage
     *
     * @param Request $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     *
     * @example
     * curl -X POST http://your-domain.com/api/v1/tasks/1/comments \
     *   -H "Authorization: Bearer {your_token}" \
     *   -H "Content-Type: application/json" \
     *   -d '{"body":"This is a comment","parent_id":null}'
     */
    public function storeComment(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required|string|max:1000',
            'parent_id' => [
                'nullable',
                'exists:comments,id',
                function ($attribute, $value, $fail) use ($task) {
                    if ($value && !Comment::where('id', $value)->where('task_id', $task->id)->exists()) {
                        $fail('The selected parent comment is invalid.');
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $comment = $task->comments()->create([
            'body' => $request->body,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
        ]);

        LogsController::log(__('comment_created_api') . ': Task - ' . $task->title, $comment->id, 'comment');

        return response()->json(['message' => 'Comment created successfully', 'comment' => $comment], 201);
    }

    /**
     * Update the specified comment in storage
     *
     * @param Request $request
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     *
     * @example
     * curl -X PUT http://your-domain.com/api/v1/comments/1 \
     *   -H "Authorization: Bearer {your_token}" \
     *   -H "Content-Type: application/json" \
     *   -d '{"body":"Updated comment"}'
     */
    public function updateComment(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validator = Validator::make($request->all(), [
            'body' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $comment->update($validator->validated());

        LogsController::log(__('comment_updated_api') . ': Task - ' . $comment->task->title, $comment->id, 'comment');

        return response()->json(['message' => 'Comment updated successfully', 'comment' => $comment]);
    }

    /**
     * Remove the specified comment from storage
     *
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     *
     * @example
     * curl -X DELETE http://your-domain.com/api/v1/comments/1 \
     *   -H "Authorization: Bearer {your_token}"
     */
    public function destroyComment(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $commentId = $comment->id;
        $taskTitle = $comment->task->title;

        $comment->delete();

        LogsController::log(__('comment_deleted_api') . ': Task - ' . $taskTitle, $commentId, 'comment');

        return response()->json(['message' => 'Comment deleted successfully']);
    }

}