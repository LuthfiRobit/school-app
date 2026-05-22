@extends('admin.layouts.app')

@section('title', 'Dashboard SPMB')

@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page">Dashboard</li>
@endsection

@section('page_title', 'Dashboard SPMB Tunas Luhur')

@section('content')
<div class="row">

    @if($pendingPaymentsCount > 0)
    <div class="col-12 mb-4">
        <div class="alert alert-warning alert-dismissible fade show border-0 d-flex align-items-center" role="alert">
            <i class="ti ti-bell-ringing f-24 me-3"></i>
            <div>
                <strong>Perhatian!</strong> Terdapat <strong>{{ $pendingPaymentsCount }}</strong> pembayaran yang menunggu verifikasi Anda.
                <a href="{{ route('admin.spmb.pembayaran.index') }}" class="alert-link ms-2">Lihat Antrian</a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @endif

    <!-- KPI Cards -->
    <div class="col-md-6 col-xxl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-primary">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M13 9H7" stroke="#4680FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M22.0002 10.9702V13.0302C22.0002 13.5802 21.5602 14.0302 21.0002 14.0502H19.0402C17.9602 14.0502 16.9702 13.2602 16.8802 12.1802C16.8202 11.5502 17.0602 10.9602 17.4802 10.5502C17.8502 10.1702 18.3602 9.9502 18.9202 9.9502H21.0002C21.5602 9.9702 22.0002 10.4202 22.0002 10.9702Z" stroke="#4680FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M17.48 10.55C17.06 10.96 16.82 11.55 16.88 12.18C16.97 13.26 17.96 14.05 19.04 14.05H21V15.5C21 18.5 19 20.5 16 20.5H7C4 20.5 2 18.5 2 15.5V8.5C2 5.78 3.64 3.88 6.19 3.56C6.45 3.52 6.72 3.5 7 3.5H16C16.26 3.5 16.51 3.50999 16.75 3.54999C19.33 3.84999 21 5.76 21 8.5V9.95001H18.92C18.36 9.95001 17.85 10.17 17.48 10.55Z" stroke="#4680FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Pendaftar Aktif</h6>
                    </div>
                </div>
                <div class="bg-body p-3 mt-3 rounded">
                    <div class="mt-3 row align-items-center">
                        <div class="col-12">
                            <h5 class="mb-1">{{ number_format($totalPendaftarAktif) }}</h5>
                            <p class="text-primary mb-0"><i class="ti ti-calendar-event"></i> Tahun Ajaran Aktif</p>
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
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 7V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V7C3 4 4.5 2 8 2H16C19.5 2 21 4 21 7Z" stroke="#E58A00" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                <path opacity="0.6" d="M14.5 4.5V6.5C14.5 7.6 15.4 8.5 16.5 8.5H18.5" stroke="#E58A00" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                <path opacity="0.6" d="M8 13H12" stroke="#E58A00" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                <path opacity="0.6" d="M8 17H16" stroke="#E58A00" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Lulus Seleksi</h6>
                    </div>
                </div>
                <div class="bg-body p-3 mt-3 rounded">
                    <div class="mt-3 row align-items-center">
                        <div class="col-12">
                            <h5 class="mb-1">{{ number_format($totalPassed) }}</h5>
                            <p class="text-warning mb-0"><i class="ti ti-check"></i> Berstatus PASSED</p>
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
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 2V5" stroke="#2ca87f" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M16 2V5" stroke="#2ca87f" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                <path opacity="0.4" d="M3.5 9.08984H20.5" stroke="#2ca87f" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z" stroke="#2ca87f" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                <path opacity="0.4" d="M15.6947 13.7002H15.7037" stroke="#2ca87f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path opacity="0.4" d="M15.6947 16.7002H15.7037" stroke="#2ca87f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path opacity="0.4" d="M11.9955 13.7002H12.0045" stroke="#2ca87f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path opacity="0.4" d="M11.9955 16.7002H12.0045" stroke="#2ca87f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path opacity="0.4" d="M8.29431 13.7002H8.30329" stroke="#2ca87f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path opacity="0.4" d="M8.29395 16.7002H8.30293" stroke="#2ca87f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Siswa Tetap (Daftar Ulang)</h6>
                    </div>
                </div>
                <div class="bg-body p-3 mt-3 rounded">
                    <div class="mt-3 row align-items-center">
                        <div class="col-12">
                            <h5 class="mb-1">{{ number_format($totalPermanentStudent) }}</h5>
                            <p class="text-success mb-0"><i class="ti ti-user-check"></i> Berstatus PERMANENT</p>
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
                        <div class="avtar avtar-s bg-light-info">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#3ec9d6" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                <path opacity="0.4" d="M8.4707 10.7402L12.0007 14.2602L15.5307 10.7402" stroke="#3ec9d6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Penerimaan</h6>
                    </div>
                </div>
                <div class="bg-body p-3 mt-3 rounded">
                    <div class="mt-3 row align-items-center">
                        <div class="col-12">
                            <h5 class="mb-1">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</h5>
                            <p class="text-info mb-0"><i class="ti ti-wallet"></i> Pembayaran Terkonfirmasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pendaftar & Kuota per Jalur</h5>
            </div>
            <div class="card-body">
                <canvas id="jalurChart" height="100"></canvas>
                
                <div class="mt-4">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Jalur Pendaftaran</th>
                                    <th class="text-end">Kuota</th>
                                    <th class="text-end">Terisi (Lulus)</th>
                                    <th>Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($chartKuotaTerisi as $item)
                                <tr>
                                    <td>{{ $item['track'] }}</td>
                                    <td class="text-end">{{ $item['quota'] }}</td>
                                    <td class="text-end">{{ $item['filled'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress w-100 me-2" style="height: 8px;">
                                                @php
                                                    $bgClass = 'bg-success';
                                                    if ($item['percentage'] >= 100) $bgClass = 'bg-danger';
                                                    elseif ($item['percentage'] >= 80) $bgClass = 'bg-warning';
                                                @endphp
                                                <div class="progress-bar {{ $bgClass }}" role="progressbar" style="width: {{ min(100, $item['percentage']) }}%"></div>
                                            </div>
                                            <span class="text-muted small">{{ $item['percentage'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Distribusi Status</h5>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="mx-auto" style="position: relative; height: 300px; width: 100%;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Enrollments Table -->
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pendaftar Terbaru</h5>
                <a href="{{ route('admin.spmb.pendaftar.index') }}" class="btn btn-sm btn-light-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No. Pendaftaran</th>
                                <th>Nama Lengkap</th>
                                <th>Jalur Pendaftaran</th>
                                <th>Tanggal Daftar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestEnrollments as $enrollment)
                                <tr>
                                    <td><span class="text-muted">{{ $enrollment->enrollment_number }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avtar avtar-xs bg-light-primary me-2">
                                                {{ substr($enrollment->applicant->full_name, 0, 1) }}
                                            </div>
                                            <h6 class="mb-0">{{ $enrollment->applicant->full_name }}</h6>
                                        </div>
                                    </td>
                                    <td>{{ $enrollment->spmbTrack->trackType->name ?? '-' }}</td>
                                    <td>{{ $enrollment->created_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        <span class="badge {{ $enrollment->status->badgeClass() }}">
                                            {{ $enrollment->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data pendaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data Jalur
        const jalurData = @json($chartJalurData);
        if (jalurData.labels && jalurData.labels.length > 0) {
            const ctxJalur = document.getElementById('jalurChart').getContext('2d');
            new Chart(ctxJalur, {
                type: 'bar',
                data: {
                    labels: jalurData.labels,
                    datasets: [{
                        label: 'Total Pendaftar',
                        data: jalurData.data,
                        backgroundColor: '#4680FF',
                        borderRadius: 4,
                        barPercentage: 0.5
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }

        // Data Status
        const statusData = @json($chartStatusData);
        if (statusData.labels && statusData.labels.length > 0) {
            // Preset colors matching our status badges
            const colors = [
                '#E58A00', // Warning (Registered, Waiting)
                '#4680FF', // Primary (Verified, In Review)
                '#2CA87F', // Success (Passed, Permanent)
                '#DC2626', // Danger (Rejected)
                '#6B7280', // Secondary
                '#0F172A', // Dark
                '#10B981', // Emerald
                '#8B5CF6', // Violet
                '#EC4899', // Pink
                '#F59E0B'  // Amber
            ];
            
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: statusData.labels,
                    datasets: [{
                        data: statusData.data,
                        backgroundColor: colors.slice(0, statusData.labels.length),
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                boxWidth: 12
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    });
</script>
@endpush
