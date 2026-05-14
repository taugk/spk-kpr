{{-- resources/views/admin/pages/penilaian/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Penilaian SMART - ' . ($pengajuan->kode_pengajuan ?? 'Pengajuan'))

@section('page_action')
    <div class="btn-list">
        <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-secondary btn-sm">
            <i class="dw dw-left-arrow1"></i> Kembali
        </a>
        <button type="button" onclick="window.print()" class="btn btn-outline-primary btn-sm">
            <i class="dw dw-print"></i> Cetak
        </button>
    </div>
@endsection

@section('content')
@php
    function rupiah_penilaian($value) {
        return 'Rp ' . number_format((float) ($value ?? 0), 0, ',', '.');
    }

    $statusMap = [
        'belum_menikah' => 'Belum Menikah',
        'menikah' => 'Menikah',
        'cerai' => 'Cerai',
    ];

    $jenisKelaminMap = [
        'L' => 'Laki-laki',
        'P' => 'Perempuan',
    ];
@endphp

<div class="min-height-200px">
    <form action="{{ route('admin.penilaian.store') }}" method="POST" id="penilaianForm">
        @csrf

        <input type="hidden" name="pengajuan_id" value="{{ $pengajuan->id ?? 1 }}">

        {{-- Header Ringkasan --}}
        <div class="page-header mb-30">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4 class="text-blue">
                            {{ $pengajuan->nama_debitur ?? ($debiturPribadi->nama_lengkap ?? '-') }}
                        </h4>
                    </div>

                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="#">Penilaian</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ $pengajuan->kode_pengajuan ?? 'KPR-2026-00001' }}
                            </li>
                        </ol>
                    </nav>
                </div>

                <div class="col-md-6 col-sm-12 text-right">
                    <div class="bg-light p-2 rounded border d-inline-block">
                        <small class="text-muted d-block text-uppercase">Plafon Pinjaman</small>
                        <h5 class="text-primary mb-0">
                            {{ rupiah_penilaian($pengajuan->jumlah_pinjaman ?? 0) }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- KOLOM KIRI --}}
            <div class="col-xl-8 col-lg-8 col-md-12 mb-30">

                {{-- Detail Pengajuan --}}
                <div class="card-box mb-30">
                    <div class="pd-20">
                        <h4 class="text-blue h4 mb-0">Detail Pengajuan Debitur</h4>
                    </div>

                    <div class="tab">
                        <ul class="nav nav-tabs customtab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tabPribadi" role="tab">
                                    <i class="dw dw-user1"></i> Pribadi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabPekerjaan" role="tab">
                                    <i class="dw dw-briefcase"></i> Pekerjaan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabKeuangan" role="tab">
                                    <i class="dw dw-money"></i> Keuangan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabProperti" role="tab">
                                    <i class="dw dw-house-1"></i> Properti
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabDokumen" role="tab">
                                    <i class="dw dw-file"></i> Dokumen
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content pd-20">

                            {{-- TAB PRIBADI --}}
                            <div class="tab-pane fade show active" id="tabPribadi" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Nama Lengkap</label>
                                        <p>{{ $debiturPribadi->nama_lengkap ?? ($pengajuan->nama_debitur ?? '-') }}</p>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">NIK</label>
                                        <p>{{ $debiturPribadi->nik ?? '-' }}</p>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Tempat Lahir</label>
                                        <p>{{ $debiturPribadi->tempat_lahir ?? '-' }}</p>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Tanggal Lahir</label>
                                        <p>
                                            {{ !empty($debiturPribadi->tanggal_lahir) ? date('d M Y', strtotime($debiturPribadi->tanggal_lahir)) : '-' }}
                                        </p>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Jenis Kelamin</label>
                                        <p>{{ $jenisKelaminMap[$debiturPribadi->jenis_kelamin ?? ''] ?? '-' }}</p>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Agama</label>
                                        <p>{{ $debiturPribadi->agama ?? '-' }}</p>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Status Pernikahan</label>
                                        <p>{{ $statusMap[$debiturPribadi->status_pernikahan ?? ''] ?? '-' }}</p>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Jumlah Tanggungan</label>
                                        <p>{{ $debiturPribadi->jumlah_tanggungan ?? 0 }} orang</p>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Pendidikan Terakhir</label>
                                        <p>{{ $debiturPribadi->pendidikan_terakhir ?? '-' }}</p>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">No. HP</label>
                                        <p>{{ $debiturPribadi->no_hp ?? $debiturPribadi->telepon ?? '-' }}</p>
                                    </div>

                                    <div class="col-md-12 form-group">
                                        <label class="font-weight-bold">Alamat KTP</label>
                                        <p class="mb-0">{{ $debiturPribadi->alamat_ktp ?? '-' }}</p>
                                        <small class="text-muted">
                                            {{ $debiturPribadi->kelurahan ?? '-' }},
                                            {{ $debiturPribadi->kecamatan ?? '-' }},
                                            {{ $debiturPribadi->kota ?? '-' }},
                                            {{ $debiturPribadi->provinsi ?? '-' }}
                                        </small>
                                    </div>

                                    <div class="col-md-12 form-group">
                                        <label class="font-weight-bold">Alamat Domisili</label>
                                        <p class="mb-0">{{ $debiturPribadi->alamat_domisili ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB PEKERJAAN --}}
                            <div class="tab-pane fade" id="tabPekerjaan" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Jenis Pekerjaan</label>
                                        <p>{{ $debiturPekerjaan->jenis_pekerjaan ?? '-' }}</p>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Nama Perusahaan</label>
                                        <p>{{ $debiturPekerjaan->nama_perusahaan ?? '-' }}</p>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Jabatan</label>
                                        <p>{{ $debiturPekerjaan->jabatan ?? '-' }}</p>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Lama Bekerja</label>
                                        <p>{{ $debiturPekerjaan->lama_bekerja ?? '-' }}</p>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Gaji Pokok</label>
                                        <p>{{ rupiah_penilaian($debiturPekerjaan->gaji_pokok ?? 0) }}</p>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Penghasilan Tambahan</label>
                                        <p>{{ rupiah_penilaian($debiturPekerjaan->penghasilan_tambahan ?? 0) }}</p>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="alert alert-info py-2">
                                            Total Penghasilan:
                                            <strong>{{ rupiah_penilaian($debiturPekerjaan->total_penghasilan ?? 0) }}</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-12 form-group">
                                        <label class="font-weight-bold">Alamat Perusahaan</label>
                                        <p>{{ $debiturPekerjaan->alamat_perusahaan ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB KEUANGAN --}}
                            <div class="tab-pane fade" id="tabKeuangan" role="tabpanel">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        Penghasilan Bulanan
                                        <strong>{{ rupiah_penilaian($debiturKeuangan->penghasilan_bulanan ?? $debiturPekerjaan->total_penghasilan ?? 0) }}</strong>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        Pengeluaran Bulanan
                                        <strong>{{ rupiah_penilaian($debiturKeuangan->pengeluaran_bulanan ?? 0) }}</strong>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        Total Cicilan Lain
                                        <span class="text-danger">
                                            {{ rupiah_penilaian($debiturKeuangan->total_cicilan_perbulan ?? 0) }}
                                        </span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        Rasio Cicilan
                                        <span class="badge badge-primary badge-pill">
                                            {{ number_format($pengajuan->rasio_angsuran ?? $debiturKeuangan->rasio_cicilan ?? 0, 2) }}%
                                        </span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        Status Kredit
                                        <span class="badge badge-success">
                                            {{ strtoupper($debiturKeuangan->status_kredit ?? 'lancar') }}
                                        </span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        Riwayat Tunggakan
                                        <strong>{{ $debiturKeuangan->riwayat_tunggakan ?? '-' }}</strong>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        Tabungan / Aset
                                        <strong>{{ rupiah_penilaian($debiturKeuangan->tabungan_aset ?? 0) }}</strong>
                                    </li>
                                </ul>
                            </div>

                            {{-- TAB PROPERTI --}}
                            <div class="tab-pane fade" id="tabProperti" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="text-muted">Proyek</label>
                                        <h6>{{ $pengajuan->nama_proyek ?? '-' }}</h6>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="text-muted">Tipe Unit</label>
                                        <h6>{{ $pengajuan->nama_tipe ?? '-' }}</h6>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="text-muted">Harga Properti</label>
                                        <h6>{{ rupiah_penilaian($pengajuan->harga_properti ?? $pengajuan->harga_unit ?? 0) }}</h6>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="text-muted">Uang Muka</label>
                                        <h6>{{ rupiah_penilaian($pengajuan->uang_muka ?? $pengajuan->dp ?? 0) }}</h6>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="text-muted">Jumlah Pinjaman</label>
                                        <h6>{{ rupiah_penilaian($pengajuan->jumlah_pinjaman ?? 0) }}</h6>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="text-muted">Tenor</label>
                                        <h6>{{ $pengajuan->tenor ?? 0 }} Tahun</h6>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="text-muted">Estimasi Angsuran</label>
                                        <h6>{{ rupiah_penilaian($pengajuan->estimasi_angsuran ?? $pengajuan->angsuran_perbulan ?? 0) }}</h6>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="text-muted">Status Pengajuan</label>
                                        <h6>{{ ucwords(str_replace('_', ' ', $pengajuan->status ?? '-')) }}</h6>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB DOKUMEN --}}
                            <div class="tab-pane fade" id="tabDokumen" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Jenis Dokumen</th>
                                                <th>Nama File</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($dokumen as $jenis => $docs)
                                                @foreach($docs as $doc)
                                                    <tr>
                                                        <td>{{ ucfirst(str_replace('_', ' ', $jenis)) }}</td>
                                                        <td>{{ $doc->nama_file ?? $doc->file ?? '-' }}</td>
                                                        <td>
                                                            @if(($doc->status_verifikasi ?? '') == 'valid')
                                                                <span class="badge badge-success">
                                                                    <i class="fa fa-check-circle"></i> Valid
                                                                </span>
                                                            @elseif(($doc->status_verifikasi ?? '') == 'tidak_valid')
                                                                <span class="badge badge-danger">
                                                                    <i class="fa fa-times-circle"></i> Tidak Valid
                                                                </span>
                                                            @else
                                                                <span class="badge badge-warning">
                                                                    <i class="fa fa-clock-o"></i> Menunggu
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">
                                                        Belum ada dokumen.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Kriteria Penilaian --}}
                <div class="card-box pd-20 mb-30">
                    <h4 class="text-blue h4 mb-20">Kriteria Penilaian SMART</h4>

                    @foreach($kriteria as $krit)
                        <div class="p-3 mb-3 border rounded border-light" id="card-krit-{{ $krit->id }}">
                            <div class="d-flex justify-content-between mb-3">
                                <h6 class="h6">
                                    <span class="badge badge-outline-primary mr-2">
                                        {{ $krit->kode_kriteria }}
                                    </span>
                                    {{ $krit->nama_kriteria }}
                                </h6>

                                <span class="small text-muted">
                                    Bobot: {{ $krit->bobot }}%
                                </span>
                            </div>

                            <div class="row">
                                @foreach($krit->skala as $skala)
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-radio">
                                            <input type="radio"
                                                   id="radio-{{ $krit->id }}-{{ $skala->skor }}"
                                                   name="nilai[{{ $krit->id }}]"
                                                   value="{{ $skala->skor }}"
                                                   data-kid="{{ $krit->id }}"
                                                   class="custom-control-input"
                                                   {{ old('nilai.' . $krit->id) == $skala->skor ? 'checked' : '' }}>

                                            <label class="custom-control-label" for="radio-{{ $krit->id }}-{{ $skala->skor }}">
                                                <span class="badge badge-secondary mx-1">
                                                    {{ $skala->skor }}
                                                </span>
                                                {{ $skala->keterangan }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-2 text-right border-top pt-2">
                                <small class="text-blue font-weight-bold" id="selval-{{ $krit->id }}">
                                    Terpilih: —
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- SIDEBAR KANAN --}}
            <div class="col-xl-4 col-lg-4 col-md-12">

                {{-- Status Pengisian --}}
                <div class="card-box pd-20 mb-30">
                    <h5 class="h5 mb-20">Status Pengisian</h5>

                    <div class="progress mb-10" style="height: 10px;">
                        <div class="progress-bar" role="progressbar" id="progressBar" style="width: 0%"></div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <small id="progressLabel">0/0</small>
                        <small id="progressPct">0%</small>
                    </div>

                    <div class="mt-3" style="max-height: 200px; overflow-y: auto;">
                        @foreach($kriteria as $krit)
                            <div class="d-flex align-items-center mb-1 small">
                                <i class="fa fa-circle text-light mr-2" id="chkdot-{{ $krit->id }}"></i>
                                <span class="text-muted">{{ $krit->kode_kriteria }}</span>
                                <span class="ml-2 text-truncate">{{ $krit->nama_kriteria }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pengaturan --}}
                <div class="card-box pd-20 mb-30">
                    <div class="form-group">
                        <label>Threshold Kelayakan (%)</label>
                        <input type="number"
                               name="threshold"
                               id="threshold"
                               class="form-control"
                               value="{{ old('threshold', $threshold ?? 65) }}">
                    </div>

                    <div class="form-group">
                        <label>Catatan Penilaian</label>
                        <textarea name="catatan"
                                  class="form-control"
                                  rows="4">{{ old('catatan', $nilaiTersimpan['catatan'] ?? '') }}</textarea>
                    </div>

                    <div class="alert alert-warning py-2 mb-20 d-none" id="alertIncomplete">
                        <i class="fa fa-exclamation-triangle"></i>
                        Belum lengkap:
                        <span id="sisaKrit"></span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="btnHitung">
                        <i class="dw dw-checked"></i> Simpan & Hitung
                    </button>

                    <p class="text-center text-muted small mt-2">
                        Data akan diproses menggunakan metode SMART
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
(function () {
    'use strict';

    var TOTAL = {{ $kriteria->count() }};

    function refresh() {
        var filled = {};

        document.querySelectorAll('input[type="radio"]:checked').forEach(function (r) {
            filled[r.dataset.kid] = r.value;
        });

        document.querySelectorAll('input[type="radio"]').forEach(function (r) {
            var kid = r.dataset.kid;
            var dot = document.getElementById('chkdot-' + kid);
            var textVal = document.getElementById('selval-' + kid);

            if (dot) {
                dot.classList.remove('text-success');
                dot.classList.add('text-light');
            }

            if (textVal) {
                textVal.textContent = 'Terpilih: —';
            }

            if (filled[kid]) {
                if (dot) {
                    dot.classList.remove('text-light');
                    dot.classList.add('text-success');
                }

                if (textVal) {
                    textVal.textContent = 'Terpilih: Skor ' + filled[kid];
                }
            }
        });

        var filledCount = Object.keys(filled).length;
        var pct = TOTAL > 0 ? Math.round((filledCount / TOTAL) * 100) : 0;

        document.getElementById('progressLabel').textContent = filledCount + ' / ' + TOTAL + ' Kriteria';
        document.getElementById('progressPct').textContent = pct + '%';
        document.getElementById('progressBar').style.width = pct + '%';

        var btn = document.getElementById('btnHitung');
        var alert = document.getElementById('alertIncomplete');

        if (filledCount < TOTAL) {
            btn.disabled = true;
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-secondary');
            alert.classList.remove('d-none');
            document.getElementById('sisaKrit').textContent = (TOTAL - filledCount);
        } else {
            btn.disabled = false;
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-primary');
            alert.classList.add('d-none');
        }
    }

    document.querySelectorAll('input[type="radio"]').forEach(function (r) {
        r.addEventListener('change', refresh);
    });

    refresh();
}());
</script>
@endsection