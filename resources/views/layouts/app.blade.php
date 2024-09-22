<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('app_title') }}</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container{{ Auth::check() ? '-fluid' : '' }}">
            @auth
                <a class="navbar-brand text-primary" href="{{ url('/') }}">
                    <i class="fas fa-tasks"></i> {{ __('app_title') }}
                </a>
            @else
                <a class="navbar-brand text-primary mx-auto" href="{{ url('/') }}">
                    <i class="fas fa-tasks"></i> {{ __('app_title') }}
                </a>
            @endauth
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link text-primary" href="{{ route('tasks.create') }}"><i class="fas fa-plus"></i> {{ __('task_create') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-primary" href="{{ route('user.dashboard', ['user' => auth()->id()]) }}">
                                <i class="fas fa-tachometer-alt"></i> {{ __('user_dashboard', ['name' => ucwords(auth()->user()->name)]) }}
                            </a>
                        </li>
                    @endauth
                </ul>

                <ul class="navbar-nav ms-auto">
                    @auth
                        @if(Auth::user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link text-primary" href="{{ route('logs.index') }}"><i class="fas fa-clipboard-list"></i> {{ __('logs') }}</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-primary" href="#" id="taskManagementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-cogs"></i> {{ __('task_management') }}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="taskManagementDropdown">
                                    <li><a class="dropdown-item" href="{{ route('task-types.index') }}"><i class="fas fa-list-ul me-2"></i>{{ __('task_types') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('task-statuses.index') }}"><i class="fas fa-chart-bar me-2"></i>{{ __('task_statuses') }}</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-primary" href="#" id="userManagementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-users-cog"></i> {{ __('user_management') }}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="userManagementDropdown">
                                    <li><a class="dropdown-item" href="{{ route('users.index') }}"><i class="fas fa-users me-2"></i>{{ __('users') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('users-groups.index') }}"><i class="fas fa-user-friends me-2"></i>{{ __('user_groups') }}</a></li>
                                </ul>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link text-primary" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user-edit"></i> {{ Auth::user()->name }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link text-primary"><i class="fas fa-sign-out-alt"></i> {{ __('logout') }}</button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>
</div>

<footer class="text-center py-3">
    <p>{{ __('created_with_love') }} <i class="fas fa-heart text-danger"></i></p>
</footer>

@stack('scripts')
</body>
</html>