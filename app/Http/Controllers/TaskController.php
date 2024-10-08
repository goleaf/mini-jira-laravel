<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    protected $taskCreators;
    protected $assignedUsers;
    protected $assignedTesters;
    protected $taskStatuses;
    protected $taskTypes;
    protected $users;

    public function __construct(User $user, TaskStatus $taskStatus, TaskType $taskType)
    {
        $this->middleware('auth');
        $this->users = $user->all();
        $this->taskStatuses = $taskStatus->all();
        $this->taskTypes = $taskType->all();
        $this->taskCreators = $user->has('tasksCreated')->get();
        $this->assignedUsers = $user->has('tasksAssigned')->get();
        $this->assignedTesters = $user->has('tasksAssignedAsTester')->get();
    }

    public function index(Request $request)
    {
        $tasks = $this->buildTaskQuery($request)->paginate($this->getPaginationCount($request));
        $this->calculateTaskDifference($tasks);

        return view('task.index', array_merge(
            $this->getViewData($tasks),
            ['currentPaginationCount' => $tasks->perPage()]
        ));
    }

    private function buildTaskQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = Task::query()
            ->with(['taskCreator', 'assignedUser', 'assignedTester', 'taskType', 'taskStatus'])
            ->withCount('comments');

        $this->applyFilters($query, $request);

        return $query;
    }

    private function applyFilters(\Illuminate\Database\Eloquent\Builder $query, Request $request): void
    {
        $filters = [
            'created_at' => 'whereDate',
            'task_deadline_date' => 'whereDate',
            'search' => ['where', 'title', 'like', '%{value}%'],
            'task_creator_user_id' => 'where',
            'assigned_user_id' => 'where',
            'assigned_tester_user_id' => 'where',
            'task_status_id' => 'where',
            'task_type_id' => 'where',
            'created_at_from' => ['whereBetween', 'created_at', [$request->created_at_from, $request->created_at_to]],
            'task_deadline_date_from' => ['whereBetween', 'task_deadline_date', [$request->task_deadline_date_from, $request->task_deadline_date_to]],
        ];

        foreach ($filters as $param => $method) {
            if ($request->filled($param)) {
                if (is_array($method)) {
                    $query->{$method[0]}($method[1], $method[2]);
                } else {
                    $this->applyFilter($query, $method, $param, $request->$param);
                }
            }
        }
    }

    public function create()
    {
        $viewData = $this->getCreateEditViewData();
        return view('task.create', $viewData);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate($this->getValidationRules(), $this->getValidationMessages());

        $validatedData['task_creator_user_id'] = Auth::id();

        $task = Task::create($validatedData);
        LogsController::log(__('action_created'), $task->id, 'task');
        return $this->redirectToIndex(__('task_created_success'));
    }
    
    public function show(Task $task)
    {
        $comments = $task->comments()->whereNull('parent_id')->with('user', 'replies')->get();
        $differenceInDays = $this->calculateDaysDifference($task->task_deadline_date);
        
        return view('task.show', compact('task', 'comments', 'differenceInDays'));
    }

    public function edit(Task $task)
    {
        $task->load(['taskCreator', 'assignedUser', 'assignedTester', 'taskType', 'taskStatus']);
        $viewData = $this->getCreateEditViewData($task);

        return view('task.edit', $viewData);
    }

    public function update(Request $request, Task $task)
    {
        $validatedData = $request->validate($this->getValidationRules(), $this->getValidationMessages());

        $task->update($validatedData);
        LogsController::log(__('action_updated'), $task->id, 'task');
        return $this->redirectToIndex(__('task_updated_success'));
    }

    public function destroy(Task $task)
    {
        $taskTitle = $task->title;
        $task->delete();
        LogsController::log(__('action_deleted') . ': ' . $taskTitle, $task->id, 'task');
        return $this->redirectToIndex(__('task_deleted_success'));
    }

    public function updatePaginationCount(Request $request)
    {
        $request->validate([
            'paginationCount' => 'required|integer|min:5|max:100',
        ]);

        $request->session()->put('paginationCount', $request->paginationCount);
        return redirect()->back()->with('success', __('session_message'));
    }

    private function applyFilter(\Illuminate\Database\Eloquent\Builder &$query, string|array $method, string $param, mixed $value): void
    {
        if (is_array($method)) {
            $query->{$method[0]}($method[1], $method[2], str_replace('{value}', $value, $method[3]));
        } else {
            $query->$method($param, $value);
        }
    }

    private function getPaginationCount(Request $request): int
    {
        return $request->session()->get('paginationCount', 15);
    }

    private function getViewData($tasks): array
    {
        return [
            'tasks' => $tasks,
            'taskCreators' => $this->taskCreators,
            'assignedUsers' => $this->assignedUsers,
            'assignedTesters' => $this->assignedTesters,
            'taskStatuses' => $this->taskStatuses,
            'taskTypes' => $this->taskTypes
        ];
    }

    private function getCreateEditViewData(?Task $task = null): array
    {
        return [
            'task' => $task,
            'users' => $this->users,
            'taskStatuses' => $this->taskStatuses,
            'taskTypes' => $this->taskTypes
        ];
    }

    private function calculateTaskDifference($tasks): void
    {
        foreach ($tasks as $task) {
            $task->differenceInDays = $this->calculateDaysDifference($task->task_deadline_date);
        }
    }

    private function calculateDaysDifference(string $date): int
    {
        $deadlineDate = Carbon::parse($date);
        $currentDate = Carbon::now();
        return $currentDate->diffInDays($deadlineDate, false);
    }

    private function redirectToIndex(string $message)
    {
        return redirect()->route('tasks.index')->with('success', $message);
    }

    private function getValidationRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'task_deadline_date' => ['required', 'date', 'after:today'],
            'assigned_user_id' => ['required', Rule::exists('users', 'id')],
            'assigned_tester_user_id' => ['required', Rule::exists('users', 'id')],
            'task_type_id' => ['required', Rule::exists('task_types', 'id')],
            'task_status_id' => ['required', Rule::exists('task_statuses', 'id')],
        ];
    }

    private function getValidationMessages(): array
    {
        return [
            'title.required' => __('task_title') . ' ' . __('all_fields_required'),
            'title.max' => __('task_title') . ' ' . __('validation_errors'),
            'description.required' => __('task_details') . ' ' . __('all_fields_required'),
            'description.max' => __('task_details') . ' ' . __('validation_errors'),
            'task_deadline_date.required' => __('task_deadline') . ' ' . __('all_fields_required'),
            'task_deadline_date.after' => __('task_deadline') . ' ' . __('validation_errors'),
            'assigned_user_id.required' => __('task_assigned_to') . ' ' . __('all_fields_required'),
            'assigned_user_id.exists' => __('task_assigned_to') . ' ' . __('validation_errors'),
            'assigned_tester_user_id.required' => __('task_assigned_to_qa') . ' ' . __('all_fields_required'),
            'assigned_tester_user_id.exists' => __('task_assigned_to_qa') . ' ' . __('validation_errors'),
            'task_type_id.required' => __('task_type') . ' ' . __('all_fields_required'),
            'task_type_id.exists' => __('task_type') . ' ' . __('validation_errors'),
            'task_status_id.required' => __('status') . ' ' . __('all_fields_required'),
            'task_status_id.exists' => __('status') . ' ' . __('validation_errors'),
        ];
    }
}
