@extends('admin.layouts.app')

@section('title', 'Penilaian & Penetapan Kelulusan SPMB')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">SPMB</li>
    <li class="breadcrumb-item active" aria-current="page">Penilaian & Kelulusan</li>
@endsection

@section('page_title', 'Penilaian & Penetapan Kelulusan SPMB')

@section('content')
<div x-data="assessmentApp()" x-init="init()" x-cloak>
    <div class="row g-4">
        {{-- Tabel Pendaftar Penilaian --}}
        @include('admin.spmb.penilaian.partials.table')
    </div>

    {{-- Modal Input / Lihat Nilai per Komponen --}}
    @include('admin.spmb.penilaian.partials.modal_nilai')

    {{-- Modal Penetapan Status Kelulusan --}}
    @include('admin.spmb.penilaian.partials.modal_kelulusan')
</div>
@endsection

@push('js')
    @include('admin.spmb.penilaian.partials.scripts')
@endpush
