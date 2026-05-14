@extends('admin.layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .dashboard-wrapper {
        padding: 10px 0 30px;
    }

    .dashboard-hero {
        background: linear-gradient(135deg, #1b00ff, #6c5ce7);
        border-radius: 18px;
        padding: 26px 30px;
        margin-bottom: 25px;
        color: #fff;
        box-shadow: 0 12px 30px rgba(27, 0, 255, 0.18);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .dashboard-hero h4 {
        color: #fff;
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .dashboard-hero p {
        margin-bottom: 0;
        opacity: .9;
        font-size: 15px;
    }

    .dashboard-hero .btn {
        background: #fff;
        color: #1b00ff;
        border: 0;
        font-weight: 600;
        border-radius: 10px;
        padding: 10px 18px;
    }

    .stat-card-clean {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        min-height: 115px;
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: .2s ease;
    }

    .stat-card-clean:hover {
        transform: translateY(-3px);
    }

    .stat-card-clean h3 {
        font-size: 30px;
        font-weight: 700;
        margin-bottom: 4px;
        color: #1f2937;
    }

    .stat-card-clean p {
        margin-bottom: 0;
        color: #6b7280;
        font-size: 14px;
        font-weight: 500;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 23px;
        color: #fff;
    }

    .dashboard-card {
        background: #fff;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.06);
        border: 0;
        height: 100%;
    }

    .dashboard-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        gap: 15px;
    }

    .dashboard-card-header h4 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #1f2937;
    }

    .dashboard-card-header p {
        margin-bottom: 0;
        color: #6b7280;
        font-size: 14px;
    }

    .dashboard-card-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #f1efff;
        color: #1b00ff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .table-card {
        margin-top: 30px;
        overflow: hidden;
    }

    .table-card .table thead th {
        background: #f8fafc;
        color: #374151;
        font-size: 13px;
        font-weight: 700;
        border-bottom: 0;
        padding: 14px 16px;
    }

    .table-card .table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        color: #374151;
    }

    .kode-pengajuan {
        color: #1b00ff;
        font-weight: 700;
    }

    .amount-text {
        color: #059669;
        font-weight: 700;
        white-space: nowrap;
    }

    .empty-state {
        padding: 45px 15px;
        text-align: center;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 42px;
        display: block;
        margin-bottom: 10px;
        color: #cbd5e1;
    }

    #chart-pengajuan,
    #chart-hasil {
        min-height: 320px;
    }

    @media (max-width: 768px) {
        .dashboard-hero {
            padding: 22px;
        }

        .dashboard-hero h4 {
            font-size: 22px;
        }

        .stat-card-clean {
            min-height: 100px;
        }

        .dashboard-card-header {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')

<div class="dashboard-wrapper">

    <div class="dashboard-hero">
        <div>
            <h4>Dashboard Admin</h4>
            <p>Ringkasan pengajuan KPR, status verifikasi, dan hasil perhitungan metode SMART.</p>
        </div>

        <a href="{{ route('admin.pengajuan.index') }}" class="btn">
            <i class="dw dw-file mr-1"></i>
            Lihat Pengajuan
        </a>
    </div>

    <div class="row pb-10">
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="stat-card-clean">
                <div>
                    <h3>{{ $totalPengajuan ?? 0 }}</h3>
                    <p>Total Pengajuan</p>
                </div>
                <div class="stat-icon" style="background:#1b00ff;">
                    <i class="dw dw-file"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="stat-card-clean">
                <div>
                    <h3>{{ $antrianAdmin ?? 0 }}</h3>
                    <p>Antrian Admin</p>
                </div>
                <div class="stat-icon" style="background:#f59e0b;">
                    <i class="dw dw-wall-clock"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="stat-card-clean">
                <div>
                    <h3>{{ $disetujui ?? 0 }}</h3>
                    <p>Disetujui</p>
                </div>
                <div class="stat-icon" style="background:#10b981;">
                    <i class="dw dw-checked"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="stat-card-clean">
                <div>
                    <h3>{{ $ditolak ?? 0 }}</h3>
                    <p>Ditolak</p>
                </div>
                <div class="stat-icon" style="background:#ef4444;">
                    <i class="dw dw-cancel"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-8 mb-30">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h4>Statistik Pengajuan Bulanan</h4>
                        <p>Jumlah pengajuan berdasarkan bulan.</p>
                    </div>
                    <div class="dashboard-card-icon">
                        <i class="dw dw-analytics-21"></i>
                    </div>
                </div>

                <div id="chart-pengajuan"></div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-4 mb-30">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h4>Komposisi Hasil SMART</h4>
                        <p>Perbandingan hasil kelayakan.</p>
                    </div>
                    <div class="dashboard-card-icon">
                        <i class="dw dw-pie-chart1"></i>
                    </div>
                </div>

                <div id="chart-hasil"></div>
            </div>
        </div>
    </div>

    <div class="dashboard-card table-card">
        <div class="dashboard-card-header">
            <div>
                <h4>Pengajuan Terbaru</h4>
                <p>Daftar pengajuan KPR terbaru yang masuk ke sistem.</p>
            </div>

            <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-sm btn-outline-primary">
                Lihat Semua
            </a>
        </div>

        <div class="table-responsive">
            <table class="table hover nowrap">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Debitur</th>
                        <th>Properti</th>
                        <th>Pinjaman</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse(($pengajuanTerbaru ?? []) as $item)
                        @php
                            $pengajuanId = $item->id ?? $item->pengajuan_id ?? null;

                            $namaDebitur = $item->user->nama_lengkap
                                ?? $item->nama_debitur
                                ?? '-';

                            $namaProperti = $item->unit->tipeUnit->proyek->nama_proyek
                                ?? $item->nama_proyek
                                ?? '-';

                            $jumlahPinjaman = $item->jumlah_pinjaman ?? 0;
                        @endphp

                        <tr>
                            <td>
                                <span class="kode-pengajuan">
                                    {{ $item->kode_pengajuan ?? '-' }}
                                </span>
                            </td>

                            <td>{{ $namaDebitur }}</td>

                            <td>{{ $namaProperti }}</td>

                            <td>
                                <span class="amount-text">
                                    Rp {{ number_format($jumlahPinjaman, 0, ',', '.') }}
                                </span>
                            </td>

                            <td>
                                @include('admin.components.status-badge', [
                                    'status' => $item->status ?? '-'
                                ])
                            </td>

                            <td>
                                {{ optional($item->created_at)->format('d M Y') ?? '-' }}
                            </td>

                            <td class="text-center">
                                @if($pengajuanId)
                                    <a href="{{ route('admin.pengajuan.show', $pengajuanId) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="dw dw-eye mr-1"></i>
                                        Detail
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="dw dw-inbox"></i>
                                    Belum ada data pengajuan.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    const monthlyLabels = @json($monthlyLabels ?? []);
    const monthlyTotals = @json($monthlyTotals ?? []);
    const hasilSeries = @json($hasilSeries ?? [0, 0]);

    const chartPengajuan = document.querySelector("#chart-pengajuan");
    const chartHasil = document.querySelector("#chart-hasil");

    if (chartPengajuan) {
        new ApexCharts(chartPengajuan, {
            chart: {
                type: 'area',
                height: 320,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            series: [{
                name: 'Pengajuan',
                data: monthlyTotals
            }],
            xaxis: {
                categories: monthlyLabels
            },
            yaxis: {
                min: 0,
                forceNiceScale: true
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            grid: {
                borderColor: '#edf2f7',
                strokeDashArray: 4
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return value + ' pengajuan';
                    }
                }
            }
        }).render();
    }

    if (chartHasil) {
        new ApexCharts(chartHasil, {
            chart: {
                type: 'donut',
                height: 320
            },
            series: hasilSeries,
            labels: ['Layak', 'Tidak Layak'],
            legend: {
                position: 'bottom'
            },
            dataLabels: {
                enabled: true
            },
            stroke: {
                width: 0
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return value + ' pengajuan';
                    }
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '68%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            }
        }).render();
    }
</script>
@endpush