@extends('layouts.app')

@section('content')

    <div class="mb-4 ms-0 me-0 ps-4 pe-4 pt-4 pb-3 bg-white rounded shadow">

        <table class="table">
            <thead>
                <tr>
                    <th scope="col" class="text-nowrap"><i class="fas fa-calendar-plus me-2"></i>{{ __('date_created') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-calendar-alt me-2"></i>{{ __('task_deadline') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-heading me-2"></i>{{ __('task_title') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-user-plus me-2"></i>{{ __('task_created_by') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-user-check me-2"></i>{{ __('task_assigned_to') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-user-shield me-2"></i>{{ __('task_assigned_to_tester') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-info-circle me-2"></i>{{ __('status') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-tasks me-2"></i>{{ __('task_type') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-cogs me-2"></i>{{ __('actions') }}</th>
                </tr>
                <tr>
                    <form method="GET" action="{{ route('tasks.index') }}">
                        <td><input type="date" class="form-control form-control-sm" id="created_at" name="created_at" value="{{ request('created_at') }}"></td>
                        <td><input type="date" class="form-control form-control-sm" id="task_deadline_date" name="task_deadline_date" value="{{ request('task_deadline_date') }}"></td>
                        <td><input type="text" class="form-control form-control-sm" id="title" name="search" value="{{ request('search') }}" placeholder="{{ __('task_title') }}"></td>
                        <td>
                            <select class="form-select form-select-sm" id="task_creator_user_id" name="task_creator_user_id">
                                <option value="">{{ __('select') }}</option>
                                @foreach($taskCreators as $taskCreator)
                                    <option value="{{ $taskCreator->id }}" {{ request('task_creator_user_id') == $taskCreator->id ? 'selected' : '' }}>
                                        {{ $taskCreator->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm" id="assigned_user_id" name="assigned_user_id">
                                <option value="">{{ __('select') }}</option>
                                @foreach($assignedUsers as $assignedUser)
                                    <option value="{{ $assignedUser->id }}" {{ request('assigned_user_id') == $assignedUser->id ? 'selected' : '' }}>
                                        {{ $assignedUser->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm" id="assigned_tester_user_id" name="assigned_tester_user_id">
                                <option value="">{{ __('select') }}</option>
                                @foreach($assignedTesters as $assignedTester)
                                    <option value="{{ $assignedTester->id }}" {{ request('assigned_tester_user_id') == $assignedTester->id ? 'selected' : '' }}>
                                        {{ $assignedTester->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm" id="task_status_id" name="task_status_id">
                                <option value="">{{ __('select') }}</option>
                                @foreach($taskStatuses as $taskStatus)
                                    <option value="{{ $taskStatus->id }}" {{ request('task_status_id') == $taskStatus->id ? 'selected' : '' }}>
                                        {{ $taskStatus->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm" id="task_type_id" name="task_type_id">
                                <option value="">{{ __('select') }}</option>
                                @foreach($taskTypes as $taskType)
                                    <option value="{{ $taskType->id }}" {{ request('task_type_id') == $taskType->id ? 'selected' : '' }}>
                                        {{ $taskType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-outline-secondary btn-sm me-2"><i class="fas fa-filter me-2"></i>{{ __('Filter') }}</button>
                            </div>
                        </td>
                    </form>
                </tr>
            </thead>
            <tbody>
            @if(isset($tasks) && count($tasks) > 0)
                @foreach ($tasks as $task)
                    <tr>
                        <td class="text-nowrap">{{ $task->created_at->format('Y-m-d') }}</td>
                        <td class="text-nowrap">{{ $task->task_deadline_date }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('tasks.show', ['task' => $task->id]) }}" class="text-decoration-none">
                                <i class="fas fa-tasks me-2"></i>{{ $task->title }}
                            </a>
                            @if($task->comments_count > 0)
                                <i class="fas fa-comments ms-2"></i> {{$task->comments_count}}
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $task->taskCreator->name }}</td>
                        <td class="text-nowrap">{{ $task->assignedUser->name ?? __('not_assigned') }}</td>
                        <td class="text-nowrap">{{ $task->assignedTester->name ?? __('not_assigned') }}</td>
                        <td class="text-nowrap">{{ $task->taskStatus->name ?? __('deleted') }}</td>
                        <td class="text-nowrap">{{ $task->taskType->name ?? __('deleted') }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit float-end"></i></a>
                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('confirm_delete_task') }}')"><i class="fas fa-trash float-end"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9"><i class="fas fa-exclamation-circle me-2"></i>{{ __('no_tasks_found') }}</td>
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
                    <div class="input-group input-group-sm mb-3">
                        <select name="paginationCount" id="paginationCount" class="form-select form-select-sm">
                            @foreach(range(5, 50, 5) as $value)
                                <option value="{{ $value }}" @if(session('paginationCount') == $value) selected @endif>{{ $value }} {{ __('tasks_per_page') }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-sync-alt me-2"></i>{{ __('update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
