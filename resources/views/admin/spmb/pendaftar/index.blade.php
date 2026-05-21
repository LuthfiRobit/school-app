@extends('admin.layouts.app')

@section('title', 'Manajemen Pendaftar SPMB')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">SPMB</li>
    <li class="breadcrumb-item active" aria-current="page">Pendaftar</li>
@endsection

@section('page_title', 'Manajemen Pendaftar SPMB')

@section('content')
<div x-data="enrollmentApp()" x-init="init()" x-cloak>
    <div class="row g-4">
        {{-- Tabel Pendaftar --}}
        @include('admin.spmb.pendaftar.partials.table')
    </div>

    {{-- Modal Detail Pendaftar --}}
    @include('admin.spmb.pendaftar.partials.modal_detail')

    {{-- Modal Ubah Status --}}
    @include('admin.spmb.pendaftar.partials.modal_status')
</div>
@endsection

@push('js')
    @include('admin.spmb.pendaftar.partials.scripts')
@endpush
