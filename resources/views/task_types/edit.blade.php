@extends('layouts.app')

@section('content')
    <div class="container bg-white border p-3">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Edit Task Type - {{ $taskType->name }}</h2>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card-body">
                <form action="{{ route('task-types.update', $taskType->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="form-label">Title:</label>
                        <input type="text" class="form-control"  name="name" value="{{ $taskType->name }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
