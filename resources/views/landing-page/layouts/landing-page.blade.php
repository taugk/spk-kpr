<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="PT Citra Pasada Property - Hunian Modern & Nyaman. Temukan rumah idaman dengan fasilitas lengkap, lokasi strategis, dan harga terjangkau. Kualitas terbaik untuk masa depan cerah Anda.">
    <meta name="keywords" content="Citra Pasada Property, perumahan, rumah dijual, hunian modern, fasilitas lengkap, lokasi strategis, harga terjangkau">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PT Citra Pasada Property | Hunian Modern & Nyaman')</title>

    <!-- Bootstrap 5 + Icons + Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary: #0F5B5A;
            --primary-light: #20807E;
            --secondary: #E2A526;
            --secondary-light: #F5D99B;
            --tertiary: #F4F0E6;
            --dark: #2C3E3B;
            --text-light: #6B7A77;
            --white: #FFFFFF;
            --shadow-sm: 0 10px 30px -8px rgba(0,0,0,0.08);
            --shadow-md: 0 20px 35px -12px rgba(0,0,0,0.12);
            --transition: all 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--tertiary);
            color: var(--dark);
            overflow-x: hidden;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(35px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slowFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }

        .animate-float { animation: slowFloat 5s ease-in-out infinite; }
        
        /* Navbar */
        .navbar {
            background: rgba(255,255,255,0.96);
            backdrop-filter: blur(12px);
            padding: 0.8rem 0;
            box-shadow: 0 1px 0 rgba(0,0,0,0.03), 0 8px 20px rgba(0,0,0,0.03);
            transition: var(--transition);
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #0F5B5A 0%, #1B7E7C 100%);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }
        .nav-link {
            font-weight: 600;
            color: var(--dark);
            margin: 0 0.2rem;
            transition: var(--transition);
        }
        .nav-link:hover { color: var(--secondary); }
        
        /* Tombol Login & Register di navbar */
        .btn-login-nav {
            border: 1.5px solid #D4CBB8;
            border-radius: 50px;
            padding: 8px 24px;
            font-weight: 600;
            transition: var(--transition);
            background: transparent;
            color: var(--dark);
            text-decoration: none;
        }
        .btn-login-nav:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        .btn-register-nav {
            background: var(--primary);
            border-radius: 50px;
            padding: 8px 24px;
            font-weight: 700;
            color: white;
            box-shadow: 0 5px 15px rgba(15,91,90,0.25);
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-register-nav:hover {
            background: #0a4a49;
            transform: translateY(-2px);
            box-shadow: 0 12px 22px rgba(15,91,90,0.3);
            color: white;
        }
        .btn-outline-custom {
            border: 1.5px solid #D4CBB8;
            border-radius: 50px;
            padding: 8px 24px;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-outline-custom:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        .btn-primary-custom {
            background: var(--primary);
            border-radius: 50px;
            padding: 8px 28px;
            font-weight: 700;
            color: white;
            box-shadow: 0 5px 15px rgba(15,91,90,0.25);
            transition: var(--transition);
        }
        .btn-primary-custom:hover {
            background: #0a4a49;
            transform: translateY(-2px);
            color: white;
        }
        .btn-gold {
            background: var(--secondary);
            border: none;
            border-radius: 60px;
            padding: 12px 32px;
            font-weight: 800;
            color: #1F3E3B;
            transition: var(--transition);
            box-shadow: 0 12px 20px rgba(226,165,38,0.25);
        }
        .btn-gold:hover {
            background: #c9921f;
            transform: translateY(-3px);
            color: #1F3E3B;
        }

        /* Hero */
        .hero-company {
            background: linear-gradient(128deg, #F7F3EA 0%, #FEFAF2 100%);
            padding: 7rem 0 4rem;
            position: relative;
        }
        .hero-title {
            font-weight: 800;
            font-size: clamp(2rem, 5vw, 3.8rem);
            line-height: 1.2;
            color: #1F3E3B;
        }
        .hero-title span { color: var(--secondary); display: inline-block; position: relative; }
        .hero-title span:after {
            content: ''; position: absolute; bottom: 8px; left: 0; width: 100%; height: 8px;
            background: rgba(226,165,38,0.3); border-radius: 10px; z-index: -1;
        }
        .hero-image-wrapper { border-radius: 45px; overflow: hidden; box-shadow: var(--shadow-md); }

        /* Section */
        .section-padding { padding: 80px 0; }
        .section-title { font-weight: 800; font-size: clamp(1.8rem, 4vw, 2.6rem); color: var(--dark); margin-bottom: 15px; }
        .section-subtitle { color: var(--secondary); font-weight: 700; letter-spacing: 1px; margin-bottom: 10px; }

        /* Modern Card untuk properti */
        .modern-card {
            background: white;
            border-radius: 32px;
            overflow: hidden;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            border: 1px solid #F0E8DC;
            height: 100%;
            margin-bottom: 20px;
        }
        .modern-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-md); }
        .property-img { height: 230px; overflow: hidden; }
        .property-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .modern-card:hover .property-img img { transform: scale(1.05); }
        .price { font-size: 1.6rem; font-weight: 800; color: var(--primary); }
        
        /* Facility icon */
        .facility-icon {
            width: 70px; height: 70px; background: linear-gradient(135deg, #EFF7F6, #FEF6E8);
            border-radius: 24px; display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: var(--primary); margin-bottom: 1.2rem;
        }
        .promo-badge { background: var(--secondary); color: #1F3E3B; font-weight: 800; border-radius: 30px; padding: 5px 16px; display: inline-block; }
        .testimonial-card { background: white; border-radius: 28px; padding: 2rem; box-shadow: var(--shadow-sm); border: 1px solid #EFE6DB; }
        .testimonial-avatar { width: 60px; height: 60px; background: var(--primary-light); border-radius: 60px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; }
        .location-card { background: white; border-radius: 32px; overflow: hidden; box-shadow: var(--shadow-sm); }
        .stat-card { background: white; border-radius: 40px; padding: 2rem; margin-top: -2rem; position: relative; z-index: 10; box-shadow: var(--shadow-sm); border: 1px solid #EDE5D6; }
        .stat-number { font-size: 2.2rem; font-weight: 800; color: var(--primary); }

        /* Simulator */
        .simulator-section { background: linear-gradient(135deg, #FFFFFF 0%, #FCF9F3 100%); border-radius: 48px; box-shadow: var(--shadow-sm); }
        .result-box { background: var(--primary); border-radius: 28px; color: white; padding: 1.5rem; }
        .range-slider { width: 100%; height: 6px; border-radius: 10px; background: #E2D5C4; outline: none; }
        .range-slider::-webkit-slider-thumb { -webkit-appearance: none; width: 20px; height: 20px; border-radius: 50%; background: var(--primary); cursor: pointer; }
        .tahun-btn { border-radius: 40px; background: #F0E8DE; margin: 0 5px; border: none; padding: 6px 18px; font-weight: 600; transition:0.2s; }
        .tahun-btn.active-tahun { background: #0F5B5A; color: white; }
        .cta-banner { background: linear-gradient(115deg, #0F5B5A 0%, #1B4E4D 100%); border-radius: 48px; padding: 3rem; }
        footer { background: #1C2F2D; color: #C7D6D2; }
        
        /* Custom Carousel (slider) */
        .property-slider {
            position: relative;
            overflow: hidden;
            padding: 0 10px;
        }
        .slider-container {
            display: flex;
            overflow-x: hidden;
            scroll-behavior: smooth;
            scrollbar-width: none;
            gap: 24px;
        }
        .slider-container::-webkit-scrollbar { display: none; }
        .slider-slide {
            flex: 0 0 calc(33.333% - 16px);
            min-width: calc(33.333% - 16px);
            transition: transform 0.3s;
        }
        .slider-nav {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 30px;
        }
        .slider-btn {
            width: 48px;
            height: 48px;
            border-radius: 60px;
            background: white;
            border: 1px solid #e2d9ce;
            color: var(--primary);
            font-size: 1.4rem;
            transition: all 0.2s;
            box-shadow: var(--shadow-sm);
        }
        .slider-btn:hover {
            background: var(--primary);
            color: white;
            transform: scale(1.05);
        }
        .dot-indicators {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
        }
        .dot {
            width: 10px;
            height: 10px;
            border-radius: 10px;
            background: #cdc2b0;
            cursor: pointer;
            transition: 0.2s;
        }
        .dot.active {
            width: 26px;
            background: var(--secondary);
        }
        @media (max-width: 992px) {
            .slider-slide { flex: 0 0 calc(50% - 12px); min-width: calc(50% - 12px); }
        }
        @media (max-width: 768px) {
            .slider-slide { flex: 0 0 100%; min-width: 100%; }
            .section-padding { padding: 50px 0; }
            .hero-company { padding: 6rem 0 2rem; }
            .stat-card { margin-top: 1rem; }
        }
    </style>
    
    @stack('styles')
</head>
<body>

    @include('landing-page.components.navbar')

    <main>
        @yield('content')
    </main>

    @include('landing-page.components.footer')
    @include('landing-page.components.modals')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    @stack('scripts')
    
    
</body>
</html>