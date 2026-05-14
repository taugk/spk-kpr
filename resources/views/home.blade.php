@extends('landing-page.layouts.landing-page')

@section('title', 'PT Citra Persada Property | Hunian Modern & Nyaman')

@section('content')
    @include('landing-page.components.hero', ['proyekTerbaru' => $proyekTerbaru ])
    @include('landing-page.components.stats', ['statistik' => $statistik ?? []])
    @include('landing-page.components.perumahan-slider', ['tipeUnit' => $tipeUnit ?? collect()])
    @include('landing-page.components.fasilitas', ['fasilitas' => $fasilitas ?? collect()])
    @include('landing-page.components.kenapa-kami')
    @include('landing-page.components.promo')
    @include('landing-page.components.testimoni', ['testimoni' => $testimoni ?? collect()])
    @include('landing-page.components.lokasi')
    @include('landing-page.components.simulasi-kpr')
    {{-- @include('landing-page.components.cta-banner') --}}
@endsection

@push('scripts')
<script>
    AOS.init({ duration: 800, once: true, offset: 30 });
    
    // Simulasi KPR Logic
    const hargaInput = document.getElementById('hargaProperti');
    const dpSlider = document.getElementById('dpSlider');
    const dpValueSpan = document.getElementById('dpValue');
    const tahunBtns = document.querySelectorAll('.tahun-btn');
    let selectedTahun = 15;
    const bungaInput = document.getElementById('bunga');
    const hasilSpan = document.getElementById('cicilanBulanan');
    
    function formatRupiah(angka) { 
        return 'Rp ' + angka.toLocaleString('id-ID'); 
    }
    
    function hitungKPR() {
        let harga = parseFloat(hargaInput?.value) || 0;
        let dpPersen = parseFloat(dpSlider?.value) || 0;
        let bungaTahunan = parseFloat(bungaInput?.value) || 9.5;
        let tenorTahun = selectedTahun;
        let pokokPinjaman = harga - (harga * dpPersen / 100);
        if(pokokPinjaman < 0) pokokPinjaman = 0;
        let bungaFlatTotal = pokokPinjaman * (bungaTahunan / 100) * tenorTahun;
        let angsuran = (pokokPinjaman + bungaFlatTotal) / (tenorTahun * 12);
        if(hasilSpan) hasilSpan.innerText = formatRupiah(Math.round(angsuran));
    }
    
    if(dpSlider) {
        dpSlider.addEventListener('input', function() { 
            if(dpValueSpan) dpValueSpan.innerText = this.value + '%'; 
            hitungKPR(); 
        });
    }
    if(hargaInput) hargaInput.addEventListener('input', hitungKPR);
    if(bungaInput) bungaInput.addEventListener('input', hitungKPR);
    
    if(tahunBtns.length > 0) {
        tahunBtns.forEach(btn => { 
            btn.addEventListener('click', function() { 
                tahunBtns.forEach(b => b.classList.remove('active-tahun')); 
                this.classList.add('active-tahun'); 
                selectedTahun = parseInt(this.getAttribute('data-tahun')); 
                hitungKPR(); 
            }); 
        });
    }
    
    const hitungBtn = document.getElementById('hitungSimulasi');
    if(hitungBtn) hitungBtn.addEventListener('click', hitungKPR);
    hitungKPR();
    
    // Smooth scroll navbar
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => { 
        link.addEventListener('click', function(e) { 
            e.preventDefault(); 
            const targetId = this.getAttribute('href').substring(1); 
            const targetEl = document.getElementById(targetId); 
            if(targetEl) targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' }); 
        }); 
    });
    
    // SLIDER LOGIC
    const container = document.getElementById('sliderContainer');
    const slides = document.querySelectorAll('.slider-slide');
    const prevBtn = document.getElementById('prevSlide');
    const nextBtn = document.getElementById('nextSlide');
    const sliderNav = document.getElementById('sliderNav');
    const dotContainer = document.getElementById('dotIndicators');
    let currentIndex = 0;
    let slidesPerView = 3;
    let totalSlides = slides.length;
    
    if(container && slides.length > 0) {
        function updateSlidesPerView() {
            if (window.innerWidth >= 992) slidesPerView = 3;
            else if (window.innerWidth >= 768) slidesPerView = 2;
            else slidesPerView = 1;
            renderDots();
            scrollToIndex(0);
        }
        
        function totalGroups() { 
            return Math.max(1, Math.ceil(totalSlides / slidesPerView)); 
        }
        
        function renderDots() {
            if(!dotContainer) return;
            dotContainer.innerHTML = '';
            const groups = totalGroups();
            for (let i = 0; i < groups; i++) {
                const dot = document.createElement('div');
                dot.classList.add('dot');
                if (i === currentIndex) dot.classList.add('active');
                dot.addEventListener('click', () => scrollToIndex(i));
                dotContainer.appendChild(dot);
            }
        }
        
        function scrollToIndex(groupIndex) {
            currentIndex = Math.min(groupIndex, totalGroups() - 1);
            if (currentIndex < 0) currentIndex = 0;
            const slideWidth = slides[0]?.offsetWidth + 24;
            const scrollAmount = currentIndex * slidesPerView * slideWidth;
            container.scrollTo({ left: scrollAmount, behavior: 'smooth' });
            updateDotsActive();
        }
        
        function updateDotsActive() {
            const dots = document.querySelectorAll('.dot');
            dots.forEach((dot, idx) => { 
                if (idx === currentIndex) dot.classList.add('active'); 
                else dot.classList.remove('active'); 
            });
        }
        
        function nextGroup() { 
            if (currentIndex + 1 < totalGroups()) scrollToIndex(currentIndex + 1); 
        }
        
        function prevGroup() { 
            if (currentIndex - 1 >= 0) scrollToIndex(currentIndex - 1); 
        }
        
        if (totalSlides > 3 && sliderNav) {
            sliderNav.style.display = 'flex';
            updateSlidesPerView();
            window.addEventListener('resize', () => { updateSlidesPerView(); });
            if(prevBtn) prevBtn.addEventListener('click', prevGroup);
            if(nextBtn) nextBtn.addEventListener('click', nextGroup);
            if(container) {
                container.addEventListener('scroll', () => {
                    const slideWidth = slides[0]?.offsetWidth + 24;
                    const scrollPos = container.scrollLeft;
                    const newIndex = Math.round(scrollPos / (slidesPerView * slideWidth));
                    if (newIndex !== currentIndex && newIndex < totalGroups()) { 
                        currentIndex = newIndex; 
                        updateDotsActive(); 
                    }
                });
            }
        } else if(sliderNav) { 
            sliderNav.style.display = 'none'; 
            if(dotContainer) dotContainer.style.display = 'none';
        }
    }
</script>
@endpush