@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('edit_user') }}: {{ $user->name }}</h1>
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row mb-3">
            <label for="name" class="col-sm-2 col-form-label">{{ __('name') }}</label>
            <div class="col-sm-10">
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <label for="email" class="col-sm-2 col-form-label">{{ __('email') }}</label>
            <div class="col-sm-10">
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">{{ __('user_groups') }}</label>
            <div class="col-sm-10">
                @foreach($userGroups as $userGroup)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="user_groups[]" value="{{ $userGroup->id }}" id="userGroup{{ $userGroup->id }}" {{ in_array($userGroup->id, old('user_groups', $user->userGroups->pluck('id')->toArray())) ? 'checked' : '' }}>
                        <label class="form-check-label" for="userGroup{{ $userGroup->id }}">
                            {{ $userGroup->name }}
                        </label>
                    </div>
                @endforeach
                @error('user_groups')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <label for="work_position" class="col-sm-2 col-form-label">{{ __('work_position') }}</label>
            <div class="col-sm-10">
                <input type="text" class="form-control @error('work_position') is-invalid @enderror" id="work_position" name="work_position" value="{{ old('work_position', $user->work_position) }}" required>
                @error('work_position')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-2">{{ __('is_admin') }}</div>
            <div class="col-sm-10">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input @error('is_admin') is-invalid @enderror" id="is_admin" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_admin">{{ __('is_admin') }}</label>
                    @error('is_admin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label for="password" class="col-sm-2 col-form-label">{{ __('password') }}</label>
            <div class="col-sm-10">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                <small class="form-text text-muted">{{ __('leave_blank_to_keep_current_password') }}</small>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <label for="password_confirmation" class="col-sm-2 col-form-label">{{ __('confirm_password') }}</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10 offset-sm-2">
                <button type="submit" class="btn btn-primary">{{ __('update') }}</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection