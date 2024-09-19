<?php

    namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginRegisterController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'dashboard']);
    }

    public function dashboard()
    {
        if (Auth::check()) {
            return view('task.index');
        }

        return redirect()->route('login')
            ->withErrors(['email' => 'Please login to access the dashboard.'])
            ->onlyInput('email');
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
        $validatedData = $request->validate([
                                                'name' => 'required|string|max:250',
                                                'email' => 'required|email|max:250|unique:users',
                                                'password' => 'required|min:8|confirmed'
                                            ]);

        $user = User::create([
                                 'name' => $validatedData['name'],
                                 'email' => $validatedData['email'],
                                 'password' => Hash::make($validatedData['password'])
                             ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('tasks.index');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
                                              'email' => 'required|email',
                                              'password' => 'required'
                                          ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('tasks.index');
        }

        return back()
            ->withErrors(['email' => 'Your provided credentials do not match in our records.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withSuccess('You have logged out successfully!');
    }
}
