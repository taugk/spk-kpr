@extends('admin.layouts.app')

@section('title', 'Bobot & Tipe Kriteria')

@section('page_action')
<a href="{{ route('admin.kriteria.create') }}" class="btn btn-primary">
    <i class="dw dw-plus"></i> Tambah Kriteria
</a>
@endsection



@section('content')
<div class="pd-20 card-box mb-30">
    <div class="clearfix mb-3">
        <div class="pull-left">
            <h4 class="text-blue h4">Bobot & Tipe Kriteria</h4>
            <p>Total bobot: <strong>{{ total_bobot_kriteria() }}</strong></p>
        </div>
        
    </div>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Kriteria</th>
                <th>Tipe</th>
                <th>Bobot</th>
                <th>Min</th>
                <th>Max</th>
                <th>Status</th>
                <th width="140">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kriteria as $item)
                <tr>
                    <td>{{ $item->kode_kriteria }}</td>
                    <td>{{ $item->nama_kriteria }}</td>
                    <td>{!! label_tipe_kriteria($item->tipe) !!}</td>
                    <td>{{ format_bobot($item->bobot) }}</td>
                    <td>{{ $item->nilai_min }}</td>
                    <td>{{ $item->nilai_max }}</td>
                    <td>{!! $item->aktif ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.kriteria.edit', $item->id) }}" class="btn btn-warning btn-sm"><i class="dw dw-edit"></i></a>
                        <form action="{{ route('admin.kriteria.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kriteria ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="dw dw-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $kriteria->links() }}
</div>
@endsection
