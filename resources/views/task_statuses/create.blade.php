@extends('layouts.app')

@section('content')
    <div class="container bg-white border p-3">
        <h1>Create new task status</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <form action="{{ route('task-statuses.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Title:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Task Status</button>
        </form>
    </div>
@endsection
