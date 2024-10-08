@extends('layouts.app')

@section('content')

    <div class="mb-4 ms-0 me-0 ps-4 pe-4 pt-4 pb-3 bg-white rounded shadow">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                <tr>
                    <th scope="col" class="text-nowrap"><i class="fas fa-calendar-plus me-2"></i>{{ __('date_created') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-calendar-alt me-2"></i>{{ __('task_deadline') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-heading me-2"></i>{{ __('task_title') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-user-plus me-2"></i>{{ __('task_created_by') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-user-check me-2"></i>{{ __('task_assigned_to') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-user-shield me-2"></i>{{ __('task_assigned_to_qa') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-info-circle me-2"></i>{{ __('status') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-tasks me-2"></i>{{ __('task_type') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-cogs me-2"></i>{{ __('actions') }}</th>
                </tr>
                @include('task.partials.filter')
             
            </thead>
            <tbody>
            @if(isset($tasks) && count($tasks) > 0)
                @foreach ($tasks as $task)
                    <tr>
                        <td class="text-nowrap">{{ $task->created_at->format('Y-m-d') }}</td>
                        <td class="text-nowrap">{{ $task->task_deadline_date }}</td>
                        <td>
                            <a href="{{ route('tasks.show', ['task' => $task->id]) }}" class="text-decoration-none">
                                {{ $task->title }}
                            </a>
                            @if($task->comments_count > 0)
                                <span class="ms-2 badge bg-secondary">
                                    <i class="fas fa-comments"></i> {{$task->comments_count}}
                                </span>
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $task->taskCreator->name }} <span class="badge bg-secondary">{{ $task->taskCreator->noOfTaskCreated() }}</span></td>
                        <td class="text-nowrap">{{ $task->assignedUser->name ?? __('not_assigned') }} <span class="badge bg-secondary">{{ $task->assignedUser->noOfTaskAssigned() ?? 0 }}</span></td>
                        <td class="text-nowrap">{{ $task->assignedTester->name ?? __('not_assigned') }} <span class="badge bg-secondary">{{ $task->assignedTester->tasksAssignedCount() ?? 0 }}</span></td>
                        <td class="text-nowrap">{{ $task->taskStatus->name ?? __('deleted') }} <span class="badge bg-secondary">{{ $task->taskStatusCount ?? 0 }}</span></td>
                        <td class="text-nowrap">{{ $task->taskType->name ?? __('deleted') }} <span class="badge bg-secondary">{{ $task->taskTypeCount ?? 0 }}</span></td>
                        <td class="text-nowrap">
                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> {{ __('edit') }}
                            </a>
                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ __('confirm_delete_task') }}')">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9" class="text-center">
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ __('no_tasks_found') }}
                        </div>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
        </div>
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
                                <option value="{{ $value }}" @if($currentPaginationCount == $value) selected @endif>{{ $value }} {{ __('tasks_per_page') }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fas fa-sync-alt me-2"></i>{{ __('update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
