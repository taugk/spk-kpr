@csrf
<div class="row">
    <div class="col-md-12 form-group">
        <label>Kriteria</label>
        <select name="kriteria_id" class="form-control" required>
            <option value="">Pilih Kriteria</option>
            @foreach($kriteria as $item)
                <option value="{{ $item->id }}" @selected(old('kriteria_id', $kriteriaSkala->kriteria_id ?? '') == $item->id)>
                    {{ $item->kode_kriteria }} - {{ $item->nama_kriteria }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 form-group">
        <label>Skor</label>
        <input type="number" name="skor" class="form-control" value="{{ old('skor', $kriteriaSkala->skor ?? '') }}" required>
    </div>
    <div class="col-md-4 form-group">
        <label>Nilai Minimum</label>
        <input type="number" step="0.0001" name="nilai_min" class="form-control" value="{{ old('nilai_min', $kriteriaSkala->nilai_min ?? '') }}">
    </div>
    <div class="col-md-4 form-group">
        <label>Nilai Maksimum</label>
        <input type="number" step="0.0001" name="nilai_max" class="form-control" value="{{ old('nilai_max', $kriteriaSkala->nilai_max ?? '') }}">
    </div>
    <div class="col-md-12 form-group">
        <label>Keterangan</label>
        <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan', $kriteriaSkala->keterangan ?? '') }}" required>
    </div>
</div>
<button class="btn btn-primary">Simpan</button>
<a href="{{ route('admin.kriteria-skala.index') }}" class="btn btn-secondary">Kembali</a>
