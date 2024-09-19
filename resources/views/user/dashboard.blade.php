@extends('layouts.app')

@section('content')

    <div class="container bg-white border p-3">
        <h2 class="mt-5 mb-4">{{ __('messages.tasks') }}: {{ ucwords($user->name) }}</h2>

        <div class="row mb-4">
            <div class="col-md-3">
                <p>{{ __('messages.task_created_by') }} {{ ucwords($user->name) }}: {{ $user->noOfTaskCreated() }}</p>
            </div>
            <div class="col-md-3">
                <p>{{ __('messages.task_assigned_to') }} {{ ucwords($user->name) }}: {{ $user->noOfTaskAssigned() }}</p>
            </div>
        </div>

        <table class="table">
            <thead>
            <tr>
                <th scope="col">{{ __('messages.date_created') }}</th>
                <th scope="col">{{ __('messages.deadline_date') }}</th>
                <th scope="col">{{ __('messages.task_name') }}</th>
                <th scope="col">{{ __('messages.created_by') }}</th>
                <th scope="col">{{ __('messages.assigned_to') }}</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($tasks) && count($tasks) > 0)
                @foreach ($tasks as $task)
                    <tr class="">
                        <td>{{ date_format($task->created_at, 'Y-m-d') }}</td>
                        <td>{{ $task->task_deadline_date }}</td>
                        <td>
                            <a href="{{ route('tasks.show', ['task' => $task->id]) }}">{{ $task->title }}</a>
                        </td>
                        <td>{{ $task->getTaskCreatorUser() }}</td>
                        <td>{{ $task->getAssignedUser() }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6">{{ __('messages.no_tasks_found') }}</td>
                </tr>
            @endif
            </tbody>
        </table>

        {{ $tasks->links() }}

    </div>

@endsection
