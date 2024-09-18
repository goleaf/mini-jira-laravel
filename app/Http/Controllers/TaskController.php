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
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

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

        return redirect()->route('tasks.index')->with('success', 'A new task has been created');
    }


    public function show(Task $task)
    {

        $differenceInDays = $this->calculateDateDifference($task);

        return view('task.show', compact('task', 'differenceInDays'));
    }


    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $users = User::orderBy('name')->get();;
        $taskStatuses = TaskStatus::orderBy('name')->get();
        $taskTypes = TaskType::orderBy('name')->get();

        return view('task.edit', compact('task', 'users', 'taskTypes', 'taskStatuses'));
    }


    public function update(Request $request)
    {
        $attributes = $this->validateTask($request);
        $task = Task::find($request->task);
        $task->update($attributes);

        LogService::logAction('updated', $task->id, 'task' );

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
    }


    public function destroy(Task $task)
    {
        $task->delete();

        LogService::logAction('deleted', $task->id, 'task');

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
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
            'title.required' => 'The title field is required.',
            'task_deadline_date.required' => 'The task deadline field is required.',
            'description.required' => 'The description field is required.',
            'task_creator_user_id.required' => 'The task creator user ID field is required.',
            'task_creator_user_id.exists' => 'The selected task creator user ID is invalid.',
            'assigned_user_id.required' => 'The assigned user ID field is required.',
            'assigned_user_id.exists' => 'The selected assigned user ID is invalid.',
            'assigned_tester_user_id.required' => 'The assigned tester user ID field is required.',
            'assigned_tester_user_id.exists' => 'The selected assigned tester user ID is invalid.',
            'task_type_id.required' => 'The task type ID field is required.',
            'task_type_id.exists' => 'The selected task type ID is invalid.',
            'task_status_id.required' => 'The task status ID field is required.',
            'task_status_id.exists' => 'The selected task status ID is invalid.',
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

        return back()->with('success', 'Pagination count updated successfully');
    }

    public function calculateDateDifference(Task $task)
    {
        $createdAt = strtotime($task->created_at);
        $deadlineDate = strtotime($task->task_deadline_date);

        $difference = $deadlineDate - $createdAt;

        $differenceInDays = floor($difference / (60 * 60 * 24));

        if ($differenceInDays > 0) {
            return "Task has $differenceInDays days";
        } else {
            return "Task has expired";
        }
    }

}
