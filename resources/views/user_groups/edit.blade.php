@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ __('edit_user_group') }}</h1>
        <form action="{{ route('admin.user_groups.update', $userGroup) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('name') }}</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $userGroup->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">{{ __('description') }}</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $userGroup->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">{{ __('update') }}</button>
            <a href="{{ route('admin.user_groups.index') }}" class="btn btn-secondary">{{ __('cancel') }}</a>
        </form>
    </div>
@endsection