@extends('admin.layouts.app')

@section('title', 'Konfigurasi Tahun Ajaran SPMB')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">SPMB</li>
    <li class="breadcrumb-item active" aria-current="page">Konfigurasi</li>
@endsection

@section('page_title', 'Konfigurasi Tahun Ajaran SPMB')

@push('css')
<style>
    /* Fix SweetAlert2 behind bootstrap modal */
    .swal2-container {
        z-index: 9999 !important;
    }
</style>
@endpush

@section('content')
<div x-data="configApp()" x-init="init()" x-cloak>
    <div class="row g-4">
        {{-- Partial Form Input/Setup --}}
        @include('admin.spmb.configurations.partials.form')

        {{-- Partial Tabel Data --}}
        @include('admin.spmb.configurations.partials.table')
    </div>

    {{-- Partial Modal Detail --}}
    @include('admin.spmb.configurations.partials.modal')

    {{-- Partial Modal Clone --}}
    @include('admin.spmb.configurations.partials.clone-modal')
</div>
@endsection

@push('js')
    @include('admin.spmb.configurations.partials.scripts')
@endpush
