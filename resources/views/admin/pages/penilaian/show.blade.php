{{-- resources/views/admin/pages/penilaian/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detail Penilaian SMART - ' . ($penilaian->kode_pengajuan ?? 'Penilaian'))

@section('page_action')
    <div class="btn-list">
        <a href="{{ route('admin.penilaian.index') }}" class="btn btn-secondary btn-sm">
            <i class="dw dw-left-arrow1"></i> Kembali
        </a>
        <button type="button" onclick="window.print()" class="btn btn-outline-primary btn-sm">
            <i class="dw dw-print"></i> Cetak
        </button>
        @if($penilaian->hasil == 'layak')
            <a href="{{ route('admin.pengajuan.approve', $penilaian->pengajuan_id) }}" 
               class="btn btn-success btn-sm">
                <i class="dw dw-checked"></i> Setujui Pengajuan
            </a>
        @endif
    </div>
@endsection

@section('content')
<div class="min-height-200px">
    {{-- Header --}}
    <div class="page-header mb-30">
        <div class="row">
            <div class="col-md-8">
                <div class="title">
                    <h4>Hasil Penilaian SMART</h4>
                    <p class="text-muted">
                        Kode Pengajuan: <strong>{{ $penilaian->kode_pengajuan ?? '-' }}</strong>
                        | Debitur: <strong>{{ $penilaian->nama_debitur ?? '-' }}</strong>
                        | Tanggal: <strong>{{ \Carbon\Carbon::parse($penilaian->tgl_penilaian)->format('d/m/Y H:i') }}</strong>
                    </p>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <div class="bg-light p-3 rounded">
                    <small class="text-muted d-block">Keputusan Akhir</small>
                    <h2 class="{{ $penilaian->hasil == 'layak' ? 'text-success' : 'text-danger' }} mb-0">
                        {{ strtoupper($penilaian->hasil == 'layak' ? 'LAYAK' : 'TIDAK LAYAK') }}
                    </h2>
                    <small>
                        Skor: {{ number_format($penilaian->skor_akhir, 4) }} 
                        (Threshold: {{ $penilaian->threshold }})
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- KOLOM KIRI - Detail Perhitungan --}}
        <div class="col-xl-8 col-lg-8 col-md-12">
            
            {{-- Ringkasan Perhitungan --}}
            <div class="card-box pd-20 mb-30">
                <h4 class="text-blue h4 mb-20">
                    <i class="fa fa-calculator"></i> Ringkasan Perhitungan SMART
                </h4>
                
                <div class="alert alert-info">
                    <strong>Metode SMART (Simple Multi-Attribute Rating Technique)</strong><br>
                    <small>
                        Total Skor Akhir = Σ (Nilai Normalisasi × Bobot Kriteria × 100)<br>
                        Dimana Nilai Normalisasi = 
                        @if($penilaian->details->first()->tipe ?? 'benefit' == 'benefit')
                            (X - min) / (max - min) untuk benefit
                        @else
                            (max - X) / (max - min) untuk cost
                        @endif
                    </small>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Kode</th>
                                <th>Kriteria</th>
                                <th>Tipe</th>
                                <th>Nilai Input</th>
                                <th>Min/Max</th>
                                <th>Normalisasi</th>
                                <th>Bobot</th>
                                <th>Skor Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalSkor = 0; @endphp
                            @foreach($detail as $d)
                            @php 
                                $totalSkor += $d->skor_kontribusi;
                                $isBenefit = $d->tipe == 'benefit';
                                $rangeText = "{$d->nilai_min} - {$d->nilai_max}";
                            @endphp
                            <tr>
                                <td><strong>{{ $d->kode_kriteria }}</strong></td>
                                <td>{{ $d->nama_kriteria }}</td>
                                <td>
                                    <span class="badge badge-{{ $isBenefit ? 'success' : 'warning' }}">
                                        {{ ucfirst($d->tipe) }}
                                    </span>
                                </td>
                                <td><strong>{{ number_format($d->nilai_input, 2) }}</strong></td>
                                <td><small>{{ $rangeText }}</small></td>
                                <td>
                                    {{ number_format($d->nilai_normalisasi, 6) }}
                                    <button type="button" class="btn btn-sm btn-link p-0 ml-1" 
                                            data-toggle="tooltip" 
                                            title="{{ $isBenefit ? "({$d->nilai_input} - {$d->nilai_min}) / ({$d->nilai_max} - {$d->nilai_min})" : "({$d->nilai_max} - {$d->nilai_input}) / ({$d->nilai_max} - {$d->nilai_min})" }}">
                                        <i class="fa fa-info-circle"></i>
                                    </button>
                                </td>
                                <td>{{ number_format($d->bobot_snapshot, 2) }}%</td>
                                <td class="font-weight-bold">
                                    {{ number_format($d->skor_kontribusi, 4) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="7" class="text-right font-weight-bold">TOTAL SKOR AKHIR:</td>
                                <td class="font-weight-bold text-primary h5">
                                    {{ number_format($totalSkor, 4) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" class="text-right font-weight-bold">THRESHOLD KELAYAKAN:</td>
                                <td class="font-weight-bold">{{ number_format($penilaian->threshold, 2) }}</td>
                            </tr>
                            <tr class="{{ $totalSkor >= $penilaian->threshold ? 'table-success' : 'table-danger' }}">
                                <td colspan="7" class="text-right font-weight-bold">KEPUTUSAN:</td>
                                <td class="font-weight-bold h6">
                                    {{ $totalSkor >= $penilaian->threshold ? 'LAYAK' : 'TIDAK LAYAK' }}
                                    @if($totalSkor >= $penilaian->threshold)
                                        <i class="fa fa-check-circle text-success"></i>
                                    @else
                                        <i class="fa fa-times-circle text-danger"></i>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Detail Perhitungan per Kriteria --}}
            <div class="card-box pd-20 mb-30">
                <h4 class="text-blue h4 mb-20">
                    <i class="fa fa-list-alt"></i> Detail Perhitungan per Kriteria
                </h4>
                
                <div class="accordion" id="calculationAccordion">
                    @foreach($detail as $index => $d)
                    <div class="card">
                        <div class="card-header" id="heading{{ $index }}">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left collapsed" 
                                        type="button" 
                                        data-toggle="collapse" 
                                        data-target="#collapse{{ $index }}" 
                                        aria-expanded="false" 
                                        aria-controls="collapse{{ $index }}">
                                    <strong>{{ $d->kode_kriteria }}</strong> - {{ $d->nama_kriteria }}
                                    <span class="float-right">
                                        Skor: <strong>{{ number_format($d->skor_kontribusi, 4) }}</strong>
                                        <i class="fa fa-chevron-down ml-2"></i>
                                    </span>
                                </button>
                            </h2>
                        </div>
                        
                        <div id="collapse{{ $index }}" class="collapse" 
                             aria-labelledby="heading{{ $index }}" 
                             data-parent="#calculationAccordion">
                            <div class="card-body">
                                @php
                                    $isBenefit = $d->tipe == 'benefit';
                                    $range = $d->nilai_max - $d->nilai_min;
                                @endphp
                                
                                <h6>1. Nilai Normalisasi (Utility)</h6>
                                <div class="alert alert-secondary">
                                    <strong>Rumus {{ ucfirst($d->tipe) }}:</strong><br>
                                    @if($isBenefit)
                                        <code>U = (X - min) / (max - min)</code>
                                        <div class="mt-2">
                                            = ({{ number_format($d->nilai_input, 2) }} - {{ number_format($d->nilai_min, 2) }}) 
                                            / ({{ number_format($d->nilai_max, 2) }} - {{ number_format($d->nilai_min, 2) }})
                                            <br>
                                            = {{ number_format($d->nilai_input - $d->nilai_min, 2) }} 
                                            / {{ number_format($range, 2) }}
                                            <br>
                                            = <strong class="text-primary">{{ number_format($d->nilai_normalisasi, 6) }}</strong>
                                        </div>
                                    @else
                                        <code>U = (max - X) / (max - min)</code>
                                        <div class="mt-2">
                                            = ({{ number_format($d->nilai_max, 2) }} - {{ number_format($d->nilai_input, 2) }}) 
                                            / ({{ number_format($d->nilai_max, 2) }} - {{ number_format($d->nilai_min, 2) }})
                                            <br>
                                            = {{ number_format($d->nilai_max - $d->nilai_input, 2) }} 
                                            / {{ number_format($range, 2) }}
                                            <br>
                                            = <strong class="text-primary">{{ number_format($d->nilai_normalisasi, 6) }}</strong>
                                        </div>
                                    @endif
                                </div>
                                
                                <h6>2. Nilai Kontribusi</h6>
                                <div class="alert alert-secondary">
                                    <strong>Rumus:</strong><br>
                                    <code>S = U × (Bobot / 100) × 100</code>
                                    <div class="mt-2">
                                        = {{ number_format($d->nilai_normalisasi, 6) }} 
                                        × ({{ number_format($d->bobot_snapshot, 2) }}% / 100) × 100
                                        <br>
                                        = {{ number_format($d->nilai_normalisasi, 6) }} 
                                        × {{ number_format($d->bobot_snapshot, 2) / 100, 4 }} × 100
                                        <br>
                                        = <strong class="text-success">{{ number_format($d->skor_kontribusi, 4) }}</strong>
                                    </div>
                                </div>
                                
                                <div class="progress mt-3" style="height: 30px;">
                                    @php
                                        $maxScore = 100;
                                        $percentage = ($d->skor_kontribusi / $maxScore) * 100;
                                    @endphp
                                    <div class="progress-bar bg-info" 
                                         role="progressbar" 
                                         style="width: {{ $percentage }}%"
                                         aria-valuenow="{{ $d->skor_kontribusi }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="{{ $maxScore }}">
                                        {{ number_format($d->skor_kontribusi, 2) }} / {{ $maxScore }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- SIDEBAR KANAN --}}
        <div class="col-xl-4 col-lg-4 col-md-12">
            
            {{-- Grafik Skor --}}
            <div class="card-box pd-20 mb-30">
                <h5 class="h5 mb-20">Visualisasi Skor</h5>
                
                <canvas id="scoreChart" height="200"></canvas>
                
                <hr>
                
                <div class="text-center">
                    <div class="row">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <small class="text-muted">Skor Akhir</small>
                                <h3 class="mb-0 {{ $totalSkor >= $penilaian->threshold ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($totalSkor, 2) }}
                                </h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <small class="text-muted">Threshold</small>
                                <h3 class="mb-0 text-primary">{{ number_format($penilaian->threshold, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="progress" style="height: 8px;">
                            @php $persentase = min(100, ($totalSkor / $penilaian->threshold) * 100); @endphp
                            <div class="progress-bar {{ $totalSkor >= $penilaian->threshold ? 'bg-success' : 'bg-warning' }}" 
                                 role="progressbar" 
                                 style="width: {{ $persentase }}%"
                                 aria-valuenow="{{ $totalSkor }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="{{ $penilaian->threshold }}">
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ number_format($persentase, 1) }}% dari threshold
                        </small>
                    </div>
                </div>
            </div>
            
            {{-- Informasi Tambahan --}}
            <div class="card-box pd-20 mb-30">
                <h5 class="h5 mb-20">Informasi Penilaian</h5>
                
                <table class="table table-sm">
                    <tr>
                        <td width="40%">Admin Penilai</td>
                        <td><strong>{{ $penilaian->nama_admin ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Tanggal Penilaian</td>
                        <td>{{ \Carbon\Carbon::parse($penilaian->tgl_penilaian)->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td>Total Plafon</td>
                        <td>Rp {{ number_format($penilaian->jumlah_pinjaman ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Tenor</td>
                        <td>{{ $penilaian->tenor_tahun ?? 0 }} Tahun</td>
                    </tr>
                    @if($penilaian->catatan_admin)
                    <tr>
                        <td>Catatan Admin</td>
                        <td class="text-muted">{{ $penilaian->catatan_admin }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            
            {{-- Rekomendasi --}}
            <div class="card-box pd-20 {{ $totalSkor >= $penilaian->threshold ? 'bg-success-light' : 'bg-danger-light' }}">
                <h5 class="h5 mb-20">Rekomendasi</h5>
                
                @if($totalSkor >= $penilaian->threshold)
                    <div class="text-success">
                        <i class="fa fa-check-circle fa-2x mb-2"></i>
                        <p><strong>PENGADAAN DISETUJUI</strong></p>
                        <p class="small">
                            Berdasarkan hasil penilaian SMART, debitur memenuhi kriteria kelayakan 
                            dengan skor {{ number_format($totalSkor, 2) }} (melebihi threshold {{ $penilaian->threshold }}).
                        </p>
                        <p class="small mt-2">
                            Rekomendasi: Pengajuan KPR dapat dilanjutkan ke proses persetujuan final.
                        </p>
                    </div>
                @else
                    <div class="text-danger">
                        <i class="fa fa-times-circle fa-2x mb-2"></i>
                        <p><strong>PENGADAAN DITOLAK</strong></p>
                        <p class="small">
                            Berdasarkan hasil penilaian SMART, debitur tidak memenuhi kriteria kelayakan 
                            dengan skor {{ number_format($totalSkor, 2) }} (di bawah threshold {{ $penilaian->threshold }}).
                        </p>
                        <p class="small mt-2">
                            Rekomendasi: Pengajuan KPR tidak dapat dilanjutkan. Debitur disarankan 
                            untuk memperbaiki profil kredit dan mengajukan kembali.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart for scores
        const ctx = document.getElementById('scoreChart').getContext('2d');
        const detailData = @json($detail);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: detailData.map(d => d.kode_kriteria),
                datasets: [{
                    label: 'Skor Kontribusi',
                    data: detailData.map(d => d.skor_kontribusi),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }, {
                    label: 'Bobot Maksimal',
                    data: detailData.map(d => d.bobot_snapshot),
                    backgroundColor: 'rgba(255, 99, 132, 0.3)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    type: 'line'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nilai Skor'
                        }
                    }
                }
            }
        });
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<style>
    .bg-success-light {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
    }
    .bg-danger-light {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
    }
    .card-box .progress {
        border-radius: 20px;
    }
    .accordion .card-header button {
        text-decoration: none;
    }
    .accordion .card-header button:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection