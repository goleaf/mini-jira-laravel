@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas {{ isset($taskStatus) ? 'fa-edit' : 'fa-plus-circle' }}"></i>
                        {{ isset($taskStatus) ? __('edit') : __('create') }}
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li><i class="fas fa-exclamation-circle"></i> {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ isset($taskStatus) ? route('task-statuses.update', $taskStatus->id) : route('task-statuses.store') }}" method="POST">
                            @csrf
                            @if(isset($taskStatus))
                                @method('PUT')
                            @endif
                            <div class="mb-3">
                                <label for="name" class="form-label"><i class="fas fa-tag"></i> {{ __('name') }}:</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $taskStatus->name ?? old('name') }}" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> 
                                    {{ isset($taskStatus) ? __('update') : __('create') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
