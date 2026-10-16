@extends('templates.cdc')

@push('head-scripts')
<style>
    /* Styling khusus untuk Menu Grid */
    .menu-grid-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem 1rem;
        border: 1px solid #eee;
        border-radius: 10px;
        background: #fff;
        transition: all 0.3s ease;
        color: #575962;
        text-decoration: none !important;
        height: 100%;
    }
    .menu-grid-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border-color: #1572e8;
        color: #1572e8;
    }
    .menu-grid-btn i {
        font-size: 2rem;
        margin-bottom: 0.8rem;
    }
    .menu-grid-btn span {
        font-weight: 600;
        font-size: 0.9rem;
        text-align: center;
        line-height: 1.2;
    }
    .section-title {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888;
        margin-bottom: 1rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 0.5rem;
    }
</style>
@endpush

@section('main-content')

@php
    $d = $data; // Alias biar pendek nulisnya
    
    // Helper Status Badge
    $badgeClass = 'secondary';
    $statusLabel = $d['status'];
    switch ($d['status']) {
        case 'Approved': case 'Active': case 'Yes': $badgeClass = 'success'; $statusLabel = 'Active'; break;
        case 'Waiting':  $badgeClass = 'warning'; break;
        case 'No': case 'Rejected': $badgeClass = 'danger'; break;
        case 'Finished': $badgeClass = 'info'; break;
    }

    // Helper Foto Profil
    $fotoProfil = !empty($d['profile_picture']) ? base_url('assets/img/students/' . $d['profile_picture']) : base_url('assets/img/default-profile.jpg');
    
    // Helper Logo Company
    $logoCompany = !empty($d['company_logo']) ? base_url('assets/img/company/' . $d['company_logo']) : base_url('assets/img/default-company.png');
@endphp

