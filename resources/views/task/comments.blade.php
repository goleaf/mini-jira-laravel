<div class="row">
    <div class="col-md-12">
        @if ($task->comments->isNotEmpty())
            <div class="comments-list">
                @foreach ($comments as $comment)
                    <div class="comment pt-3 @if($comment->parent_id != null) ps-5 @endif">
                        <div class="comment-header border-bottom pb-2">
                            <strong>{{ $comment->user->name }}</strong> <small>{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="comment-body">{{ $comment->body }}</div>
                        <form method="post" action="{{ route('comments.store', ['task' => $task->id]) }}">
                            @csrf
                            <div class="form-group d-flex align-items-center">
                                <input type="text" name="body" class="form-control flex-grow-1 me-2" />
                                <input type="hidden" name="task_id" value="{{ $task->id }}" />
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}" />

                                <input type="submit" class="btn btn-warning" value="Reply" />
                            </div>
                        </form>
                        @include('task.comments', ['comments' => $comment->replies])

                    </div>
                @endforeach
            </div>
        @else
            <p>No comments yet.</p>
        @endif
    </div>
</div>
