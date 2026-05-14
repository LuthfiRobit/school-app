@extends('admin.layouts.app')

@section('title', 'Master Jalur Pendaftaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">SPMB</li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item active" aria-current="page">Jalur</li>
@endsection

@section('page_title', 'Master Jalur Pendaftaran')

@section('content')
<div x-data="trackApp()" x-init="init()" x-cloak>
    <div class="row g-4">
        {{-- Partial Form Input/Edit --}}
        @include('admin.spmb.master.jalur.partials.form')

        {{-- Partial Tabel Data --}}
        @include('admin.spmb.master.jalur.partials.table')
    </div>

    {{-- Partial Modal Detail --}}
    @include('admin.spmb.master.jalur.partials.modal')
</div>
@endsection

@push('js')
    @include('admin.spmb.master.jalur.partials.scripts')
@endpush
