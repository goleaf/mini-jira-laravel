@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header"><i class="fas fa-list"></i> {{ __('task_types') }}</div>

                    <div class="card-body">
                        @include('partials.flash-messages') 
                        
                        <ul class="list-group">
                            @foreach ($taskTypes as $taskType)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-tasks"></i> {{ $taskType->name }}
                                        <span class="badge bg-secondary rounded-pill ms-2">{{ $taskType->tasks->count() }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('task-types.edit', $taskType->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> {{ __('edit') }}</a>
                                        @if ($taskType->tasks->count() > 0)
                                            <span class="btn btn-sm btn-outline-danger opacity-75" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('cannot_delete_task_type_with_tasks', ['count' => $taskType->tasks->count()]) }}">
                                                <i class="fas fa-info-circle"></i> {{ __('delete') }}
                                            </span>
                                        @else
                                            <form action="{{ route('task-types.destroy', $taskType->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('task_types_confirm_delete') }}')"><i class="fas fa-trash-alt"></i> {{ __('delete') }}</button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('task-types.create') }}" class="btn btn-outline-primary mb-3 mt-4"><i class="fas fa-plus"></i> {{ __('create') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection