<nav class="navbar-debitur">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center w-100">
            <!-- Brand -->
            <a href="{{ route('debitur.dashboard') }}" class="brand">
                <i class="bi bi-building"></i> Citra Pasada Property
            </a>

            <!-- User Menu -->
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5"></i>
                    <span>{{ Auth::user()->nama_lengkap ?? 'Debitur' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('debitur.profil') }}">
                        <i class="bi bi-person"></i> Profil Saya
                    </a></li>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('debitur.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>