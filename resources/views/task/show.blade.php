@extends('layouts.app')

@section('content')

    {{-- tasks block --}}
    <div class="mb-4 ms-5 me-5 ps-4 pe-4 pb-3 bg-white rounded shadow">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card-header text-white">

                </div>
                <div class="card-body bg-light">
                        <div class="d-flex justify-content-end mt-3 p-4">
                            <a href="{{ route('tasks.edit', ['task' => $task->id]) }}" class="btn btn-primary me-1">Edit</a>
                            <form method="post" action="{{ route('tasks.destroy', ['task' => $task->id]) }}" onsubmit="return confirm('Please confirm task deletion')">
                                @csrf
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <th scope="row" class="fw-bold">Task title:</th>
                                <td>{{ $task->title }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="fw-bold">Date Created:</th>
                                <td>{{ date_format($task->created_at, 'Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="fw-bold">Deadline Date:</th>
                                <td>{{ date('Y-m-d', strtotime($task->task_deadline_date)) }} ({{ $differenceInDays }})</td>
                            </tr>
                            <tr>
                                <th scope="row" class="fw-bold">Created by:</th>
                                <td>{{ $task->getTaskCreatorUser() }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="fw-bold">Assigned to:</th>
                                <td>{{ $task->getAssignedUser() }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="fw-bold">Assigned to tester:</th>
                                <td>{{ $task->getAssignedTester() }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="fw-bold">Status:</th>
                                <td>{{ $task->getTaskStatusId->name ?? 'Status was deleted' }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="fw-bold">Type:</th>
                                <td>{{ $task->getTaskTypeId->name ?? 'Type was deleted' }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="fw-bold">Description:</th>
                                <td>{{ $task->description }}</td>
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
                Task: {{ $task->title }}
            </div>
            <div class="card-body bg-white p-4">
                <div class="row">
                    <div class="col-md-12">
                        <form method="post" action="{{ route('comments.store', ['task' => $task->id]) }}">
                            @csrf
                            <div class="form-group">
                                <textarea class="form-control" name="body"></textarea>
                                <input type="hidden" name="task_id" value="{{ $task->id }}" />
                            </div>
                            <div class="form-group mt-2">
                                <input type="submit" class="btn btn-success" value="Add Comment" />
                            </div>
                        </form>
                    </div>
                </div>

                @include('task.comments', ['comments' => $task->comments, 'task_id' => $task->id])
            </div>
        </div>
    </div>


@endsection
