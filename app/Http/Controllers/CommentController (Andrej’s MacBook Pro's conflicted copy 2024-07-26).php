<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $attributes = $request->validate([
                                             'body' => 'required'
                                         ]);
        $attributes['task_id'] = $task->id;
        $attributes['user_id'] = 1;
        Comment::create($attributes);
        return back();
    }
}
