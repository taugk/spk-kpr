@extends('admin.layouts.app')
@section('title', 'Tambah Kriteria')
@section('content')
<div class="pd-20 card-box mb-30">
    <h4 class="text-blue h4 mb-3">Tambah Kriteria</h4>
    <form action="{{ route('admin.kriteria.store') }}" method="POST">
        @include('admin.pages.kriteria._form')
    </form>
</div>
@endsection
