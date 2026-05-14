<aside class="sidebar" id="sidebar">
    <div class="p-3 d-flex align-items-center justify-content-between border-bottom border-light">
        <a href="{{ route('debitur.dashboard') }}" class="text-white text-decoration-none">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-building fs-4"></i>
                <span class="logo-text fw-bold">Citra Pasada</span>
            </div>
        </a>
        <button class="btn btn-sm text-white d-none d-md-block" onclick="toggleSidebar()">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button class="btn btn-sm text-white d-md-none" onclick="toggleMobileSidebar()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    
    <div class="mt-4">
        <!-- User Info (Collapsed mode) -->
        <div class="px-3 mb-4 sidebar-text">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white bg-opacity-25 rounded-circle p-2">
                    <i class="bi bi-person-circle fs-4"></i>
                </div>
                <div>
                    <div class="small opacity-75">Halo,</div>
                    <div class="fw-semibold">{{ Auth::user()->nama_lengkap ?? 'Debitur' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <nav class="nav flex-column">
            <a href="{{ route('debitur.dashboard') }}" class="sidebar-link {{ request()->routeIs('debitur.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
            
            <a href="{{ route('debitur.pengajuan-kpr') }}" class="sidebar-link {{ request()->routeIs('debitur.pengajuan-kpr') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-plus"></i>
                <span class="sidebar-text">Pengajuan KPR</span>
            </a>
            
            <a href="{{ route('debitur.riwayat-pengajuan') }}" class="sidebar-link {{ request()->routeIs('debitur.riwayat-pengajuan') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span class="sidebar-text">Riwayat Pengajuan</span>
            </a>
            
            <a href="{{ route('debitur.simulasi-kpr') }}" class="sidebar-link {{ request()->routeIs('debitur.simulasi-kpr') ? 'active' : '' }}">
                <i class="bi bi-calculator"></i>
                <span class="sidebar-text">Simulasi KPR</span>
            </a>
            
            <a href="{{ route('debitur.properti') }}" class="sidebar-link {{ request()->routeIs('debitur.properti') ? 'active' : '' }}">
                <i class="bi bi-house"></i>
                <span class="sidebar-text">Daftar Properti</span>
            </a>
            
            <a href="{{ route('debitur.dokumentasi') }}" class="sidebar-link {{ request()->routeIs('debitur.dokumentasi') ? 'active' : '' }}">
                <i class="bi bi-folder2"></i>
                <span class="sidebar-text">Dokumentasi</span>
            </a>
            
            <a href="{{ route('debitur.profile') }}" class="sidebar-link {{ request()->routeIs('debitur.profile') ? 'active' : '' }}">
                <i class="bi bi-person"></i>
                <span class="sidebar-text">Profil Saya</span>
            </a>
        </nav>
    </div>
    
    <div class="mt-auto mb-4">
        <hr class="mx-3 text-white opacity-25">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link w-100 text-start border-0 bg-transparent">
                <i class="bi bi-box-arrow-right"></i>
                <span class="sidebar-text">Logout</span>
            </button>
        </form>
    </div>
</aside>