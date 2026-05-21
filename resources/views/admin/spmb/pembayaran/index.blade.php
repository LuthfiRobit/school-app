@extends('admin.layouts.app')

@section('title', 'Verifikasi Pembayaran SPMB')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">SPMB</li>
    <li class="breadcrumb-item active" aria-current="page">Pembayaran</li>
@endsection

@section('page_title', 'Verifikasi Pembayaran SPMB')

@section('content')
<div x-data="paymentApp()" x-init="init()" x-cloak>
    <div class="row g-4">
        {{-- Tabel Pembayaran --}}
        @include('admin.spmb.pembayaran.partials.table')
    </div>

    {{-- Modal Detail & Preview Bukti --}}
    @include('admin.spmb.pembayaran.partials.modal_detail')

    {{-- Modal Konfirmasi --}}
    @include('admin.spmb.pembayaran.partials.modal_confirm')

    {{-- Modal Penolakan --}}
    @include('admin.spmb.pembayaran.partials.modal_reject')

    {{-- Modal Pembayaran Manual Cash --}}
    @include('admin.spmb.pembayaran.partials.modal_manual')
</div>
@endsection

@push('js')
    @include('admin.spmb.pembayaran.partials.scripts')
@endpush
