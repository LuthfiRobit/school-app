@extends('admin.layouts.app')

@section('title', 'Laporan SPMB')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item" aria-current="page">Laporan SPMB</li>
@endsection

@section('page_title', 'Laporan Penerimaan Siswa Baru')

@section('content')
<div x-data="laporanSpmb()">
    <!-- Filter Section -->
    @include('admin.spmb.laporan.partials.filter')

    <!-- Loading State -->
    <div x-show="loading">
        <div class="d-flex justify-content-center my-5 py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <div x-show="!loading" style="display: none;">
        <!-- KPI Cards -->
        @include('admin.spmb.laporan.partials.kpi_cards')

        <div class="row">
            <!-- Charts -->
            <div class="col-lg-7">
                @include('admin.spmb.laporan.partials.charts')
            </div>

            <!-- Table Summary & Export -->
            <div class="col-lg-5">
                @include('admin.spmb.laporan.partials.table_summary')
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    @include('admin.spmb.laporan.partials.scripts')
@endpush
