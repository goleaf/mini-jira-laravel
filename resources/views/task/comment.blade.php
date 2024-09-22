<div class="comment mb-3 @if($comment->parent_id) ms-4 @endif" id="comment-{{ $comment->id }}">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fas fa-user-circle fa-2x me-2"></i>
                <div>
                    <strong>{{ $comment->user->name }}</strong>
                    <small class="text-muted d-block">{{ $comment->created_at->diffForHumans() }}</small>
                </div>
            </div>
            @if($comment->canEdit(auth()->user()) || $comment->canDelete(auth()->user()))
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary text-muted p-0" type="button" id="dropdownMenuButton-{{ $comment->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton-{{ $comment->id }}">
                        @if($comment->canEdit(auth()->user()))
                            <li><a class="dropdown-item" href="#" onclick="toggleEditForm({{ $comment->id }}); return false;">{{ __('edit') }}</a></li>
                        @endif
                        @if($comment->canDelete(auth()->user()))
                            <li>
                                <form action="{{ route('comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('{{ __('confirm_delete') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">{{ __('delete') }}</button>
                                </form>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>
        <div class="card-body bg-white">
            <div class="d-flex justify-content-between align-items-start">
                <p class="card-text mb-2" id="comment-body-{{ $comment->id }}">
                    <i class="fas fa-comment me-2"></i>{{ $comment->body }}
                </p>
                <button class="btn btn-sm btn-outline-success" id="reply-button-{{ $comment->id }}" onclick="toggleReplyForm({{ $comment->id }})">{{ __('reply') }}</button>
            </div>
            <div id="edit-form-{{ $comment->id }}" style="display: none;">
                <form action="{{ route('comments.update', $comment) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <textarea class="form-control auto-height" name="body" required>{{ $comment->body }}</textarea>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('update') }}</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleEditForm({{ $comment->id }})">{{ __('cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="reply-form-{{ $comment->id }}" style="display: none;" class="mt-2">
        <form action="{{ route('comments.store', ['task' => $task->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="task_id" value="{{ $task->id }}">
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            <div class="input-group">
                <textarea class="form-control auto-height" name="body" required placeholder="{{ __('type_reply') }}"></textarea>
                <button type="submit" class="btn btn-outline-primary">{{ __('reply') }}</button>
            </div>
        </form>
    </div>
    @if($comment->replies->isNotEmpty())
        <div class="replies mt-3 ms-4">
            @foreach($comment->replies as $reply)
                @include('task.comment', ['comment' => $reply])
            @endforeach
        </div>
    @endif
</div>
