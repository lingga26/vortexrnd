<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Allow login with username or email
        $field = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $credentials['username'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $u = Auth::user();
            if ((isset($u->aktif) && !$u->aktif) || ($u->client && !$u->client->aktif)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['username' => 'Akun atau client Anda sedang dinonaktifkan.'])->onlyInput('username');
            }

            // User dari client yang punya subdomain tidak boleh login di domain utama.
            $base   = strtolower((string) parse_url(config('app.url'), PHP_URL_HOST));
            $host   = strtolower($request->getHost());
            $subnya = $u->client?->subdomain;

            if ($base !== '' && ($host === $base || $host === 'www.' . $base)
                && !$u->isSuperAdmin() && !empty($subnya)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Akun ini hanya dapat login di ' . $request->getScheme() . '://' . $subnya . '.' . $base,
                ])->onlyInput('username');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}