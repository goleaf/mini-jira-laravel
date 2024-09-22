@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4 ms-0 me-0 ps-4 pe-4 pt-4 pb-3 bg-white rounded shadow">
        <h1 class="mb-4"><i class="fas fa-plus-circle me-2"></i>{{ __('create_user_group') }}</h1>
        <form action="{{ route('users-groups.store') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <label for="name" class="col-sm-3 col-form-label"><i class="fas fa-heading me-2"></i>{{ __('name') }}</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="description" class="col-sm-3 col-form-label"><i class="fas fa-align-left me-2"></i>{{ __('description') }}</label>
                <div class="col-sm-9">
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="users" class="col-sm-3 col-form-label"><i class="fas fa-users me-2"></i>{{ __('users') }}</label>
                <div class="col-sm-9">
                    <select multiple class="form-select @error('users') is-invalid @enderror" id="users" name="users[]">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ in_array($user->id, old('users', [])) ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('users')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="work_position" class="col-sm-2 col-form-label">{{ __('work_position') }}</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control @error('work_position') is-invalid @enderror" id="work_position" name="work_position" value="{{ old('work_position') }}" required>
                    @error('work_position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>{{ __('create') }}</button>
                    <a href="{{ route('users-groups.index') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>{{ __('cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection