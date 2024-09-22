@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4 ms-0 me-0 ps-4 pe-4 pt-4 pb-3 bg-white rounded shadow">
        <h1 class="mb-4"><i class="fas fa-plus-circle me-2"></i>{{ __('task_create') }}</h1>
        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <label for="title" class="col-sm-3 col-form-label"><i class="fas fa-heading me-2"></i>{{ __('task_title') }}</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required maxlength="255">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="description" class="col-sm-3 col-form-label"><i class="fas fa-align-left me-2"></i>{{ __('task_details') }}</label>
                <div class="col-sm-9">
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="task_deadline_date" class="col-sm-3 col-form-label"><i class="fas fa-calendar-alt me-2"></i>{{ __('task_deadline') }}</label>
                <div class="col-sm-9">
                    <input type="date" class="form-control @error('task_deadline_date') is-invalid @enderror" id="task_deadline_date" name="task_deadline_date" min="{{ date('Y-m-d') }}" value="{{ old('task_deadline_date') }}" required>
                    @error('task_deadline_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="assigned_user_id" class="col-sm-3 col-form-label"><i class="fas fa-user-check me-2"></i>{{ __('task_assigned_to') }}</label>
                <div class="col-sm-9">
                    <select class="form-select @error('assigned_user_id') is-invalid @enderror" id="assigned_user_id" name="assigned_user_id">
                        <option value="">{{ __('select') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="assigned_tester_user_id" class="col-sm-3 col-form-label"><i class="fas fa-user-shield me-2"></i>{{ __('task_assigned_to_qa') }}</label>
                <div class="col-sm-9">
                    <select class="form-select @error('assigned_tester_user_id') is-invalid @enderror" id="assigned_tester_user_id" name="assigned_tester_user_id">
                        <option value="">{{ __('select') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_tester_user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_tester_user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="task_type_id" class="col-sm-3 col-form-label"><i class="fas fa-tasks me-2"></i>{{ __('task_type') }}</label>
                <div class="col-sm-9">
                    <select class="form-select @error('task_type_id') is-invalid @enderror" id="task_type_id" name="task_type_id">
                        <option value="">{{ __('select') }}</option>
                        @foreach($taskTypes as $taskType)
                            <option value="{{ $taskType->id }}" {{ old('task_type_id') == $taskType->id ? 'selected' : '' }}>
                                {{ $taskType->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('task_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="task_status_id" class="col-sm-3 col-form-label"><i class="fas fa-info-circle me-2"></i>{{ __('status') }}</label>
                <div class="col-sm-9">
                    <select class="form-select @error('task_status_id') is-invalid @enderror" id="task_status_id" name="task_status_id">
                        <option value="">{{ __('select') }}</option>
                        @foreach($taskStatuses as $taskStatus)
                            <option value="{{ $taskStatus->id }}" {{ old('task_status_id') == $taskStatus->id ? 'selected' : '' }}>
                                {{ $taskStatus->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('task_status_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>{{ __('create') }}</button>
                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>{{ __('cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection