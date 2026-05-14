<section class="section-padding" id="testimoni">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-subtitle">Testimoni</span>
            <h2 class="section-title">Apa Kata Mereka?</h2>
            <p class="text-secondary">Pengalaman nyata dari para pembeli yang sudah bersama kami</p>
        </div>
        
        @if($testimoni && $testimoni->count() > 0)
        <div class="row g-4">
            @foreach($testimoni as $item)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                <div class="testimonial-card">
                    <div class="d-flex align-items-center mb-3">
                        <div class="testimonial-avatar me-3">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $item->user_name ?? $item->nama_lengkap }}</h5>
                            <small class="text-muted">{{ $item->pekerjaan ?? 'Konsumen' }}</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= ($item->rating ?? 5) ? '-fill' : '' }}" style="color: #E2A526;"></i>
                        @endfor
                    </div>
                    <p class="text-muted">"{{ $item->komentar }}"</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <p class="text-muted">Belum ada testimoni</p>
        </div>
        @endif
    </div>
</section>