<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KriteriaSkalaController extends Controller
{
    public function index()
    {
        $skala = DB::table('kriteria_skala')
            ->join('kriteria', 'kriteria.id', '=', 'kriteria_skala.kriteria_id')
            ->select(
                'kriteria_skala.id',
                'kriteria_skala.kriteria_id',
                'kriteria_skala.skor',
                'kriteria_skala.keterangan',
                'kriteria_skala.nilai_min',
                'kriteria_skala.nilai_max',
                'kriteria.kode_kriteria',
                'kriteria.nama_kriteria'
            )
            ->orderBy('kriteria.urutan')
            ->orderByDesc('kriteria_skala.skor')
            ->paginate(10);

        return view('admin.pages.kriteria-skala.index', compact('skala'));
    }

    public function create()
    {
        $kriteria = DB::table('kriteria')
            ->where('aktif', 1)
            ->orderBy('urutan')
            ->get();

        return view('admin.pages.kriteria-skala.create', compact('kriteria'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kriteria_id' => ['required', 'integer', 'exists:kriteria,id'],
            'skor' => ['required', 'integer', 'min:1', 'max:100'],
            'keterangan' => ['required', 'string', 'max:200'],
            'nilai_min' => ['nullable', 'numeric'],
            'nilai_max' => ['nullable', 'numeric'],
        ]);

        DB::table('kriteria_skala')->insert($data);

        return redirect()
            ->route('admin.kriteria-skala.index')
            ->with('success', 'Sub kriteria berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $skala = DB::table('kriteria_skala')
            ->where('id', $id)
            ->firstOrFail();

        $kriteria = DB::table('kriteria')
            ->where('aktif', 1)
            ->orderBy('urutan')
            ->get();

        return view('admin.pages.kriteria-skala.edit', compact('skala', 'kriteria'));
    }

    public function update(Request $request, int $id)
    {
        $skala = DB::table('kriteria_skala')
            ->where('id', $id)
            ->firstOrFail();

        $data = $request->validate([
            'kriteria_id' => ['required', 'integer', 'exists:kriteria,id'],
            'skor' => ['required', 'integer', 'min:1', 'max:100'],
            'keterangan' => ['required', 'string', 'max:200'],
            'nilai_min' => ['nullable', 'numeric'],
            'nilai_max' => ['nullable', 'numeric'],
        ]);

        DB::table('kriteria_skala')
            ->where('id', $skala->id)
            ->update($data);

        return redirect()
            ->route('admin.kriteria-skala.index')
            ->with('success', 'Sub kriteria berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('kriteria_skala')
            ->where('id', $id)
            ->delete();

        return redirect()
            ->route('admin.kriteria-skala.index')
            ->with('success', 'Sub kriteria berhasil dihapus.');
    }
}