@extends('landing-page.layouts.landing-page')
@section('title', 'Login | PT Citra Pasada Property')

@section('content')
<div class="container py-5" style="margin-top: 80px;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold" style="color: #0F5B5A;">Selamat Datang</h2>
                        <p class="text-muted">Silakan login untuk melanjutkan</p>
                    </div>
                    
                    <form method="POST" action="{{ route('debitur.login.process') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn w-100 py-2 fw-bold" style="background: #E2A526;">
                            Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


<!-- @push('styles')
<style>
    /* ========================================
       LOGIN PAGE STYLES (ISOLATED)
       ======================================== */
    
    /* Wrapper utama */
    .login-page-wrapper {
        background: linear-gradient(135deg, #F7F3EA 0%, #FEFAF2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }
    
    /* Background decoration */
    .login-page-wrapper::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 70%;
        height: 140%;
        background: radial-gradient(circle, rgba(15,91,90,0.03) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    
    .login-page-wrapper::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 60%;
        height: 120%;
        background: radial-gradient(circle, rgba(226,165,38,0.03) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    
    /* Container */
    .login-container {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        padding: 0 20px;
        position: relative;
        z-index: 1;
    }
    
    /* Card */
    .login-card {
        background: white;
        border-radius: 32px;
        padding: 2.5rem;
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border: 1px solid rgba(226,165,38,0.1);
        transition: transform 0.3s ease;
    }
    
    .login-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.12);
    }
    
    /* Brand */
    .login-brand {
        font-weight: 800;
        font-size: 1.5rem;
        text-decoration: none;
        background: linear-gradient(135deg, #0F5B5A 0%, #1B7E7C 100%);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        display: inline-block;
        margin-bottom: 1rem;
    }
    
    .login-brand:hover {
        opacity: 0.8;
    }
    
    /* Titles */
    .login-title {
        font-weight: 800;
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        color: #2C3E3B;
    }
    
    .login-subtitle {
        color: #6B7A77;
        font-size: 0.95rem;
    }
    
    /* Form Group */
    .login-form-group {
        margin-bottom: 1.25rem;
    }
    
    .login-label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #2C3E3B;
        font-size: 0.9rem;
    }
    
    /* Input Group */
    .login-input-group {
        display: flex;
        align-items: center;
        border: 1px solid #E2D5C4;
        border-radius: 12px;
        background: white;
        transition: all 0.2s ease;
    }
    
    .login-input-group:focus-within {
        border-color: #E2A526;
        box-shadow: 0 0 0 3px rgba(226,165,38,0.1);
    }
    
    .login-input-icon {
        padding: 0 12px;
        color: #0F5B5A;
        font-size: 1.1rem;
    }
    
    .login-input {
        flex: 1;
        border: none;
        padding: 0.75rem 0;
        font-size: 0.95rem;
        outline: none;
        background: transparent;
    }
    
    .login-input:focus {
        outline: none;
    }
    
    .login-password-toggle {
        background: none;
        border: none;
        padding: 0 12px;
        color: #6B7A77;
        cursor: pointer;
    }
    
    .login-password-toggle:hover {
        color: #0F5B5A;
    }
    
    /* Error */
    .login-error {
        color: #dc3545;
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }
    
    .login-input.is-invalid {
        border-color: #dc3545;
    }
    
    /* Options */
    .login-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .login-checkbox {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #6B7A77;
        cursor: pointer;
    }
    
    .login-checkbox input {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #0F5B5A;
    }
    
    .login-forgot-link {
        font-size: 0.85rem;
        color: #0F5B5A;
        text-decoration: none;
    }
    
    .login-forgot-link:hover {
        text-decoration: underline;
        color: #E2A526;
    }
    
    /* Submit Button */
    .login-btn-submit {
        width: 100%;
        background: #E2A526;
        border: none;
        border-radius: 12px;
        padding: 0.9rem;
        font-weight: 800;
        color: #1F3E3B;
        transition: all 0.3s ease;
        margin-bottom: 1.25rem;
        cursor: pointer;
    }
    
    .login-btn-submit:hover {
        background: #c9921f;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(226,165,38,0.3);
    }
    
    /* Register Link */
    .login-register-link {
        text-align: center;
        color: #6B7A77;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }
    
    .login-register-link a {
        color: #0F5B5A;
        text-decoration: none;
        font-weight: 600;
    }
    
    .login-register-link a:hover {
        text-decoration: underline;
        color: #E2A526;
    }
    
    /* Social Login */
    .login-social {
        text-align: center;
        padding-top: 1rem;
        border-top: 1px solid #F0E8DC;
    }
    
    .login-social-text {
        font-size: 0.8rem;
        color: #6B7A77;
        margin-bottom: 1rem;
    }
    
    .login-social-icons {
        display: flex;
        gap: 12px;
        justify-content: center;
    }
    
    .login-social-btn {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #E2D5C4;
        border-radius: 50%;
        color: #6B7A77;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .login-social-btn:hover {
        background: #0F5B5A;
        border-color: #0F5B5A;
        color: white;
        transform: translateY(-2px);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .login-card {
            padding: 1.5rem;
        }
        
        .login-title {
            font-size: 1.5rem;
        }
        
        .login-container {
            padding: 0 16px;
        }
    }
</style>
@endpush -->

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                const icon = this.querySelector('i');
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        }
    });
</script>
@endpush