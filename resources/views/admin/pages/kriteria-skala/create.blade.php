@extends('admin.layouts.app')
@section('title', 'Tambah Sub Kriteria')
@section('content')
<div class="pd-20 card-box mb-30">
    <h4 class="text-blue h4 mb-3">Tambah Sub Kriteria</h4>
    <form action="{{ route('admin.kriteria-skala.store') }}" method="POST">
        @include('admin.pages.kriteria-skala._form')
    </form>
</div>
@endsection
