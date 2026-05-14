@extends('admin.layouts.app')

@section('title', isset($unit) ? 'Edit Unit' : 'Tambah Unit')

@section('page_action')
<div>
    <a href="{{ route('admin.properti.unit') }}" class="btn btn-sm btn-outline-secondary">
        <i class="icon-copy dw dw-back"></i> Kembali
    </a>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="pd-20">
                    <h4 class="text-blue h4">
                        {{ isset($unit) ? 'Edit Unit' : 'Tambah Unit Baru' }}
                    </h4>
                    <p class="text-muted">
                        {{ isset($unit) ? 'Ubah data unit yang sudah ada' : 'Isi form berikut untuk menambahkan unit baru' }}
                    </p>
                </div>
                <div class="pd-20">
                    <form method="POST" 
                          action="{{ isset($unit) ? route('admin.properti.unit.update', $unit->id) : route('admin.properti.unit.store') }}">
                        @csrf
                        @if(isset($unit))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Tipe Unit -->
                                <div class="form-group required">
                                    <label class="control-label">Tipe Unit</label>
                                    <select name="tipe_unit_id" class="form-control @error('tipe_unit_id') is-invalid @enderror" required>
                                        <option value="">Pilih Tipe Unit</option>
                                        @foreach($tipeUnitList ?? [] as $tipe)
                                            <option value="{{ $tipe->id }}" 
                                                data-proyek="{{ $tipe->nama_proyek }}"
                                                {{ old('tipe_unit_id', $unit->tipe_unit_id ?? $selectedTipeUnit ?? '') == $tipe->id ? 'selected' : '' }}>
                                                {{ $tipe->nama_proyek }} - {{ $tipe->nama_tipe }} ({{ $tipe->kode_tipe }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tipe_unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Kode Unit -->
                                <div class="form-group required">
                                    <label class="control-label">Kode Unit</label>
                                    <input type="text" 
                                           name="kode_unit" 
                                           class="form-control @error('kode_unit') is-invalid @enderror" 
                                           value="{{ old('kode_unit', $unit->kode_unit ?? '') }}"
                                           placeholder="Contoh: UNT-001"
                                           required>
                                    @error('kode_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kode unik untuk unit ini</small>
                                </div>

                                <!-- Status Unit -->
                                <div class="form-group required">
                                    <label class="control-label">Status Unit</label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="tersedia" {{ old('status', $unit->status ?? '') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                        <option value="dipesan" {{ old('status', $unit->status ?? '') == 'dipesan' ? 'selected' : '' }}>Dipesan</option>
                                        <option value="terjual" {{ old('status', $unit->status ?? '') == 'terjual' ? 'selected' : '' }}>Terjual</option>
                                        <option value="dibatalkan" {{ old('status', $unit->status ?? '') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="icon-copy dw dw-info"></i>
                                    <strong>Informasi:</strong>
                                    <p class="mb-0 mt-2" id="infoTipeUnit">
                                        @if(isset($unit) && $unit->tipe_unit_id)
                                            @php
                                                $tipeInfo = $tipeUnitList->firstWhere('id', $unit->tipe_unit_id);
                                            @endphp
                                            @if($tipeInfo)
                                                Proyek: {{ $tipeInfo->nama_proyek }}<br>
                                                Luas: {{ $tipeInfo->luas_tanah }}/{{ $tipeInfo->luas_bangunan }} m²<br>
                                                Harga: Rp {{ number_format($tipeInfo->harga, 0, ',', '.') }}
                                            @endif
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-copy dw dw-save"></i> 
                                    {{ isset($unit) ? 'Update Unit' : 'Simpan Unit' }}
                                </button>
                                <a href="{{ route('admin.properti.unit') }}" class="btn btn-secondary">
                                    <i class="icon-copy dw dw-cancel"></i> Batal
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Tampilkan info tipe unit saat dipilih
    $('select[name="tipe_unit_id"]').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const tipeId = $(this).val();
        
        if (tipeId) {
            $.ajax({
                url: '{{ route("admin.properti.tipe-unit.detail", "") }}/' + tipeId,
                method: 'GET',
                success: function(data) {
                    $('#infoTipeUnit').html(`
                        Proyek: ${data.nama_proyek}<br>
                        Luas: ${data.luas_tanah}/${data.luas_bangunan} m²<br>
                        Harga: Rp ${new Intl.NumberFormat('id-ID').format(data.harga)}<br>
                        Stok tersedia: ${data.stok_tersedia} unit
                    `);
                }
            });
        } else {
            $('#infoTipeUnit').html('Pilih tipe unit untuk melihat informasi');
        }
    });
});
</script>
@endsection