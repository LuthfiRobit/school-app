@extends('admin.layouts.app')

@section('title', 'Konfigurasi Lembaga')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Pengaturan</li>
    <li class="breadcrumb-item active" aria-current="page">Konfigurasi Lembaga</li>
@endsection

@section('page_title', 'Konfigurasi Informasi Lembaga')

@section('content')
<div x-data="schoolIdentityApp()" x-cloak>
    <form @submit.prevent="submitForm()" enctype="multipart/form-data">
        @csrf
        
        <!-- 1. Identitas Utama -->
        @include('admin.settings.school.partials.identity')

        <!-- 2. Lokasi & Kontak -->
        @include('admin.settings.school.partials.location')

        <!-- 3. Pejabat & Aset Visual -->
        @include('admin.settings.school.partials.headmaster')

        <!-- Action Buttons -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body py-3 d-flex justify-content-end align-items-center gap-2">
                <div class="text-muted small me-auto">
                    <i class="ti ti-info-circle me-1"></i> Pastikan semua data sudah benar sebelum disimpan.
                </div>
                <button type="reset" class="btn btn-light-secondary" :disabled="loading">Reset Form</button>
                <button type="submit" class="btn btn-primary" :disabled="loading">
                    <span x-show="!loading"><i class="ti ti-device-floppy me-1"></i> Simpan Perubahan</span>
                    <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

{{-- Partial Scripts --}}
@include('admin.settings.school.partials.scripts')