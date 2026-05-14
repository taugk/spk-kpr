<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash, Log};
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of users (kecuali debitur)
     */
  // Di UserController.php method index()

public function index()
{
    $users = DB::table('users')
        ->whereIn('role', ['marketing', 'admin', 'manajer'])
        ->orderBy('role')
        ->orderBy('nama_lengkap')
        ->paginate(10);

    
    
    // Konversi created_at ke Carbon
    foreach ($users as $user) {
        $user->created_at = $user->created_at ? Carbon::parse($user->created_at) : null;
    }
    
    return view('admin.pages.users.index', compact('users'));
}
    
    /**
     * Show form for creating new user
     */
    public function create()
    {
        $roles = [
            'marketing' => 'Marketing',
            'admin' => 'Admin',
            'manajer' => 'Manajer'
        ];
        
        return view('admin.pages.users.create', compact('roles'));
    }
    
    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:marketing,admin,manajer',
            'status' => 'required|in:aktif,nonaktif',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Handle foto profil
            $fotoPath = null;
            if ($request->hasFile('foto_profil')) {
                $file = $request->file('foto_profil');
                $filename = time() . '_' . $file->getClientOriginalName();
                $fotoPath = $file->storeAs('users/foto', $filename, 'public');
            }
            
            // Insert user
            $userId = DB::table('users')->insertGetId([
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'status' => $validated['status'],
                'foto_profil' => $fotoPath,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            DB::commit();
            
            return redirect()
                ->route('admin.users.index')
                ->with('success', "User {$validated['nama_lengkap']} berhasil ditambahkan");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create user error: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Gagal menambahkan user: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show form for editing user
     */
    public function edit($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        
        if (!$user) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User tidak ditemukan');
        }
        
        // Hanya bisa edit user dengan role marketing, admin, manajer
        if (!in_array($user->role, ['marketing', 'admin', 'manajer'])) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat mengedit user dengan role debitur');
        }
        
        $roles = [
            'marketing' => 'Marketing',
            'admin' => 'Admin',
            'manajer' => 'Manajer'
        ];
        
        return view('admin.pages.users.edit', compact('user', 'roles'));
    }
    
    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        
        if (!$user) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User tidak ditemukan');
        }
        
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id)
            ],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:marketing,admin,manajer',
            'status' => 'required|in:aktif,nonaktif',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);
        
        try {
            DB::beginTransaction();
            
            $updateData = [
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'status' => $validated['status'],
                'updated_at' => now()
            ];
            
            // Update password jika diisi
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }
            
            // Handle foto profil
            if ($request->hasFile('foto_profil')) {
                // Hapus foto lama jika ada
                if ($user->foto_profil && file_exists(storage_path('app/public/' . $user->foto_profil))) {
                    unlink(storage_path('app/public/' . $user->foto_profil));
                }
                
                $file = $request->file('foto_profil');
                $filename = time() . '_' . $file->getClientOriginalName();
                $fotoPath = $file->storeAs('users/foto', $filename, 'public');
                $updateData['foto_profil'] = $fotoPath;
            }
            
            DB::table('users')->where('id', $id)->update($updateData);
            
            DB::commit();
            
            return redirect()
                ->route('admin.users.index')
                ->with('success', "User {$validated['nama_lengkap']} berhasil diupdate");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update user error: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Gagal mengupdate user: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete the specified user
     */
    public function destroy($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        
        if (!$user) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User tidak ditemukan');
        }
        
        // Cegah menghapus diri sendiri
        if ($user->id == auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri');
        }
        
        try {
            // Hapus foto profil jika ada
            if ($user->foto_profil && file_exists(storage_path('app/public/' . $user->foto_profil))) {
                unlink(storage_path('app/public/' . $user->foto_profil));
            }
            
            DB::table('users')->where('id', $id)->delete();
            
            return redirect()
                ->route('admin.users.index')
                ->with('success', "User {$user->nama_lengkap} berhasil dihapus");
                
        } catch (\Exception $e) {
            Log::error('Delete user error: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
    
    /**
     * Toggle user status (aktif/nonaktif)
     */
    public function toggleStatus($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan']);
        }
        
        $newStatus = $user->status == 'aktif' ? 'nonaktif' : 'aktif';
        
        DB::table('users')->where('id', $id)->update([
            'status' => $newStatus,
            'updated_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => "Status user berhasil diubah menjadi {$newStatus}",
            'new_status' => $newStatus
        ]);
    }
}