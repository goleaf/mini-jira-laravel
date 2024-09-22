
@extends('layouts.app')

@section('content')
<div class="mb-4 ms-0 me-0 ps-4 pe-4 pt-4 pb-3 bg-white rounded shadow">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">{{ __('user_groups') }}</h1>
        <a href="{{ route('users-groups.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>{{ __('create') }} {{ __('user_group') }}
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="text-nowrap"><i class="fas fa-heading me-2"></i>{{ __('name') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-align-left me-2"></i>{{ __('description') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-users me-2"></i>{{ __('users') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-cogs me-2"></i>{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($userGroups as $userGroup)
                    <tr>
                        <td class="text-nowrap">{{ $userGroup->name }}</td>
                        <td class="text-nowrap">{{ Str::limit($userGroup->description, 50) }}</td>
                        <td class="text-nowrap">{{ $userGroup->users_count }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('users-groups.show', $userGroup) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye me-1"></i>{{ __('show') }}
                            </a>
                            <a href="{{ route('users-groups.edit', $userGroup) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>{{ __('edit') }}
                            </a>
                            <form action="{{ route('users-groups.destroy', $userGroup) }}" method="POST" class="d-inline">
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
                        <td colspan="4" class="text-center"><i class="fas fa-exclamation-circle me-2"></i>{{ __('no_user_groups_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $userGroups->links() }}
    </div>
</div>
@endsection