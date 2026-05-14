<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\PengajuanAdminService;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function __construct(private readonly PengajuanAdminService $pengajuanService) {}

    public function index(Request $request)
    {
        
        return view('admin.pages.pengajuan.index', [
            'pengajuan' => $this->pengajuanService->paginate($request->only(['status', 'keyword'])),
        ]);
    }

    public function show(int $id)
    {
        return view('admin.pages.pengajuan.show', [
            'pengajuan' => $this->pengajuanService->detail($id),
        ]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', 'string'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->pengajuanService->ubahStatus($id, $data['status'], $data['keterangan'] ?? null);

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
