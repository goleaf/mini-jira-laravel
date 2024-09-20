<div class="row">
    <div class="col-md-12">
        @if ($task->comments->isNotEmpty())
            <div class="comments-list">
                @foreach ($comments as $comment)
                    <div class="comment pt-3 @if($comment->parent_id != null) ps-5 @endif">
                        <div class="comment-header border-bottom pb-2 mb-2">
                            <i class="fas fa-user-circle text-primary me-2"></i>
                            <strong>{{ $comment->user->name }}</strong>
                            <small class="text-muted ms-2">
                                <i class="far fa-clock me-1"></i>
                                {{ $comment->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <div class="comment-body mb-3">
                            <i class="fas fa-comment-dots text-secondary me-2"></i>
                            {{ $comment->body }}
                        </div>
                        <form method="post" action="{{ route('comments.store', ['task' => $task->id]) }}" id="comment-form-{{ $comment->id }}" class="mb-3">
                            @csrf
                            <div class="form-group d-flex align-items-center">
                                <input type="text" name="body" class="form-control flex-grow-1 me-2" required minlength="2" maxlength="1000" placeholder="{{ __('type_reply') }}" />
                                <input type="hidden" name="task_id" value="{{ $task->id }}" />
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}" />
                                <button type="submit" class="btn btn-outline-warning d-inline-flex align-items-center">
                                    <i class="fas fa-reply me-1"></i>
                                    <span>{{ __('reply') }}</span>
                                </button>
                            </div>
                        </form>
                        @include('task.comments', ['comments' => $comment->replies])
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted">
                <i class="fas fa-comments me-2"></i>
                {{ __('no_comments_yet') }}
            </p>
        @endif
    </div>
</div>