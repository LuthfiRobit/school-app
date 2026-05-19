@extends('admin.layouts.app')

@section('title', 'Manajemen Jalur SPMB - ' . $spmbConfig->academicYear->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">SPMB</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.spmb.configurations.index') }}">Konfigurasi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Jalur</li>
@endsection

@section('page_title', 'Jalur SPMB: ' . $spmbConfig->academicYear->name)

@section('content')
<div x-data="trackApp()" x-init="init()" x-cloak>
    <div class="row g-4">
        {{-- Form Tambah/Edit Jalur --}}
        @include('admin.spmb.configurations.tracks.partials.form')

        {{-- Tabel Daftar Jalur --}}
        @include('admin.spmb.configurations.tracks.partials.table')
    </div>

    {{-- Modal Kelola Persyaratan (Biaya, Tes, Form) --}}
    @include('admin.spmb.configurations.tracks.partials.mapping-modal')
</div>
@endsection

@push('js')
    @include('admin.spmb.configurations.tracks.partials.scripts')
@endpush
