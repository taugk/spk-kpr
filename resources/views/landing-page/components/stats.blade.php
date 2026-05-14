<section class="container mt-4" data-aos="fade-up">
    <div class="stat-card">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-number">{{ $statistik['total_proyek'] ?? 0 }}</div>
                <div class="text-muted">Proyek Aktif</div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-number">{{ $statistik['total_tipe_unit'] ?? 0 }}</div>
                <div class="text-muted">Tipe Rumah</div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-number">{{ $statistik['total_unit_tersedia'] ?? 0 }}</div>
                <div class="text-muted">Unit Tersedia</div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                @php
                    $harga = $statistik['harga_termurah'] ?? 0;
                    if ($harga >= 1000000000) {
                        $formatHarga = number_format($harga / 1000000000, 1, ',', '.') . ' M';
                    } elseif ($harga >= 1000000) {
                        $formatHarga = number_format($harga / 1000000, 1, ',', '.') . ' Jt';
                    } else {
                        $formatHarga = number_format($harga, 0, ',', '.');
                    }
                @endphp
                <div class="stat-number">Rp {{ $formatHarga }}</div>
                <div class="text-muted">Harga Mulai Dari</div>
            </div>
        </div>
    </div>
</section>