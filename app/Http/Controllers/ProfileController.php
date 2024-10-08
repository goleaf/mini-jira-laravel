<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProfileController extends Controller
{
    public function edit(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new ModelNotFoundException('User not found');
            }
            return view('profile.edit', ['user' => $user]);
        } catch (\Exception $e) {
            Log::error('Error in ProfileController@edit: ' . $e->getMessage());
            return redirect()->route('home')->with('error', __('error_occurred'));
        }
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new ModelNotFoundException('User not found');
            }

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'current_password' => ['required_with:password', 'string']
            ]);

            if (isset($validated['password'])) {
                if (!Hash::check($validated['current_password'], $user->password)) {
                    throw ValidationException::withMessages(['current_password' => [__('current_password_incorrect')]]);
                }
                $user->password = Hash::make($validated['password']);
            }

            $user->fill([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ])->save();

            Log::info('User profile updated', ['user_id' => $user->id]);
            return redirect()->route('profile.edit')->with('success', __('profile_updated_successfully'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            Log::error('User not found in ProfileController@update: ' . $e->getMessage());
            return redirect()->route('home')->with('error', __('user_not_found'));
        } catch (\Exception $e) {
            Log::error('Error in ProfileController@update: ' . $e->getMessage());
            return back()->with('error', __('error_updating_profile'));
        }
    }
}
