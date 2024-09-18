<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{

    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }


    public function index(Request $request)
    {
        $tasks = $this->task
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('column_name', 'like', '%' . $request->input('search') . '%');
            })
            ->when($request->filled('searchbody'), function ($query) use ($request) {
                $query->where('body_column_name', 'like', '%' . $request->input('searchbody') . '%');
            })
            ->latest()
            ->paginate(10);

        return view('task.index', compact('tasks'));
    }


    public function create()
    {
        $users = User::latest()->get();

        return view('task.create', compact('users'));
    }


    public function store(Request $request)
    {
        $attributes = $this->validateTask($request);
        $attributes['taskcreator_id'] = Auth::user()->id;
        $attributes['completed'] = 0;
        $attributes['slug'] = Str::slug($request->title);
        $task = Task::create($attributes);

        $this->notifyUser($task->assigneduser_id);

        return redirect('/')->with('success', 'Task updated and assigned user notified by email');
    }


    public function show($id)
    {
        $task = Task::findOrFail($id);

        return view('task.show', compact('task'));
    }


    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $users = User::latest()->get();

        return view('task.edit', compact('task', 'users'));
    }


    public function update(Request $request, $id)
    {
        $attributes = $this->validateTask($request);
        $task = Task::find($id);
        $attributes['taskcreator_id'] = Auth::user()->id;
        $attributes['completed'] = 0;
        $attributes['slug'] = Str::slug($request->title);
        $task->update($attributes);

        $this->notifyUser($task->assigneduser_id);

        return redirect('/task')->with('success', 'Task updated and assigned user notified by email');
    }


    public function destroy($id)
    {
        $task = Task::find($id);
        $task->delete;
        return redirect('/task')->with('success', 'Task Deleted');
    }


    public function validateTask(Request $request)
    {
        $attributes = $request->validate([
                                             'title' => 'required',
                                             'due' => 'required',
                                             'description' => 'required',
                                             'assigneduser_id' => ['required', Rule::exists('users', 'id')],
                                         ]);

        return $attributes;
    }


    public function completed($id)
    {
        $task = Task::find($id);
        $task->completed = 1;
        $task->update();
        $users = User::where('id', $task->assigneduser_id)
            ->orWhere('id', $task->taskcreator_id)
            ->get();
        Notification::send($users, new TaskCompleted($task));
        return redirect('/task')->with('success', 'Task marked completed');
    }


    public function notifyUser($assignedUserId)
    {
        $task = Task::where('assigneduser_id', $assignedUserId)->first();
        $user = User::where('id', $assignedUserId)->first();
        Notification::send($user, new TaskAssigned($task));

        return back()->with('success', 'Task notification email has been sent to the assigned user');
    }

}
