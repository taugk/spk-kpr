{{-- resources/views/admin/pages/users/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit User - ' . $user->nama_lengkap)

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
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Foto Profil --}}
                    <div class="form-group text-center">
                        <div class="profile-upload">
                            @if($user->foto_profil)
                                <img src="{{ asset('storage/' . $user->foto_profil) }}" 
                                     alt="Foto Profil" 
                                     class="rounded-circle mb-2"
                                     style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="avatar-placeholder rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-2"
                                     style="width: 120px; height: 120px;">
                                    <i class="fa fa-user fa-4x text-secondary"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Nama Lengkap --}}
                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nama_lengkap" 
                               class="form-control @error('nama_lengkap') is-invalid @enderror" 
                               value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
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
                               value="{{ old('email', $user->email) }}"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password (Opsional) --}}
                    <div class="form-group">
                        <label>Password <span class="text-muted">(Kosongkan jika tidak diubah)</span></label>
                        <input type="password" 
                               name="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Minimal 6 karakter">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <input type="password" 
                               name="password_confirmation" 
                               class="form-control"
                               placeholder="Ulangi password">
                    </div>

                    {{-- Role --}}
                    <div class="form-group">
                        <label>Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                            @foreach($roles as $key => $role)
                                <option value="{{ $key }}" {{ old('role', $user->role) == $key ? 'selected' : '' }}>
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
                            <option value="aktif" {{ old('status', $user->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', $user->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Foto Profil --}}
                    <div class="form-group">
                        <label>Ganti Foto Profil</label>
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
                            <i class="dw dw-save"></i> Update User
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