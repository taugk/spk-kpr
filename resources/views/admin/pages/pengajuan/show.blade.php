@extends('admin.layouts.app')

@section('title', 'Detail Pengajuan')

@section('page_action')
{{-- Kembali --}}
<a href="{{ route('admin.pengajuan.index') }}" class="btn btn-secondary">
    <i class="dw dw-arrow-left"></i> Kembali
</a>
@if(!($pengajuan->penilaian ?? false))
<a href="{{ route('admin.penilaian.create', ['pengajuan_id' => $pengajuan->id]) }}" class="btn btn-primary">
    <i class="dw dw-analytics-21"></i> Input Penilaian
</a>
@else
<a href="{{ route('admin.penilaian.show', $pengajuan->penilaian->id) }}" class="btn btn-info">
    <i class="dw dw-eye"></i> Lihat Penilaian
</a>
@endif
@endsection

@section('content')
<div class="row">
    <div class="col-xl-8 col-lg-8 mb-30">
        <!-- Informasi Pengajuan -->
        <div class="card-box pd-20 mb-30">
            <h4 class="h4 text-blue mb-20">{{ $pengajuan->kode_pengajuan ?? '-' }}</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Debitur:</strong> {{ $pengajuan->nama_debitur ?? '-' }}</p>
                    <p><strong>Email:</strong> {{ $pengajuan->email_debitur ?? '-' }}</p>
                    <p><strong>Status:</strong> @include('admin.components.status-badge', ['status' => $pengajuan->status ?? 'draft'])</p>
                    <p><strong>Tgl Pengajuan:</strong> {{ optional($pengajuan->created_at)->format('d/m/Y H:i') ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Proyek:</strong> {{ $pengajuan->nama_proyek ?? '-' }}</p>
                    <p><strong>Tipe Unit:</strong> {{ $pengajuan->nama_tipe ?? '-' }}</p>
                    <p><strong>Kode Unit:</strong> {{ $pengajuan->kode_unit ?? '-' }}</p>
                    <p><strong>Tenor:</strong> {{ $pengajuan->tenor_tahun ?? '-' }} tahun</p>
                </div>
            </div>
            
            @if($pengajuan->catatan_debitur ?? false)
            <div class="alert alert-info mt-3">
                <strong>Catatan Debitur:</strong> {{ $pengajuan->catatan_debitur }}
            </div>
            @endif
        </div>

        <!-- Informasi Debitur Pribadi -->
        <div class="card-box pd-20 mb-30">
            <h5 class="mb-20">Data Debitur Pribadi</h5>
            @if(isset($pengajuan->debitur_pribadi) && $pengajuan->debitur_pribadi)
            <div class="row">
                <div class="col-md-6">
                    <p><strong>NIK:</strong> {{ $pengajuan->debitur_pribadi->nik ?? '-' }}</p>
                    <p><strong>Tempat, Tgl Lahir:</strong> {{ $pengajuan->debitur_pribadi->tempat_lahir ?? '-' }}, {{ optional($pengajuan->debitur_pribadi->tanggal_lahir ?? null)->format('d/m/Y') ?? '-' }}</p>
                    <p><strong>Jenis Kelamin:</strong> {{ ($pengajuan->debitur_pribadi->jenis_kelamin ?? '') == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    <p><strong>Agama:</strong> {{ $pengajuan->debitur_pribadi->agama ?? '-' }}</p>
                    <p><strong>Status Pernikahan:</strong> {{ str_replace('_', ' ', $pengajuan->debitur_pribadi->status_pernikahan ?? '-') }}</p>
                    <p><strong>Jumlah Tanggungan:</strong> {{ $pengajuan->debitur_pribadi->jumlah_tanggungan ?? 0 }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Pendidikan:</strong> {{ $pengajuan->debitur_pribadi->pendidikan_terakhir ?? '-' }}</p>
                    <p><strong>Nama Ibu Kandung:</strong> {{ $pengajuan->debitur_pribadi->nama_ibu_kandung ?? '-' }}</p>
                    <p><strong>No HP:</strong> {{ $pengajuan->debitur_pribadi->no_hp ?? '-' }}</p>
                    <p><strong>Email:</strong> {{ $pengajuan->debitur_pribadi->email_aktif ?? '-' }}</p>
                    <p><strong>Alamat KTP:</strong> {{ $pengajuan->debitur_pribadi->alamat_ktp ?? '-' }}</p>
                    <p><strong>Status Tempat Tinggal:</strong> {{ str_replace('_', ' ', $pengajuan->debitur_pribadi->status_tempat_tinggal ?? '-') }}</p>
                </div>
            </div>
            
            @if($pengajuan->debitur_pribadi->nama_pasangan ?? false)
            <hr>
            <h6 class="mb-3">Data Pasangan</h6>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nama Pasangan:</strong> {{ $pengajuan->debitur_pribadi->nama_pasangan ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>NIK Pasangan:</strong> {{ $pengajuan->debitur_pribadi->nik_pasangan ?? '-' }}</p>
                </div>
            </div>
            @endif
            @else
            <div class="alert alert-warning">
                Data pribadi debitur belum lengkap.
            </div>
            @endif
        </div>

        <!-- Informasi Pekerjaan -->
        <div class="card-box pd-20 mb-30">
            <h5 class="mb-20">Data Pekerjaan</h5>
            @if(isset($pengajuan->debitur_pekerjaan) && $pengajuan->debitur_pekerjaan)
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Status Pekerjaan:</strong> {{ str_replace('_', ' ', $pengajuan->debitur_pekerjaan->status_pekerjaan ?? '-') }}</p>
                    <p><strong>Nama Perusahaan:</strong> {{ $pengajuan->debitur_pekerjaan->nama_perusahaan ?? '-' }}</p>
                    <p><strong>Bidang Usaha:</strong> {{ $pengajuan->debitur_pekerjaan->bidang_usaha ?? '-' }}</p>
                    <p><strong>Jabatan:</strong> {{ $pengajuan->debitur_pekerjaan->jabatan ?? '-' }}</p>
                    <p><strong>Status Kepegawaian:</strong> {{ str_replace('_', ' ', $pengajuan->debitur_pekerjaan->status_kepegawaian ?? '-') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Lama Bekerja:</strong> {{ $pengajuan->debitur_pekerjaan->lama_bekerja_tahun ?? 0 }} tahun {{ $pengajuan->debitur_pekerjaan->lama_bekerja_bulan ?? 0 }} bulan</p>
                    <p><strong>Alamat Perusahaan:</strong> {{ $pengajuan->debitur_pekerjaan->alamat_perusahaan ?? '-' }}</p>
                    <p><strong>NPWP:</strong> {{ $pengajuan->debitur_pekerjaan->npwp ?? '-' }}</p>
                    <p><strong>Penghasilan Pokok:</strong> Rp {{ number_format($pengajuan->debitur_pekerjaan->penghasilan_pokok ?? 0, 0, ',', '.') }}</p>
                    <p><strong>Total Penghasilan:</strong> Rp {{ number_format($pengajuan->debitur_pekerjaan->total_penghasilan ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
            @else
            <div class="alert alert-warning">
                Data pekerjaan debitur belum lengkap.
            </div>
            @endif
        </div>

        <!-- Informasi Keuangan -->
        <div class="card-box pd-20 mb-30">
            <h5 class="mb-20">Data Keuangan</h5>
            @if(isset($pengajuan->debitur_keuangan) && $pengajuan->debitur_keuangan)
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Bank:</strong> {{ $pengajuan->debitur_keuangan->nama_bank ?? '-' }}</p>
                    <p><strong>No Rekening:</strong> {{ $pengajuan->debitur_keuangan->nomor_rekening ?? '-' }}</p>
                    <p><strong>Rata-rata Saldo 3 Bln:</strong> Rp {{ number_format($pengajuan->debitur_keuangan->rata_saldo_3bln ?? 0, 0, ',', '.') }}</p>
                    <p><strong>Total Cicilan per Bulan:</strong> Rp {{ number_format($pengajuan->debitur_keuangan->total_cicilan_perbulan ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Jumlah Kredit Aktif:</strong> {{ $pengajuan->debitur_keuangan->jumlah_kredit_aktif ?? 0 }}</p>
                    <p><strong>Status Kredit:</strong> {{ str_replace('_', ' ', $pengajuan->debitur_keuangan->status_kredit ?? '-') }}</p>
                    <p><strong>Memiliki KPR Aktif:</strong> {{ ($pengajuan->debitur_keuangan->memiliki_kpr_aktif ?? false) ? 'Ya' : 'Tidak' }}</p>
                    <p><strong>Rasio Cicilan:</strong> {{ $pengajuan->debitur_keuangan->rasio_cicilan ?? 0 }}%</p>
                </div>
            </div>
            @else
            <div class="alert alert-warning">
                Data keuangan debitur belum lengkap.
            </div>
            @endif
        </div>

        <!-- Rincian Pembiayaan -->
        <div class="card-box pd-20 mb-30">
            <h5 class="mb-20">Rincian Pembiayaan</h5>
            <table class="table">
                <tr><th>Harga Properti</th><td>Rp {{ number_format($pengajuan->harga_properti ?? 0, 0, ',', '.') }}</td></tr>
                <tr><th>Uang Muka</th><td>Rp {{ number_format($pengajuan->uang_muka ?? 0, 0, ',', '.') }} ({{ $pengajuan->persen_dp ?? 0 }}%)</td></tr>
                <tr><th>Jumlah Pinjaman</th><td>Rp {{ number_format($pengajuan->jumlah_pinjaman ?? 0, 0, ',', '.') }}</td></tr>
                <tr><th>Estimasi Angsuran</th><td>Rp {{ number_format($pengajuan->estimasi_angsuran ?? 0, 0, ',', '.') }}</td></tr>
                <tr><th>Rasio Angsuran</th><td>{{ $pengajuan->rasio_angsuran ?? 0 }}%</td></tr>
                <tr><th>Tujuan Pembelian</th><td>{{ str_replace('_', ' ', $pengajuan->tujuan_pembelian ?? '-') }}</td></tr>
                <tr><th>Sumber DP</th><td>{{ str_replace('_', ' ', $pengajuan->sumber_dp ?? '-') }}</td></tr>
            </table>
        </div>

        <!-- Dokumen Pengajuan -->
        <div class="card-box pd-20">
            <h5 class="mb-20">Dokumen Pengajuan</h5>
            <table class="table table-striped">
                <thead>
                    <tr><th>Jenis</th><th>File</th><th>Status</th><th>Catatan</th></tr>
                </thead>
                <tbody>
                    @forelse(($pengajuan->dokumen ?? []) as $dokumen)
                    <tr>
                        <td>{{ Str::headline(str_replace('_',' ', $dokumen->jenis_dokumen)) }}</td>
                        <td>
                            @if($dokumen->path_file ?? false)
                            <a href="{{ asset('storage/'.$dokumen->path_file) }}" target="_blank">{{ $dokumen->nama_file }}</a>
                            @else
                            {{ $dokumen->nama_file ?? '-' }}
                            @endif
                        </td>
                        <td>@include('admin.components.status-badge', ['status' => $dokumen->status_verifikasi ?? 'belum_diperiksa'])</td>
                        <td>{{ $dokumen->catatan_verifikasi ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">Belum ada dokumen.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-xl-4 col-lg-4 mb-30">
        <!-- Hasil Penilaian -->
        <div class="card-box pd-20 mb-30">
            <h5 class="mb-20">Hasil Penilaian SMART</h5>
            @if(($pengajuan->penilaian ?? false) && ($pengajuan->penilaian->skor_akhir ?? false))
                <div class="text-center">
                    <div class="h1 text-blue">{{ number_format($pengajuan->penilaian->skor_akhir, 2) }}</div>
                    @include('admin.components.status-badge', ['status' => $pengajuan->penilaian->hasil ?? 'belum_dinilai'])
                    <p class="mt-3">Threshold: {{ $pengajuan->penilaian->threshold ?? 65 }}%</p>
                    @if($pengajuan->penilaian->catatan ?? false)
                        <p class="mt-3"><strong>Catatan:</strong> {{ $pengajuan->penilaian->catatan }}</p>
                    @endif
                </div>
            @else
                <div class="text-center py-4">
                    <i class="dw dw-question-circle" style="font-size: 48px; color: #ccc;"></i>
                    <p class="text-muted mt-2">Belum dilakukan penilaian.</p>
                    @if(in_array($pengajuan->status ?? '', ['antrian_admin', 'penilaian_admin', 'verifikasi_marketing']))
                    <a href="{{ route('admin.penilaian.create', ['pengajuan_id' => $pengajuan->id]) }}" class="btn btn-primary btn-sm mt-2">
                        <i class="dw dw-analytics-21"></i> Lakukan Penilaian
                    </a>
                    @endif
                </div>
            @endif
        </div>

        <!-- Informasi Marketing & Admin -->
        <div class="card-box pd-20 mb-30">
            <h5 class="mb-20">Informasi Proses</h5>
            <p><strong>Marketing:</strong> {{ $pengajuan->nama_marketing ?? '-' }}</p>
            <p><strong>Admin:</strong> {{ $pengajuan->nama_admin ?? '-' }}</p>
            <p><strong>Tgl Submit:</strong> {{ optional($pengajuan->tgl_submitted)->format('d/m/Y H:i') ?? '-' }}</p>
            <p><strong>Tgl Marketing Proses:</strong> {{ optional($pengajuan->tgl_marketing_proses)->format('d/m/Y H:i') ?? '-' }}</p>
            <p><strong>Tgl Admin Proses:</strong> {{ optional($pengajuan->tgl_admin_proses)->format('d/m/Y H:i') ?? '-' }}</p>
            <p><strong>Tgl Selesai:</strong> {{ optional($pengajuan->tgl_selesai)->format('d/m/Y H:i') ?? '-' }}</p>
        </div>

        <!-- Spesifikasi Unit -->
        <div class="card-box pd-20 mb-30">
            <h5 class="mb-20">Spesifikasi Unit</h5>
            <p><strong>Luas Tanah:</strong> {{ $pengajuan->luas_tanah ?? '-' }} m²</p>
            <p><strong>Luas Bangunan:</strong> {{ $pengajuan->luas_bangunan ?? '-' }} m²</p>
            <p><strong>Jumlah Kamar:</strong> {{ $pengajuan->jumlah_kamar ?? '-' }}</p>
            <p><strong>Jumlah WC:</strong> {{ $pengajuan->jumlah_wc ?? '-' }}</p>
            <p><strong>Lokasi Proyek:</strong> {{ $pengajuan->proyek_lokasi ?? ($pengajuan->nama_proyek ?? '-') }}</p>
        </div>

        <!-- Riwayat Status -->
        <div class="card-box pd-20">
            <h5 class="mb-20">Riwayat Status</h5>
            <ul class="timeline">
                @forelse(($pengajuan->riwayat ?? []) as $riwayat)
                    <li>
                        <div class="timeline-date">{{ optional($riwayat->created_at)->format('d M Y H:i') }}</div>
                        <div class="timeline-desc">
                            <strong>{{ Str::headline(str_replace('_',' ', $riwayat->status_baru)) }}</strong>
                            @if($riwayat->keterangan ?? false)
                            <p class="mb-1">{{ $riwayat->keterangan }}</p>
                            @endif
                            <small class="text-muted">
                                @if($riwayat->diubah_oleh)
                                    Oleh: {{ $riwayat->diubah_oleh == auth()->id() ? 'Anda' : 'Admin/Marketing' }}
                                @else
                                    Oleh: Sistem
                                @endif
                            </small>
                        </div>
                    </li>
                @empty
                    <li class="text-center py-3">Belum ada riwayat perubahan status.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection