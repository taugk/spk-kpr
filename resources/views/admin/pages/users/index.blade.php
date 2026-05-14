{{-- resources/views/admin/pages/users/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Manajemen User')

@section('page_action')
    <div class="btn-list">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <i class="dw dw-add"></i> Tambah User
        </a>
    </div>
@endsection

@section('content')
<div class="min-height-200px">
    <div class="page-header">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="title">
                    <h4>Manajemen User</h4>
                    <p class="text-muted">Marketing, Admin, dan Manajer</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card-box pd-20 mb-30">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th width="5%">#</th>
                        <th width="10%">Foto</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                {{-- resources/views/admin/pages/users/index.blade.php --}}
<tbody>
    @forelse($users as $key => $user)
    <tr>
        <td>{{ $users->firstItem() + $key }}</td>
        <td>
            @if($user->foto_profil)
                <img src="{{ asset('storage/' . $user->foto_profil) }}" 
                     class="rounded-circle"
                     style="width: 40px; height: 40px; object-fit: cover;">
            @else
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center"
                     style="width: 40px; height: 40px;">
                    <i class="fa fa-user text-secondary"></i>
                </div>
            @endif
        </td>
        <td>{{ $user->nama_lengkap }}</td>
        <td>{{ $user->email }}</td>
        <td>
            @php
                $badgeClass = [
                    'marketing' => 'badge-info',
                    'admin' => 'badge-warning',
                    'manajer' => 'badge-danger'
                ][$user->role] ?? 'badge-secondary';
            @endphp
            <span class="badge {{ $badgeClass }}">{{ ucfirst($user->role) }}</span>
        </td>
        <td>
            @if($user->status == 'aktif')
                <span class="badge badge-success">Aktif</span>
            @else
                <span class="badge badge-danger">Nonaktif</span>
            @endif
        </td>
        <td>
            {{-- Handle null created_at --}}
            @if($user->created_at)
                {{ date('d/m/Y', strtotime($user->created_at)) }}
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            <div class="btn-group btn-group-sm">
                <a href="{{ route('admin.users.edit', $user->id) }}" 
                   class="btn btn-warning" title="Edit">
                    <i class="dw dw-edit2"></i>
                </a>
                <button type="button" 
                        class="btn btn-primary toggle-status"
                        data-id="{{ $user->id }}"
                        data-status="{{ $user->status }}"
                        title="Toggle Status">
                    <i class="dw dw-refresh"></i>
                </button>
                <button type="button" 
                        class="btn btn-danger btn-delete"
                        data-id="{{ $user->id }}"
                        data-name="{{ $user->nama_lengkap }}"
                        title="Hapus">
                    <i class="dw dw-delete-3"></i>
                </button>
            </div>
        </td>
    </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center text-muted">
                Tidak ada data user
            </td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
$(document).ready(function() {
    // Delete user
    $('.btn-delete').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        Swal.fire({
            title: 'Hapus User?',
            html: `Apakah yakin ingin menghapus user <strong>${name}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#deleteForm').attr('action', '/admin/users/' + id).submit();
            }
        });
    });
    
    // Toggle status
    $('.toggle-status').click(function() {
        var id = $(this).data('id');
        var currentStatus = $(this).data('status');
        var newStatus = currentStatus == 'aktif' ? 'nonaktif' : 'aktif';
        
        Swal.fire({
            title: 'Ubah Status?',
            text: `Ubah status user menjadi ${newStatus}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/users/' + id + '/toggle-status',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            location.reload();
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
@endsection