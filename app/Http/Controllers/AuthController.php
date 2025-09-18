<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    // Login
    public function showLogin() { return view('auth.login'); }

public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        $user = Auth::user();

        // 🔑 Redirect sesuai role
        if ($user->role === 'admin') {
            return redirect()->intended('/admin');
        }

        return redirect()->intended(route('home'));
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
}


    // Register
    public function showRegister() { return view('auth.register'); }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255', Rule::unique('users','email')],
            'phone'    => ['nullable','string','max:30'],
            'password' => ['required','confirmed','min:6'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => 'user',
            'is_active'=> true,
        ]);

        Auth::login($user);
        return redirect()->route('home');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    // Forgot & Reset
    public function showForgot() { return view('auth.forgot'); }

    public function sendForgot(Request $request)
    {
        $request->validate(['email' => ['required','email']]);
        Password::sendResetLink($request->only('email'));
        return back()->with('status','Link reset password telah dikirim jika email terdaftar.');
    }

    public function showReset(string $token)
    {
        return view('auth.reset', ['token' => $token, 'email' => request('email')]);
    }

    public function doReset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $status = Password::reset(
            $request->only('email','password','password_confirmation','token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status','Password berhasil direset.')
            : back()->withErrors(['email' => 'Token reset tidak valid atau kedaluwarsa.']);
    }
}
