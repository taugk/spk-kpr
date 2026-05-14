<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DokumenController extends Controller
{
    public function verify(Request $request, int $id)
    {
        $data = $request->validate([
            'status_verifikasi' => ['required', 'in:belum_diperiksa,valid,tidak_valid,perlu_revisi'],
            'catatan_verifikasi' => ['nullable', 'string', 'max:500'],
        ]);

        DB::table('dokumen_pengajuan')->where('id', $id)->update([
            'status_verifikasi' => $data['status_verifikasi'],
            'catatan_verifikasi' => $data['catatan_verifikasi'] ?? null,
            'diperiksa_oleh' => auth()->id(),
            'tgl_diperiksa' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Status dokumen berhasil diperbarui.');
    }
}
