@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header"><i class="fas fa-list"></i> {{ __('task_statuses') }}</div>

                    <div class="card-body">
                        @include('partials.flash-messages') 
                        
                        <ul class="list-group">
                            @foreach ($taskStatuses as $taskStatus)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-tag"></i> {{ $taskStatus->name }}
                                        <span class="badge bg-secondary rounded-pill ms-2">{{ $taskStatus->tasks->count() }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('task-statuses.edit', $taskStatus->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> {{ __('edit') }}</a>
                                        @if ($taskStatus->tasks->count() > 0)
                                            @php
                                                $tooltipText = __('cannot_delete_status_with_tasks', ['count' => $taskStatus->tasks->count()]);
                                            @endphp
                                            <span class="btn btn-sm btn-outline-danger opacity-75" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $tooltipText }}">
                                                <i class="fas fa-info-circle"></i> {{ __('delete') }}
                                            </span>
                                        @else
                                            <form action="{{ route('task-statuses.destroy', $taskStatus->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('confirm_delete_status') }}')"><i class="fas fa-trash-alt"></i> {{ __('delete') }}</button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('task-statuses.create') }}" class="btn btn-primary mb-3 mt-4"><i class="fas fa-plus"></i> {{ __('create') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
