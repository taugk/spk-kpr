<section class="py-5 position-relative overflow-hidden"
    id="hero"
    style="background:linear-gradient(135deg,var(--tertiary) 0%,var(--white) 48%,rgba(15,91,90,.08) 100%);">

    <!-- Background Glow -->
    <div class="position-absolute top-0 start-0 rounded-circle"
        style="width:420px;height:420px;background:rgba(15,91,90,.10);filter:blur(110px);">
    </div>

    <div class="position-absolute bottom-0 end-0 rounded-circle"
        style="width:360px;height:360px;background:rgba(226,165,38,.16);filter:blur(110px);">
    </div>

    <div class="container position-relative py-5">
        <div class="row align-items-center g-5">

            <!-- Content -->
            <div class="col-lg-6" data-aos="fade-right">

                <span class="badge rounded-pill px-4 py-2 fw-semibold mb-4"
                    style="
                        background:rgba(226,165,38,.13);
                        color:var(--secondary);
                        animation:floatBadge 3s ease-in-out infinite;
                    ">

                    <i class="bi bi-house-heart-fill me-1"></i>
                    Property Developer Terpercaya
                </span>

                <h1 class="fw-black display-3 mb-4"
                    style="
                        font-family:'Plus Jakarta Sans',sans-serif;
                        font-weight:900;
                        color:var(--dark);
                        line-height:1.08;
                        letter-spacing:-1.5px;
                    ">

                    Hunian Modern
                    <br>
                    & Nyaman Bersama
                    <span style="color:var(--primary);">
                        Citra Persada
                    </span>
                </h1>

                <p class="fs-5 mb-4"
                    style="
                        max-width:650px;
                        color:var(--text-light);
                        line-height:1.9;
                    ">

                    Temukan rumah impian dengan fasilitas lengkap,
                    lokasi strategis, legalitas aman, dan proses KPR
                    yang lebih mudah untuk keluarga Anda.
                </p>

                <!-- Button -->
                <div class="d-flex flex-wrap gap-3 mt-4">

                    <a href="#daftar-perum"
                        class="btn btn-lg rounded-pill px-5 py-3 fw-bold"
                        style="
                            background:linear-gradient(135deg,var(--primary),var(--primary-light));
                            color:var(--white);
                            box-shadow:0 18px 45px rgba(15,91,90,.25);
                            transition:var(--transition);
                        ">

                        Lihat Properti
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>

                    <a href="#"
                        class="btn btn-lg rounded-pill px-5 py-3 fw-bold border"
                        data-bs-toggle="modal"
                        data-bs-target="#kprSimulatorModal"
                        style="
                            background:rgba(255,255,255,.75);
                            color:var(--primary);
                            border-color:rgba(15,91,90,.18)!important;
                            backdrop-filter:blur(10px);
                            transition:var(--transition);
                        ">

                        <i class="bi bi-calculator me-2"></i>
                        Simulasi KPR
                    </a>

                </div>

                <!-- Stats -->
                <div class="row g-3 mt-5">

                    <div class="col-6 col-md-4">
                        <div class="rounded-4 p-3 border"
                            style="
                                background:rgba(255,255,255,.78);
                                border-color:rgba(15,91,90,.08)!important;
                                box-shadow:var(--shadow-sm);
                            ">

                            <h3 class="fw-bold mb-0"
                                style="color:var(--primary);">
                                10+
                            </h3>

                            <small style="color:var(--text-light);">
                                Tahun Pengalaman
                            </small>
                        </div>
                    </div>

                    <div class="col-6 col-md-4">
                        <div class="rounded-4 p-3 border"
                            style="
                                background:rgba(255,255,255,.78);
                                border-color:rgba(15,91,90,.08)!important;
                                box-shadow:var(--shadow-sm);
                            ">

                            <h3 class="fw-bold mb-0"
                                style="color:var(--primary);">
                                500+
                            </h3>

                            <small style="color:var(--text-light);">
                                Unit Terbangun
                            </small>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="rounded-4 p-3 border"
                            style="
                                background:rgba(255,255,255,.78);
                                border-color:rgba(15,91,90,.08)!important;
                                box-shadow:var(--shadow-sm);
                            ">

                            <h3 class="fw-bold mb-0"
                                style="color:var(--primary);">
                                SHM
                            </h3>

                            <small style="color:var(--text-light);">
                                Legal & Aman
                            </small>
                        </div>
                    </div>

                </div>

                @if(isset($proyekTerbaru) && $proyekTerbaru->count() > 0)
                <div class="mt-5">
                    <small class="fw-semibold"
                        style="color:var(--text-light);">
                        Proyek Terbaru:
                    </small>

                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        @foreach($proyekTerbaru as $item)
                        <span class="badge rounded-pill px-3 py-2 border"
                            style="
                                background:rgba(255,255,255,.8);
                                color:var(--dark);
                                border-color:rgba(15,91,90,.10)!important;
                            ">

                            <i class="bi bi-geo-alt-fill me-1"
                                style="color:var(--secondary);"></i>
                            {{ $item->nama_proyek }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>

            <!-- Image -->
            <div class="col-lg-6" data-aos="fade-left">

                <div class="position-relative">

                    <!-- Floating Card 1 -->
                    <div class="position-absolute top-0 start-0 translate-middle-y z-3 d-none d-md-block">
                        <div class="rounded-5 px-4 py-3 border"
                            style="
                                background:rgba(255,255,255,.9);
                                border-color:rgba(15,91,90,.08)!important;
                                backdrop-filter:blur(14px);
                                box-shadow:var(--shadow-md);
                                animation:floatBox 5s ease-in-out infinite;
                            ">

                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-4 d-flex align-items-center justify-content-center"
                                    style="
                                        width:58px;
                                        height:58px;
                                        background:linear-gradient(135deg,var(--primary),var(--primary-light));
                                    ">
                                    <i class="bi bi-shield-check text-white fs-3"></i>
                                </div>

                                <div>
                                    <h5 class="fw-bold mb-0"
                                        style="color:var(--dark);">
                                        Legal Aman
                                    </h5>
                                    <small style="color:var(--text-light);">
                                        SHM & bebas sengketa
                                    </small>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Image Wrapper -->
                    <div class="rounded-5 overflow-hidden border"
                        style="
                            border-color:rgba(15,91,90,.08)!important;
                            box-shadow:var(--shadow-md);
                            transition:var(--transition);
                        ">

                        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c"
                            class="img-fluid"
                            alt="Hero Image"
                            onerror="this.onerror=null; this.src='https://placehold.co/1200x800/0F5B5A/white?text=Citra+Persada+Property';"
                            style="
                                width:100%;
                                height:620px;
                                object-fit:cover;
                                transition:var(--transition);
                            ">
                    </div>

                    <!-- Floating Card 2 -->
                    <div class="position-absolute bottom-0 end-0 translate-middle-y z-3 d-none d-md-block">
                        <div class="rounded-5 px-4 py-3 border"
                            style="
                                background:rgba(255,255,255,.92);
                                border-color:rgba(15,91,90,.08)!important;
                                backdrop-filter:blur(14px);
                                box-shadow:var(--shadow-md);
                                animation:floatBox 6s ease-in-out infinite;
                            ">

                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-4 d-flex align-items-center justify-content-center"
                                    style="
                                        width:58px;
                                        height:58px;
                                        background:rgba(226,165,38,.14);
                                    ">
                                    <i class="bi bi-bank fs-3"
                                        style="color:var(--secondary);"></i>
                                </div>

                                <div>
                                    <h5 class="fw-bold mb-0"
                                        style="color:var(--dark);">
                                        KPR Mudah
                                    </h5>
                                    <small style="color:var(--text-light);">
                                        BTN, Mandiri, BNI
                                    </small>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Animation -->
    <style>
        @keyframes floatBox {
            0%,100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes floatBadge {
            0%,100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-4px);
            }
        }

        #hero .btn:hover {
            transform: translateY(-5px) scale(1.03);
            box-shadow:var(--shadow-md)!important;
        }

        #hero img:hover {
            transform:scale(1.05);
        }
    </style>
</section>