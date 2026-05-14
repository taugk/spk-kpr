@extends('admin.layouts.app')

@section('title', 'Pengaturan Sistem')

@section('styles')
<style>
    .pengaturan-card {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }
    
    .pengaturan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-color: transparent;
    }
    
    .setting-value {
        word-break: break-word;
        line-height: 1.6;
        background: #f8f9fa;
        padding: 10px;
        border-radius: 6px;
        font-family: monospace;
    }
    
    .edit-icon {
        opacity: 0.6;
        transition: opacity 0.2s;
        font-size: 14px;
    }
    
    .edit-setting-btn:hover .edit-icon {
        opacity: 1;
    }
    
    /* Dark mode styles */
    @media (prefers-color-scheme: dark) {
        .setting-value {
            background: #2c2c2c;
            color: #e0e0e0;
        }
    }
    
    /* Animation for save confirmation */
    @keyframes flashSuccess {
        0% { background-color: #d4edda; }
        100% { background-color: transparent; }
    }
    
    .flash-success {
        animation: flashSuccess 1s ease;
    }
    
    /* Loading overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Toast notification */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        min-width: 300px;
    }
</style>
@endsection

@section('page_action')

<button class="btn btn-sm btn-outline-primary toggle-edit-all" id="toggleEditAll">
                            <i class="icon-copy dw dw-edit"></i>
                            Edit Semua
                        </button>
                        <button class="btn btn-sm btn-primary ml-2" id="saveAllSettings" style="display: none;">
                            <i class="icon-copy dw dw-save"></i>
                            Simpan Semua
                        </button>
                        <button class="btn btn-sm btn-outline-secondary ml-2" id="refreshSettings">
                            <i class="icon-copy dw dw-refresh"></i>
                            Refresh
                        </button>

@endsection

