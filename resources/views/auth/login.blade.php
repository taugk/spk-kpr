<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login - SPK KPR Persada</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('deskapp/vendors/images/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('deskapp/vendors/images/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('deskapp/vendors/images/favicon-16x16.png') }}">

<link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/core.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/icon-font.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('deskapp/src/plugins/datatables/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('deskapp/src/plugins/datatables/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/style.css') }}">


    <style>
        body {
            background: linear-gradient(135deg, #eef4ff 0%, #ffffff 45%, #f5f7fb 100%);
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .auth-card {
            width: 100%;
            max-width: 430px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(15, 23, 42, .12);
            border: 1px solid #eef2f7;
            background: #fff;
        }

        .auth-header {
            padding: 35px 35px 20px;
            text-align: center;
        }

        .auth-logo {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            background: linear-gradient(135deg, #1b00ff, #00b4d8);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            margin-bottom: 18px;
        }

        .auth-body {
            padding: 0 35px 35px;
        }

        .form-control {
            border-radius: 12px;
            height: 48px;
        }

        .btn-auth {
            height: 48px;
            border-radius: 12px;
            font-weight: 600;
        }

        .auth-footer {
            text-align: center;
            padding-top: 18px;
        }
    </style>
</head>

<body>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-header">
            <div class="auth-logo">
                <i class="dw dw-house-1"></i>
            </div>

            <h4 class="mb-1">Masuk Akun</h4>
            <p class="text-muted mb-0">
                Sistem Pendukung Keputusan KPR Persada
            </p>
        </div>

        <div class="auth-body">

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Email</label>
                    <div class="input-group custom">
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="Masukkan email"
                               required>

                        <div class="input-group-append custom">
                            <span class="input-group-text">
                                <i class="icon-copy dw dw-email1"></i>
                            </span>
                        </div>

                        @error('email')
                            <div class="invalid-feedback mr-4 d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
    <label>Password</label>

    <div class="input-group custom">

        <input type="password"
               id="password"
               name="password"
               class="form-control @error('password') is-invalid @enderror"
               placeholder="Masukkan password"
               required>

        <div class="input-group-append mr-4 custom">
            <span class="input-group-text"
                  id="togglePassword"
                  style="cursor: pointer;">
                <i class="dw dw-view"></i>
            </span>
        </div>

        <div class="input-group-append custom">
            <span class="input-group-text">
                <i class="dw dw-padlock1"></i>
            </span>
        </div>

        

        @error('password')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
        @enderror

    </div>
</div>

                <button type="submit" class="btn btn-primary btn-block btn-auth">
                    Login
                </button>
            </form>

            

        </div>
    </div>
</div>

<script src="{{ asset('deskapp/vendors/scripts/core.js') }}"></script>
<script src="{{ asset('deskapp/vendors/scripts/script.min.js') }}"></script>
<script src="{{ asset('deskapp/vendors/scripts/process.js') }}"></script>
<script src="{{ asset('deskapp/vendors/scripts/layout-settings.js') }}"></script>

<script src="{{ asset('deskapp/src/plugins/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('deskapp/src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('deskapp/src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('deskapp/src/plugins/datatables/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('deskapp/src/plugins/datatables/js/responsive.bootstrap4.min.js') }}"></script>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('password');
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.querySelector('i').classList.toggle('dw-view');
        this.querySelector('i').classList.toggle('dw-hide');
    });
</script>

</body>
</html>