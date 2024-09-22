@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('edit_user') }}: {{ $user->name }}</h1>
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('name') }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('email') }}</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="user_group_id" class="form-label">{{ __('user_group') }}</label>
            <select class="form-select @error('user_group_id') is-invalid @enderror" id="user_group_id" name="user_group_id" required>
                <option value="">{{ __('select_user_group') }}</option>
                @foreach($userGroups as $userGroup)
                    <option value="{{ $userGroup->id }}" {{ old('user_group_id', $user->user_group_id) == $userGroup->id ? 'selected' : '' }}>
                        {{ $userGroup->name }}
                    </option>
                @endforeach
            </select>
            @error('user_group_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input @error('is_admin') is-invalid @enderror" id="is_admin" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_admin">{{ __('is_admin') }}</label>
            @error('is_admin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('new_password') }}</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
            <small class="form-text text-muted">{{ __('leave_blank_to_keep_current_password') }}</small>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('confirm_new_password') }}</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>
        <button type="submit" class="btn btn-primary">{{ __('update_user') }}</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('cancel') }}</a>
    </form>
</div>
@endsection