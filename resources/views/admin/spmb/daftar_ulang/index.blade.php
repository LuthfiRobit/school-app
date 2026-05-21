@extends('admin.layouts.app')

@section('title', 'Daftar Ulang SPMB')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">SPMB</li>
    <li class="breadcrumb-item active" aria-current="page">Daftar Ulang</li>
@endsection

@section('page_title', 'Daftar Ulang SPMB')

@section('content')
<div x-data="daftarUlangApp()" x-init="initPage()" x-cloak>
    <div class="row g-4">
        <div class="col-12">
            <!-- Filter Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary"><i class="ti ti-filter me-2"></i>Filter Daftar Ulang</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tahun Pelajaran</label>
                            <select class="form-select select2-alpine" 
                                x-init="initSelect2($el, 'academic_year_id')"
                                data-placeholder="Semua Tahun Pelajaran">
                                <option value=""></option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Jalur Pendaftaran</label>
                            <select class="form-select select2-alpine" 
                                x-init="initSelect2($el, 'spmb_track_id')"
                                data-placeholder="Semua Jalur">
                                <option value=""></option>
                                @foreach($tracks as $track)
                                    <option value="{{ $track->id }}">{{ $track->trackType?->name ?? '-' }} ({{ $track->spmbConfiguration?->academicYear?->name ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status Daftar Ulang</label>
                            <select class="form-select select2-alpine" 
                                x-init="initSelect2($el, 'status')"
                                data-placeholder="Semua Status">
                                <option value=""></option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="ti ti-list me-2"></i>Data Daftar Ulang</h5>
                    <button class="btn btn-sm btn-light-primary" @click="refreshTable">
                        <i class="ti ti-refresh me-1"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    @include('admin.spmb.daftar_ulang.partials.table')
                </div>
            </div>
        </div>
    </div>

    @include('admin.spmb.daftar_ulang.partials.modal_detail')
    @include('admin.spmb.daftar_ulang.partials.modal_finalisasi')
</div>
@endsection

@push('js')
    @include('admin.spmb.daftar_ulang.partials.scripts')
@endpush

