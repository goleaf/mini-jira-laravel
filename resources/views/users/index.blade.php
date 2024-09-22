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
        <h1 class="mb-0"><i class="fas fa-users me-2"></i>{{ __('users') }}</h1>
        <a href="{{ route('users.create') }}" class="btn btn-outline-primary">
            <i class="fas fa-user-plus me-2"></i>{{ __('create_new_user') }}
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="text-nowrap"><i class="fas fa-user me-2"></i>{{ __('name') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-envelope me-2"></i>{{ __('email') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-briefcase me-2"></i>{{ __('work_position') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-users me-2"></i>{{ __('user_groups') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-user-shield me-2"></i>{{ __('is_admin') }}</th>
                    <th scope="col" class="text-nowrap"><i class="fas fa-cogs me-2"></i>{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="text-nowrap">{{ $user->name }}</td>
                        <td class="text-nowrap">{{ $user->email }}</td>
                        <td class="text-nowrap">{{ $user->work_position }}</td>
                        <td class="text-nowrap">
                            @if($user->userGroups->isNotEmpty())
                                @foreach($user->userGroups as $key => $userGroup)
                                    {{ $userGroup->name }}@if(!$loop->last),@endif
                                @endforeach
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $user->is_admin ? __('yes') : __('no') }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>{{ __('edit') }}
                            </a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ __('confirm_delete_user') }}')">
                                    <i class="fas fa-trash-alt me-1"></i>{{ __('delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ __('no_users_found') }}
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection