<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use App\Services\LogService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function show(Task $task)
    {
        $task->load('comments.user');

        return view('task.show', compact('task'));
    }

    public function store(Request $request, Task $task)
    {
        $attributes = $request->validate([
            'body' => 'required'
        ]);

        $attributes['task_id'] = $task->id;
        $attributes['parent_id'] = $request->parent_id ?? null;
        $attributes['user_id'] = auth()->id();

        Comment::create($attributes);

        LogService::logAction(__('action_created'), $task->id, 'comment');

        return back()->with('success', __('comment_added_success'));
    }
}