<div class="container">
    
    {{-- HEADER --}}
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Internship Detail</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="{{ router('home') }}"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('landing') }}">Dashboard</a></li>
                
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('cdc.internship.management') }}">Internship Management</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="#">Detail</a></li>
            </ul>
            <div class="ml-auto">
                <a href="{{ router('cdc.internship.management') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            
            {{-- KOLOM KIRI: PROFIL & COMPANY (Compact) --}}
            <div class="col-md-4">
                
                {{-- 1. Card Profile Mahasiswa --}}
                <div class="card card-profile shadow-sm mb-4">
                    <div class="card-header" style="background-image: url('{{ base_url('assets/img/blogpost.jpg') }}'); background-size: cover;">
                        
                        {{-- [BARU] Tombol Aksi KHUSUS MOBILE (d-block d-md-none) --}}
                        <div class="dropdown position-absolute d-block d-md-none" style="top: 15px; right: 15px;">
                            <button class="btn btn-sm btn-light rounded-circle shadow-sm" type="button" id="mobileMenuAction" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 32px; height: 32px; padding: 0;">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow-sm border-0" aria-labelledby="mobileMenuAction">
                                <h6 class="dropdown-header">Manage Data</h6>
                                <a class="dropdown-item" href="">
                                    <i class="fas fa-pen mr-2 text-primary"></i> Edit Data
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="confirmDelete('{{ base64_encode($d['id_internship']) }}')">
                                    <i class="fas fa-trash mr-2"></i> Delete Data
                                </a>
                            </div>
                        </div>
                        {{-- [END] Tombol Mobile --}}

                        <div class="profile-picture">
                            <div class="avatar avatar-xl">
                                <img src="{{ $fotoProfil }}" alt="..." class="avatar-img rounded-circle border border-white shadow-sm" style="background-color: white;">
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="user-profile text-center">
                            <div class="name font-weight-bold text-dark mt-4">{{ $d['student_name'] }}</div>
                            <div class="job text-muted mb-2">{{ $d['nim'] }}</div>
                            <span class="badge badge-{{ $badgeClass }} px-3 py-2">{{ strtoupper($statusLabel) }}</span>
                            
                            <div class="view-profile mt-4 text-left">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Major</span>
                                    <span class="font-weight-bold small">{{ $d['major'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Prodi</span>
                                    <span class="font-weight-bold small text-right" style="max-width: 60%">{{ $d['jenjang'] }} {{ $d['study_program'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Phone</span>
                                    <span class="font-weight-bold small">{{ $d['student_phone'] ?? '-' }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Lecturer</span>
                                    <span class="font-weight-bold small text-primary">{{ $d['lecturer_name'] ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. [BARU] Card Internship Letter Status --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="font-weight-bold mb-0">Internship Letter</h5>
                            <i class="fas fa-file-contract text-secondary" style="font-size: 1.2rem;"></i>
                        </div>

                        {{-- Logika Pengecekan Surat (Sesuaikan variabel 'id_letter' dengan database nanti) --}}
                        @php
                            $hasLetter = false; // Contoh: Cek apakah ID surat ada
                        @endphp

                        @if($hasLetter)
                            {{-- KONDISI 1: SUDAH MENGAJUKAN --}}
                            <div class="alert alert-success p-2 mb-2 rounded d-flex align-items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span class="small font-weight-bold">Letter Issued</span>
                            </div>
                            <div class="small text-muted mb-3">
                                <i class="fas fa-barcode mr-1"></i> Ref: {{ $d['letter_number'] ?? '-' }}
                            </div>
                            <a href="" class="btn btn-outline-primary btn-block btn-sm">
                                View Letter
                            </a>
                        @else
                            {{-- KONDISI 2: BELUM MENGAJUKAN (WARNING) --}}
                            <div class="alert alert-warning p-2 mb-2 rounded d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span class="small font-weight-bold">Not Applied</span>
                            </div>
                            <p class="text-muted small mb-3" style="line-height: 1.3;">
                                Student has not applied for an official internship letter yet.
                            </p>
                            {{-- Opsional: Tombol untuk force create atau cek history --}}
                            {{-- <a href="" class="btn btn-secondary btn-block btn-sm disabled">No Data</a> --}}
                        @endif
                    </div>
                </div>

                {{-- 3. Card Company Info --}}
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">

                            <h5 class="font-weight-bold mb-0">Company Info</h5>
                        </div>
                        <h6 class="font-weight-bold text-dark">{{ $d['company_name'] ?? 'Unknown Company' }}</h6>
                        <p class="text-muted small mb-1"><i class="fas fa-map-marker-alt mr-1"></i> {{ $d['company_address'] ?? '-' }}</p>
                        <p class="text-muted small mb-0"><i class="fas fa-phone mr-1"></i> {{ $d['company_phone'] ?? '-' }}</p>
                        
                        @if(!empty($d['id_company']))
                        <a href="" class="btn btn-outline-secondary btn-block btn-sm mt-3">
                            View Company Profile
                        </a>
                        @endif
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: DETAIL & MENU GRID --}}
            <div class="col-md-8">
                                
                {{-- 1. INFO MAGANG (Top) --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        {{-- Judul Kiri --}}
                        <div class="card-title">
                            <i class="fas fa-briefcase mr-2 text-primary"></i> Internship Information
                        </div>
                        
                        {{-- Tombol Kanan (Edit & Delete) --}}
                        <div class="card-tools d-none d-md-block">
                            {{-- Tombol Edit (HREF KOSONG) --}}
                            <a href="" class="btn btn-primary btn-sm mr-1" data-toggle="tooltip" title="Edit Data">
                                <i class="fas fa-pen mr-1"></i> Edit
                            </a>

                            {{-- Tombol Delete --}}
                            <button type="button" class="btn btn-danger btn-sm" 
                                    onclick="confirmDelete('{{ base64_encode($d['id_internship']) }}')" 
                                    data-toggle="tooltip" title="Delete Data">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </div>
                    </div>
    
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <div class="d-flex align-items-center">
                                    <div class="icon-big text-center icon-primary bubble-shadow-small rounded-circle mr-3" style="width: 50px; height: 50px; line-height: 50px;">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-1">Position</p>
                                        <h5 class="font-weight-bold mb-0">{{ $d['internship_position'] ?? '-' }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="icon-big text-center icon-info bubble-shadow-small rounded-circle mr-3" style="width: 50px; height: 50px; line-height: 50px;">
                                        <i class="far fa-clock"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-1">Duration</p>
                                        @php
                                            $start = new DateTime($d['start_date']);
                                            $end   = new DateTime($d['end_date']);
                                            $interval = $start->diff($end);
                                            $months = $interval->m + ($interval->y * 12);
                                        @endphp
                                        <h5 class="font-weight-bold mb-0">{{ date('d M Y', strtotime($d['start_date'])) }} - {{ date('d M Y', strtotime($d['end_date'])) }}</h5>
                                        <small class="text-muted">({{ $months }} Months)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. MENU GRID (Action Buttons) --}}
                {{-- Hanya tampil jika status Valid --}}
                @if( !in_array($d['status'], ['Waiting', 'No', 'Rejected']) )
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        
                        {{-- GROUP: ADMINISTRATION --}}
                        <div class="section-title">Administration</div>
                        <div class="row mb-4">
                            <div class="col-6 col-sm-3 mb-3">
                                <a href="" 
                                class="menu-grid-btn">
                                    <i class="fab fa-wpforms text-primary"></i>
                                    <span>Registration Data</span>
                                </a>
                            </div>
                            <div class="col-6 col-sm-3 mb-3">
                                <a href="" 
                                class="menu-grid-btn">
                                    <i class="fas fa-user-tie text-success"></i>
                                    <span>Supervisor Profile</span>
                                </a>
                            </div>
                            <div class="col-6 col-sm-3 mb-3">
                                <a href="" 
                                class="menu-grid-btn">
                                    <i class="fas fa-tasks text-warning"></i>
                                    <span>Jobdesk</span>
                                </a>
                            </div>
                            @if($d['kode_prodi'] == 'LPI')
                            <div class="col-6 col-sm-3 mb-3">
                                <a href="" 
                                class="menu-grid-btn">
                                    <i class="fas fa-star text-danger"></i>
                                    <span>Competency</span>
                                </a>
                            </div>
                            @endif
                        </div>

                        {{-- GROUP: DAILY ACTIVITY --}}
                        <div class="section-title">Daily Activity</div>
                        <div class="row mb-4">
                            <div class="col-6 col-sm-3 mb-3">
                                <a href="" 
                                class="menu-grid-btn">
                                    <i class="fas fa-comments text-info"></i>
                                    <span>Discussion</span>
                                </a>
                            </div>
                            <div class="col-6 col-sm-3 mb-3">
                                <a href="" 
                                class="menu-grid-btn">
                                    <i class="fas fa-calendar-check text-danger"></i>
                                    <span>Attendance</span>
                                </a>
                            </div>
                            <div class="col-6 col-sm-3 mb-3">
                                <a href="" 
                                class="menu-grid-btn">
                                    <i class="fas fa-book-open text-primary"></i>
                                    <span>Logbook</span>
                                </a>
                            </div>
                        </div>

                        {{-- GROUP: REPORT & FEEDBACK --}}
                        <div class="section-title">Report & Feedback</div>
                        <div class="row">
                            <div class="col-6 col-sm-3 mb-3">
                                <a href="" 
                                class="menu-grid-btn">
                                    <i class="fas fa-file-alt text-dark"></i>
                                    <span>Final Report</span>
                                </a>
                            </div>
                            <div class="col-6 col-sm-3 mb-3">
                                <a href="" 
                                class="menu-grid-btn">
                                    <i class="fas fa-comment-dots text-secondary"></i>
                                    <span>Student Feedback</span>
                                </a>
                            </div>
                            <div class="col-6 col-sm-3 mb-3">
                                <a href="" 
                                class="menu-grid-btn">
                                    <i class="fas fa-industry text-secondary"></i>
                                    <span>Industry Feedback</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
                @endif

                {{-- 3. DOCUMENTS TABLE (Tetap di bawah) --}}
                <div class="card shadow-sm">
                    <div class="card-header">
                        <div class="card-title h5">Uploaded Documents</div>
                    </div>
                    <div class="card-body p-0">
                        {{-- ... (Tabel Dokumen sama seperti sebelumnya, copy paste saja isinya) ... --}}
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th class="pl-4">Document</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-right pr-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- ... Isi Tbody Dokumen ... --}}
                                    {{-- Copy dari kode sebelumnya --}}
                                    {{-- File 1 --}}
                                    <tr>
                                        <td class="pl-4">Acceptance Letter (LoA)</td>
                                        <td class="text-center">
                                            @if(!empty($d['file_1'])) <i class="fas fa-check-circle text-success"></i> 
                                            @else <i class="fas fa-times-circle text-muted"></i> @endif
                                        </td>
                                        <td class="text-right pr-4">
                                            @if(!empty($d['file_1']))
                                                <a href="{{ base_url('uploads/internship/' . $d['file_1']) }}" target="_blank" class="btn btn-icon btn-round btn-primary btn-xs"><i class="fas fa-download"></i></a>
                                            @else <span class="text-muted small">Empty</span> @endif
                                        </td>
                                    </tr>
                                    {{-- File 2 & 3 dst... --}}
                                     <tr>
                                        <td class="pl-4">Progress Report</td>
                                        <td class="text-center">
                                            @if(!empty($d['file_2'])) <i class="fas fa-check-circle text-success"></i> 
                                            @else <i class="fas fa-times-circle text-muted"></i> @endif
                                        </td>
                                        <td class="text-right pr-4">
                                            @if(!empty($d['file_2']))
                                                <a href="{{ base_url('uploads/internship/' . $d['file_2']) }}" target="_blank" class="btn btn-icon btn-round btn-primary btn-xs"><i class="fas fa-download"></i></a>
                                            @else <span class="text-muted small">Empty</span> @endif
                                        </td>
                                    </tr>
                                     <tr>
                                        <td class="pl-4"><strong>Final Report</strong></td>
                                        <td class="text-center">
                                            @if(!empty($d['final_report'])) <i class="fas fa-check-circle text-success"></i> 
                                            @else <i class="fas fa-times-circle text-muted"></i> @endif
                                        </td>
                                        <td class="text-right pr-4">
                                            @if(!empty($d['final_report']))
                                                <a href="{{ base_url('uploads/internship/' . $d['final_report']) }}" target="_blank" class="btn btn-icon btn-round btn-success btn-xs"><i class="fas fa-download"></i></a>
                                            @else <span class="text-muted small">Empty</span> @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
</div>
@endsection