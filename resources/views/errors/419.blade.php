@extends('admin.layouts.app')

@section('page_title', 'Sesi Berakhir')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="javascript: void(0)">Error</a></li>
    <li class="breadcrumb-item" aria-current="page">419</li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="ti ti-clock-stop text-info" style="font-size: 100px;"></i>
                </div>
                <h1 class="display-3 fw-bold text-dark mb-2">419</h1>
                <h3 class="mb-3">Sesi Telah Berakhir</h3>
                <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                    Halaman ini telah kedaluwarsa karena Anda terlalu lama tidak melakukan aktivitas. Silakan segarkan halaman dan coba lagi.
                </p>
                <div class="mt-2">
                    <a href="{{ url()->previous() }}" class="btn btn-primary d-inline-flex align-items-center shadow-sm px-4">
                        <i class="ti ti-arrow-left me-2"></i> Kembali & Coba Lagi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
