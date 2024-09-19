@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="mb-4 p-4 pb-3 bg-white rounded shadow">
            <h2>{{ __('log.logs') }}</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('log.id') }}</th>
                        <th>{{ __('log.created') }}</th>
                        <th>{{ __('log.action') }}</th>
                        <th>{{ __('log.object') }}</th>
                        <th>{{ __('log.user') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->created_at->format(__('log.date_format')) }}</td>
                        <td>
                            @switch($log->type)
                                @case('task')
                                    {{ __('log.task_action', ['action' => __('log.actions.' . $log->action)]) }}
                                    @break
                                @case('task_status')
                                    {{ __('log.task_status_action', ['action' => __('log.actions.' . $log->action)]) }}
                                    @break
                                @case('task_type')
                                    {{ __('log.task_type_action', ['action' => __('log.actions.' . $log->action)]) }}
                                    @break
                                @case('comment')
                                    @if($log->action == 'created')
                                        {{ __('log.comment_created') }}
                                    @elseif($log->action == 'commentReply')
                                        {{ __('log.comment_replied') }}
                                    @endif
                                    @break
                                @default
                                    {{ __('log.unknown_action') }}
                            @endswitch
                        </td>
                        <td>
                            @switch($log->type)
                                @case('task')
                                    <a href="{{ route('tasks.show', ['task' => $log->object_id]) }}">{{ $log->task->title }}</a>
                                    @if($log->task->trashed())
                                        <span class="text-muted">{{ __('log.deleted') }}</span>
                                    @endif
                                    @break
                                @case('task_status')
                                    <a href="{{ route('task-statuses.edit', ['taskStatus' => $log->object_id]) }}">{{ $log->taskStatus->name }}</a>
                                    @if($log->taskStatus->trashed())
                                        <span class="text-muted">{{ __('log.deleted') }}</span>
                                    @endif
                                    @break
                                @case('task_type')
                                    <a href="{{ route('task-types.edit', ['task_type' => $log->object_id]) }}">{{ $log->taskType->name }}</a>
                                    @if($log->taskType->trashed())
                                        <span class="text-muted">{{ __('log.deleted') }}</span>
                                    @endif
                                    @break
                                @case('comment')
                                    <a href="{{ route('tasks.show', ['task' => $log->object_id]) }}">{{ $log->task->title }}</a>
                                    @if($log->task->trashed())
                                        <span class="text-muted">{{ __('log.deleted') }}</span>
                                    @endif
                                    @break
                                @default
                                    {{ __('log.unknown_object') }}
                            @endswitch
                        </td>
                        <td>{{ $log->user->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">{{ __('log.no_logs') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{ $logs->links() }}
        </div>
    </div>
@endsection
