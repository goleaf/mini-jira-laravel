<!-- resources/views/task_types/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container bg-white border p-3">
        <div class="row">
            <div class="col-md-12">
                <h2>Create new task type</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('task-types.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create task type</button>
                </form>
            </div>
        </div>
    </div>
@endsection
