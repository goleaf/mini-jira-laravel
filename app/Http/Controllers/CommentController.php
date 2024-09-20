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
        $comments = $task->comments()->whereNull('parent_id')->with('user', 'replies')->get();
        return view('task.show', compact('task', 'comments'));
    }

    public function store(Request $request, Task $task)
    {
        $attributes = $request->validate([
            'body' => 'required',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        // dd($request->all());

        $attributes['task_id'] = $request->task_id;
        $attributes['parent_id'] = $request->parent_id ?? null;
        $attributes['user_id'] = auth()->id();
        
        $comment = Comment::create($attributes);

        if ($comment) {
            LogService::logAction(__('comment_created'), $task->id, 'comment');
            return back()->with('success', __('comment_added_success'));
        } else {
            return back()->with('error', __('comment_add_error'));
        }
    }

    public function update(Request $request, Comment $comment)
    {
        $attributes = $request->validate([
            'body' => 'required'
        ]);

        $comment->update($attributes);

        LogService::logAction(__('comment_updated'), $comment->task_id, 'comment');

        return back()->with('success', __('comment_updated_success'));
    }

    public function destroy(Comment $comment)
    {
        $taskId = $comment->task_id;

        if ($comment->delete()) {
            LogService::logAction(__('comment_deleted'), $taskId, 'comment');
            return back()->with('success', __('comment_deleted_success'));
        } else {
            return back()->with('error', __('comment_delete_error'));
        }
    }
}
