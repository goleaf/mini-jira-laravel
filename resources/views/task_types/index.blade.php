@extends('layouts.app')

@section('content')
    <div class="container bg-white border p-3 mb-4 p-4 pb-3 bg-white rounded shadow">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('task_types.title') }}</h2>
                <a href="{{ route('task-types.create') }}" class="btn btn-primary">{{ __('task_types.create_new') }}</a>
                <table class="table mt-3">
                    <thead>
                    <tr>
                        <th>{{ __('task_types.table.title') }}</th>
                        <th>{{ __('task_types.table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($taskTypes as $taskType)
                        <tr>
                            <td class="align-middle">{{ $taskType->name }}</td>
                            <td class="align-middle">
                                <a href="{{ route('task-types.edit', $taskType->id) }}" class="btn btn-primary">{{ __('task_types.edit') }}</a>
                                <form action="{{ route('task-types.destroy', $taskType->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('task_types.confirm_delete') }}')">{{ __('task_types.delete') }}</button>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                if (!confirm('{{ __('task_types.confirm_delete') }}')) {
                    return;
                }
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    });
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const result = await response.json();
                    if (result.success) {
                        alert('{{ __('task_type_controller.type_deleted') }}');
                        window.location.reload();
                    } else {
                        throw new Error(result.message || 'Unknown error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('{{ __('general.error') }}');
                } finally {
                    console.log('Form submission attempt finished.');
                }
            });
        });
    });
</script>
@endpush
