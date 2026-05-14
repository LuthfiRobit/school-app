@extends('admin.layouts.app')

@section('title', 'Tahun Pelajaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Akademik</li>
    <li class="breadcrumb-item active" aria-current="page">Tahun Pelajaran</li>
@endsection

@section('page_title', 'Manajemen Tahun Pelajaran')

@section('content')
<div x-data="academicYearApp()" x-init="init()" x-cloak>
    <div class="row g-4">
        {{-- Partial Form Input/Edit --}}
        @include('admin.akademik.tahun_ajaran.partials.form')

        {{-- Partial Tabel Data --}}
        @include('admin.akademik.tahun_ajaran.partials.table')
    </div>

    {{-- Partial Modal Detail --}}
    @include('admin.akademik.tahun_ajaran.partials.modal')
</div>
@endsection

{{-- Partial Scripts --}}
@include('admin.akademik.tahun_ajaran.partials.scripts')
