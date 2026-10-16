@extends('templates.auth')

@section('login-form')

<style>
    :root {
        --primary-color: #005b9f;
        --accent-color: #3498DB;
        --text-dark: #2c3e50;
        --bg-light: #f4f6f9;
        --radius-main: 5px; 
    }

    body {
        background-color: var(--bg-light);
    }

    .wrapper-login {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 15px;
    }

    .container-login {
        max-width: 480px;
        width: 100%;
        background: #fff;
        padding: 40px;
        border-radius: var(--radius-main);
        border: 1px solid #e1e4e8; /* Border halus */
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    /* Role Button Style */
    .btn-role-item {
        display: flex;
        align-items: center;
        width: 100%;
        background: #fff;
        border: 1px solid #e1e4e8;
        border-radius: var(--radius-main);
        padding: 12px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
        text-decoration: none;
        color: var(--text-dark);
        cursor: pointer;
    }

    .btn-role-item:hover {
        border-color: var(--primary-color);
        background-color: #f8fbff;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0, 91, 159, 0.1);
        text-decoration: none;
        color: var(--primary-color);
    }

    /* Icon Box di kiri tombol */
    .role-icon-box {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius-main);
        margin-right: 15px;
        font-size: 18px;
        flex-shrink: 0;
    }

    /* Warna spesifik per role */
    .icon-student { background: #e3f2fd; color: #1976d2; }
    .icon-lecturer { background: #e8f5e9; color: #388e3c; }
    .icon-industry { background: #fff3e0; color: #f57c00; }
    .icon-admin { background: #eceff1; color: #455a64; }

    /* Text Style */
    .role-info h6 {
        font-weight: 700;
        margin: 0;
        font-size: 15px;
        letter-spacing: 0.3px;
    }
    .role-info span {
        font-size: 13px;
        color: #6c757d;
        display: block;
        margin-top: 2px;
    }

    .btn-role-item:hover .role-info span {
        color: var(--primary-color);
    }

    /* Tombol Utama (Create Account) */
    .btn-action-primary {
        background: var(--primary-color);
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: var(--radius-main);
        font-weight: 600;
        font-size: 14px;
        width: 100%;
        transition: 0.3s;
    }
    
    .btn-action-primary:hover {
        background: #004a80;
        color: #fff;
    }

    .modal-content {
        border-radius: var(--radius-main);
        border: none;
    }
</style>

<div class="wrapper wrapper-login">
    <div class="container container-login animated fadeIn">
        
        <div class="text-center mb-4">
            <a href="{{ base_url() }}">
                <img src="{{ base_url('assets/jobcy/img/logo.png') }}" alt="Logo" style="max-height: 50px;" class="mb-3">
            </a>
            <h5 class="font-weight-bold" style="color: var(--text-dark);">Welcome Back!</h5>
            <p class="text-muted small">Please select your role to login.</p>
        </div>

        <div id="role-selector">
            
            <button class="btn-role-item" onclick="showRoleInfo('student')">
                <div class="role-icon-box icon-student">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="role-info text-left">
                    <h6>Student</h6>
                    <span>Mahasiswa</span>
                </div>
            </button>

            <button class="btn-role-item" onclick="showRoleInfo('lecturer')">
                <div class="role-icon-box icon-lecturer">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="role-info text-left">
                    <h6>Lecturer</h6>
                    <span>Dosen / Koordinator</span>
                </div>
            </button>

            <button class="btn-role-item" onclick="showRoleInfo('industry')">
                <div class="role-icon-box icon-industry">
                    <i class="fas fa-building"></i>
                </div>
                <div class="role-info text-left">
                    <h6>Industry Partner</h6>
                    <span>Mitra Industri</span>
                </div>
            </button>

            <button class="btn-role-item" onclick="showRoleInfo('cdc')">
                <div class="role-icon-box icon-admin">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="role-info text-left">
                    <h6>CDC Administrator</h6>
                    <span>Administrator</span>
                </div>
            </button>

            <div class="mt-4 mb-3" style="border-top: 1px solid #eee;"></div>

            <div class="text-center">
                <p class="small text-muted mb-2">New here?</p>
                <button type="button" class="btn-action-primary" data-toggle="modal" data-target="#registerModal">
                    Create New Account
                </button>
                <a href="{{ base_url() }}" class="btn btn-link btn-sm text-muted mt-2" style="text-decoration: none;">
                    <i class="fa fa-arrow-left mr-1"></i> Back to Home
                </a>
            </div>

        </div>
    </div>
</div>

{{-- ======================================================================= --}}
{{-- MODAL 1: LOGIN INFO (ROLE DESCRIPTION)                                --}}
{{-- ======================================================================= --}}
<div class="modal fade" id="roleInfoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-main); overflow: hidden;">
            
            <div class="modal-header border-0 pb-0 pt-4 px-4 position-relative">
                <div class="text-center w-100">
                    <h5 class="modal-title font-weight-bold" style="color: var(--primary-color); font-size: 1.25rem;">
                        Role Information
                    </h5>
                    <p class="text-muted small mb-0">Please read the access details below</p>
                </div>
                <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 20px; top: 20px; opacity: 0.5;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body px-4 pt-3 pb-2" id="roleDescription">
            </div>
            
            <div class="modal-footer border-0 px-4 pb-4 pt-2 d-block">
                
                <div class="form-group mb-3">
                    <div class="custom-control custom-checkbox text-center">
                        <input type="checkbox" class="custom-control-input" id="skipRoleInfo">
                        <label class="custom-control-label text-muted small user-select-none" for="skipRoleInfo" style="cursor: pointer;">
                            Don't show this information again today
                        </label>
                    </div>
                </div>
                
                <div class="row no-gutters">
                    <div class="col-5 pr-2">
                        <button type="button" class="btn btn-outline-secondary btn-block font-weight-bold py-2" data-dismiss="modal" style="border-radius: var(--radius-main); border-width: 1px;">
                            Cancel
                        </button>
                    </div>
                    <div class="col-7 pl-2">
                        <button type="button" id="redirectNowButton" class="btn btn-primary btn-block font-weight-bold py-2 shadow-sm" style="border-radius: var(--radius-main); background: var(--primary-color); border: 1px solid var(--primary-color);">
                            Continue <span class="badge badge-light text-primary ml-1" id="countdown" style="min-width: 20px;">5</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

{{-- ======================================================================= --}}
{{-- MODAL 2: REGISTER SELECTION                                           --}}
{{-- ======================================================================= --}}
<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: var(--radius-main);">
            
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-dark w-100 text-center">Create New Account</h5>
                <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 20px; top: 20px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body px-4 pt-2 pb-5">
                <p class="text-muted text-center mb-4 small">Please select the type of account you wish to register.</p>
                
                <a href="{{ router('register.student.index') }}" class="btn-role-item text-decoration-none">
                    <div class="role-icon-box icon-student">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="role-info text-left">
                        <h6>Student Registration</h6>
                        <span>Pendaftaran Mahasiswa</span>
                    </div>
                    <i class="fas fa-arrow-right ml-auto text-muted"></i>
                </a>

                </div>
        </div>
    </div>
</div>
@endsection

@push('footer-scripts')
<script>
    let currentRole = '';
    let interval; 

    // Deskripsi Role (Bilingual & Lengkap)
    // Deskripsi Role (Fixed: Icon Kotak Radius Kecil, Bukan Oval)
    const roleDescriptions = {
        'student': `
            <div class="text-center">
                <div class="mx-auto mb-3 d-inline-flex align-items-center justify-content-center" 
                     style="width: 70px; height: 70px; background-color: #e3f2fd; color: #005b9f; border-radius: 5px;">
                    <i class="fas fa-user-graduate fa-2x"></i>
                </div>
                
                <h5 class="font-weight-bold text-dark mb-1">Student (Mahasiswa)</h5>
                <span class="badge badge-light mb-3 px-3 py-1 border" style="border-radius: 5px;">Internship Participant</span>
                
                <div class="text-left bg-light p-3 border mb-3" style="border-radius: 5px;">
                    <h6 class="font-weight-bold text-primary mb-1" style="font-size: 13px;">🇬🇧 English</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">
                        As a student, you can access the platform to search for available internship positions, submit applications, fill out your daily logbooks, and download certificates.
                    </p>
                </div>

                <div class="text-left bg-light p-3 border" style="border-radius: 5px;">
                    <h6 class="font-weight-bold text-primary mb-1" style="font-size: 13px;">🇮🇩 Bahasa Indonesia</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">
                        Sebagai mahasiswa, Anda dapat mencari posisi magang, mengirim lamaran, mengisi logbook harian, dan mengunduh sertifikat.
                    </p>
                </div>
            </div>
        `,
        'lecturer': `
            <div class="text-center">
                <div class="mx-auto mb-3 d-inline-flex align-items-center justify-content-center" 
                     style="width: 70px; height: 70px; background-color: #e8f5e9; color: #2e7d32; border-radius: 5px;">
                    <i class="fas fa-chalkboard-teacher fa-2x"></i>
                </div>

                <h5 class="font-weight-bold text-dark mb-1">Lecturer (Dosen)</h5>
                <span class="badge badge-light mb-3 px-3 py-1 border" style="border-radius: 5px;">Supervisor & Coordinator</span>

                <div class="text-left bg-light p-3 border mb-3" style="border-radius: 5px;">
                    <h6 class="font-weight-bold text-success mb-1" style="font-size: 13px;">🇬🇧 English</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">
                        Access for Lecturers to monitor students' progress, validate monthly reports, and provide academic assessments.
                    </p>
                </div>

                <div class="text-left bg-light p-3 border" style="border-radius: 5px;">
                    <h6 class="font-weight-bold text-success mb-1" style="font-size: 13px;">🇮🇩 Bahasa Indonesia</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">
                        Akses Dosen untuk memantau progres mahasiswa, memvalidasi laporan bulanan, dan memberikan penilaian akademik.
                    </p>
                </div>
            </div>
        `,
        'industry': `
            <div class="text-center">
                <div class="mx-auto mb-3 d-inline-flex align-items-center justify-content-center" 
                     style="width: 70px; height: 70px; background-color: #fff3e0; color: #ef6c00; border-radius: 5px;">
                    <i class="fas fa-building fa-2x"></i>
                </div>

                <h5 class="font-weight-bold text-dark mb-1">Industry Partner</h5>
                <span class="badge badge-light mb-3 px-3 py-1 border" style="border-radius: 5px;">Mitra Industri</span>

                <div class="text-left bg-light p-3 border mb-3" style="border-radius: 5px;">
                    <h6 class="font-weight-bold text-warning mb-1" style="font-size: 13px;">🇬🇧 English</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">
                        Partners can publish vacancies, approve student placements, and provide technical performance evaluations.
                    </p>
                </div>

                <div class="text-left bg-light p-3 border" style="border-radius: 5px;">
                    <h6 class="font-weight-bold text-warning mb-1" style="font-size: 13px;">🇮🇩 Bahasa Indonesia</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">
                        Mitra dapat mempublikasikan lowongan, menyetujui penempatan, dan memberikan evaluasi kinerja teknis.
                    </p>
                </div>
            </div>
        `,
        'cdc': `
            <div class="text-center">
                <div class="mx-auto mb-3 d-inline-flex align-items-center justify-content-center" 
                     style="width: 70px; height: 70px; background-color: #eceff1; color: #455a64; border-radius: 5px;">
                    <i class="fas fa-user-shield fa-2x"></i>
                </div>

                <h5 class="font-weight-bold text-dark mb-1">CDC Administrator</h5>
                <span class="badge badge-light mb-3 px-3 py-1 border" style="border-radius: 5px;">System Admin</span>

                <div class="text-left bg-light p-3 border mb-3" style="border-radius: 5px;">
                    <h6 class="font-weight-bold text-secondary mb-1" style="font-size: 13px;">🇬🇧 English</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">
                        Full access to manage master data, oversee the entire internship process, and generate reports.
                    </p>
                </div>

                <div class="text-left bg-light p-3 border" style="border-radius: 5px;">
                    <h6 class="font-weight-bold text-secondary mb-1" style="font-size: 13px;">🇮🇩 Bahasa Indonesia</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">
                        Akses penuh untuk mengelola data master, mengawasi proses magang, dan membuat laporan.
                    </p>
                </div>
            </div>
        `
    };

    const roleUrls = {
        'student': "{{ router('login.student') }}",
        'lecturer': "{{ router('login.lecturer') }}",
        'industry': "{{ router('login.industry') }}",
        'cdc': "{{ router('login.cdc') }}"
    };

    function showRoleInfo(role) {
        currentRole = role;
        const skipKey = "skipRoleInfo_" + role;
        const today = new Date().toISOString().slice(0, 10);
        const savedValue = JSON.parse(localStorage.getItem(skipKey));

        if (savedValue && savedValue.date === today) {
            redirectNow();
            return;
        }

        document.getElementById("roleDescription").innerHTML = roleDescriptions[role];
        document.getElementById("skipRoleInfo").checked = false;
        $('#roleInfoModal').modal('show');
        startRedirect();
    }

    function startRedirect() {
        let countdown = 5;
        document.getElementById("countdown").innerText = countdown;
        if(interval) clearInterval(interval);
        interval = setInterval(function() {
            countdown--;
            document.getElementById("countdown").innerText = countdown;
            if (countdown <= 0) {
                clearInterval(interval);
                redirectNow();
            }
        }, 1000);
    }

    function redirectNow() {
        const skipCheckbox = document.getElementById("skipRoleInfo");
        if (skipCheckbox && skipCheckbox.checked) {
            const skipKey = "skipRoleInfo_" + currentRole;
            const today = new Date().toISOString().slice(0, 10);
            localStorage.setItem(skipKey, JSON.stringify({ date: today }));
        }
        window.location.href = roleUrls[currentRole];
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('redirectNowButton').addEventListener('click', redirectNow);
        $('#roleInfoModal').on('hidden.bs.modal', function () {
            if(interval) clearInterval(interval);
        });
    });
</script>
@endpush