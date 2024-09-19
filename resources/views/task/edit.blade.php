@extends('layouts.app')

@section('content')
    <div class="container bg-white rounded shadow ps-4 pe-4 pb-4">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="my-4">{{ __('tasks.updating_task', ['title' => $task->title]) }}</h2>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="mb-3 col-6">
                <label class="form-label">{{ __('tasks.created_by') }}:</label>
                <p>{{ $task->getTaskCreatorUser() }}</p>
            </div>
            <div class="mb-3 col-6">
                <label class="form-label">{{ __('tasks.assigned_to') }}:</label>
                <p>{{ $task->getAssignedUser() }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form class="mt-2" method="POST" action="{{ route('tasks.update', ['task' => $task->id]) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="title" class="form-label">{{ __('tasks.task_title') }}</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $task->title) }}" required maxlength="255">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('tasks.task_details') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="task_deadline_date" class="form-label">{{ __('tasks.deadline_date') }}</label>
                        <input type="date" class="form-control @error('task_deadline_date') is-invalid @enderror" id="task_deadline_date" name="task_deadline_date" value="{{ old('task_deadline_date', $task->task_deadline_date) }}" required>
                        @error('task_deadline_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="assigned_user_id" class="form-label">{{ __('tasks.assign_new_user') }}</label>
                        <select class="form-select @error('assigned_user_id') is-invalid @enderror" id="assigned_user_id" name="assigned_user_id">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_user_id', $task->assigned_user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="assigned_tester_user_id" class="form-label">{{ __('tasks.assign_tester') }}</label>
                        <select class="form-select @error('assigned_tester_user_id') is-invalid @enderror" id="assigned_tester_user_id" name="assigned_tester_user_id">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_tester_user_id', $task->assigned_tester_user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_tester_user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="task_type_id" class="form-label">{{ __('tasks.task_type') }}</label>
                        <select class="form-select @error('task_type_id') is-invalid @enderror" id="task_type_id" name="task_type_id">
                            @foreach ($taskTypes as $taskType)
                                <option value="{{ $taskType->id }}" {{ old('task_type_id', $task->task_type_id) == $taskType->id ? 'selected' : '' }}>
                                    {{ $taskType->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('task_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="task_status_id" class="form-label">{{ __('tasks.task_status') }}</label>
                        <select class="form-select @error('task_status_id') is-invalid @enderror" id="task_status_id" name="task_status_id">
                            @foreach ($taskStatuses as $taskStatus)
                                <option value="{{ $taskStatus->id }}" {{ old('task_status_id', $task->task_status_id) == $taskStatus->id ? 'selected' : '' }}>
                                    {{ $taskStatus->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('task_status_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="btn btn-primary" type="submit">{{ __('tasks.update_task') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
