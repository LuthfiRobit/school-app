@extends('admin.layouts.app')

@section('title', 'Master Jenis Penilaian')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">SPMB</li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item active" aria-current="page">Jenis Penilaian</li>
@endsection

@section('page_title', 'Master Jenis Penilaian')

@section('content')
<div x-data="assessmentApp()" x-init="init()" x-cloak>
    <div class="row g-4">
        {{-- Partial Form Input/Edit --}}
        @include('admin.spmb.master.jenis_penilaian.partials.form')

        {{-- Partial Tabel Data --}}
        @include('admin.spmb.master.jenis_penilaian.partials.table')
    </div>

    {{-- Partial Modal Detail --}}
    @include('admin.spmb.master.jenis_penilaian.partials.modal')
</div>
@endsection

@push('js')
    @include('admin.spmb.master.jenis_penilaian.partials.scripts')
@endpush
