@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4 ms-0 me-0 ps-4 pe-4 pt-4 pb-3 bg-white rounded shadow">
        <h1 class="mb-4"><i class="fas fa-edit me-2"></i>{{ __('edit_user_group') }}</h1>
        <form action="{{ route('users-groups.update', $userGroup) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <label for="name" class="col-sm-3 col-form-label"><i class="fas fa-heading me-2"></i>{{ __('name') }}</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $userGroup->name) }}" required maxlength="255">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="description" class="col-sm-3 col-form-label"><i class="fas fa-align-left me-2"></i>{{ __('description') }}</label>
                <div class="col-sm-9">
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6">{{ old('description', $userGroup->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label"><i class="fas fa-users me-2"></i>{{ __('users') }}</label>
                <div class="col-sm-9">
                    @error('users')
                        <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
                    @enderror
                    
                    <h4 class="mt-3"><i class="fas fa-users-cog me-2"></i>{{ __('assigned_users') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-nowrap"><i class="fas fa-user me-2"></i>{{ __('name') }}</th>
                                    <th scope="col" class="text-nowrap"><i class="fas fa-envelope me-2"></i>{{ __('email') }}</th>
                                    <th scope="col" class="text-nowrap"><i class="fas fa-briefcase me-2"></i>{{ __('work_position') }}</th>
                                    <th scope="col" class="text-nowrap"><i class="fas fa-check-square me-2"></i>{{ __('select') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userGroup->users->sortBy('name') as $user)
                                    <tr>
                                        <td class="text-nowrap">{{ $user->name }}</td>
                                        <td class="text-nowrap">{{ $user->email }}</td>
                                        <td class="text-nowrap">{{ $user->work_position }}</td>
                                        <td class="text-nowrap">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="users[]" value="{{ $user->id }}" id="user_{{ $user->id }}" checked>
                                                <label class="form-check-label" for="user_{{ $user->id }}">
                                                    {{ __('select') }}
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <h4 class="mt-3"><i class="fas fa-users me-2"></i>{{ __('all_users') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-nowrap"><i class="fas fa-user me-2"></i>{{ __('name') }}</th>
                                    <th scope="col" class="text-nowrap"><i class="fas fa-envelope me-2"></i>{{ __('email') }}</th>
                                    <th scope="col" class="text-nowrap"><i class="fas fa-briefcase me-2"></i>{{ __('work_position') }}</th>
                                    <th scope="col" class="text-nowrap"><i class="fas fa-check-square me-2"></i>{{ __('select') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users->sortBy('name') as $user)
                                    @if(!$userGroup->users->contains($user))
                                        <tr>
                                            <td class="text-nowrap">{{ $user->name }}</td>
                                            <td class="text-nowrap">{{ $user->email }}</td>
                                            <td class="text-nowrap">{{ $user->work_position }}</td>
                                            <td class="text-nowrap">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="users[]" value="{{ $user->id }}" id="user_{{ $user->id }}"
                                                        {{ in_array($user->id, old('users', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="user_{{ $user->id }}">
                                                        {{ __('select') }}
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>{{ __('update') }}</button>
                    <a href="{{ route('users-groups.index') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>{{ __('cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection