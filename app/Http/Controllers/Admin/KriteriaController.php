<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria = DB::table('kriteria')
            ->orderBy('urutan')
            ->paginate(10);

        return view('admin.pages.kriteria.index', compact('kriteria'));
    }

    public function create()
    {
        return view('admin.pages.kriteria.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_kriteria' => ['required', 'string', 'max:10', 'unique:kriteria,kode_kriteria'],
            'nama_kriteria' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'tipe' => ['required', 'in:benefit,cost'],
            'bobot' => ['required', 'numeric', 'min:0', 'max:1'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'nilai_min' => ['nullable', 'numeric'],
            'nilai_max' => ['nullable', 'numeric'],
            'urutan' => ['required', 'integer', 'min:1'],
            'aktif' => ['nullable', 'boolean'],
        ]);

        $data['aktif'] = $request->has('aktif') ? 1 : 0;
        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table('kriteria')->insert($data);

        return redirect()
            ->route('admin.kriteria.index')
            ->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function show(int $id)
    {
        $kriteria = DB::table('kriteria')->where('id', $id)->firstOrFail();

        $skala = DB::table('kriteria_skala')
            ->where('kriteria_id', $id)
            ->orderByDesc('skor')
            ->get();

        return view('admin.pages.kriteria.show', compact('kriteria', 'skala'));
    }

    public function edit(int $id)
    {
        $kriteria = DB::table('kriteria')->where('id', $id)->firstOrFail();

        return view('admin.pages.kriteria.edit', compact('kriteria'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'kode_kriteria' => ['required', 'string', 'max:10', 'unique:kriteria,kode_kriteria,' . $id],
            'nama_kriteria' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'tipe' => ['required', 'in:benefit,cost'],
            'bobot' => ['required', 'numeric', 'min:0', 'max:1'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'nilai_min' => ['nullable', 'numeric'],
            'nilai_max' => ['nullable', 'numeric'],
            'urutan' => ['required', 'integer', 'min:1'],
            'aktif' => ['nullable', 'boolean'],
        ]);

        $data['aktif'] = $request->has('aktif') ? 1 : 0;
        $data['updated_at'] = now();

        DB::table('kriteria')->where('id', $id)->update($data);

        return redirect()
            ->route('admin.kriteria.index')
            ->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('kriteria')->where('id', $id)->delete();

        return redirect()
            ->route('admin.kriteria.index')
            ->with('success', 'Kriteria berhasil dihapus.');
    }
}