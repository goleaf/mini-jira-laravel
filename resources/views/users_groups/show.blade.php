@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4 ms-0 me-0 ps-4 pe-4 pt-4 pb-3 bg-white rounded shadow">
        <h1 class="mb-4"><i class="fas fa-users-cog me-2"></i>{{ __('user_group_details') }}</h1>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-heading me-2"></i>{{ $userGroup->name }}</h5>
                <p class="card-text"><i class="fas fa-align-left me-2"></i>{{ $userGroup->description }}</p>
            </div>
        </div>

        <h2 class="mb-3"><i class="fas fa-users me-2"></i>{{ __('users_in_group') }}</h2>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th><i class="fas fa-user me-2"></i>{{ __('name') }}</th>
                        <th><i class="fas fa-envelope me-2"></i>{{ __('email') }}</th>
                        <th><i class="fas fa-briefcase me-2"></i>{{ __('work_position') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($userGroup->users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->work_position }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">{{ __('no_users_in_group') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <a href="{{ route('users-groups.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>{{ __('back_to_list') }}
            </a>
            <a href="{{ route('users-groups.edit', $userGroup) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>{{ __('edit') }}
            </a>
        </div>
    </div>
</div>
@endsection