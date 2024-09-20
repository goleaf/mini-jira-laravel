@extends('layouts.app')

@section('content')

    <div class="container bg-white rounded shadow ps-4 pe-4 pb-4">
   
        <div class="row">
            <div class="col-md-12 my-4"">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="mt-2" method="POST" action="{{ route('tasks.update', ['task' => $task->id]) }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="title" class="form-label"><i class="fas fa-heading me-2"></i>{{ __('task_title') }}</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $task->title) }}" required maxlength="255">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label"><i class="fas fa-file-alt me-2"></i>{{ __('task_details') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 d-flex justify-content-between">
                        <div class="me-2">
                            <label for="task_deadline_date" class="form-label"><i class="fas fa-calendar-alt me-2"></i>{{ __('task_deadline') }}</label>
                            <input type="date" class="form-control @error('task_deadline_date') is-invalid @enderror" id="task_deadline_date" name="task_deadline_date" value="{{ old('task_deadline_date', $task->task_deadline_date) }}" required>
                            @error('task_deadline_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="me-2">
                            <label for="assigned_user_id" class="form-label text-nowrap"><i class="fas fa-user-check me-2"></i>{{ __('assign_to') }}</label>
                            <select class="form-select @error('assigned_user_id') is-invalid @enderror" id="assigned_user_id" name="assigned_user_id" required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_user_id', $task->assigned_user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('assigned_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="me-2">
                            <label for="assigned_tester_user_id" class="form-label text-nowrap"><i class="fas fa-user-shield me-2"></i>{{ __('assign_tester') }}</label>
                            <select class="form-select @error('assigned_tester_user_id') is-invalid @enderror" id="assigned_tester_user_id" name="assigned_tester_user_id" required>
                                @foreach ($users->where('is_admin', true) as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_tester_user_id', $task->assigned_tester_user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('assigned_tester_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="me-2">
                            <label for="task_type_id" class="form-label text-nowrap"><i class="fas fa-tasks me-2"></i>{{ __('task_type') }}</label>
                            <select class="form-select @error('task_type_id') is-invalid @enderror" id="task_type_id" name="task_type_id" required>
                                @foreach ($taskTypes as $taskType)
                                    <option value="{{ $taskType->id }}" {{ old('task_type_id', $task->task_type_id) == $taskType->id ? 'selected' : '' }}>{{ $taskType->name }}</option>
                                @endforeach
                            </select>
                            @error('task_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="task_status_id" class="form-label text-nowrap"><i class="fas fa-info-circle me-2"></i>{{ __('status') }}</label>
                            <select class="form-select @error('task_status_id') is-invalid @enderror" id="task_status_id" name="task_status_id" required>
                                @foreach ($taskStatuses as $taskStatus)
                                    <option value="{{ $taskStatus->id }}" {{ old('task_status_id', $task->task_status_id) == $taskStatus->id ? 'selected' : '' }}>{{ $taskStatus->name }}</option>
                                @endforeach
                            </select>
                            @error('task_status_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>{{ __('update') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
