@extends('admin.layouts.app')

@section('title', 'Tambah Role Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.rbac.index') }}">RBAC</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Role</li>
@endsection

@section('page_title', 'Tambah Role Baru')

@section('content')
<div x-data="rbacFormApp()" x-cloak>
    <form @submit.prevent="submitForm()">
        <div class="row g-4">
            <!-- Role Info -->
            <div class="col-xl-4 col-lg-5">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px; z-index: 10;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="ti ti-edit-circle me-2 text-primary"></i>
                            <span x-text="editMode ? 'Edit Role Pengguna' : 'Tambah Role Baru'"></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Nama Role</label>
                            <input type="text" class="form-control" x-model="formData.name" placeholder="Contoh: Administrator" required>
                            <div class="form-text small">Nama ini akan digunakan untuk pengecekan hak akses di sistem.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Scope / Jangkauan</label>
                            <select class="form-select select2-alpine" 
                                x-init="initSelect2($el, 'scope')"
                                data-placeholder="Pilih Scope">
                                <option value=""></option>
                                @foreach($scopes as $scope)
                                    <option value="{{ $scope->value }}">{{ $scope->label() }}</option>
                                @endforeach
                            </select>
                            <div class="form-text small">Menentukan kategori pengguna yang bisa menggunakan role ini.</div>
                        </div>
                        
                        <hr class="my-4 opacity-50">
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" :disabled="loading">
                                <span x-show="!loading"><i class="ti ti-device-floppy me-1"></i> Simpan Role</span>
                                <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
                            </button>
                            <a href="{{ route('admin.settings.rbac.index') }}" class="btn btn-light-secondary">Batal</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Selection -->
            <div class="col-xl-8 col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0"><i class="ti ti-shield-lock me-2 text-primary"></i>Hak Akses (Permissions)</h5>
                        <div class="d-flex gap-2 align-items-center">
                            <button type="button" class="btn btn-sm btn-light-info d-inline-flex align-items-center" @click="syncPermissions()">
                                <i class="ti ti-refresh me-1"></i> Sync Permission
                            </button>
                            <div class="form-check form-switch mb-0 ms-2">
                                <input class="form-check-input" type="checkbox" id="checkAll" @change="toggleAll($event)">
                                <label class="form-check-label fw-bold" for="checkAll">Pilih Semua</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <template x-for="(resources, module) in groupedPermissions" :key="module">
                                <div class="col-12 mb-4">
                                    <div class="card border border-opacity-50 shadow-none mb-0">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="fw-bold mb-0 text-primary">
                                                <i class="ti ti-folder me-1"></i> Modul: <span x-text="module"></span>
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row g-3">
                                                <template x-for="(perms, resource) in resources" :key="resource">
                                                    <div class="col-md-6 col-xl-4">
                                                        <div class="p-3 border rounded-3 bg-white h-100">
                                                            <div class="fw-bold mb-2 border-bottom pb-1 small text-muted">
                                                                <i class="ti ti-subtask me-1"></i> <span x-text="resource"></span>
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-x-3 gap-y-2">
                                                                <template x-for="perm in perms" :key="perm.id">
                                                                    <div class="form-check me-2">
                                                                        <input class="form-input-check-custom form-check-input" type="checkbox" 
                                                                            :value="perm.slug" 
                                                                            :id="'perm-'+perm.id" 
                                                                            x-model="formData.permissions">
                                                                        <label class="form-check-label small" :for="'perm-'+perm.id" x-text="perm.name"></label>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@include('admin.settings.rbac.partials.scripts_form')
@endsection
