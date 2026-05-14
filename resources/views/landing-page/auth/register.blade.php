@extends('landing-page.layouts.landing-page')

@section('title', 'Daftar | PT Citra Pasada Property')

@section('content')
<div class="container py-5" style="margin-top: 80px;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-4 p-md-5">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <a href="/" class="text-decoration-none">
                            <h3 class="fw-bold" style="color: #0F5B5A;">
                                <i class="bi bi-building"></i> Citra Pasada Property
                            </h3>
                        </a>
                        <h2 class="fw-bold mt-3" style="color: #2C3E3B;">Daftar Akun</h2>
                        <p class="text-muted">Bergabunglah untuk mendapatkan penawaran eksklusif</p>
                    </div>

                    <!-- Alert Errors -->
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Register Form -->
                    <form method="POST" action="{{ route('debitur.register.process') }}">
                        @csrf

                        <!-- Nama Lengkap -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-person text-secondary"></i>
                                </span>
                                <input type="text" 
                                       class="form-control border-start-0 @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Budi Santoso"
                                       required>
                            </div>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-envelope text-secondary"></i>
                                </span>
                                <input type="email" 
                                       class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="nama@email.com"
                                       required>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock text-secondary"></i>
                                </span>
                                <input type="password" 
                                       class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Minimal 8 karakter"
                                       required>
                                <button class="btn btn-outline-secondary border-start-0 toggle-password" 
                                        type="button" 
                                        data-target="password">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <div class="form-text">Minimal 8 karakter, mengandung huruf dan angka</div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-shield-lock text-secondary"></i>
                                </span>
                                <input type="password" 
                                       class="form-control border-start-0" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Ulangi kata sandi"
                                       required>
                                <button class="btn btn-outline-secondary border-start-0 toggle-password" 
                                        type="button" 
                                        data-target="password_confirmation">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Terms & Conditions -->
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                Saya setuju dengan <a href="#" class="text-decoration-none">Syarat & Ketentuan</a>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn w-100 py-2 fw-bold mb-3" 
                                style="background: #E2A526; color: #1F3E3B;">
                            <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                        </button>

                        <!-- Login Link -->
                        <p class="text-center text-muted mb-0">
                            Sudah punya akun? 
                            <a href="{{ route('debitur.login') }}" class="text-decoration-none fw-semibold" style="color: #0F5B5A;">
                                Masuk di sini
                            </a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    });
</script>
@endpush