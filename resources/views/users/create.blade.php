@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4 ms-0 me-0 ps-4 pe-4 pt-4 pb-3 bg-white rounded shadow">
        <h1 class="mb-4"><i class="fas fa-user-plus me-2"></i>{{ __('create_user') }}</h1>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <label for="name" class="col-sm-3 col-form-label"><i class="fas fa-user me-2"></i>{{ __('name') }}</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="email" class="col-sm-3 col-form-label"><i class="fas fa-envelope me-2"></i>{{ __('email') }}</label>
                <div class="col-sm-9">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
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
                            <input class="form-check-input" type="checkbox" name="user_groups[]" value="{{ $userGroup->id }}" id="userGroup{{ $userGroup->id }}" {{ in_array($userGroup->id, old('user_groups', [])) ? 'checked' : '' }}>
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
                <label for="work_position" class="col-sm-3 col-form-label"><i class="fas fa-briefcase me-2"></i>{{ __('work_position') }}</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control @error('work_position') is-invalid @enderror" id="work_position" name="work_position" value="{{ old('work_position') }}" required>
                    @error('work_position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input @error('is_admin') is-invalid @enderror" id="is_admin" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_admin"><i class="fas fa-user-shield me-2"></i>{{ __('is_admin') }}</label>
                    </div>
                    @error('is_admin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="password" class="col-sm-3 col-form-label"><i class="fas fa-lock me-2"></i>{{ __('password') }}</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="password_confirmation" class="col-sm-3 col-form-label"><i class="fas fa-lock me-2"></i>{{ __('confirm_password') }}</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>{{ __('create') }}</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>{{ __('cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection