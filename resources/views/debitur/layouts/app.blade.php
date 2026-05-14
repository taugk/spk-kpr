<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Debitur | Citra Pasada Property')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0F5B5A;
            --secondary: #E2A526;
            --dark: #2C3E3B;
            --gray: #6B7A77;
            --light-bg: #F8F9FA;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--light-bg);
        }

        /* Navbar Debitur */
        .navbar-debitur {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 0.8rem 0;
        }

        .brand {
            font-weight: 800;
            font-size: 1.3rem;
            background: linear-gradient(135deg, #0F5B5A 0%, #1B7E7C 100%);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            text-decoration: none;
        }

        /* Cards */
        .status-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            transition: all 0.3s;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .status-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .status-icon {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        /* Timeline */
        .timeline-item {
            border-left: 2px solid #E2A526;
            padding-left: 1.5rem;
            position: relative;
            margin-bottom: 1.5rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 0;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #E2A526;
        }

        /* Badges */
        .badge-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-waiting { background: #FFF3E0; color: #E65100; }
        .badge-verified { background: #E8F5E9; color: #2E7D32; }
        .badge-process { background: #E3F2FD; color: #1565C0; }
        .badge-completed { background: #E0F2F1; color: #00695C; }

        /* Button */
        .btn-gold {
            background: #E2A526;
            border: none;
            color: #1F3E3B;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 12px;
        }
        .btn-gold:hover {
            background: #c9921f;
            color: #1F3E3B;
        }

        .btn-outline-primary-custom {
            border: 1.5px solid #0F5B5A;
            background: transparent;
            color: #0F5B5A;
            border-radius: 12px;
            padding: 10px 24px;
            font-weight: 600;
        }
        .btn-outline-primary-custom:hover {
            background: #0F5B5A;
            color: white;
        }

        /* Main Content */
        .main-content {
            min-height: calc(100vh - 70px);
            padding: 30px 0;
        }

        footer {
            background: #1C2F2D;
            color: #C7D6D2;
            padding: 20px 0;
            margin-top: 40px;
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Navbar -->
    @include('debitur.components.navbar')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    @include('debitur.components.footer')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init();</script>
    @stack('scripts')
</body>
</html>