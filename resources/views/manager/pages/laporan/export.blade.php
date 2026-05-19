@extends('manager.layouts.app')

@section('title', 'Export Laporan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="#">Laporan</a></li>
<li class="breadcrumb-item active">Export Laporan</li>
@endsection

@section('content')
<!-- Header Banner Ala DeskApp -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Export Laporan</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb mb-0">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="col-md-6 col-sm-12 text-md-right d-none d-md-block">
            <span class="text-muted">Export data laporan dalam berbagai format</span>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10 col-sm-12">

        <!-- Form Konfigurasi Utama -->
        <div class="card-box pd-30 mb-4">
            <div class="clearfix mb-4">
                <div class="pull-left">
                    <h5 class="text-blue h4"><i class="fa fa-sliders mr-2"></i>Konfigurasi Export</h5>
                </div>
            </div>

            <form id="exportForm">
                @csrf

                <!-- Jenis Laporan -->
                <div class="form-group row mb-4">
                    <label class="col-sm-12 col-md-3 col-form-label weight-600">Jenis Laporan</label>
                    <div class="col-sm-12 col-md-9">
                        <select name="jenis_laporan" class="custom-select form-control form-control-lg" id="jenisLaporan" required>
                            <option value="">-- Pilih Jenis Laporan --</option>
                            <option value="bulanan">📅 Laporan Bulanan</option>
                            <option value="tahunan">📆 Laporan Tahunan</option>
                            <option value="kinerja">👥 Laporan Kinerja Marketing</option>
                            <option value="penilaian">⭐ Laporan Penilaian</option>
                        </select>
                    </div>
                </div>

                <!-- Field Bulan (muncul jika jenis = bulanan) -->
                <div class="form-group row mb-4" id="bulanField" style="display: none;">
                    <label class="col-sm-12 col-md-3 col-form-label weight-600">Bulan</label>
                    <div class="col-sm-12 col-md-9">
                        <select name="bulan" class="custom-select form-control">
                            @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Tahun -->
                <div class="form-group row mb-4">
                    <label class="col-sm-12 col-md-3 col-form-label weight-600">Tahun</label>
                    <div class="col-sm-12 col-md-9">
                        <select name="tahun" class="custom-select form-control" required>
                            @foreach($tahunTersedia as $tahun)
                            <option value="{{ $tahun }}" {{ $tahun == date('Y') ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Format Export -->
                <div class="form-group row mb-4">
                    <label class="col-sm-12 col-md-3 col-form-label weight-600">Format Export</label>
                    <div class="col-sm-12 col-md-9">
                        <div class="row pt-2">
                            <div class="col-md-4 col-sm-12 mb-2">
                                <div class="custom-control custom-radio mb-5">
                                    <input type="radio" id="formatExcel" name="format" class="custom-control-input" value="excel" checked>
                                    <label class="custom-control-label" for="formatExcel">
                                        <i class="fa fa-file-excel-o text-success mr-1"></i> MS Excel (.xlsx)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 mb-2">
                                <div class="custom-control custom-radio mb-5">
                                    <input type="radio" id="formatCsv" name="format" class="custom-control-input" value="csv">
                                    <label class="custom-control-label" for="formatCsv">
                                        <i class="fa fa-file-text-o text-info mr-1"></i> CSV (.csv)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 mb-2">
                                <div class="custom-control custom-radio mb-5">
                                    <input type="radio" id="formatPdf" name="format" class="custom-control-input" value="pdf">
                                    <label class="custom-control-label" for="formatPdf">
                                        <i class="fa fa-file-pdf-o text-danger mr-1"></i> PDF (.pdf)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Opsi Tambahan (khusus PDF) -->
                <div class="form-group row mb-4" id="pdfOptions" style="display: none;">
                    <div class="offset-md-3 col-sm-12 col-md-9">
                        <div class="p-3 bg-light rounded border">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" name="include_chart" class="custom-control-input" id="includeChart" value="1">
                                <label class="custom-control-label" for="includeChart">
                                    <i class="fa fa-line-chart text-secondary mr-1"></i> Sertakan Grafik/Chart
                                </label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="include_summary" class="custom-control-input" id="includeSummary" value="1" checked>
                                <label class="custom-control-label" for="includeSummary">
                                    <i class="fa fa-file-text mr-1"></i> Sertakan Ringkasan Eksekutif
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Tombol Aksi -->
                <div class="form-group row mb-0">
                    <div class="offset-md-3 col-sm-12 col-md-9">
                        <button type="submit" class="btn btn-primary btn-lg btn-block mb-3">
                            <i class="fa fa-download mr-2"></i> Proses Export
                        </button>
                        <button type="button" onclick="previewLaporan()" class="btn btn-outline-info btn-block">
                            <i class="fa fa-eye mr-2"></i> Preview Laporan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Informasi Box DeskApp Style -->
        <div class="card-box pd-20 mb-30 bg-light-info text-blue">
            <h5 class="h5 text-blue mb-2"><i class="fa fa-info-circle mr-2"></i>Informasi Penting</h5>
            <ul class="pl-3 mb-0" style="list-style-type: square;">
                <li class="mb-1">Export data akan diproses dalam beberapa saat.</li>
                <li class="mb-1">File akan otomatis terdownload setelah proses selesai.</li>
                <li class="mb-1">Untuk format PDF dengan chart, proses mungkin memerlukan waktu lebih lama.</li>
                <li>Pastikan koneksi internet stabil saat melakukan export.</li>
            </ul>
        </div>

    </div>
</div>

<!-- Modal Preview Bootstrap 4 & DeskApp Layout -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content text-dark">
            <div class="modal-header">
                <h5 class="modal-title font-18"><i class="fa fa-eye mr-2"></i>Preview Laporan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="previewContent" style="max-height: 65vh; overflow-y: auto;">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat preview...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="exportFromPreview()">
                    <i class="fa fa-download mr-1"></i> Export
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styling pembantu khusus info panel dan penyesuaian kustom radio deskapp */
    .bg-light-info {
        background-color: #ecf5ff;
        border: 1px solid #b3d7ff;
    }
    .custom-control-label {
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentFormData = null;

    $('#jenisLaporan').on('change', function() {
        if ($(this).val() === 'bulanan') {
            $('#bulanField').slideDown();
        } else {
            $('#bulanField').slideUp();
        }
    });

    $('input[name="format"]').on('change', function() {
        if ($(this).val() === 'pdf') {
            $('#pdfOptions').slideDown();
        } else {
            $('#pdfOptions').slideUp();
        }
    });

    $('#exportForm').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();
        currentFormData = formData;

        Swal.fire({
            title: 'Proses Export',
            text: 'Sedang memproses export data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.post('{{ route("manager.laporan.export.proses") }}', formData, function(response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message || 'Laporan berhasil diekspor',
                    confirmButtonText: 'OK'
                }).then(() => {
                    if (response.filename) {
                        window.location.href = '/manager/laporan/download/' + response.filename;
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message || 'Terjadi kesalahan saat export'
                });
            }
        }).fail(function(xhr) {
            Swal.close();
            let errorMsg = 'Terjadi kesalahan saat memproses export';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: errorMsg
            });
        });
    });

    function previewLaporan() {
        const formData = $('#exportForm').serialize();
        currentFormData = formData;

        $('#previewModal').modal('show');
        $('#previewContent').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Memuat preview...</p>
            </div>
        `);

        $.post('{{ route("manager.laporan.preview") }}', formData, function(response) {
            $('#previewContent').html(response);
        }).fail(function() {
            $('#previewContent').html(`
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle mr-2"></i>
                    Gagal memuat preview. Silakan coba lagi.
                </div>
            `);
        });
    }

    function exportFromPreview() {
        if (currentFormData) {
            $('#previewModal').modal('hide');

            Swal.fire({
                title: 'Proses Export',
                text: 'Sedang memproses export data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.post('{{ route("manager.laporan.export.proses") }}', currentFormData, function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Laporan berhasil diekspor',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (response.filename) {
                            window.location.href = '/manager/laporan/download/' + response.filename;
                        }
                    });
                }
            }).fail(function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat export'
                });
            });
        }
    }
</script>
@endpush
