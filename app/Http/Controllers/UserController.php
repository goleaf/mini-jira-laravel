<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\LogsController;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::with('userGroups')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $userGroups = UserGroup::all();
        return view('users.create', compact('userGroups'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'user_groups' => ['required', 'array'],
            'user_groups.*' => ['exists:users_groups,id'],
            'work_position' => ['required', 'string', 'max:255'],
            'is_admin' => ['boolean'],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'work_position' => $validatedData['work_position'],
            'is_admin' => $validatedData['is_admin'] ?? false,
            'password' => Hash::make($validatedData['password']),
        ]);

        $user->userGroups()->attach($validatedData['user_groups']);

        LogsController::log(__('user_created') . ': ' . $user->name, $user->id, 'user');
        LogsController::log(__('user_groups_assigned') . ': ' . $user->name, $user->id, 'user');

        return redirect()->route('users.index')->with('success', __('user_created_successfully'));
    }

    public function edit(User $user)
    {
        $userGroups = UserGroup::all();
        return view('users.edit', compact('user', 'userGroups'));
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'user_groups' => ['required', 'array'],
            'user_groups.*' => ['exists:users_groups,id'],
            'work_position' => ['required', 'string', 'max:255'],
            'is_admin' => ['boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'work_position' => $validatedData['work_position'],
            'is_admin' => $validatedData['is_admin'] ?? false,
        ]);

        $oldGroups = $user->userGroups->pluck('id')->toArray();
        $user->userGroups()->sync($validatedData['user_groups'] ?? []);
        $newGroups = $validatedData['user_groups'] ?? [];

        LogsController::log(__('user_updated') . ': ' . $user->name, $user->id, 'user');

        if ($oldGroups != $newGroups) {
            LogsController::log(__('user_groups_updated') . ': ' . $user->name, $user->id, 'user');
        }

        return redirect()->route('users.index')->with('success', __('user_updated_successfully'));
    }

    public function destroy(User $user)
    {
        $tasksAssigned = Task::where(function ($query) use ($user) {
            $query->where('task_creator_user_id', $user->id)
                  ->orWhere('assigned_user_id', $user->id)
                  ->orWhere('assigned_tester_user_id', $user->id);
        })->exists();

        if ($tasksAssigned) {
            return redirect()->route('users.index')->with('error', __('cannot_delete_user_with_tasks'));
        }

        $userName = $user->name;
        $userId = $user->id;
        $user->delete();
        LogsController::log(__('user_deleted') . ': ' . $userName, $userId, 'user');
        return redirect()->route('users.index')->with('success', __('user_deleted_successfully'));
    }
}
