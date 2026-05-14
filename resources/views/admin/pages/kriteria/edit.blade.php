@extends('admin.layouts.app')
@section('title', 'Edit Kriteria')
@section('content')
<div class="pd-20 card-box mb-30">
    <h4 class="text-blue h4 mb-3">Edit Kriteria</h4>
    <form action="{{ route('admin.kriteria.update', $kriteria->id) }}" method="POST">
        @method('PUT')
        @include('admin.pages.kriteria._form')
    </form>
</div>
@endsection
