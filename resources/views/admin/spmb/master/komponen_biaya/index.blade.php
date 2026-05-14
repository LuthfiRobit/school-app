@extends('admin.layouts.app')

@section('title', 'Master Komponen Biaya')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">SPMB</li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item active" aria-current="page">Komponen Biaya</li>
@endsection

@section('page_title', 'Master Komponen Biaya')

@section('content')
<div x-data="feeApp()" x-init="init()" x-cloak>
    <div class="row g-4">
        {{-- Partial Form Input/Edit --}}
        @include('admin.spmb.master.komponen_biaya.partials.form')

        {{-- Partial Tabel Data --}}
        @include('admin.spmb.master.komponen_biaya.partials.table')
    </div>

    {{-- Partial Modal Detail --}}
    @include('admin.spmb.master.komponen_biaya.partials.modal')
</div>
@endsection

@push('js')
    @include('admin.spmb.master.komponen_biaya.partials.scripts')
@endpush
