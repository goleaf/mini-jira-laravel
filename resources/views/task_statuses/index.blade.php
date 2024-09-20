@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header"><i class="fas fa-tasks"></i> {{ __('task_statuses') }}</div>

                    <div class="card-body">
                        @include('partials.flash-messages') 
                        
                        <ul class="list-group">
                            @foreach ($taskStatuses as $taskStatus)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-tag"></i> {{ $taskStatus->name }}
                                        <span class="badge bg-secondary rounded-pill ms-2">{{ $taskStatus->tasks->count() }}</span>
                                    </span>
                                    <div>
                                        <a href="{{ route('task-statuses.edit', $taskStatus->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> {{ __('edit') }}</a>
                                        <form action="{{ route('task-statuses.destroy', $taskStatus->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('confirm_delete_status') }}')" {{ $taskStatus->tasks->count() > 0 ? 'disabled' : '' }}><i class="fas fa-trash-alt"></i> {{ __('delete') }}</button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('task-statuses.create') }}" class="btn btn-primary mb-3 mt-4"><i class="fas fa-plus-circle"></i> {{ __('create') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
