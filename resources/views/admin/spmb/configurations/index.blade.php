@extends('admin.layouts.app')

@section('title', 'Konfigurasi Tahun Ajaran SPMB')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">SPMB</li>
    <li class="breadcrumb-item active" aria-current="page">Konfigurasi</li>
@endsection

@section('page_title', 'Konfigurasi Tahun Ajaran SPMB')

@section('content')
<div x-data="configApp()" x-init="init()" x-cloak>
    <div class="row g-4">
        {{-- Partial Form Input/Setup --}}
        @include('admin.spmb.configurations.partials.form')

        {{-- Partial Tabel Data --}}
        @include('admin.spmb.configurations.partials.table')
    </div>

    {{-- Partial Modal Detail (Optional, but matching jalur structure) --}}
    @include('admin.spmb.configurations.partials.modal')
</div>
@endsection

@push('js')
    @include('admin.spmb.configurations.partials.scripts')
@endpush
