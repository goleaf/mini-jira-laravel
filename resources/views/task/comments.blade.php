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
                        <form method="post" action="{{ route('comments.store', ['task' => $task->id]) }}" id="comment-form-{{ $comment->id }}">
                            @csrf
                            <div class="form-group d-flex align-items-center">
                                <input type="text" name="body" class="form-control flex-grow-1 me-2" required minlength="2" maxlength="1000" />
                                <input type="hidden" name="task_id" value="{{ $task->id }}" />
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}" />
                                <button type="submit" class="btn btn-warning">{{ __('comments.reply') }}</button>
                            </div>
                        </form>
                        @include('task.comments', ['comments' => $comment->replies])
                    </div>
                @endforeach
            </div>
        @else
            <p>{{ __('comments.no_comments') }}</p>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('form[id^="comment-form-"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('{{ __('comments.error_submitting') }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __('comments.error_submitting') }}');
        });
    });
});
</script>
@endpush
