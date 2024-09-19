@extends('layouts.app')

@section('content')
    <div class="container bg-white border p-3">
        <h1>{{ __('tasks.create_task_status') }}</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('task-statuses.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('tasks.title') }}:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <button type="submit" class="btn btn-primary">{{ __('tasks.create_task_status_button') }}</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelector('form').addEventListener('submit', async function(event) {
            event.preventDefault();
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: document.getElementById('name').value
                    })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const result = await response.json();
                if (result.success) {
                    alert('{{ __('tasks.task_status_created') }}');
                    window.location.href = '{{ route('task-statuses.index') }}';
                } else {
                    throw new Error(result.message || 'Unknown error');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('{{ __('tasks.error_creating_task_status') }}');
            } finally {
                Log::info('User {{ auth()->user()->id }} attempted to create a task status');
            }
        });
    </script>
@endpush
