@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="my-4">Create a Task</h2>
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
            <div class="col-lg-12">

                <form class="p-4 bg-white rounded shadow mt-2" action="{{ route('task.store') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Task Title</label>
                        <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Task details</label>
                        <textarea class="form-control" rows="6" name="description" required>{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deadline Date</label>
                        <input type="date" class="form-control" name="task_deadline_date" min="2022-01-01" value="{{ old('due') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign to</label>
                        <select class="form-select" name="assigned_user_id">
                            <option value="" selected disabled>- Select -</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign tester</label>
                        <select class="form-select" name="assigned_tester_user_id">
                            <option value="" selected disabled>- Select -</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_tester_user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Task type</label>
                        <select class="form-select" name="task_type_id">
                            <option value="" selected disabled>- Select -</option>
                            @foreach ($taskTypes as $taskType)
                                <option value="{{ $taskType->id }}" {{ old('task_type_id') == $taskType->id ? 'selected' : '' }}>
                                    {{ $taskType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Task status</label>
                        <select class="form-select" name="task_status_id">
                            <option value="" selected disabled>- Select -</option>
                            @foreach ($taskStatuses as $taskStatus)
                                <option value="{{ $taskStatus->id }}" {{ old('task_status_id') == $taskStatus->id ? 'selected' : '' }}>
                                    {{ $taskStatus->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Create new task</button>

                </form>
            </div>
        </div>
    </div>

@endsection
