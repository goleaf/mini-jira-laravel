@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="mb-4 p-4 pb-3 bg-white rounded shadow">
            <h2>{{ __('logs') }}</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('id') }}</th>
                        <th>{{ __('created') }}</th>
                        <th>{{ __('action') }}</th>
                        <th>{{ __('object') }}</th>
                        <th>{{ __('user') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->created_at->format(__('date_format')) }}</td>
                        <td>
                            @switch($log->type)
                                @case('task')
                                    {{ __('task_action', ['action' => __($log->action)]) }}
                                    @break
                                @case('task_status')
                                    {{ __('status_' . $log->action) }}
                                    @break
                                @case('task_type')
                                    {{ __('task_type_' . $log->action) }}
                                    @break
                                @case('comment')
                                    @if($log->action == 'created')
                                        {{ __('comment_created') }}
                                    @elseif($log->action == 'commentReply')
                                        {{ __('comment_replied') }}
                                    @endif
                                    @break
                                @default
                                    {{ __('unknown_action') }}
                            @endswitch
                        </td>
                        <td>
                            @switch($log->type)
                                @case('task')
                                    <a href="{{ route('tasks.show', ['task' => $log->object_id]) }}">{{ $log->task->title }}</a>
                                    @if($log->task->trashed())
                                        <span class="text-muted">{{ __('deleted') }}</span>
                                    @endif
                                    @break
                                @case('task_status')
                                    <a href="{{ route('task-statuses.edit', ['taskStatus' => $log->object_id]) }}">{{ $log->taskStatus->name }}</a>
                                    @if($log->taskStatus->trashed())
                                        <span class="text-muted">{{ __('deleted') }}</span>
                                    @endif
                                    @break
                                @case('task_type')
                                    <a href="{{ route('task-types.edit', ['task_type' => $log->object_id]) }}">{{ $log->taskType->name }}</a>
                                    @if($log->taskType->trashed())
                                        <span class="text-muted">{{ __('deleted') }}</span>
                                    @endif
                                    @break
                                @case('comment')
                                    <a href="{{ route('tasks.show', ['task' => $log->object_id]) }}">{{ $log->task->title }}</a>
                                    @if($log->task->trashed())
                                        <span class="text-muted">{{ __('deleted') }}</span>
                                    @endif
                                    @break
                                @default
                                    {{ __('unknown_action') }}
                            @endswitch
                        </td>
                        <td>{{ $log->user->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">{{ __('no_logs_found') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{ $logs->links() }}
        </div>
    </div>
@endsection
