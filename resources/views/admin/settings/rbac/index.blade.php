@extends('admin.layouts.app')

@section('title', 'Manajemen Role')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Sistem</li>
    <li class="breadcrumb-item active" aria-current="page">RBAC</li>
@endsection

@section('page_title', 'Daftar Peran (Roles)')

@section('content')
    <div x-data="rbacIndexApp()" x-cloak>
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div
                        class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="mb-0"><i class="ti ti-settings me-2 text-primary"></i>Data Role & Hak Akses</h5>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <div style="min-width: 200px;">
                                <select id="filter-scope" class="form-select select2" data-placeholder="Filter Scope">
                                    <option value=""></option>
                                    <option value="all">Semua Scope</option>
                                    @foreach($scopes as $scope)
                                        <option value="{{ $scope->value }}">{{ $scope->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-light-primary d-inline-flex align-items-center" @click="refreshTable()">
                                <i class="ti ti-refresh me-1"></i> Refresh
                            </button>
                            <a href="{{ route('admin.settings.rbac.create') }}"
                                class="btn btn-primary d-inline-flex align-items-center">
                                <i class="ti ti-plus me-1"></i> Tambah Role
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('admin.settings.rbac.partials.role_table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.settings.rbac.partials.scripts_index')
@endsection