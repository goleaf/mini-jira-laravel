<div class="comments-container">
    @foreach($comments as $comment)
        @include('task.comment', ['comment' => $comment])
    @endforeach
</div>
