@extends('layouts.app')

@section('content')

    {{-- tasks block --}}
    <div class="mb-4 ms-5 me-5 ps-4 pe-4 pb-3 bg-white rounded shadow">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mt-3">{{ $task->title }}</h2>
                    <div>
                        <a href="{{ route('tasks.edit', ['task' => $task->id]) }}" class="btn btn-primary me-1">
                            <i class="fas fa-edit"></i> {{ __('edit') }}
                        </a>
                        <form method="post" action="{{ route('tasks.destroy', ['task' => $task->id]) }}" onsubmit="return confirm('{{ __('delete_task_confirmation') }}')" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt"></i> {{ __('delete') }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body bg-light">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th scope="row" class="fw-bold text-nowrap align-middle"><i class="fas fa-calendar-alt ps-2"></i> {{ __('date_created') }}</th>
                            <td class="align-middle">{{ $task->created_at->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold text-nowrap align-middle"><i class="fas fa-hourglass-end ps-2"></i> {{ __('task_deadline') }}</th>
                            <td class="align-middle">{{ $task->task_deadline_date }} ({{ $differenceInDays }})</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold text-nowrap align-middle"><i class="fas fa-user-plus ps-2"></i> {{ __('task_created_by') }}</th>
                            <td class="align-middle">{{ $task->taskCreator->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold text-nowrap align-middle"><i class="fas fa-user-check ps-2"></i> {{ __('task_assigned_to') }}</th>
                            <td class="align-middle">{{ $task->assignedUser->name ?? __('not_assigned') }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold text-nowrap align-middle"><i class="fas fa-user-shield ps-2"></i> {{ __('task_assigned_to_qa') }}</th>
                            <td class="align-middle">{{ $task->assignedTester->name ?? __('not_assigned') }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold text-nowrap align-middle"><i class="fas fa-info-circle ps-2"></i> {{ __('status') }}</th>
                            <td class="align-middle">{{ $task->taskStatus->name ?? __('deleted') }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold text-nowrap align-middle"><i class="fas fa-tasks ps-2"></i> {{ __('task_type') }}</th>
                            <td class="align-middle">{{ $task->taskType->name ?? __('deleted') }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold text-nowrap align-middle"><i class="fas fa-file-alt ps-2"></i> {{ __('task_details') }}</th>
                            <td class="align-middle">{{ $task->description }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- comments block --}}
    <div class="container mt-2">
        <div class="card">
            <div class="card-header p-4">
                {{ __('title') }}: {{ $task->title }}
            </div>
            <div class="card-body bg-white p-4">
                <div class="row">
                    <div class="col-md-12">
                        <form method="post" action="{{ route('comments.store', ['task' => $task->id]) }}">
                            @csrf
                            <div class="form-group">
                                <textarea class="form-control" name="body" required></textarea>
                                <input type="hidden" name="task_id" value="{{ $task->id }}" />
                            </div>
                            <div class="form-group mt-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-reply"></i> {{ __('reply') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                @include('task.comments', ['comments' => $task->comments, 'task_id' => $task->id])
            </div>
        </div>
    </div>


@endsection
