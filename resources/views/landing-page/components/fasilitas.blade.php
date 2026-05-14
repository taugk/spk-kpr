<section class="py-5 position-relative overflow-hidden"
    id="fasilitas"
    style="background:linear-gradient(135deg,var(--white) 0%,var(--tertiary) 100%);">

    <!-- Background Glow -->
    <div class="position-absolute top-0 start-0 rounded-circle"
        style="width:350px;height:350px;background:rgba(15,91,90,.08);filter:blur(90px);">
    </div>

    <div class="position-absolute bottom-0 end-0 rounded-circle"
        style="width:320px;height:320px;background:rgba(226,165,38,.10);filter:blur(100px);">
    </div>

    <div class="container position-relative">

        <!-- Heading -->
        <div class="text-center mb-5" data-aos="fade-up">

            <span class="badge rounded-pill px-4 py-2 fw-semibold mb-3"
                style="background:rgba(226,165,38,.12); color:var(--secondary);">

                <i class="bi bi-buildings me-1"></i>
                Fasilitas Unggulan
            </span>

            <h2 class="fw-bold display-5 mb-3"
                style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--dark);">

                Nikmati
                <span style="color:var(--primary);">
                    Kemewahan & Kenyamanan
                </span>
            </h2>

            <p class="mx-auto fs-5"
                style="max-width:720px; color:var(--text-light); line-height:1.9;">

                Kami menghadirkan fasilitas premium dengan desain modern
                untuk menciptakan lingkungan hunian yang nyaman, aman,
                dan berkualitas bagi keluarga Anda.
            </p>
        </div>

        @if($fasilitas && $fasilitas->count() > 0)

        <div class="row g-4">

            @foreach($fasilitas as $item)

            <div class="col-lg-4 col-md-6"
                data-aos="zoom-in"
                data-aos-delay="{{ $loop->iteration * 100 }}">

                <div class="rounded-5 h-100 p-4 position-relative overflow-hidden border"
                    style="
                        background:rgba(255,255,255,.85);
                        border-color:rgba(15,91,90,.08)!important;
                        box-shadow:var(--shadow-sm);
                        backdrop-filter:blur(14px);
                        transition:var(--transition);
                        animation:floatCard {{ 5 + $loop->iteration }}s ease-in-out infinite;
                    ">

                    <!-- Decorative Background Icon -->
                    <div class="position-absolute top-0 end-0 opacity-10">
                        <i class="bi {{ $item->icon ?? 'bi-building' }}"
                            style="font-size:7rem; color:var(--primary);"></i>
                    </div>

                    <!-- Icon -->
                    <div class="rounded-4 d-inline-flex align-items-center justify-content-center mb-4"
                        style="
                            width:85px;
                            height:85px;
                            background:linear-gradient(135deg,var(--primary),var(--primary-light));
                            box-shadow:0 18px 40px rgba(15,91,90,.22);
                            animation:rotateSoft 8s linear infinite;
                        ">

                        <i class="bi {{ $item->icon ?? 'bi-building' }} text-white fs-2"></i>
                    </div>

                    <!-- Title -->
                    <h4 class="fw-bold mb-3"
                        style="
                            font-family:'Plus Jakarta Sans',sans-serif;
                            color:var(--dark);
                        ">

                        {{ $item->nama }}
                    </h4>

                    <!-- Description -->
                    <p class="mb-4"
                        style="
                            color:var(--text-light);
                            line-height:1.9;
                        ">

                        {{ $item->deskripsi ?? 'Fasilitas lengkap untuk kenyamanan Anda.' }}
                    </p>

                    <!-- Bottom Badge -->
                    <span class="badge rounded-pill px-3 py-2"
                        style="
                            background:rgba(226,165,38,.12);
                            color:var(--secondary);
                        ">

                        Premium Facility
                    </span>

                </div>
            </div>

            @endforeach

        </div>

        @else

        <div class="row g-4">

            <!-- Card 1 -->
            <div class="col-lg-4 col-md-6" data-aos="zoom-in">

                <div class="rounded-5 h-100 p-4 text-center position-relative overflow-hidden border"
                    style="
                        background:rgba(255,255,255,.9);
                        border-color:rgba(15,91,90,.08)!important;
                        box-shadow:var(--shadow-sm);
                        transition:var(--transition);
                        animation:floatCard 6s ease-in-out infinite;
                    ">

                    <div class="position-absolute top-0 end-0 opacity-10">
                        <i class="bi bi-shield-check"
                            style="font-size:7rem; color:var(--primary);"></i>
                    </div>

                    <div class="rounded-4 d-inline-flex align-items-center justify-content-center mb-4"
                        style="
                            width:85px;
                            height:85px;
                            background:linear-gradient(135deg,var(--primary),var(--primary-light));
                            box-shadow:0 18px 40px rgba(15,91,90,.22);
                        ">

                        <i class="bi bi-shield-check text-white fs-2"></i>
                    </div>

                    <h4 class="fw-bold mb-3"
                        style="color:var(--dark);">
                        Keamanan 24 Jam
                    </h4>

                    <p style="color:var(--text-light); line-height:1.9;">
                        Sistem keamanan modern dengan pengawasan penuh selama 24 jam.
                    </p>

                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="100">

                <div class="rounded-5 h-100 p-4 text-center position-relative overflow-hidden border"
                    style="
                        background:rgba(255,255,255,.9);
                        border-color:rgba(15,91,90,.08)!important;
                        box-shadow:var(--shadow-sm);
                        transition:var(--transition);
                        animation:floatCard 7s ease-in-out infinite;
                    ">

                    <div class="position-absolute top-0 end-0 opacity-10">
                        <i class="bi bi-tree"
                            style="font-size:7rem; color:var(--primary);"></i>
                    </div>

                    <div class="rounded-4 d-inline-flex align-items-center justify-content-center mb-4"
                        style="
                            width:85px;
                            height:85px;
                            background:linear-gradient(135deg,var(--primary),var(--primary-light));
                            box-shadow:0 18px 40px rgba(15,91,90,.22);
                        ">

                        <i class="bi bi-tree text-white fs-2"></i>
                    </div>

                    <h4 class="fw-bold mb-3"
                        style="color:var(--dark);">
                        Area Hijau
                    </h4>

                    <p style="color:var(--text-light); line-height:1.9;">
                        Lingkungan asri dan nyaman untuk kualitas hidup keluarga lebih baik.
                    </p>

                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="200">

                <div class="rounded-5 h-100 p-4 text-center position-relative overflow-hidden border"
                    style="
                        background:rgba(255,255,255,.9);
                        border-color:rgba(15,91,90,.08)!important;
                        box-shadow:var(--shadow-sm);
                        transition:var(--transition);
                        animation:floatCard 8s ease-in-out infinite;
                    ">

                    <div class="position-absolute top-0 end-0 opacity-10">
                        <i class="bi bi-water"
                            style="font-size:7rem; color:var(--primary);"></i>
                    </div>

                    <div class="rounded-4 d-inline-flex align-items-center justify-content-center mb-4"
                        style="
                            width:85px;
                            height:85px;
                            background:linear-gradient(135deg,var(--primary),var(--primary-light));
                            box-shadow:0 18px 40px rgba(15,91,90,.22);
                        ">

                        <i class="bi bi-water text-white fs-2"></i>
                    </div>

                    <h4 class="fw-bold mb-3"
                        style="color:var(--dark);">
                        Kolam Renang
                    </h4>

                    <p style="color:var(--text-light); line-height:1.9;">
                        Fasilitas rekreasi keluarga modern untuk kenyamanan penghuni.
                    </p>

                </div>
            </div>

        </div>

        @endif
    </div>

    <!-- Animation -->
    <style>
        @keyframes floatCard {
            0%,100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes rotateSoft {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        #fasilitas .border:hover {
            transform: translateY(-14px) scale(1.02);
            box-shadow: var(--shadow-md) !important;
            border-color: rgba(15,91,90,.18)!important;
        }
    </style>
</section>