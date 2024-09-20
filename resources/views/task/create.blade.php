@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="my-4">{{ __('task_create') }}</h2>
            </div>
        </div>

        @include('partials.flash-messages')

        <div class="row">
            <div class="col-lg-12">
                <form class="p-4 rounded shadow mt-2" action="{{ route('tasks.store') }}" method="post">
                    @csrf
                    <table class="table">
                        <tbody>
                            <tr>
                                <th class="align-middle">
                                    <label for="title" class="form-label mb-0"><i class="fas fa-heading me-2"></i>{{ __('task_title') }}</label>
                                </th>
                                <td>
                                    <input type="text" id="title" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required maxlength="255">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <th class="align-middle">
                                    <label for="description" class="form-label mb-0"><i class="fas fa-align-left me-2"></i>{{ __('task_details') }}</label>
                                </th>
                                <td>
                                    <textarea id="description" class="form-control @error('description') is-invalid @enderror" rows="6" name="description" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <th class="align-middle">
                                    <label for="task_deadline_date" class="form-label mb-0"><i class="fas fa-calendar-alt me-2"></i>{{ __('task_deadline') }}</label>
                                </th>
                                <td>
                                    <input type="date" id="task_deadline_date" class="form-control @error('task_deadline_date') is-invalid @enderror" name="task_deadline_date" min="{{ date('Y-m-d') }}" value="{{ old('task_deadline_date') }}" required>
                                    @error('task_deadline_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <th class="align-middle">
                                    <label for="assigned_user_id" class="form-label mb-0"><i class="fas fa-user-check me-2"></i>{{ __('task_assigned_to') }}</label>
                                </th>
                                <td>
                                    <select id="assigned_user_id" class="form-select @error('assigned_user_id') is-invalid @enderror" name="assigned_user_id">
                                        <option value="" selected disabled>{{ __('select') }}</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ old('assigned_user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <th class="align-middle">
                                    <label for="assigned_tester_user_id" class="form-label mb-0"><i class="fas fa-user-shield me-2"></i>{{ __('task_assigned_to_qa') }}</label>
                                </th>
                                <td>
                                    <select id="assigned_tester_user_id" class="form-select @error('assigned_tester_user_id') is-invalid @enderror" name="assigned_tester_user_id">
                                        <option value="" selected disabled>{{ __('select') }}</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ old('assigned_tester_user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_tester_user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <th class="align-middle">
                                    <label for="task_type_id" class="form-label mb-0"><i class="fas fa-tasks me-2"></i>{{ __('task_type') }}</label>
                                </th>
                                <td>
                                    <select id="task_type_id" class="form-select @error('task_type_id') is-invalid @enderror" name="task_type_id">
                                        <option value="" selected disabled>{{ __('select') }}</option>
                                        @foreach ($taskTypes as $taskType)
                                            <option value="{{ $taskType->id }}" {{ old('task_type_id') == $taskType->id ? 'selected' : '' }}>
                                                {{ $taskType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('task_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <th class="align-middle">
                                    <label for="task_status_id" class="form-label mb-0"><i class="fas fa-info-circle me-2"></i>{{ __('status') }}</label>
                                </th>
                                <td>
                                    <select id="task_status_id" class="form-select @error('task_status_id') is-invalid @enderror" name="task_status_id">
                                        <option value="" selected disabled>{{ __('select') }}</option>
                                        @foreach ($taskStatuses as $taskStatus)
                                            <option value="{{ $taskStatus->id }}" {{ old('task_status_id') == $taskStatus->id ? 'selected' : '' }}>
                                                {{ $taskStatus->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('task_status_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>{{ __('task_create') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection