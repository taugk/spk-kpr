<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Attempt login
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            // Redirect berdasarkan role
            return $this->authenticated($request, Auth::user());
        }

        // Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    protected function authenticated(Request $request, $user)
    {
        // Redirect berdasarkan role
        switch ($user->role) {
            case 'admin':
                return redirect()->intended('/admin/dashboard');
                break;
            case 'manajer':
            case 'manager' :
                return redirect()->intended('/manager/dashboard');
                break;
            case 'marketing':
                return redirect()->intended('/marketing/dashboard');
                break;
            default:
                return redirect()->intended('/dashboard');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
