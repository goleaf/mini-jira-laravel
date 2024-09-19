<!-- resources/views/task_types/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container bg-white border p-3">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('tasks.create_task_type') }}</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('task-types.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="title">{{ __('tasks.title') }}:</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('tasks.create_task_type') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');
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
                    alert('{{ __('tasks.type_created') }}');
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
