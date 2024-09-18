@extends('layouts.app')

@section('content')


    <div class="container bg-white rounded shadow ps-4 pe-4 pb-4">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="my-4">Updating Task: {{ $task->title }}</h2>
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
                    <label class="form-label ">Created by:</label>
                    <p>{{ $task->getTaskCreatorUser() }}</p>
                </div>
                <div class="mb-3 col-6">
                    <label class="form-label ">Assigned to:</label>
                    <p>{{ $task->getAssignedUser() }}</p>
                </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form class="mt-2" method="post" action="{{ route('tasks.update', ['task' => $task->id]) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Task Title</label>
                        <input type="text" class="form-control" name="title" value="{{ $task->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Task Details</label>
                        <textarea class="form-control" name="description" rows="6" required>{{ $task->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deadline Date</label>
                        <input type="date" class="form-control" name="task_deadline_date" value="{{ $task->task_deadline_date }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign new user</label>
                        <select class="form-select" name="assigned_user_id">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id === $task->assigned_user_id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign tester</label>
                        <select class="form-select" name="assigned_tester_user_id">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id === $task->assigned_tester_user_id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Task type</label>
                        <select class="form-select" name="task_type_id">
                            @foreach ($taskTypes as $taskType)
                                <option value="{{ $taskType->id }}" {{ $taskType->id === $task->task_type_id ? 'selected' : '' }}>
                                    {{ $taskType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Task status</label>
                        <select class="form-select" name="task_status_id">
                            @foreach ($taskStatuses as $taskStatuse)
                                <option value="{{ $taskStatuse->id }}" {{ $taskStatuse->id === $task->task_status_id ? 'selected' : '' }}>
                                    {{ $taskStatuse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-primary" type="submit">Update task</button>

                </form>
            </div>
        </div>
    </div>



@endsection
