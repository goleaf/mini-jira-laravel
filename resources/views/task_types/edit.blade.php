@extends('layouts.app')

@section('content')
    <div class="container bg-white border p-3">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">{{ __('tasks.edit_task_type') }} - {{ $taskType->name }}</h2>
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
                <form action="{{ route('task-types.update', $taskType->id) }}" method="POST" id="editTaskTypeForm">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="form-label">{{ __('tasks.title') }}:</label>
                        <input type="text" class="form-control" name="name" value="{{ $taskType->name }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">{{ __('tasks.update') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('editTaskTypeForm');
        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                });
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const result = await response.json();
                if (result.success) {
                    alert('{{ __('task_type_controller.type_updated') }}');
                    window.location.href = '{{ route('task-types.index') }}';
                } else {
                    throw new Error(result.message || 'Unknown error');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('{{ __('general.error') }}');
            } finally {
                console.log('Form submission attempt finished.');
            }
        });
    });
</script>
@endpush
