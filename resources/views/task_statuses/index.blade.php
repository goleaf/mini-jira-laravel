@extends('layouts.app')

@section('content')
    <div class="container bg-white border p-3 mb-4 p-4 pb-3 bg-white rounded shadow">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('messages.task_statuses') }}</h2>
                <a href="{{ route('task-statuses.create') }}" class="btn btn-primary">{{ __('messages.create_new_task_status') }}</a>
                <table class="table mt-3">
                    <thead>
                    <tr>
                        <th>{{ __('messages.title') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($taskStatuses as $taskStatus)
                        <tr>
                            <td class="align-middle">{{ $taskStatus->name }}</td>
                            <td class="align-middle">
                                <a href="{{ route('task-statuses.edit', $taskStatus->id) }}" class="btn btn-primary">{{ __('messages.edit') }}</a>
                                <form action="{{ route('task-statuses.destroy', $taskStatus->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('messages.confirm_delete_task_status') }}')">{{ __('messages.delete') }}</button>
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
