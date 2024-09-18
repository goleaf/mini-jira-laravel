@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="mb-4 p-4 pb-3 bg-white rounded shadow">
        <h2>Logs</h2>
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Created</th>
                <th>Action</th>
                <th>Object</th>
                <th>User</th>
            </tr>
            </thead>
            <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->created_at }}</td>

                    <td>
                        @if($log->type == 'task')
                            Task {{ $log->action }}
                        @elseif($log->type == 'task_status')
                            Task status {{ $log->action }}
                        @elseif($log->type == 'task_type')
                            Task type {{ $log->action }}
                        @elseif($log->type == 'comment' && $log->action == 'created')
                            Comment {{ $log->action }}
                        @elseif($log->type == 'comment' && $log->action == 'commentReply')
                            Comment replied
                        @endif
                    </td>

                    <td>
                        @if($log->type == 'task')
                            <a href="{{ route('tasks.show', ['task' => $log->object_id]) }}">{{ $log->task->title }}</a> @if($log->task->deleted_at) - deleted @endif
                        @elseif($log->type == 'task_status')
                            <a href="{{ route('task-statuses.edit', ['taskStatus' => $log->object_id]) }}">{{ $log->taskStatus->name }}</a> @if($log->taskStatus->deleted_at) - deleted @endif
                        @elseif($log->type == 'task_type')
                            <a href="{{ route('task-types.edit', ['task_type' => $log->object_id]) }}">{{ $log->taskType->name }}</a> @if($log->taskType->deleted_at) - deleted @endif
                        @elseif($log->type == 'comment')
                            <a href="{{ route('tasks.show', ['task' => $log->object_id]) }}">{{ $log->task->title }}</a> @if($log->task->deleted_at) - deleted @endif
                        @endif
                    </td>

                    <td>{{ $log->getUser->name }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $logs->links() }}
    </div>
    </div>
@endsection
