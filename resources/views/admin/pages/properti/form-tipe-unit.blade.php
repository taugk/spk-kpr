@extends('admin.layouts.app')

@section('title', isset($tipeUnit) ? 'Edit Tipe Unit' : 'Tambah Tipe Unit')

@section('styles')
<style>
    .image-preview {
        max-width: 200px;
        margin-top: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .preview-container {
        margin-top: 10px;
    }
</style>
@endsection

@section('page_action')
<div>
    <a href="{{ route('admin.properti.tipe-unit', isset($tipeUnit) ? $tipeUnit->proyek_id : request()->proyek_id) }}" 
       class="btn btn-sm btn-outline-secondary">
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
                        {{ isset($tipeUnit) ? 'Edit Tipe Unit' : 'Tambah Tipe Unit Baru' }}
                    </h4>
                    <p class="text-muted">
                        {{ isset($tipeUnit) ? 'Ubah data tipe unit yang sudah ada' : 'Isi form berikut untuk menambahkan tipe unit baru' }}
                    </p>
                </div>
                <div class="pd-20">
                    <form method="POST" 
                          action="{{ isset($tipeUnit) ? route('admin.properti.tipe-unit.update', $tipeUnit->id) : route('admin.properti.tipe-unit.store') }}" 
                          enctype="multipart/form-data">
                        @csrf
                        @if(isset($tipeUnit))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Proyek -->
                                <div class="form-group required">
                                    <label class="control-label">Proyek</label>
                                    <select name="proyek_id" class="form-control @error('proyek_id') is-invalid @enderror" required>
                                        <option value="">Pilih Proyek</option>
                                        @foreach($proyekList ?? [] as $proyek)
                                            <option value="{{ $proyek->id }}" 
                                                {{ old('proyek_id', $tipeUnit->proyek_id ?? $selectedProyek ?? '') == $proyek->id ? 'selected' : '' }}>
                                                {{ $proyek->kode_proyek }} - {{ $proyek->nama_proyek }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('proyek_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Kode Tipe -->
                                <div class="form-group required">
                                    <label class="control-label">Kode Tipe</label>
                                    <input type="text" 
                                           name="kode_tipe" 
                                           class="form-control @error('kode_tipe') is-invalid @enderror" 
                                           value="{{ old('kode_tipe', $tipeUnit->kode_tipe ?? '') }}"
                                           placeholder="Contoh: T-001"
                                           required>
                                    @error('kode_tipe')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Nama Tipe -->
                                <div class="form-group required">
                                    <label class="control-label">Nama Tipe</label>
                                    <input type="text" 
                                           name="nama_tipe" 
                                           class="form-control @error('nama_tipe') is-invalid @enderror" 
                                           value="{{ old('nama_tipe', $tipeUnit->nama_tipe ?? '') }}"
                                           placeholder="Contoh: Tipe A, Tipe B, dll"
                                           required>
                                    @error('nama_tipe')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Luas Tanah -->
                                <div class="form-group required">
                                    <label class="control-label">Luas Tanah (m²)</label>
                                    <input type="number" 
                                           step="0.01" 
                                           name="luas_tanah" 
                                           class="form-control @error('luas_tanah') is-invalid @enderror" 
                                           value="{{ old('luas_tanah', $tipeUnit->luas_tanah ?? '') }}"
                                           required>
                                    @error('luas_tanah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Luas Bangunan -->
                                <div class="form-group required">
                                    <label class="control-label">Luas Bangunan (m²)</label>
                                    <input type="number" 
                                           step="0.01" 
                                           name="luas_bangunan" 
                                           class="form-control @error('luas_bangunan') is-invalid @enderror" 
                                           value="{{ old('luas_bangunan', $tipeUnit->luas_bangunan ?? '') }}"
                                           required>
                                    @error('luas_bangunan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Jumlah Kamar -->
                                <div class="form-group">
                                    <label>Jumlah Kamar</label>
                                    <input type="number" 
                                           name="jumlah_kamar" 
                                           class="form-control @error('jumlah_kamar') is-invalid @enderror" 
                                           value="{{ old('jumlah_kamar', $tipeUnit->jumlah_kamar ?? '') }}"
                                           placeholder="Contoh: 3">
                                    @error('jumlah_kamar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Jumlah WC -->
                                <div class="form-group">
                                    <label>Jumlah WC</label>
                                    <input type="number" 
                                           name="jumlah_wc" 
                                           class="form-control @error('jumlah_wc') is-invalid @enderror" 
                                           value="{{ old('jumlah_wc', $tipeUnit->jumlah_wc ?? '') }}"
                                           placeholder="Contoh: 2">
                                    @error('jumlah_wc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Harga -->
                                <div class="form-group required">
                                    <label class="control-label">Harga (Rp)</label>
                                    <input type="number" 
                                           name="harga" 
                                           class="form-control @error('harga') is-invalid @enderror" 
                                           value="{{ old('harga', $tipeUnit->harga ?? '') }}"
                                           required>
                                    @error('harga')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Stok Tersedia -->
                                <div class="form-group required">
                                    <label class="control-label">Stok Tersedia</label>
                                    <input type="number" 
                                           name="stok_tersedia" 
                                           class="form-control @error('stok_tersedia') is-invalid @enderror" 
                                           value="{{ old('stok_tersedia', $tipeUnit->stok_tersedia ?? '0') }}"
                                           required>
                                    @error('stok_tersedia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Gambar -->
                                <div class="form-group">
                                    <label>Gambar Tipe Unit</label>
                                    <input type="file" 
                                           name="gambar" 
                                           class="form-control @error('gambar') is-invalid @enderror" 
                                           accept="image/*"
                                           id="gambarInput">
                                    @error('gambar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    @if(isset($tipeUnit) && $tipeUnit->gambar)
                                        <div class="preview-container" id="existingImage">
                                            <img src="{{ asset('storage/' . $tipeUnit->gambar) }}" 
                                                 class="image-preview" 
                                                 alt="Gambar Tipe Unit">
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-danger remove-gambar">
                                                    <i class="icon-copy dw dw-trash"></i> Hapus Gambar
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div id="imagePreview" class="preview-container" style="display: none;">
                                        <img src="" class="image-preview" alt="Preview">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-copy dw dw-save"></i> 
                                    {{ isset($tipeUnit) ? 'Update Tipe Unit' : 'Simpan Tipe Unit' }}
                                </button>
                                <a href="{{ route('admin.properti.tipe-unit', isset($tipeUnit) ? $tipeUnit->proyek_id : request()->proyek_id) }}" 
                                   class="btn btn-secondary">
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
    // Preview gambar
    $('#gambarInput').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview img').attr('src', e.target.result);
                $('#imagePreview').show();
                $('#existingImage').hide();
            }
            reader.readAsDataURL(file);
        }
    });
    
    // Hapus gambar existing
    $('.remove-gambar').on('click', function() {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Hapus gambar ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.properti.tipe-unit.remove-gambar") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: {{ $tipeUnit->id ?? 0 }}
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#existingImage').remove();
                            Swal.fire('Berhasil!', 'Gambar dihapus', 'success');
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal menghapus gambar', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection