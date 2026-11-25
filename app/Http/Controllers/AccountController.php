<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email|max:255', 'password' => 'required|string|max:72']);
        $credentials['email'] = Str::lower($credentials['email']);
        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi salah.']);
        }
        $request->session()->regenerate();

        return ['redirect' => $request->user()->canAccessCms() ? '/admin' : '/saved'];
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:72|confirmed',
            'role' => ['required', Rule::in([UserRole::User->value, UserRole::Admin->value, UserRole::Roastery->value])],
        ]);
        $data['email'] = Str::lower($data['email']);
        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['redirect' => $user->canAccessCms() ? '/admin' : '/saved'], 201);
    }
}
