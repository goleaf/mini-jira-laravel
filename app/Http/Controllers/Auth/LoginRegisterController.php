<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LogsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class LoginRegisterController extends Controller
{
    use ThrottlesLogins;

    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'dashboard']);
        $this->middleware('auth')->only('dashboard');
    }

    public function dashboard()
    {
        return view('task.index');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:250', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'email' => ['required', 'string', 'email', 'max:250', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
        ]);

        $user = User::create([
            'name' => strip_tags($validated['name']),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        LogsController::log(__('user_registered') . ': ' . $user->name, $user->id, 'user');

        return redirect()->route('tasks.index')->with('success', __('registration_successful'));
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            
            LogsController::log(__('user_logged_in') . ': ' . Auth::user()->name, Auth::id(), 'user');

            return redirect()->intended(route('tasks.index'))->with('success', __('login_successful'));
        }

        $this->incrementLoginAttempts($request);

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        $userName = Auth::user()->name;
        $userId = Auth::id();
        
        LogsController::log(__('user_logged_out') . ': ' . $userName, $userId, 'user');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();


        return redirect()->route('login')->with('success', __('logout_successful'));
    }

    protected function username()
    {
        return 'email';
    }

    protected function maxAttempts()
    {
        return 5; 
    }

    protected function decayMinutes()
    {
        return 15;
    }
}
