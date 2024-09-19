@extends('layouts.app')

@section('content')
    <div class="container bg-white border p-3">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">{{ __('tasks.edit_task_status') }}: {{ $taskStatus->name }}</h2>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card-body">
                <form action="{{ route('task-statuses.update', $taskStatus) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="form-label">{{ __('tasks.title') }}:</label>
                        <input type="text" class="form-control" name="name" value="{{ $taskStatus->name }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">{{ __('tasks.update') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelector('form').addEventListener('submit', async function(event) {
            event.preventDefault();
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!response.ok) {
                    throw new Error('{{ __('general.error') }}');
                }
                const result = await response.json();
                if (result.errors) {
                    let errorList = document.createElement('ul');
                    errorList.classList.add('alert', 'alert-danger');
                    result.errors.forEach(error => {
                        let listItem = document.createElement('li');
                        listItem.textContent = error;
                        errorList.appendChild(listItem);
                    });
                    document.querySelector('.card-body').prepend(errorList);
                } else {
                    window.location.href = '{{ route('task-statuses.index') }}';
                }
            } catch (error) {
                console.error('{{ __('general.error') }}:', error);
                Log.error('{{ __('general.error_logging_user_action') }}', { user_id: '{{ Auth::user()->id }}', error: error });
            } finally {
                Log.info('{{ __('general.action') }}: {{ __('tasks.task_status_action', ['action' => 'updated']) }}', { user_id: '{{ Auth::user()->id }}' });
            }
        });
    </script>
@endpush
