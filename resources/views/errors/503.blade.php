@extends('admin.layouts.app')

@section('page_title', 'Pemeliharaan Sistem')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="javascript: void(0)">Error</a></li>
    <li class="breadcrumb-item" aria-current="page">503</li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="ti ti-settings-automation text-secondary" style="font-size: 100px;"></i>
                </div>
                <h1 class="display-3 fw-bold text-dark mb-2">503</h1>
                <h3 class="mb-3">Sedang Pemeliharaan</h3>
                <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                    Kami sedang melakukan pemeliharaan rutin untuk meningkatkan layanan. Kami akan segera kembali dalam beberapa saat.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
