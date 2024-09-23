<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\LogsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Task $task)
    {
        $comments = $task->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->get();

        return view('task.show', compact('task', 'comments'));
    }

    public function store(Request $request)
    {

        $attributes = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:comments,id']
        ]);

        $comment = Comment::create([
            'body' => $attributes['body'],
            'parent_id' => $attributes['parent_id'] ?? null,
            'user_id' => Auth::id(),
            'task_id' => $request->task_id,
        ]);

        if ($comment) {
            LogsController::log(
                __('comment_created') . ': ' . Str::limit($comment->body, 30),
                $comment->id,
                'comment'
            );
            return back()->with('success', __('comment_added_success'));
        }

        return back()->with('error', __('comment_add_error'));
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $attributes = $request->validate([
            'body' => ['required', 'string', 'max:1000']
        ]);

        $comment->update($attributes);

        LogsController::log(
            __('comment_updated') . ': ' . Str::limit($comment->body, 30),
            $comment->id,
            'comment'
        );

        return back()->with('success', __('comment_updated_success'));
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $commentId = $comment->id;
        $commentPreview = Str::limit($comment->body, 30);

        if ($comment->delete()) {
            LogsController::log(
                __('comment_deleted') . ': ' . $commentPreview,
                $commentId,
                'comment'
            );
            return back()->with('success', __('comment_deleted_success'));
        }

        return back()->with('error', __('comment_delete_error'));
    }
}
