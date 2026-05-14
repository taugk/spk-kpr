<section class="section-padding" id="daftar-perum">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-subtitle">Pilihan Hunian</span>
            <h2 class="section-title">Cluster & Tipe Rumah</h2>
            <p class="text-secondary">Tersedia berbagai tipe rumah dengan spesifikasi unggulan</p>
        </div>
        
        @if($tipeUnit && $tipeUnit->count() > 0)
        <div class="property-slider">
            <div class="slider-container" id="sliderContainer">
                @foreach($tipeUnit as $tipe)
                <div class="slider-slide">
                    <div class="modern-card">
                        <div class="property-img">
                            @php
                                $gambar = json_decode($tipe->gambar, true);
                                $imgSrc = ($gambar && !empty($gambar)) ? asset('storage/' . $gambar[0]) : 'https://placehold.co/600x400/0F5B5A/white?text=' . urlencode($tipe->nama_tipe);
                            @endphp
                            <img src="{{ $imgSrc }}" alt="{{ $tipe->nama_tipe }}" loading="lazy">
                        </div>
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <h4 class="mb-0">{{ $tipe->nama_tipe }}</h4>
                                <span class="badge bg-secondary">{{ $tipe->kode_tipe }}</span>
                            </div>
                            <p class="mt-2"><i class="bi bi-geo-alt"></i> {{ $tipe->nama_proyek }}, {{ $tipe->kota }}</p>
                            <div class="d-flex gap-2 my-2">
                                <span class="badge bg-light text-dark">{{ $tipe->jumlah_kamar ?? 2 }} KT</span>
                                <span class="badge bg-light text-dark">{{ $tipe->jumlah_wc ?? 1 }} KM</span>
                                <span class="badge bg-light text-dark">{{ $tipe->luas_bangunan }}m²</span>
                            </div>
                            <div class="price">Rp {{ number_format($tipe->harga, 0, ',', '.') }}</div>
                            <button class="btn w-100 mt-3 rounded-pill" style="background:#F5EFE5; color:#0F5B5A;" 
                                    onclick="simulasiKPR({{ $tipe->id }}, {{ $tipe->harga }})">
                                Simulasi KPR
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if($tipeUnit->count() > 3)
            <div class="slider-nav" id="sliderNav">
                <button class="slider-btn" id="prevSlide"><i class="bi bi-chevron-left"></i></button>
                <button class="slider-btn" id="nextSlide"><i class="bi bi-chevron-right"></i></button>
            </div>
            <div class="dot-indicators" id="dotIndicators"></div>
            @endif
        </div>
        @else
        <div class="text-center py-5">
            <p class="text-muted">Belum ada data unit tersedia</p>
        </div>
        @endif
    </div>
</section>

