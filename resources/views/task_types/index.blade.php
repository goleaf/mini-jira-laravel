@extends('layouts.app')

@section('content')
    <div class="container bg-white border p-3 mb-4 p-4 pb-3 bg-white rounded shadow">
        <div class="row">
            <div class="col-md-12">
                <h2>Task Types</h2>
                <a href="{{ route('task-types.create') }}" class="btn btn-primary">Create new task type</a>
                <table class="table mt-3">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($taskTypes as $taskType)
                        <tr>
                            <td class="align-middle">{{ $taskType->name }}</td>
                            <td class="align-middle">
                                <a href="{{ route('task-types.edit', $taskType->id) }}" class="btn btn-primary">Edit</a>
                                <form action="{{ route('task-types.destroy', $taskType->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this task type?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
