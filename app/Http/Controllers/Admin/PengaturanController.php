<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Cache, DB, Log};

use App\Http\Controllers\Controller;

class PengaturanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Clear cache untuk memastikan data terbaru
        Cache::forget('pengaturan_sistem');
        
        $pengaturan = DB::table('pengaturan')->orderBy('kunci')->get();
        
        return view('admin.pages.pengaturan.index', compact('pengaturan'));
    }

    /**
     * Update the specified resource in storage (mass update).
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'pengaturan' => ['required', 'array'],
                'pengaturan.*.kunci' => ['required', 'string'],
                'pengaturan.*.nilai' => ['nullable', 'string'],
            ]);

            $updatedCount = 0;
            $errors = [];
            
            foreach ($request->input('pengaturan') as $item) {
                try {
                    $updated = DB::table('pengaturan')
                        ->where('kunci', $item['kunci'])
                        ->update([
                            'nilai' => $item['nilai'] ?? '',
                            'updated_at' => now(),
                        ]);
                    
                    if ($updated) {
                        $updatedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = $item['kunci'];
                    Log::error("Gagal update pengaturan {$item['kunci']}: " . $e->getMessage());
                }
            }

            // Clear cache setelah update
            Cache::forget('pengaturan_sistem');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$updatedCount} pengaturan berhasil diperbarui",
                    'updated_count' => $updatedCount,
                    'errors' => $errors
                ]);
            }

            if (count($errors) > 0) {
                return redirect()
                    ->route('admin.pengaturan.index')
                    ->with('warning', "{$updatedCount} pengaturan berhasil diperbarui, namun gagal untuk: " . implode(', ', $errors));
            }

            return redirect()
                ->route('admin.pengaturan.index')
                ->with('success', "{$updatedCount} pengaturan berhasil diperbarui.");
                
        } catch (\Exception $e) {
            Log::error('Error update pengaturan massal: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update single setting
     */
    public function updateSingle(Request $request, $kunci)
    {
        try {
            $request->validate([
                'nilai' => ['nullable', 'string'],
            ]);

            $updated = DB::table('pengaturan')
                ->where('kunci', $kunci)
                ->update([
                    'nilai' => $request->input('nilai', ''),
                    'updated_at' => now(),
                ]);

            if (!$updated) {
                throw new \Exception("Pengaturan dengan kunci '{$kunci}' tidak ditemukan");
            }

            // Clear cache setelah update
            Cache::forget('pengaturan_sistem');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pengaturan berhasil diperbarui',
                    'kunci' => $kunci,
                    'nilai' => $request->input('nilai')
                ]);
            }

            return redirect()
                ->route('admin.pengaturan.index')
                ->with('success', "Pengaturan {$kunci} berhasil diperbarui.");
                
        } catch (\Exception $e) {
            Log::error("Error update pengaturan {$kunci}: " . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get single setting value (for AJAX)
     */
    public function show($kunci)
    {
        try {
            $setting = DB::table('pengaturan')->where('kunci', $kunci)->first();
            
            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengaturan tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $setting
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan'
            ], 500);
        }
    }
}