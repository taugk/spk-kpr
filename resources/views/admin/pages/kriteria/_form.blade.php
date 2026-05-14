@csrf
<div class="row">
    <div class="col-md-3 form-group">
        <label>Kode</label>
        <input type="text" name="kode_kriteria" class="form-control" value="{{ old('kode_kriteria', $kriteria->kode_kriteria ?? '') }}" required>
    </div>
    <div class="col-md-9 form-group">
        <label>Nama Kriteria</label>
        <input type="text" name="nama_kriteria" class="form-control" value="{{ old('nama_kriteria', $kriteria->nama_kriteria ?? '') }}" required>
    </div>
    <div class="col-md-4 form-group">
        <label>Tipe Kriteria</label>
        <select name="tipe" class="form-control" required>
            <option value="benefit" @selected(old('tipe', $kriteria->tipe ?? '') === 'benefit')>Benefit</option>
            <option value="cost" @selected(old('tipe', $kriteria->tipe ?? '') === 'cost')>Cost</option>
        </select>
    </div>
    <div class="col-md-4 form-group">
        <label>Bobot Kriteria</label>
        <input type="number" step="0.0001" min="0" max="1" name="bobot" class="form-control" value="{{ old('bobot', $kriteria->bobot ?? '') }}" required>
        <small>Contoh: 0.2500 untuk 25%</small>
    </div>
    <div class="col-md-4 form-group">
        <label>Satuan</label>
        <input type="text" name="satuan" class="form-control" value="{{ old('satuan', $kriteria->satuan ?? '') }}">
    </div>
    <div class="col-md-4 form-group">
        <label>Nilai Minimum</label>
        <input type="number" step="0.0001" name="nilai_min" class="form-control" value="{{ old('nilai_min', $kriteria->nilai_min ?? '') }}">
    </div>
    <div class="col-md-4 form-group">
        <label>Nilai Maksimum</label>
        <input type="number" step="0.0001" name="nilai_max" class="form-control" value="{{ old('nilai_max', $kriteria->nilai_max ?? '') }}">
    </div>
    <div class="col-md-4 form-group">
        <label>Urutan</label>
        <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $kriteria->urutan ?? 1) }}" required>
    </div>
    <div class="col-md-12 form-group">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control">{{ old('deskripsi', $kriteria->deskripsi ?? '') }}</textarea>
    </div>
    <div class="col-md-12 form-group">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" name="aktif" value="1" class="custom-control-input" id="aktif" @checked(old('aktif', $kriteria->aktif ?? 1))>
            <label class="custom-control-label" for="aktif">Aktif</label>
        </div>
    </div>
</div>
<button class="btn btn-primary">Simpan</button>
<a href="{{ route('admin.kriteria.index') }}" class="btn btn-secondary">Kembali</a>
