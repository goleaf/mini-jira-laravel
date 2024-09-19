@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="my-4">{{ __('tasks.create_task') }}</h2>
            </div>
        </div>

        @include('partials.flash-messages')

        <div class="row">
            <div class="col-lg-12">
                <form class="p-4 bg-white rounded shadow mt-2" action="{{ route('task.store') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('tasks.title') }}</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required maxlength="255">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('tasks.details') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" rows="6" name="description" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('tasks.deadline_date') }}</label>
                        <input type="date" class="form-control @error('task_deadline_date') is-invalid @enderror" name="task_deadline_date" min="{{ date('Y-m-d') }}" value="{{ old('task_deadline_date') }}" required>
                        @error('task_deadline_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('tasks.assign_to') }}</label>
                        <select class="form-select @error('assigned_user_id') is-invalid @enderror" name="assigned_user_id">
                            <option value="" selected disabled>{{ __('general.select') }}</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('tasks.assign_tester') }}</label>
                        <select class="form-select @error('assigned_tester_user_id') is-invalid @enderror" name="assigned_tester_user_id">
                            <option value="" selected disabled>{{ __('general.select') }}</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_tester_user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_tester_user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('tasks.task_type') }}</label>
                        <select class="form-select @error('task_type_id') is-invalid @enderror" name="task_type_id">
                            <option value="" selected disabled>{{ __('general.select') }}</option>
                            @foreach ($taskTypes as $taskType)
                                <option value="{{ $taskType->id }}" {{ old('task_type_id') == $taskType->id ? 'selected' : '' }}>
                                    {{ $taskType->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('task_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('tasks.task_status') }}</label>
                        <select class="form-select @error('task_status_id') is-invalid @enderror" name="task_status_id">
                            <option value="" selected disabled>{{ __('general.select') }}</option>
                            @foreach ($taskStatuses as $taskStatus)
                                <option value="{{ $taskStatus->id }}" {{ old('task_status_id') == $taskStatus->id ? 'selected' : '' }}>
                                    {{ $taskStatus->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('task_status_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('tasks.create_new_task') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            try {
                // Additional client-side validation can be added here
                form.submit();
            } catch (error) {
                console.error('Error submitting form:', error);
            } finally {
                // Log the action
                fetch('{{ route("log.action") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        action: 'task_create_attempt',
                        user_id: '{{ Auth::id() }}'
                    })
                });
            }
        });
    });
</script>
@endpush
