@extends('layouts.app')

@section('content')
    <div class="mb-4 ms-0 me-0 ps-4 pe-4 pt-4 pb-3 bg-white rounded shadow">
        <h2 class="mb-4">{{ __('tasks') }}: {{ ucwords($dashboardData['user']->name) }}</h2>

        <div class="row mb-4">
            <div class="col-md-3">
                <p><i class="fas fa-user-plus me-2"></i>{{ __('task_created_by') }} {{ ucwords($dashboardData['user']->name) }}: {{ $dashboardData['createdTasksCount'] }}</p>
            </div>
            <div class="col-md-3">
                <p><i class="fas fa-user-check me-2"></i>{{ __('task_assigned_to') }} {{ ucwords($dashboardData['user']->name) }}: {{ $dashboardData['assignedTasksCount'] }}</p>
            </div>
        </div>

        <!-- Add filters -->
        <form method="GET" action="{{ route('user.dashboard', ['user' => $dashboardData['user']->id]) }}" class="mb-4">
            <div class="row">
                <div class="col-md-2">
                    <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="{{ __('task_title') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="task_status_id">
                        <option value="">{{ __('select_status') }}</option>
                        @foreach($dashboardData['taskStatuses'] as $status)
                            <option value="{{ $status->id }}" {{ request('task_status_id') == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="task_type_id">
                        <option value="">{{ __('select_type') }}</option>
                        @foreach($dashboardData['taskTypes'] as $type)
                            <option value="{{ $type->id }}" {{ request('task_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="task_creator_user_id">
                        <option value="">{{ __('select_creator') }}</option>
                        @foreach($dashboardData['taskCreators'] as $creator)
                            <option value="{{ $creator->id }}" {{ request('task_creator_user_id') == $creator->id ? 'selected' : '' }}>
                                {{ $creator->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="assigned_user_id">
                        <option value="">{{ __('select_assigned') }}</option>
                        @foreach($dashboardData['assignedUsers'] as $assigned)
                            <option value="{{ $assigned->id }}" {{ request('assigned_user_id') == $assigned->id ? 'selected' : '' }}>
                                {{ $assigned->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="assigned_tester_user_id">
                        <option value="">{{ __('select_tester') }}</option>
                        @foreach($dashboardData['assignedTesters'] as $tester)
                            <option value="{{ $tester->id }}" {{ request('assigned_tester_user_id') == $tester->id ? 'selected' : '' }}>
                                {{ $tester->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-sm">{{ __('filter') }}</button>
                    <a href="{{ route('user.dashboard', ['user' => $dashboardData['user']->id]) }}" class="btn btn-secondary btn-sm">{{ __('reset') }}</a>
                </div>
            </div>
        </form>

        <ul class="nav nav-tabs mb-3" id="taskTabs" role="tablist">
            <li class="nav-item" role="dashboard">
                <button class="nav-link active" id="created-tab" data-bs-toggle="tab" data-bs-target="#created" type="button" role="tab" aria-controls="created" aria-selected="true">{{ __('task_created_by') }} {{ ucwords($dashboardData['user']->name) }}</button>
            </li>
            <li class="nav-item" role="dashboard">
                <button class="nav-link" id="assigned-tab" data-bs-toggle="tab" data-bs-target="#assigned" type="button" role="tab" aria-controls="assigned" aria-selected="false">{{ __('task_assigned_to') }} {{ ucwords($dashboardData['user']->name) }}</button>
            </li>
            <li class="nav-item" role="dashboard">
                <button class="nav-link" id="updated-tab" data-bs-toggle="tab" data-bs-target="#updated" type="button" role="tab" aria-controls="updated" aria-selected="false">{{ __('last_updated_tasks') }}</button>
            </li>
        </ul>

        <div class="tab-content" id="taskTabsContent">
            @foreach(['created' => 'createdTasks', 'assigned' => 'assignedTasks', 'updated' => 'lastUpdatedTasks'] as $tabId => $tasksKey)
                <div class="tab-pane fade {{ $tabId === 'created' ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel" aria-labelledby="{{ $tabId }}-tab">
                    @if($dashboardData[$tasksKey]->isNotEmpty())
                        <div class="accordion" id="{{ $tabId }}Accordion">
                            @foreach($dashboardData[$tasksKey] as $month => $tasks)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="{{ $tabId }}Heading{{ $loop->index }}">
                                        <button class="accordion-button {{ $month === __('this_month') ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $tabId }}Collapse{{ $loop->index }}" aria-expanded="{{ $month === __('this_month') ? 'true' : 'false' }}" aria-controls="{{ $tabId }}Collapse{{ $loop->index }}">
                                            {{ $month }} ({{ $tasks->count() }})
                                        </button>
                                    </h2>
                                    <div id="{{ $tabId }}Collapse{{ $loop->index }}" class="accordion-collapse collapse {{ $month === __('this_month') ? 'show' : '' }}" aria-labelledby="{{ $tabId }}Heading{{ $loop->index }}" data-bs-parent="#{{ $tabId }}Accordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-striped align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th scope="col" class="text-nowrap col-1"><i class="fas fa-calendar-plus me-2"></i>{{ __('date_created') }}</th>
                                                            <th scope="col" class="text-nowrap col-1"><i class="fas fa-calendar-alt me-2"></i>{{ __('task_deadline') }}</th>
                                                            <th scope="col" class="text-nowrap"><i class="fas fa-heading me-2"></i>{{ __('task_title') }}</th>
                                                            <th scope="col" class="text-nowrap"><i class="fas fa-info-circle me-2"></i>{{ __('status') }}</th>
                                                            <th scope="col" class="text-nowrap"><i class="fas fa-tasks me-2"></i>{{ __('task_type') }}</th>
                                                            <th scope="col" class="text-nowrap"><i class="fas fa-cogs me-2"></i>{{ __('actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($tasks as $task)
                                                            <tr>
                                                                <td class="text-nowrap">{{ $task->created_at->format('Y-m-d') }}</td>
                                                                <td class="text-nowrap">{{ $task->task_deadline_date }}</td>
                                                                <td>
                                                                    <a href="{{ route('tasks.show', ['task' => $task->id]) }}" class="text-decoration-none">
                                                                        {{ $task->title }}
                                                                    </a>
                                                                </td>
                                                                <td>{{ $task->taskStatus->name }}</td>
                                                                <td>{{ $task->taskType->name }}</td>
                                                                <td>
                                                                    <a href="{{ route('tasks.edit', ['task' => $task->id]) }}" class="btn btn-sm btn-outline-primary">
                                                                        <i class="fas fa-edit"></i> {{ __('edit') }}
                                                                    </a>
                                                                    <form action="{{ route('tasks.destroy', ['task' => $task->id]) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('confirm_delete_task') }}')">
                                                                            <i class="fas fa-trash-alt"></i> {{ __('delete') }}
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ __('no_tasks_found') }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var triggerTabList = [].slice.call(document.querySelectorAll('#taskTabs button'))
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl)
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()
            })
        })
    });
</script>
@endpush
