<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $userGroups = UserGroup::withCount('users')->paginate(10);
        return view('users_groups.index', compact('userGroups'));
    }

    public function create()
    {
        $users = User::all();
        return view('users_groups.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users_groups',
            'description' => 'nullable|string|max:1000',
            'users' => 'array',
            'users.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($request) {
            $userGroup = UserGroup::create($request->only(['name', 'description']));
            $userGroup->users()->attach($request->input('users', []));
        });

        return redirect()->route('users-groups.index')
            ->with('success', __('user_group_created_successfully'));
    }

    public function show(UserGroup $userGroup)
    {
        $userGroup->load('users');
        return view('users_groups.show', compact('userGroup'));
    }

    public function edit(UserGroup $userGroup)
    {
        $users = User::all();
        $userGroup->load('users');
        return view('users_groups.edit', compact('userGroup', 'users'));
    }

    public function update(Request $request, UserGroup $userGroup)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users_groups')->ignore($userGroup->id),
            ],
            'description' => 'nullable|string|max:1000',
            'users' => 'array',
            'users.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($request, $userGroup) {
            $userGroup->update($request->only(['name', 'description']));
            $userGroup->users()->sync($request->input('users', []));
        });

        return redirect()->route('users-groups.index')
            ->with('success', __('user_group_updated_successfully'));
    }

    public function destroy(UserGroup $userGroup)
    {
        if ($userGroup->users()->count() > 0) {
            return redirect()->route('users-groups.index')
                ->with('error', __('cannot_delete_user_group_with_users'));
        }

        $userGroup->delete();

        return redirect()->route('users-groups.index')
            ->with('success', __('user_group_deleted_successfully'));
    }
}