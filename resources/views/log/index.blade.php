@extends('layouts.app')

@section('content')
<div class="mb-4 ms-0 me-0 ps-4 pe-4 pt-4 pb-3 bg-white rounded shadow">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><i class="fas fa-history me-2"></i>{{ __('system_logs') }}</h1>
    </div>

    <form action="{{ route('logs.index') }}" method="GET" class="mb-4">
        <div class="row g-2">
            <div class="col-md-3">
                <select class="form-select" name="user_id">
                    <option value="">{{ __('select_user') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="loggable_type">
                    <option value="">{{ __('select_model_type') }}</option>
                    @foreach($modelTypes as $modelType)
                        <option value="{{ $modelType }}" {{ request('loggable_type') == $modelType ? 'selected' : '' }}>
                            {{ $modelType }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="loggable_id" value="{{ request('loggable_id') }}" placeholder="{{ __('model_id') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-filter me-1"></i>{{ __('filter') }}
                </button>
            </div>
        </div>
    </form>

    @if($logs->isEmpty())
        <div class="alert alert-info" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ __('no_logs_found') }}
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="text-nowrap"><i class="fas fa-user me-2"></i>{{ __('user') }}</th>
                        <th scope="col" class="text-nowrap"><i class="fas fa-cogs me-2"></i>{{ __('action') }}</th>
                        <th scope="col" class="text-nowrap"><i class="fas fa-cube me-2"></i>{{ __('model_type') }}</th>
                        <th scope="col" class="text-nowrap"><i class="fas fa-hashtag me-2"></i>{{ __('model_id') }}</th>
                        <th scope="col" class="text-nowrap"><i class="fas fa-calendar-alt me-2"></i>{{ __('date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td class="text-nowrap">{{ $log->user->name }}</td>
                        <td class="text-nowrap">{{ __($log->action) }}</td>
                        <td class="text-nowrap">{{ $log->loggable_type }}</td>
                        <td class="text-nowrap">{{ $log->loggable_id }}</td>
                        <td class="text-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
