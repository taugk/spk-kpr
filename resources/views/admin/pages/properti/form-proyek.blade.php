@extends('admin.layouts.app')

@section('title', isset($proyek) ? 'Edit Proyek' : 'Tambah Proyek')

@section('styles')
<style>
    .image-preview {
        max-width: 200px;
        margin-top: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .image-preview-container {
        display: inline-block;
        position: relative;
        margin: 5px;
    }
    
    .remove-image {
        position: absolute;
        top: -8px;
        right: -8px;
        background: red;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        text-align: center;
        line-height: 18px;
        cursor: pointer;
        font-size: 12px;
    }
    
    .image-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    
    .form-group.required .control-label:after {
        content: "*";
        color: red;
        margin-left: 5px;
    }
</style>
@endsection

@section('page_action')
<div>
    <a href="{{ route('admin.properti.index') }}" class="btn btn-sm btn-outline-secondary">
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
                        {{ isset($proyek) ? 'Edit Proyek' : 'Tambah Proyek Baru' }}
                    </h4>
                    <p class="text-muted">
                        {{ isset($proyek) ? 'Ubah data proyek yang sudah ada' : 'Isi form berikut untuk menambahkan proyek baru' }}
                    </p>
                </div>
                <div class="pd-20">
                    <form method="POST" 
                          action="{{ isset($proyek) ? route('admin.properti.proyek.update', $proyek->id) : route('admin.properti.proyek.store') }}" 
                          enctype="multipart/form-data">
                        @csrf
                        @if(isset($proyek))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Kode Proyek -->
                                <div class="form-group required">
                                    <label class="control-label">Kode Proyek</label>
                                    <input type="text" 
                                           name="kode_proyek" 
                                           class="form-control @error('kode_proyek') is-invalid @enderror" 
                                           value="{{ old('kode_proyek', $proyek->kode_proyek ?? '') }}"
                                           placeholder="Contoh: PRJ-001"
                                           required>
                                    @error('kode_proyek')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kode unik untuk proyek ini</small>
                                </div>

                                <!-- Nama Proyek -->
                                <div class="form-group required">
                                    <label class="control-label">Nama Proyek</label>
                                    <input type="text" 
                                           name="nama_proyek" 
                                           class="form-control @error('nama_proyek') is-invalid @enderror" 
                                           value="{{ old('nama_proyek', $proyek->nama_proyek ?? '') }}"
                                           placeholder="Contoh: Green Residence"
                                           required>
                                    @error('nama_proyek')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Lokasi -->
                                <div class="form-group required">
                                    <label class="control-label">Lokasi</label>
                                    <textarea name="lokasi" 
                                              class="form-control @error('lokasi') is-invalid @enderror" 
                                              rows="3"
                                              placeholder="Alamat lengkap proyek"
                                              required>{{ old('lokasi', $proyek->lokasi ?? '') }}</textarea>
                                    @error('lokasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Kota -->
                                <div class="form-group required">
                                    <label class="control-label">Kota</label>
                                    <input type="text" 
                                           name="kota" 
                                           class="form-control @error('kota') is-invalid @enderror" 
                                           value="{{ old('kota', $proyek->kota ?? '') }}"
                                           placeholder="Contoh: Jakarta Selatan"
                                           required>
                                    @error('kota')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Provinsi -->
                                <div class="form-group required">
                                    <label class="control-label">Provinsi</label>
                                    <input type="text" 
                                           name="provinsi" 
                                           class="form-control @error('provinsi') is-invalid @enderror" 
                                           value="{{ old('provinsi', $proyek->provinsi ?? '') }}"
                                           placeholder="Contoh: DKI Jakarta"
                                           required>
                                    @error('provinsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Status -->
                                <div class="form-group required">
                                    <label class="control-label">Status Proyek</label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="aktif" {{ old('status', $proyek->status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="tutup" {{ old('status', $proyek->status ?? '') == 'tutup' ? 'selected' : '' }}>Tutup</option>
                                        <option value="habis" {{ old('status', $proyek->status ?? '') == 'habis' ? 'selected' : '' }}>Habis</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Deskripsi -->
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" 
                                              class="form-control @error('deskripsi') is-invalid @enderror" 
                                              rows="5"
                                              placeholder="Deskripsi lengkap tentang proyek">{{ old('deskripsi', $proyek->deskripsi ?? '') }}</textarea>
                                    @error('deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Foto Proyek -->
                                <div class="form-group">
                                    <label>Foto Proyek</label>
                                    <input type="file" 
                                           name="foto_proyek[]" 
                                           class="form-control @error('foto_proyek') is-invalid @enderror" 
                                           accept="image/*"
                                           multiple>
                                    @error('foto_proyek')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Upload beberapa foto proyek (maksimal 5 foto)</small>
                                    
                                    <!-- Preview gambar existing -->
                                    @if(isset($proyek) && $proyek->foto_proyek)
                                        <div class="image-grid mt-3">
                                            @foreach(json_decode($proyek->foto_proyek, true) ?? [] as $foto)
                                            <div class="image-preview-container">
                                                <img src="{{ asset('storage/' . $foto) }}" class="image-preview" alt="Foto Proyek">
                                                <div class="remove-image" data-foto="{{ $foto }}">×</div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-copy dw dw-save"></i> 
                                    {{ isset($proyek) ? 'Update Proyek' : 'Simpan Proyek' }}
                                </button>
                                <a href="{{ route('admin.properti.index') }}" class="btn btn-secondary">
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
    // Preview gambar sebelum upload
    $('input[name="foto_proyek[]"]').on('change', function(e) {
        const files = e.target.files;
        const previewContainer = $('.image-grid');
        
        if (previewContainer.length === 0) {
            $('.form-group:has(input[name="foto_proyek[]"])').append('<div class="image-grid mt-3"></div>');
        }
        
        $('.image-grid').empty();
        
        for(let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                $('.image-grid').append(`
                    <div class="image-preview-container">
                        <img src="${e.target.result}" class="image-preview" alt="Preview">
                        <div class="remove-image-preview" data-index="${i}">×</div>
                    </div>
                `);
            }
            
            reader.readAsDataURL(file);
        }
    });
    
    // Hapus preview gambar baru
    $(document).on('click', '.remove-image-preview', function() {
        $(this).closest('.image-preview-container').remove();
    });
    
    // Hapus gambar existing (perlu AJAX ke server)
    $(document).on('click', '.remove-image', function() {
        const foto = $(this).data('foto');
        const container = $(this).closest('.image-preview-container');
        
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Hapus foto ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.properti.proyek.remove-foto") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        proyek_id: {{ $proyek->id ?? 0 }},
                        foto: foto
                    },
                    success: function(response) {
                        if (response.success) {
                            container.remove();
                            Swal.fire('Berhasil!', 'Foto dihapus', 'success');
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal menghapus foto', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection