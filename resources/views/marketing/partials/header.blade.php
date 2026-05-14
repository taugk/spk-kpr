<div class="header">
    <div class="header-left">
        <div class="menu-icon dw dw-menu"></div>

        <div class="header-search">
            <form action="{{ route('marketing.pengajuan.semua') }}" method="GET">
                <div class="form-group mb-0 position-relative">
                    <i class="dw dw-search2 search-icon"></i>
                    <input type="text" name="q" class="form-control search-input" placeholder="Cari kode, debitur, unit...">
                    </div>
            </form>
        </div>
    </div>

    <div class="header-right">
        <div class="dashboard-setting user-notification">
            <div class="dropdown">
                <a class="dropdown-toggle no-arrow" href="javascript:;" data-toggle="right-sidebar">
                    <i class="dw dw-settings2"></i>
                </a>
            </div>
        </div>

        <div class="user-notification">
            <div class="dropdown">
                <a class="dropdown-toggle no-arrow" href="#" role="button" data-toggle="dropdown">
                    <i class="icon-copy dw dw-notification"></i>
                    @if(($unreadNotifications ?? 0) > 0)
                        <span class="badge notification-active"></span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="notification-list mx-h-350 customscroll">
                        <ul>
                            @forelse(($notifications ?? collect()) as $notification)
                                <li>
                                    <a href="{{ $notification->pengajuan_id ? route('marketing.pengajuan.show', $notification->pengajuan_id) : '#' }}">
                                        <img src="{{ asset('deskapp/vendors/images/img.jpg') }}" alt="">
                                        <h3 class="clearfix">{{ $notification->judul }} <span>{{ $notification->created_at->diffForHumans() }}</span></h3>
                                        <p>{{ Str::limit($notification->pesan, 70) }}</p>
                                    </a>
                                </li>
                            @empty
                                <li class="text-center p-3">Belum ada notifikasi.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="user-info-dropdown">
            <div class="dropdown">
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <span class="user-icon">
                        <img src="{{ Auth::check() && Auth::user()->foto_profil
                                ? asset('storage/' . Auth::user()->foto_profil)
                                : asset('assets/images/default-avatar.png') }}"
                                alt="User Profile"
                            >
                    </span>
                    <span class="user-name">{{ Auth::check() ? Auth::user()->nama_lengkap : 'Marketing' }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                    <a class="dropdown-item" href="#"><i class="dw dw-user1"></i> Profil</a>
                    <a class="dropdown-item" href="{{ route('marketing.pengaturan.index') }}"><i class="dw dw-settings2"></i> Pengaturan</a>
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('logout') }}" method="POST" id="logout-form">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-left">
                            <i class="dw dw-logout"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
