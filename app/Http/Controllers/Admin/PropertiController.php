<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\View\View;

use App\Http\Controllers\Controller;

class PropertiController extends Controller
{
    // ==================== VIEWS ====================

    public function index(): View
    {
        $proyek = DB::table('proyek')->orderByDesc('id')->get();

        $tipeUnits = DB::table('tipe_unit')
            ->whereIn('proyek_id', $proyek->pluck('id'))
            ->orderBy('nama_tipe')
            ->get()
            ->groupBy('proyek_id');

        foreach ($proyek as $item) {
            $item->tipeUnit = $tipeUnits->get($item->id, collect());

            $item->created_at_formatted = $item->created_at
                ? date('d/m/Y', strtotime($item->created_at))
                : '-';

            $item->updated_at_formatted = $item->updated_at
                ? date('d/m/Y', strtotime($item->updated_at))
                : '-';
        }

        return view('admin.pages.properti.index', compact('proyek'));
    }

    public function proyek(Request $request): View
    {
        $query = DB::table('proyek');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_proyek', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_proyek', 'like', '%' . $request->search . '%');
            });
        }

        $proyek = $query->orderByDesc('id')->paginate(10);
        $proyek->appends($request->query());

        return view('admin.pages.properti.proyek', compact('proyek'));
    }

    public function tipeUnit(Request $request): View
    {
        $proyekId = $request->integer('proyek_id');

        $proyek = null;
        if ($proyekId) {
            $proyek = DB::table('proyek')->where('id', $proyekId)->first();
            abort_if(!$proyek, 404, 'Proyek tidak ditemukan');
        }

        $query = DB::table('tipe_unit')
            ->join('proyek', 'proyek.id', '=', 'tipe_unit.proyek_id')
            ->select('tipe_unit.*', 'proyek.nama_proyek', 'proyek.kode_proyek');

        if ($proyekId) {
            $query->where('tipe_unit.proyek_id', $proyekId);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('tipe_unit.nama_tipe', 'like', '%' . $request->search . '%')
                  ->orWhere('tipe_unit.kode_tipe', 'like', '%' . $request->search . '%');
            });
        }

        $tipeUnit = $query->orderByDesc('tipe_unit.id')->paginate(10);
        $tipeUnit->appends($request->query());

        return view('admin.pages.properti.tipe-unit', compact('proyek', 'tipeUnit', 'proyekId'));
    }

    public function unit(Request $request): View
    {
        $query = DB::table('unit')
            ->join('tipe_unit', 'tipe_unit.id', '=', 'unit.tipe_unit_id')
            ->join('proyek', 'proyek.id', '=', 'tipe_unit.proyek_id')
            ->select(
                'unit.*',
                'tipe_unit.nama_tipe',
                'tipe_unit.kode_tipe',
                'tipe_unit.proyek_id',
                'proyek.nama_proyek',
                'proyek.kode_proyek'
            );

        if ($request->filled('status')) {
            $query->where('unit.status', $request->status);
        }

        if ($request->filled('proyek_id')) {
            $query->where('proyek.id', $request->proyek_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('unit.kode_unit', 'like', '%' . $request->search . '%')
                  ->orWhere('tipe_unit.nama_tipe', 'like', '%' . $request->search . '%')
                  ->orWhere('proyek.nama_proyek', 'like', '%' . $request->search . '%');
            });
        }

        $unit = $query->orderByDesc('unit.id')->paginate(10);
        $unit->appends($request->query());

        $proyekList = DB::table('proyek')->orderBy('nama_proyek')->get();

        return view('admin.pages.properti.unit', compact('unit', 'proyekList'));
    }

    // ==================== CRUD PROYEK ====================

    public function createProyek(): View
    {
        return view('admin.pages.properti.form-proyek');
    }

    public function storeProyek(Request $request)
    {
        $validated = $request->validate([
            'kode_proyek'  => 'required|unique:proyek,kode_proyek|max:20',
            'nama_proyek'  => 'required|max:150',
            'lokasi'       => 'required',
            'kota'         => 'required|max:100',
            'provinsi'     => 'required|max:100',
            'deskripsi'    => 'nullable',
            'status'       => 'required|in:aktif,tutup,habis',
            'foto_proyek'  => 'nullable|array',
            'foto_proyek.*'=> 'image|max:2048',
        ]);

        // Handle multiple foto
        if ($request->hasFile('foto_proyek')) {
            $paths = [];
            foreach ($request->file('foto_proyek') as $foto) {
                $paths[] = $foto->store('proyek', 'public');
            }
            $validated['foto_proyek'] = json_encode($paths);
        } else {
            unset($validated['foto_proyek']);
        }

        DB::table('proyek')->insert(array_merge($validated, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return redirect()->route('properti.proyek')
            ->with('success', 'Proyek berhasil ditambahkan');
    }

    public function editProyek(int $id): View
    {
        $proyek = DB::table('proyek')->where('id', $id)->first();
        abort_if(!$proyek, 404);
        return view('admin.pages.properti.form-proyek', compact('proyek'));
    }

    public function updateProyek(Request $request, int $id)
    {
        $validated = $request->validate([
            'kode_proyek'  => 'required|max:20|unique:proyek,kode_proyek,' . $id,
            'nama_proyek'  => 'required|max:150',
            'lokasi'       => 'required',
            'kota'         => 'required|max:100',
            'provinsi'     => 'required|max:100',
            'deskripsi'    => 'nullable',
            'status'       => 'required|in:aktif,tutup,habis',
            'foto_proyek'  => 'nullable|array',
            'foto_proyek.*'=> 'image|max:2048',
        ]);

        if ($request->hasFile('foto_proyek')) {
            // Hapus foto lama
            $existing = DB::table('proyek')->where('id', $id)->value('foto_proyek');
            if ($existing) {
                foreach (json_decode($existing, true) ?? [] as $oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $paths = [];
            foreach ($request->file('foto_proyek') as $foto) {
                $paths[] = $foto->store('proyek', 'public');
            }
            $validated['foto_proyek'] = json_encode($paths);
        } else {
            unset($validated['foto_proyek']);
        }

        DB::table('proyek')->where('id', $id)->update(array_merge($validated, [
            'updated_at' => now(),
        ]));

        return redirect()->route('properti.proyek')
            ->with('success', 'Proyek berhasil diupdate');
    }

    public function destroyProyek(int $id)
    {
        // Hapus gambar tipe unit terkait
        $tipeUnits = DB::table('tipe_unit')->where('proyek_id', $id)->get();
        foreach ($tipeUnits as $tipe) {
            if ($tipe->gambar) {
                foreach (json_decode($tipe->gambar, true) ?? [] as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
            DB::table('unit')->where('tipe_unit_id', $tipe->id)->delete();
        }

        // Hapus foto proyek
        $proyek = DB::table('proyek')->where('id', $id)->first();
        if ($proyek && $proyek->foto_proyek) {
            foreach (json_decode($proyek->foto_proyek, true) ?? [] as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        DB::table('tipe_unit')->where('proyek_id', $id)->delete();
        DB::table('proyek')->where('id', $id)->delete();

        return response()->json(['success' => true]);
    }

    public function removeGambarProyek(Request $request)
    {
        $request->validate([
            'proyek_id' => 'required|integer',
            'path'      => 'required|string',
        ]);

        $proyek = DB::table('proyek')->where('id', $request->proyek_id)->first();
        abort_if(!$proyek, 404);

        $fotos = json_decode($proyek->foto_proyek, true) ?? [];
        $fotos = array_filter($fotos, fn($f) => $f !== $request->path);

        Storage::disk('public')->delete($request->path);

        DB::table('proyek')->where('id', $request->proyek_id)->update([
            'foto_proyek' => json_encode(array_values($fotos)),
            'updated_at'  => now(),
        ]);

        return response()->json(['success' => true]);
    }

    // ==================== CRUD TIPE UNIT ====================

    public function createTipeUnit(Request $request): View
    {
        $proyekList = DB::table('proyek')
            ->where('status', 'aktif')
            ->orderBy('nama_proyek')
            ->get();

        $selectedProyek = $request->integer('proyek_id') ?: null;

        return view('admin.pages.properti.form-tipe-unit', compact('proyekList', 'selectedProyek'));
    }

    public function storeTipeUnit(Request $request)
    {
        $validated = $request->validate([
            'proyek_id'     => 'required|exists:proyek,id',
            'kode_tipe'     => 'required|max:20',
            'nama_tipe'     => 'required|max:100',
            'luas_tanah'    => 'required|numeric|min:0',
            'luas_bangunan' => 'required|numeric|min:0',
            'jumlah_kamar'  => 'nullable|integer|min:0',
            'jumlah_wc'     => 'nullable|integer|min:0',
            'harga'         => 'required|numeric|min:0',
            'stok_tersedia' => 'required|integer|min:0',
            'gambar'        => 'nullable|array',
            'gambar.*'      => 'image|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $paths = [];
            foreach ($request->file('gambar') as $file) {
                $paths[] = $file->store('tipe-unit', 'public');
            }
            $validated['gambar'] = json_encode($paths);
        } else {
            unset($validated['gambar']);
        }

        DB::table('tipe_unit')->insert(array_merge($validated, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return redirect()->route('properti.tipe-unit', ['proyek_id' => $request->proyek_id])
            ->with('success', 'Tipe unit berhasil ditambahkan');
    }

    public function editTipeUnit(int $id): View
    {
        $tipeUnit = DB::table('tipe_unit')->where('id', $id)->first();
        abort_if(!$tipeUnit, 404);

        $proyekList = DB::table('proyek')->orderBy('nama_proyek')->get();

        return view('admin.pages.properti.form-tipe-unit', compact('tipeUnit', 'proyekList'));
    }

    public function updateTipeUnit(Request $request, int $id)
    {
        $validated = $request->validate([
            'proyek_id'     => 'required|exists:proyek,id',
            'kode_tipe'     => 'required|max:20',
            'nama_tipe'     => 'required|max:100',
            'luas_tanah'    => 'required|numeric|min:0',
            'luas_bangunan' => 'required|numeric|min:0',
            'jumlah_kamar'  => 'nullable|integer|min:0',
            'jumlah_wc'     => 'nullable|integer|min:0',
            'harga'         => 'required|numeric|min:0',
            'stok_tersedia' => 'required|integer|min:0',
            'gambar'        => 'nullable|array',
            'gambar.*'      => 'image|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            $existing = DB::table('tipe_unit')->where('id', $id)->value('gambar');
            if ($existing) {
                foreach (json_decode($existing, true) ?? [] as $oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $paths = [];
            foreach ($request->file('gambar') as $file) {
                $paths[] = $file->store('tipe-unit', 'public');
            }
            $validated['gambar'] = json_encode($paths);
        } else {
            unset($validated['gambar']);
        }

        DB::table('tipe_unit')->where('id', $id)->update(array_merge($validated, [
            'updated_at' => now(),
        ]));

        return redirect()->route('properti.tipe-unit', ['proyek_id' => $validated['proyek_id']])
            ->with('success', 'Tipe unit berhasil diupdate');
    }

    public function destroyTipeUnit(int $id)
    {
        $tipeUnit = DB::table('tipe_unit')->where('id', $id)->first();

        if ($tipeUnit && $tipeUnit->gambar) {
            foreach (json_decode($tipeUnit->gambar, true) ?? [] as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        DB::table('unit')->where('tipe_unit_id', $id)->delete();
        DB::table('tipe_unit')->where('id', $id)->delete();

        return response()->json(['success' => true]);
    }

    public function detailTipeUnit(int $id)
    {
        $tipeUnit = DB::table('tipe_unit')
            ->join('proyek', 'proyek.id', '=', 'tipe_unit.proyek_id')
            ->select('tipe_unit.*', 'proyek.nama_proyek', 'proyek.kode_proyek')
            ->where('tipe_unit.id', $id)
            ->first();

        if (!$tipeUnit) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Parse gambar JSON → ambil URL pertama
        $gambarArr  = json_decode($tipeUnit->gambar, true) ?? [];
        $gambarUrl  = !empty($gambarArr[0])
            ? asset('storage/' . $gambarArr[0])
            : null;

        // Hitung unit terjual
        $terjual = DB::table('unit')
            ->where('tipe_unit_id', $id)
            ->where('status', 'terjual')
            ->count();

        return response()->json([
            'id'             => $tipeUnit->id,
            'proyek_id'      => $tipeUnit->proyek_id,
            'nama_proyek'    => $tipeUnit->nama_proyek,
            'kode_proyek'    => $tipeUnit->kode_proyek,
            'kode_tipe'      => $tipeUnit->kode_tipe,
            'nama_tipe'      => $tipeUnit->nama_tipe,
            'luas_tanah'     => $tipeUnit->luas_tanah,
            'luas_bangunan'  => $tipeUnit->luas_bangunan,
            'jumlah_kamar'   => $tipeUnit->jumlah_kamar,
            'jumlah_wc'      => $tipeUnit->jumlah_wc,
            'harga'          => $tipeUnit->harga,
            'stok_tersedia'  => $tipeUnit->stok_tersedia,
            'terjual'        => $terjual,
            'gambar'         => $gambarUrl,
        ]);
    }

    public function removeGambarTipeUnit(Request $request)
    {
        $request->validate([
            'tipe_unit_id' => 'required|integer',
            'path'         => 'required|string',
        ]);

        $tipeUnit = DB::table('tipe_unit')->where('id', $request->tipe_unit_id)->first();
        abort_if(!$tipeUnit, 404);

        $gambar = json_decode($tipeUnit->gambar, true) ?? [];
        $gambar = array_filter($gambar, fn($g) => $g !== $request->path);

        Storage::disk('public')->delete($request->path);

        DB::table('tipe_unit')->where('id', $request->tipe_unit_id)->update([
            'gambar'     => json_encode(array_values($gambar)),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    // ==================== CRUD UNIT ====================

    public function createUnit(Request $request): View
    {
        $tipeUnitList = DB::table('tipe_unit')
            ->join('proyek', 'proyek.id', '=', 'tipe_unit.proyek_id')
            ->select('tipe_unit.*', 'proyek.nama_proyek')
            ->orderBy('proyek.nama_proyek')
            ->orderBy('tipe_unit.nama_tipe')
            ->get();

        $selectedTipeUnit = $request->integer('tipe_unit_id') ?: null;

        return view('admin.pages.properti.form-unit', compact('tipeUnitList', 'selectedTipeUnit'));
    }

    public function storeUnit(Request $request)
    {
        $validated = $request->validate([
            'tipe_unit_id' => 'required|exists:tipe_unit,id',
            'kode_unit'    => 'required|unique:unit,kode_unit|max:20',
            'status'       => 'required|in:tersedia,dipesan,terjual,dibatalkan',
        ]);

        DB::table('unit')->insert(array_merge($validated, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return redirect()->route('properti.unit')
            ->with('success', 'Unit berhasil ditambahkan');
    }

    public function editUnit(int $id): View
    {
        $unit = DB::table('unit')->where('id', $id)->first();
        abort_if(!$unit, 404);

        $tipeUnitList = DB::table('tipe_unit')
            ->join('proyek', 'proyek.id', '=', 'tipe_unit.proyek_id')
            ->select('tipe_unit.*', 'proyek.nama_proyek')
            ->orderBy('proyek.nama_proyek')
            ->orderBy('tipe_unit.nama_tipe')
            ->get();

        return view('admin.pages.properti.form-unit', compact('unit', 'tipeUnitList'));
    }

    public function updateUnit(Request $request, int $id)
    {
        $validated = $request->validate([
            'tipe_unit_id' => 'required|exists:tipe_unit,id',
            'kode_unit'    => 'required|max:20|unique:unit,kode_unit,' . $id,
            'status'       => 'required|in:tersedia,dipesan,terjual,dibatalkan',
        ]);

        DB::table('unit')->where('id', $id)->update(array_merge($validated, [
            'updated_at' => now(),
        ]));

        return redirect()->route('properti.unit')
            ->with('success', 'Unit berhasil diupdate');
    }

    public function destroyUnit(int $id)
    {
        DB::table('unit')->where('id', $id)->delete();

        return response()->json(['success' => true]);
    }
}