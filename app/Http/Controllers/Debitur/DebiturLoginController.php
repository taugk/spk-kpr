<?php

namespace App\Http\Controllers\Debitur;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Validator};

use App\Http\Controllers\Controller;

class DebiturLoginController extends Controller
{
    public function showLogin(){
        if (auth()->check() && auth()->user()->role == 'debitur') {
            return redirect()->route('debitur.dashboard');
        }
        
        return view('landing-page.auth.login');
    }

    public function login(Request $request){
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Cek user dari database langsung
        $user = DB::table('users')
                ->where('email', $request->email)
                ->where('role', 'debitur')
                ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Akun debitur tidak ditemukan.',
            ])->onlyInput('email');
        }

        // Cek status akun
        if ($user->status == 'nonaktif') {
            return back()->withErrors([
                'email' => 'Akun debitur Anda sedang dinonaktifkan.',
            ])->onlyInput('email');
        }

        // Verifikasi password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Password salah.',
            ])->onlyInput('email');
        }

        // Login menggunakan Auth (tetap pakai Auth untuk session)
        // Kita perlu mengambil model User untuk Auth::login
        $userModel = \App\Models\User::find($user->id);
        Auth::login($userModel, $request->remember);
        
        $request->session()->regenerate();
        
        // Update last_login
        DB::table('users')->where('id', $user->id)->update([
            'last_login' => now()
        ]);

        return redirect()->route('debitur.dashboard');
    }

    public function registerForm(){
        if (auth()->check()) {
            return redirect()->route('debitur.dashboard');
        }
        
        return view('landing-page.auth.register');
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'terms' => 'accepted',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Insert menggunakan DB facade langsung
        $userId = DB::table('users')->insertGetId([
            'nama_lengkap' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'debitur',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Jika ada tabel profil debitur (opsional)
        if ($request->filled('no_telepon') || $request->filled('alamat')) {
            DB::table('debitur_profiles')->insert([
                'user_id' => $userId,
                'no_telepon' => $request->no_telepon,
                'alamat' => $request->alamat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Login otomatis setelah registrasi (ambil user yang baru dibuat untuk Auth)
        $userModel = \App\Models\User::find($userId);
        Auth::login($userModel);
        
        $request->session()->regenerate();

        return redirect()->route('debitur.dashboard')
                        ->with('success', 'Selamat datang, ' . $request->name . '! Registrasi berhasil.');
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }
}