@extends('admin.layouts.app')

@section('title', 'Sub Kriteria')

@section('page_action')
<a href="{{ route('admin.kriteria-skala.create') }}" class="btn btn-primary">
    <i class="dw dw-plus"></i> Tambah Sub Kriteria
</a>
@endsection


@section('content')
<div class="pd-20 card-box mb-30">
    <div class="clearfix mb-3">
        <div class="pull-left">
            <h4 class="text-blue h4">Sub Kriteria / Skala Nilai</h4>
        </div>
       
    </div>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Kriteria</th>
                <th>Skor</th>
                <th>Keterangan</th>
                <th>Nilai Min</th>
                <th>Nilai Max</th>
                <th width="140">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($skala as $item)
                <tr>
                    <td>{{ $item->kode_kriteria }} - {{ $item->nama_kriteria }}</td>
                    <td>{{ $item->skor }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>{{ $item->nilai_min }}</td>
                    <td>{{ $item->nilai_max }}</td>
                    <td>
                        <a href="{{ route('admin.kriteria-skala.edit', $item->id) }}" class="btn btn-warning btn-sm"><i class="dw dw-edit"></i></a>
                        <form action="{{ route('admin.kriteria-skala.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus sub kriteria ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="dw dw-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $skala->links('pagination::bootstrap-4') }}
</div>
@endsection
