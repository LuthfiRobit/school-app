@extends('admin.layouts.app')

@section('title', 'Manajemen User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Sistem</li>
    <li class="breadcrumb-item active" aria-current="page">Pengguna</li>
@endsection

@section('page_title', 'Manajemen Pengguna')

@section('content')
<div x-data="userApp()" x-init="init()" x-cloak>
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5 class="mb-0"><i class="ti ti-users me-2 text-primary"></i>Daftar Pengguna Sistem</h5>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <div style="min-width: 200px;">
                            <select id="filter-role" class="form-select select2" data-placeholder="Filter Role">
                                <option value=""></option>
                                <option value="all">Semua Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-light-primary d-inline-flex align-items-center" @click="table.ajax.reload()">
                            <i class="ti ti-refresh me-1"></i> Refresh
                        </button>
                        <button class="btn btn-primary d-inline-flex align-items-center" @click="openCreateModal()">
                            <i class="ti ti-user-plus me-1"></i> Tambah User
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @include('admin.settings.user.partials.table')
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('admin.settings.user.partials.modals')
</div>

@include('admin.settings.user.partials.scripts')
@endsection
