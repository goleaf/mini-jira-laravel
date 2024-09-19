@extends('layouts.app')

@section('content')

    <div class="mb-4 ms-5 me-5 ps-4 pe-4 pt-5 pb-3 bg-white rounded shadow">
        <form method="GET" action="{{ route('tasks.index') }}" class="row mb-4">

            <div class="col-md-3">
                <fieldset>
                    <legend class="fs-6">{{ __('messages.task_created') }}</legend>
                    <div class="input-group mb-3">
                        <input type="date" class="form-control" name="created_at" value="{{ request('created_at') }}">
                    </div>
                </fieldset>
            </div>

            <div class="col-md-3">
                <fieldset>
                    <legend class="fs-6">{{ __('messages.task_deadline') }}</legend>
                    <div class="input-group mb-3">
                        <input type="date" class="form-control" name="task_deadline_date" value="{{ request('task_deadline_date') }}">
                    </div>
                </fieldset>
            </div>

            <div class="mb-3 col-md-3">
                <label for="task_creator_user_id" class="form-label">{{ __('messages.task_creator') }}</label>
                <select class="form-select" name="task_creator_user_id">
                    <option value="" selected disabled>{{ __('messages.select_task_creator') }}</option>
                    @foreach($taskCreators as $taskCreator)
                        <option value="{{ $taskCreator->id }}" {{ request('task_creator_user_id') == $taskCreator->id ? 'selected' : '' }}>
                            {{ $taskCreator->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-3">
                <label for="assigned_user_id" class="form-label">{{ __('messages.assigned_user') }}</label>
                <select class="form-select" name="assigned_user_id">
                    <option value="" selected disabled>{{ __('messages.select_assigned_user') }}</option>
                    @foreach($assignedUsers as $assignedUser)
                        <option value="{{ $assignedUser->id }}" {{ request('assigned_user_id') == $assignedUser->id ? 'selected' : '' }}>
                            {{ $assignedUser->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-3">
                <label for="assigned_tester_user_id" class="form-label">{{ __('messages.filter_by_task_tester') }}</label>
                <select class="form-select" name="assigned_tester_user_id">
                    <option value="" selected disabled>{{ __('messages.select_task_tester') }}</option>
                    @foreach($assignedTesters as $assignedTester)
                        <option value="{{ $assignedTester->id }}" {{ request('assigned_tester_user_id') == $assignedTester->id ? 'selected' : '' }}>
                            {{ $assignedTester->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-3">
                <label for="task_type_id" class="form-label">{{ __('messages.filter_by_task_type') }}</label>
                <select class="form-select" name="task_type_id">
                    <option value="" selected disabled>{{ __('messages.select_task_type') }}</option>
                    @foreach($taskTypes as $taskType)
                        <option value="{{ $taskType->id }}" {{ request('task_type_id') == $taskType->id ? 'selected' : '' }}>
                            {{ $taskType->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-3">
                <label for="task_status_id" class="form-label">{{ __('messages.filter_by_task_status') }}</label>
                <select class="form-select" name="task_status_id">
                    <option value="" selected disabled>{{ __('messages.select_task_status') }}</option>
                    @foreach($taskStatuses as $taskStatus)
                        <option value="{{ $taskStatus->id }}" {{ request('task_status_id') == $taskStatus->id ? 'selected' : '' }}>
                            {{ $taskStatus->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-3">
                <label for="search" class="form-label">{{ __('messages.search_in_title_or_description') }}</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search') }}">
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-outline-secondary">{{ __('messages.search') }}</button>
                @if(request()->filled('created_at') ||
                    request()->filled('task_creator_user_id') ||
                    request()->filled('assigned_user_id') ||
                    request()->filled('search') ||
                    request()->filled('task_type_id') ||
                    request()->filled('assigned_tester_user_id') ||
                    request()->filled('task_status_id') ||
                    request()->filled('task_deadline_date')
                )
                    <a role="button" class="btn btn-outline-danger" href="{{ route('tasks.index') }}">{{ __('messages.reset') }}</a>
                @endif
            </div>

        </form>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table">
            <thead>
            <tr>
                <th scope="col">{{ __('messages.date_created') }}</th>
                <th scope="col">{{ __('messages.deadline_date') }}</th>
                <th scope="col">{{ __('messages.task_name') }}</th>
                <th scope="col">{{ __('messages.created_by') }}</th>
                <th scope="col">{{ __('messages.assigned_to') }}</th>
                <th scope="col">{{ __('messages.assigned_to_tester') }}</th>
                <th scope="col">{{ __('messages.status') }}</th>
                <th scope="col">{{ __('messages.type') }}</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($tasks) && count($tasks) > 0)

                @foreach ($tasks as $task)
                    <tr>
                        <td class="text-nowrap">{{ date_format($task->created_at, 'Y-m-d') }}</td>
                        <td class="text-nowrap">{{ date('Y-m-d', strtotime($task->task_deadline_date)) }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('tasks.show', ['task' => $task->id]) }}" class="text-decoration-none">
                                {{ $task->title }}
                            </a>
                            @if($task->comments_count > 0)
                                {{ __('messages.comments') }}: {{$task->comments_count}}
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $task->getTaskCreatorUser() }}</td>
                        <td class="text-nowrap">{{ $task->getAssignedUser() }}</td>
                        <td class="text-nowrap">{{ $task->getAssignedTester() }}</td>
                        <td class="text-nowrap">{{ $task->taskStatus->name ?? __('messages.status_deleted') }}</td>
                        <td class="text-nowrap">{{ $task->taskType->name ?? __('messages.type_deleted') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8">{{ __('messages.no_tasks_found') }}</td>
                </tr>
            @endif
            </tbody>
        </table>

        <div class="row">
            <div class="col-md-10">
                {{ $tasks->links() }}
            </div>
            <div class="col-md-2">
                <form method="POST" action="{{ route('tasks.update-pagination-count') }}">
                    @csrf
                    <div class="input-group mb-3">
                        <select name="paginationCount" id="paginationCount" class="form-select">
                            @foreach(range(5, 50, 5) as $value)
                                <option value="{{ $value }}" @if(session('paginationCount') == $value) selected @endif>{{ $value }} {{ __('messages.tasks_per_page') }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
