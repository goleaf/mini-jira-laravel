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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TaskController extends Controller
{
    public $taskCreators;
    public $assignedUsers;
    public $assignedTesters;
    public $taskStatuses;
    public $taskTypes;

    public $users;
    public function __construct(Task $task, User $user, TaskStatus $taskStatus, TaskType $taskType)
    {
        $this->middleware('auth');
        $this->taskCreators = $user->withCount('tasks')->orderBy('name', 'asc')->get();
        $this->assignedUsers = $user->withCount('tasksAssigned')->orderBy('name', 'asc')->get();
        $this->assignedTesters = $user->withCount('tasksAssigned')->orderBy('name', 'asc')->get();
        $this->taskStatuses = $taskStatus->withCount('tasks')->orderBy('name', 'asc')->get();
        $this->taskTypes = $taskType->withCount('tasks')->orderBy('name', 'asc')->get();
        $this->users = $user->withCount('tasksAssigned')->orderBy('name', 'asc')->get();
    }

    public function index(Request $request)
    {
        $query = $this->buildTaskQuery($request);
        $tasks = $query->paginate($this->getPaginationCount($request));

        $this->calculateTaskDifference($tasks);

        return view('task.index', $this->getViewData($tasks));
    }

    private function buildTaskQuery(Request $request)
    {
        $query = Task::with(['taskCreator', 'assignedUser', 'assignedTester', 'taskType', 'taskStatus'])
                     ->withCount('comments');

        $filters = $this->getFilters();

        foreach ($filters as $param => $method) {
            if ($request->filled($param)) {
                $this->applyFilter($query, $method, $param, $request->$param);
            }
        }

        if ($request->filled('created_at_from') && $request->filled('created_at_to')) {
            $query->whereBetween('created_at', [$request->created_at_from, $request->created_at_to]);
        }

        if ($request->filled('task_deadline_date_from') && $request->filled('task_deadline_date_to')) {
            $query->whereBetween('task_deadline_date', [$request->task_deadline_date_from, $request->task_deadline_date_to]);
        }

        return $query;
    }

    private function calculateTaskDifference($tasks)
    {
        foreach ($tasks as $task) {
            $task->differenceInDays = $this->calculateDaysDifference($task->task_deadline_date);
        }
    }

    public function create()
    {
        return view('task.create', $this->getCreateEditViewData());
    }

    public function store(Request $request)
    {
        $validator = $this->validateTask($request);

        if ($validator->fails()) {
            return $this->redirectBackWithErrors($validator);
        }

        $validatedData = $this->getValidatedDataWithCreator($validator);

        $task = Task::create($validatedData);
        $this->logAction(__('action_created'), $task->id);
        return $this->redirectToIndex(__('task_created_success'));
    }

    public function show(Task $task)
    {
        $task->load(['taskCreator', 'assignedUser', 'assignedTester', 'taskType', 'taskStatus', 'comments']);
        $differenceInDays = $this->calculateDaysDifference($task->task_deadline_date);
        return view('task.show', compact('task', 'differenceInDays'));
    }

    public function edit(Task $task)
    {
        $task->load(['taskCreator', 'assignedUser', 'assignedTester', 'taskType', 'taskStatus']);
        return view('task.edit', $this->getCreateEditViewData($task));
    }

    public function update(Request $request, Task $task)
    {
        $validator = $this->validateTask($request);

        if ($validator->fails()) {
            return $this->redirectBackWithErrors($validator);
        }

        $validatedData = $validator->validated();

        $task->update($validatedData);
        $this->logAction(__('action_updated'), $task->id);
        return $this->redirectToIndex(__('task_updated_success'));
    }

    public function destroy(Task $task)
    {
        $task->delete();
        $this->logAction(__('action_deleted'), $task->id);
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

    private function validateTask(Request $request)
    {
        return Validator::make($request->all(), $this->getValidationRules(), $this->getValidationMessages());
    }

    private function getFilters()
    {
        return [
            'created_at' => 'whereDate',
            'task_deadline_date' => 'whereDate',
            'search' => ['where', 'title', 'like', '%{value}%'],
            'task_creator_user_id' => 'where',
            'assigned_user_id' => 'where',
            'assigned_tester_user_id' => 'where',
            'task_status_id' => 'where',
            'task_type_id' => 'where',
        ];
    }

    private function applyFilter(&$query, $method, $param, $value)
    {
        if (is_array($method)) {
            $query->{$method[0]}($method[1], $method[2], str_replace('{value}', $value, $method[3]));
        } else {
            $query->$method($param, $value);
        }
    }

    private function getPaginationCount(Request $request)
    {
        return $request->session()->get('paginationCount', 15);
    }

    private function getViewData($tasks)
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

    private function getCreateEditViewData($task = null)
    {
        return [
            'task' => $task,
            'users' => $this->users,
            'taskStatuses' => $this->taskStatuses,
            'taskTypes' => $this->taskTypes
        ];
    }

    private function calculateDaysDifference($date)
    {
        $deadlineDate = Carbon::parse($date);
        $currentDate = Carbon::now();
        return $currentDate->diffInDays($deadlineDate, false);
    }

    private function redirectBackWithErrors($validator)
    {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    private function getValidatedDataWithCreator($validator)
    {
        $validatedData = $validator->validated();
        $validatedData['task_creator_user_id'] = Auth::id();
        return $validatedData;
    }

    private function logAction($action, $taskId)
    {
        LogService::logAction($action, $taskId, 'task');
    }

    private function redirectToIndex($message)
    {
        return redirect()->route('tasks.index')->with('success', $message);
    }

    private function getValidationRules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'task_deadline_date' => 'required|date|after:today',
            'assigned_user_id' => 'required|exists:users,id',
            'assigned_tester_user_id' => 'required|exists:users,id',
            'task_type_id' => 'required|exists:task_types,id',
            'task_status_id' => 'required|exists:task_statuses,id',
        ];
    }

    private function getValidationMessages()
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
