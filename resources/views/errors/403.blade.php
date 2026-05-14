@extends('admin.layouts.app')

@section('page_title', 'Akses Ditolak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="javascript: void(0)">Error</a></li>
    <li class="breadcrumb-item" aria-current="page">403</li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="ti ti-shield-lock text-danger" style="font-size: 100px;"></i>
                </div>
                <h1 class="display-3 fw-bold text-dark mb-2">403</h1>
                <h3 class="mb-3">Maaf, Akses Dibatasi!</h3>
                <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                    {{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses yang diperlukan untuk masuk ke area ini.' }}
                </p>
                <div class="mt-2">
                    <a href="{{ url('/admin/dashboard') }}" class="btn btn-primary d-inline-flex align-items-center shadow-sm px-4">
                        <i class="ti ti-home me-2"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
