@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page">Dashboard</li>
@endsection

@section('page_title', 'Admin Dashboard')

@section('content')
<div class="row">
    <!-- Quick Stats -->
    <div class="col-md-6 col-xxl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-primary">
                            <i class="ti ti-users f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Students</h6>
                    </div>
                </div>
                <div class="bg-body p-3 mt-3 rounded">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <h5 class="mb-1">1,250</h5>
                            <p class="text-primary mb-0"><i class="ti ti-arrow-up-right"></i> 5.6% since last month</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xxl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-success">
                            <i class="ti ti-school f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Active Classes</h6>
                    </div>
                </div>
                <div class="bg-body p-3 mt-3 rounded">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <h5 class="mb-1">45</h5>
                            <p class="text-success mb-0"><i class="ti ti-arrow-up-right"></i> 2 new classes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xxl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-warning">
                            <i class="ti ti-book f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Courses</h6>
                    </div>
                </div>
                <div class="bg-body p-3 mt-3 rounded">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <h5 class="mb-1">128</h5>
                            <p class="text-warning mb-0">8 pending approval</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xxl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-danger">
                            <i class="ti ti-message-report f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Reports</h6>
                    </div>
                </div>
                <div class="bg-body p-3 mt-3 rounded">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <h5 class="mb-1">12</h5>
                            <p class="text-danger mb-0">3 urgent reports</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Welcome to School Management System</h5>
            </div>
            <div class="card-body">
                <p>This is your administrative dashboard. From here you can manage students, teachers, classes, and school settings.</p>
                <div class="alert alert-info border-0 shadow-none mb-0">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle f-20 me-2"></i>
                        <span>You have 5 new student registrations pending review.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