@section('content')
<div class="container-fluid">
    

    <!-- Settings Cards Grid -->
    <div class="row" id="settingsContainer">
        @forelse($pengaturan ?? [] as $index => $item)
        <div class="col-md-6 col-lg-4 mb-4" data-setting-key="{{ $item->kunci }}">
            <div class="card-box pengaturan-card h-100">
                <div class="pd-20">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <h5 class="mb-1">
                                <code class="text-primary">{{ $item->kunci }}</code>
                            </h5>
                            @if($item->keterangan)
                            <small class="text-muted d-block mt-1">{{ $item->keterangan }}</small>
                            @endif
                        </div>
                        <button class="btn btn-sm btn-outline-primary edit-setting-btn" 
                                data-kunci="{{ $item->kunci }}"
                                data-nilai="{{ e($item->nilai) }}"
                                data-keterangan="{{ e($item->keterangan) }}">
                            <i class="icon-copy dw dw-edit1 edit-icon"></i>
                        </button>
                    </div>
                    
                    <!-- Value Display -->
                    <div class="setting-value-container">
                        <label class="text-muted small mb-1">Nilai Saat Ini:</label>
                        <div class="setting-value font-weight-medium" id="value-display-{{ $item->kunci }}">
                            @if(empty($item->nilai))
                            <span class="text-muted font-italic">(kosong)</span>
                            @else
                            {{ $item->nilai }}
                            @endif
                        </div>
                    </div>
                    
                    <!-- Edit Form (Hidden by default) -->
                    <div class="edit-form mt-3" id="edit-form-{{ $item->kunci }}" style="display: none;">
                        <form class="setting-edit-form" 
                              data-kunci="{{ $item->kunci }}"
                              data-url="{{ route('admin.pengaturan.update.single', ['kunci' => $item->kunci]) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label class="small text-muted">Edit Nilai:</label>
                                <textarea class="form-control form-control-sm setting-input" 
                                          rows="3" 
                                          name="nilai"
                                          placeholder="Masukkan nilai untuk {{ $item->kunci }}">{{ $item->nilai }}</textarea>
                            </div>
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-sm btn-primary btn-save-setting">
                                    <i class="icon-copy dw dw-save"></i> Simpan
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary btn-cancel-edit" 
                                        data-kunci="{{ $item->kunci }}">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Footer -->
                    <div class="mt-3 pt-2 border-top">
                        <small class="text-muted">
                            <i class="icon-copy dw dw-calendar1"></i>
                            Terakhir update: 
                            {{ optional($item->updated_at)->format('d M Y H:i:s') ?? 'Belum pernah' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card-box">
                <div class="pd-20 text-center">
                    <i class="icon-copy dw dw-information" style="font-size: 48px;"></i>
                    <h5 class="mt-3">Belum ada pengaturan</h5>
                    <p class="text-muted">Silakan tambahkan pengaturan terlebih dahulu melalui database.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Loading Spinner -->
<div id="loadingSpinner" style="display: none;">
    <div class="loading-overlay">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-notification"></div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let editMode = false;
    
    // Show toast notification
    function showToast(message, type = 'success') {
        const toast = $(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);
        
        $('.toast-notification').html(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
    
    // Single setting edit button
    $('.edit-setting-btn').on('click', function() {
        const kunci = $(this).data('kunci');
        const currentValue = $(this).data('nilai');
        
        // Close other open edit forms
        $('.edit-form').each(function() {
            if ($(this).attr('id') !== `edit-form-${kunci}`) {
                const otherKunci = $(this).attr('id').replace('edit-form-', '');
                $(this).hide();
                $(`#value-display-${otherKunci}`).parent().show();
                $(`.edit-setting-btn[data-kunci="${otherKunci}"]`).show();
            }
        });
        
        enableEditMode(kunci, currentValue);
    });
    
    // Toggle edit all mode
    $('#toggleEditAll').on('click', function() {
        editMode = !editMode;
        
        if (editMode) {
            // Enable edit mode for all settings
            $('.edit-setting-btn').each(function() {
                const kunci = $(this).data('kunci');
                const currentValue = $(this).data('nilai');
                if ($(`#edit-form-${kunci}`).css('display') === 'none') {
                    enableEditMode(kunci, currentValue);
                }
            });
            $(this).html('<i class="icon-copy dw dw-cancel"></i> Batalkan Edit');
            $('#saveAllSettings').show();
        } else {
            // Cancel all edits
            $('.edit-form').hide();
            $('.setting-value-container').show();
            $('.edit-setting-btn').show();
            $(this).html('<i class="icon-copy dw dw-edit"></i> Edit Semua');
            $('#saveAllSettings').hide();
        }
    });
    
    // Save all settings
    $('#saveAllSettings').on('click', async function() {
        const settings = [];
        
        // Collect all modified settings
        $('.setting-edit-form').each(function() {
            const form = $(this);
            if (form.closest('.edit-form').is(':visible')) {
                const kunci = form.data('kunci');
                const nilai = form.find('.setting-input').val();
                
                settings.push({
                    kunci: kunci,
                    nilai: nilai
                });
            }
        });
        
        if (settings.length === 0) {
            Swal.fire('Info', 'Tidak ada perubahan yang disimpan', 'info');
            return;
        }
        
        // Confirm save all
        const confirm = await Swal.fire({
            title: 'Konfirmasi',
            text: `Anda akan menyimpan ${settings.length} pengaturan. Lanjutkan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        });
        
        if (!confirm.isConfirmed) return;
        
        // Show loading
        $('#loadingSpinner').show();
        
        try {
            const response = await $.ajax({
                url: '{{ route("admin.pengaturan.update") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    pengaturan: settings
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            if (response.success) {
                Swal.fire('Berhasil!', response.message, 'success')
                    .then(() => location.reload());
            } else {
                throw new Error(response.message);
            }
                
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', error.responseJSON?.message || 'Gagal menyimpan pengaturan', 'error');
        } finally {
            $('#loadingSpinner').hide();
        }
    });
    
    // Save individual setting
    $(document).on('submit', '.setting-edit-form', async function(e) {
        e.preventDefault();
        
        const form = $(this);
        const kunci = form.data('kunci');
        const url = form.data('url'); // Mengambil URL dari data-url attribute
        const nilai = form.find('.setting-input').val();
        const originalValue = $(`.edit-setting-btn[data-kunci="${kunci}"]`).data('nilai');
        
        // Check if value changed
        if (nilai === originalValue) {
            Swal.fire('Info', 'Tidak ada perubahan pada nilai', 'info');
            return;
        }
        
        // Confirm save
        const confirm = await Swal.fire({
            title: 'Konfirmasi',
            text: `Simpan perubahan untuk "${kunci}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        });
        
        if (!confirm.isConfirmed) return;
        
        // Show loading on this card
        const card = form.closest('.card-box');
        const originalContent = card.html();
        card.html('<div class="text-center p-4"><div class="spinner-border text-primary"></div><p class="mt-2">Menyimpan...</p></div>');
        
        try {
            const response = await $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    nilai: nilai
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            if (response.success) {
                // Update display value
                const displayValue = nilai.trim() === '' ? '<span class="text-muted font-italic">(kosong)</span>' : escapeHtml(nilai);
                $(`#value-display-${kunci}`).html(displayValue);
                
                // Update the data attribute
                $(`.edit-setting-btn[data-kunci="${kunci}"]`).data('nilai', nilai);
                
                // Flash success effect
                $(`#value-display-${kunci}`).parent().addClass('flash-success');
                setTimeout(() => {
                    $(`#value-display-${kunci}`).parent().removeClass('flash-success');
                }, 1000);
                
                // Hide edit form
                $(`#edit-form-${kunci}`).hide();
                $(`#value-display-${kunci}`).parent().show();
                $(`.edit-setting-btn[data-kunci="${kunci}"]`).show();
                
                showToast('Pengaturan berhasil diperbarui', 'success');
                
                // Update timestamp
                const now = new Date();
                const formattedDate = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                const cardFooter = $(`#edit-form-${kunci}`).closest('.card-box').find('.border-top small');
                if (cardFooter.length) {
                    cardFooter.html(`<i class="icon-copy dw dw-calendar1"></i> Terakhir update: ${formattedDate}`);
                }
            } else {
                throw new Error(response.message);
            }
            
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', error.responseJSON?.message || 'Gagal menyimpan pengaturan', 'error');
            // Restore original content
            card.html(originalContent);
            // Rebind events
            rebindEvents();
        }
    });
    
    // Cancel edit
    $(document).on('click', '.btn-cancel-edit', function() {
        const kunci = $(this).data('kunci');
        $(`#edit-form-${kunci}`).hide();
        $(`#value-display-${kunci}`).parent().show();
        $(`.edit-setting-btn[data-kunci="${kunci}"]`).show();
        
        if (editMode) {
            editMode = false;
            $('#toggleEditAll').html('<i class="icon-copy dw dw-edit"></i> Edit Semua');
            $('#saveAllSettings').hide();
        }
    });
    
    // Refresh settings
    $('#refreshSettings').on('click', function() {
        location.reload();
    });
    
    // Helper function to enable edit mode for a specific setting
    function enableEditMode(kunci, currentValue) {
        $(`#value-display-${kunci}`).parent().hide();
        $(`.edit-setting-btn[data-kunci="${kunci}"]`).hide();
        $(`#edit-form-${kunci}`).show();
        $(`#edit-form-${kunci} .setting-input`).val(currentValue);
        
        // Auto focus on textarea
        setTimeout(() => {
            $(`#edit-form-${kunci} .setting-input`).focus();
        }, 100);
        
        // Scroll to the edit form
        $('html, body').animate({
            scrollTop: $(`#edit-form-${kunci}`).offset().top - 100
        }, 500);
    }
    
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Rebind events after content restore
    function rebindEvents() {
        $('.edit-setting-btn').off('click').on('click', function() {
            const kunci = $(this).data('kunci');
            const currentValue = $(this).data('nilai');
            enableEditMode(kunci, currentValue);
        });
    }
    
    // Handle keyboard shortcuts
    $(document).on('keydown', function(e) {
        // Ctrl+E to toggle edit all
        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            $('#toggleEditAll').click();
        }
        // Escape to cancel edit
        if (e.key === 'Escape') {
            $('.btn-cancel-edit').click();
        }
    });
    
    // Auto-resize textarea
    $(document).on('input', '.setting-input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});
</script>
@endsection