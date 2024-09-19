@extends('layouts.app')

@section('content')

    {{-- tasks block --}}
    <div class="mb-4 ms-5 me-5 ps-4 pe-4 pb-3 bg-white rounded shadow">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card-header text-white">
                    {{ __('tasks.task_details') }}
                </div>
                <div class="card-body bg-light">
                    <div class="d-flex justify-content-end mt-3 p-4">
                        <a href="{{ route('tasks.edit', ['task' => $task->id]) }}" class="btn btn-primary me-1">{{ __('tasks.edit') }}</a>
                        <form method="post" action="{{ route('tasks.destroy', ['task' => $task->id]) }}" onsubmit="return confirm('{{ __('tasks.confirm_deletion') }}')">
                            @csrf
                            <button type="submit" class="btn btn-danger">{{ __('tasks.delete') }}</button>
                        </form>
                    </div>
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th scope="row" class="fw-bold">{{ __('tasks.task_title') }}:</th>
                            <td>{{ $task->title }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold">{{ __('tasks.date_created') }}</th>
                            <td>{{ date_format($task->created_at, 'Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold">{{ __('tasks.deadline_date') }}</th>
                            <td>{{ date('Y-m-d', strtotime($task->task_deadline_date)) }} ({{ $differenceInDays }})</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold">{{ __('tasks.created_by') }}</th>
                            <td>{{ $task->getTaskCreatorUser() }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold">{{ __('tasks.assigned_to') }}</th>
                            <td>{{ $task->getAssignedUser() }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold">{{ __('tasks.assigned_to_tester') }}</th>
                            <td>{{ $task->getAssignedTester() }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold">{{ __('tasks.status') }}</th>
                            <td>{{ $task->getTaskStatusId->name ?? __('tasks.status_deleted') }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold">{{ __('tasks.task_type') }}</th>
                            <td>{{ $task->getTaskTypeId->name ?? __('tasks.type_deleted') }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-bold">{{ __('tasks.details') }}</th>
                            <td>{{ $task->description }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- comments block --}}
    <div class="container mt-2">
        <div class="card">
            <div class="card-header p-4">
                {{ __('tasks.task') }}: {{ $task->title }}
            </div>
            <div class="card-body bg-white p-4">
                <div class="row">
                    <div class="col-md-12">
                        <form method="post" action="{{ route('comments.store', ['task' => $task->id]) }}">
                            @csrf
                            <div class="form-group">
                                <textarea class="form-control" name="body" required></textarea>
                                <input type="hidden" name="task_id" value="{{ $task->id }}" />
                            </div>
                            <div class="form-group mt-2">
                                <input type="submit" class="btn btn-success" value="{{ __('tasks.add_comment') }}" />
                            </div>
                        </form>
                    </div>
                </div>

                @include('task.comments', ['comments' => $task->comments, 'task_id' => $task->id])
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    try {
                        // Additional client-side validation can be added here
                        form.submit();
                    } catch (error) {
                        console.error('Error submitting form:', error);
                    } finally {
                        // Log the action
                        fetch('{{ route("log.action") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                action: 'form_submit_attempt',
                                user_id: '{{ Auth::id() }}',
                                form_action: form.action
                            })
                        });
                    }
                });
            });
        });
    </script>
    @endpush

@endsection
