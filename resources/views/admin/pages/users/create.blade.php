{{-- resources/views/admin/pages/users/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Tambah User Baru')

@section('page_action')
    <div class="btn-list">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
            <i class="dw dw-left-arrow1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="min-height-200px">
    

    <div class="row">
        <div class="col-xl-8 col-lg-8 col-md-12 mx-auto">
            <div class="card-box pd-20 mb-30">
                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Nama Lengkap --}}
                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nama_lengkap" 
                               class="form-control @error('nama_lengkap') is-invalid @enderror" 
                               value="{{ old('nama_lengkap') }}"
                               placeholder="Masukkan nama lengkap"
                               required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" 
                               name="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}"
                               placeholder="contoh: user@email.com"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="form-group">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="password" 
                               name="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Minimal 6 karakter"
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="form-group">
                        <label>Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" 
                               name="password_confirmation" 
                               class="form-control"
                               placeholder="Ulangi password"
                               required>
                    </div>

                    {{-- Role --}}
                    <div class="form-group">
                        <label>Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $key => $role)
                                <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Foto Profil --}}
                    <div class="form-group">
                        <label>Foto Profil</label>
                        <input type="file" 
                               name="foto_profil" 
                               class="form-control-file @error('foto_profil') is-invalid @enderror"
                               accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG. Maks: 2MB</small>
                        @error('foto_profil')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="dw dw-save"></i> Simpan User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="dw dw-cancel"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection