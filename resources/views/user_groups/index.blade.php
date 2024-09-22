@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ __('user_groups') }}</h1>
        <a href="{{ route('admin.user_groups.create') }}" class="btn btn-primary mb-3">
            <i class="fas fa-plus-circle me-2"></i>{{ __('create_new_user_group') }}
        </a>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">{{ __('name') }}</th>
                        <th scope="col">{{ __('description') }}</th>
                        <th scope="col">{{ __('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($userGroups as $userGroup)
                        <tr>
                            <td>{{ $userGroup->name }}</td>
                            <td>{{ $userGroup->description }}</td>
                            <td>
                                <a href="{{ route('admin.user_groups.edit', $userGroup) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i>{{ __('edit') }}
                                </a>
                                <form action="{{ route('admin.user_groups.destroy', $userGroup) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('confirm_delete') }}')">
                                        <i class="fas fa-trash-alt me-1"></i>{{ __('delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">{{ __('no_user_groups_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
