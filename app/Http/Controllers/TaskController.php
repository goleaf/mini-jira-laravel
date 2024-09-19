<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{

    protected $task;

    public function __construct(Task $task)
    {
        $this->middleware(function ($request, $next) use ($task) {
            if (Auth::check()) {
                $this->task = $task;
                return $next($request);
            } else {
                return redirect()->route('login');
            }
        });
    }
    
    public function index(Request $request)
    {
        $tasks = Task::query();

        try {
            if ($request->filled('search')) {
                $search = $request->input('search');
                $tasks->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%$search%")
                        ->orWhere('description', 'like', "%$search%");
                });
            }

            $filters = [
                'created_at',
                'task_deadline_date',
                'task_creator_user_id',
                'assigned_user_id',
                'task_type_id',
                'task_status_id',
                'assigned_tester_user_id'
            ];

            foreach ($filters as $filter) {
                if ($request->filled($filter)) {
                    $tasks->where($filter, $request->input($filter));
                }
            }

            $taskTypes = TaskType::orderBy('name')->get();
            $taskStatuses = TaskStatus::orderBy('name')->get();
            $taskCreators = User::orderBy('name')->get();
            $assignedTesters = User::orderBy('name')->get();
            $assignedUsers = User::orderBy('name')->get();

            $paginationCount = Session::get('paginationCount', 10);

            $tasks = $tasks->withCount('comments')->latest()->paginate($paginationCount);

            return view('task.index', compact('tasks', 'taskCreators', 'assignedUsers', 'assignedTesters', 'taskTypes', 'taskStatuses'));
        } catch (\Exception $e) {
            Log::error('Error in TaskController@index: ' . $e->getMessage());
            throw $e;
        } finally {
            // Any cleanup code can go here if needed
        }
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        $taskStatuses = TaskStatus::orderBy('name')->get();
        $taskTypes = TaskType::orderBy('name')->get();

        return view('task.create', compact('users', 'taskTypes', 'taskStatuses'));
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateTask($request);

        $task = Task::create($validatedData);

        LogService::logAction('created', $task->id, 'task');

        return redirect()->route('tasks.index')->with('success', __('task_created_success'));
    }

    public function show(Task $task)
    {
        $differenceInDays = $this->calculateDateDifference($task);

        return view('task.show', compact('task', 'differenceInDays'));
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $users = User::orderBy('name')->get();
        $taskStatuses = TaskStatus::orderBy('name')->get();
        $taskTypes = TaskType::orderBy('name')->get();

        return view('task.edit', compact('task', 'users', 'taskTypes', 'taskStatuses'));
    }

    public function update(Request $request)
    {
        $attributes = $this->validateTask($request);
        $task = Task::find($request->task);
        $task->update($attributes);

        LogService::logAction('updated', $task->id, 'task');

        return redirect()->route('tasks.index')->with('success', __('task_updated_success'));
    }

    public function destroy(Task $task)
    {
        $task->delete();

        LogService::logAction('deleted', $task->id, 'task');

        return redirect()->route('tasks.index')->with('success', __('task_deleted_success'));
    }

    public function validateTask(Request $request)
    {
        $taskId = $request->task;

        $taskCreatorUserId = $taskId ? Task::findOrFail($taskId)->task_creator_user_id : Auth::id();

        $rules = [
            'title' => 'required',
            'task_deadline_date' => 'required',
            'description' => 'required',
            'task_creator_user_id' => 'required|exists:users,id',
            'assigned_user_id' => 'required|exists:users,id',
            'assigned_tester_user_id' => 'required|exists:users,id',
            'task_type_id' => 'required|exists:task_types,id',
            'task_status_id' => 'required|exists:task_statuses,id',
        ];

        $messages = [
            'title.required' => __('validation.required', ['attribute' => __('task_title')]),
            'task_deadline_date.required' => __('validation.required', ['attribute' => __('date_created')]),
            'description.required' => __('validation.required', ['attribute' => __('task_details')]),
            'task_creator_user_id.required' => __('validation.required', ['attribute' => __('task_created_by')]),
            'task_creator_user_id.exists' => __('validation.exists', ['attribute' => __('task_created_by')]),
            'assigned_user_id.required' => __('validation.required', ['attribute' => __('task_assigned_to')]),
            'assigned_user_id.exists' => __('validation.exists', ['attribute' => __('task_assigned_to')]),
            'assigned_tester_user_id.required' => __('validation.required', ['attribute' => __('task_assigned_to_tester')]),
            'assigned_tester_user_id.exists' => __('validation.exists', ['attribute' => __('task_assigned_to_tester')]),
            'task_type_id.required' => __('validation.required', ['attribute' => __('task_type')]),
            'task_type_id.exists' => __('validation.exists', ['attribute' => __('task_type')]),
            'task_status_id.required' => __('validation.required', ['attribute' => __('status')]),
            'task_status_id.exists' => __('validation.exists', ['attribute' => __('status')]),
        ];

        $inputData = array_merge($request->all(), ['task_creator_user_id' => $taskCreatorUserId]);

        $validatedData = Validator::make($inputData, $rules, $messages)->validate();

        return $validatedData;
    }

    public function updatePaginationCount(Request $request)
    {
        $allowedValues = implode(',', range(5, 50, 5));
        $request->validate(['paginationCount' => 'required|integer|in:' . $allowedValues]);

        session(['paginationCount' => $request->paginationCount]);

        return back()->with('success', __('update'));
    }

    public function calculateDateDifference(Task $task)
    {
        $createdAt = strtotime($task->created_at);
        $deadlineDate = strtotime($task->task_deadline_date);

        $difference = $deadlineDate - $createdAt;

        $differenceInDays = floor($difference / (60 * 60 * 24));

        if ($differenceInDays > 0) {
            return __("task_progress", ['days' => $differenceInDays]);
        } else {
            return __("task_expired");
        }
    }

}
