@extends('templates.auth')

@push('head-scripts')
{{-- Load CSS Select2 & Bootstrap Theme --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">

<style>
    /* --- CSS Variables --- */
    :root {
        --radius-main: 5px;
        --primary-color: #005b9f;
        --card-border: #e1e4e8;
    }

    /* --- 1. Mode Login Form (Default) --- */
    /* Ini style bawaan untuk form login kecil di tengah */
    .wrapper-login {
        display: flex;
        justify-content: center;
        align-items: center; 
        min-height: 100vh;
        background-color: #f4f6f9;
        padding: 20px;
    }

    .container-login {
        background: #fff;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        max-width: 450px; 
        width: 100%;
        border: 1px solid #e1e4e8;
        transition: all 0.3s ease;
    }

    /* --- 2. Mode Dashboard (Express Active) --- */
    /* Class ini AKAN MENGHAPUS efek center flexbox saat dashboard aktif */
    
    .wrapper-login.express-active {
        display: block !important; /* PENTING: Matikan Flexbox */
        padding-top: 50px;
        height: auto !important;
        min-height: 100vh;
        align-items: flex-start !important;
    }

    .container-login.express-active {
        max-width: 1200px !important; /* Lebarkan container max-width */
        width: 95% !important; /* Gunakan hampir seluruh lebar layar */
        margin: 0 auto; /* Center horizontal */
        padding: 30px !important;
    }

    /* --- Card Styles for Roles --- */
    .role-card {
        border: 1px solid var(--card-border);
        border-radius: var(--radius-main);
        transition: 0.3s;
        height: 100%; /* Agar tinggi kartu seragam */
        background: #fff;
        position: relative;
        overflow: visible; /* Biarkan dropdown select2 keluar jika perlu */
        display: flex;
        flex-direction: column;
    }
    
    .role-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transform: translateY(-5px);
        border-color: #ccc;
    }

    .role-header {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        font-weight: 700;
        font-size: 15px;
        display: flex;
        align-items: center;
        background: #fafafa;
        color: #333;
    }

    .role-body {
        padding: 20px;
        flex-grow: 1; /* Isi ruang kosong agar tombol selalu di bawah */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    /* Role Indicators (Left Border Colors) */
    .border-l-cdc { border-left: 4px solid #34495e; }
    .border-l-koor { border-left: 4px solid #27ae60; }
    .border-l-dpm { border-left: 4px solid #2ecc71; }
    .border-l-mhs { border-left: 4px solid #3498db; }
    .border-l-hrd { border-left: 4px solid #e67e22; }
    .border-l-spv { border-left: 4px solid #f1c40f; }

    /* Select2 Customization */
    .select2-container--bootstrap4 .select2-selection--single {
        height: 42px !important;
        line-height: 42px !important;
        border-radius: var(--radius-main) !important;
        border: 1px solid #ced4da !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 12px;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
        line-height: 42px !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        top: 50% !important;
        transform: translateY(-50%);
    }

    .captcha-wrapper {
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: var(--radius-main);
        padding: 10px;
    }
    .captcha-question {
        font-family: 'Courier New', Courier, monospace;
        font-weight: bold;
        font-size: 1.1rem;
        color: #333;
        letter-spacing: 2px;
    }
</style>
@endpush

@section('login-form')

{{-- KONDISI 1: BELUM LOGIN (TAMPILAN KECIL) --}}
@if ( ($_SESSION['FLOGIN'] ?? FALSE) !== TRUE)

    <div class="wrapper wrapper-login">
        <div class="container container-login animated fadeIn">
            
            <div class="text-center mb-4">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded bg-dark shadow"><i class="fas fa-terminal text-white"></i></span>
                </div>
                <h4 class="font-weight-bold">Developer Console</h4>
                <p class="text-muted small">Restricted Access Area</p>
            </div>

            <form action="{{ router('login.express.verification') }}" method="post"> 
                <?= csrf() ?>
                
                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-muted">USERNAME</label>
                    <input type="text" placeholder="Dev Username" required name="username" class="form-control" autocomplete="off">
                </div>

                <div class="form-group mb-4">
                    <label class="small font-weight-bold text-muted">PASSWORD</label>
                    <input type="password" placeholder="Dev Key" required name="password" class="form-control">
                </div>

                <div class="form-group mb-4">
                    <label class="small font-weight-bold text-muted uppercase">Human Verification</label>
                    <div class="captcha-wrapper d-flex align-items-center justify-content-between">
                        <div class="captcha-question bg-white border px-3 py-1 rounded">
                            {{ $n1 }} + {{ $n2 }} = ?
                        </div>
                        <div style="width: 45%;">
                            <input type="number" name="captcha_answer" placeholder="Ans" 
                            required class="form-control text-center font-weight-bold" value="{{ old('captcha_answer') }}" autocomplete="off">
                        </div>
                    </div>
                    <small class="text-muted mt-1 d-block" style="font-size: 10px;">* Solve this math to verify you are a developer.</small>
                </div>

                <button type="submit" name="login_admin" class="btn btn-dark btn-block font-weight-bold py-2" style="border-radius: 5px;">
                    <i class="fas fa-key mr-2"></i> Access Console
                </button>
            </form>
        </div>
    </div>

{{-- KONDISI 2: SUDAH LOGIN (TAMPILAN LEBAR / DASHBOARD) --}}
@else

    {{-- Perhatikan class 'express-active' di div wrapper dan container ini --}}
    <div class="wrapper wrapper-login express-active">
        <div class="container container-login express-active animated fadeIn">

            {{-- Dashboard Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h3 class="font-weight-bold mb-0 text-primary"> <i class="fas fa-rocket mr-2"></i> Express Login</h3>
                    <p class="text-muted small mb-0">Select a user to impersonate instantly.</p>
                </div>
                <div class="text-right d-flex align-items-center">
                    <span class="badge badge-light border mr-2 p-2">
                        <i class="fas fa-clock mr-1"></i> <?= $_SESSION['FLOGIN-TIME'] ?>
                    </span>
                    <button class="btn btn-sm btn-danger font-weight-bold shadow-sm" onclick="logout_confirm()" style="border-radius: 5px;">
                        <i class="fas fa-power-off mr-1"></i> Logout Dev
                    </button>
                </div>
            </div>

            <div class="row">

                {{-- 1. CDC Admin --}}
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="role-card border-l-cdc">
                        <div class="role-header">
                            <i class="fas fa-user-shield mr-2"></i> CDC Admin
                        </div>
                        <div class="role-body">
                            <form action="index.php?form=flogin" method="POST"> 
                                <?= csrf() ?> <?= input_hidden('role', 'UPKPK') ?>
                                <div class="form-group px-0 mb-3">
                                    <select class="form-control select2" name="username" style="width:100%">
                                        <option></option>
                                        <?php 
                                        $sql1 = "SELECT id_kampus, nama_kampus FROM tb_kampus WHERE id_kampus IN( SELECT DISTINCT(id_kampus) FROM upkpk )";
                                        $exec1 = $_SERVER['KONEKSI_DB']->query($sql1); 
                                        ?>
                                        @foreach ($exec1 as $data1)
                                            <optgroup label="<?= $data1['nama_kampus'] ?>">
                                                <?php
                                                $sql = "SELECT * FROM upkpk WHERE id_kampus = '" . $data1['id_kampus'] . "'";
                                                $exec = $_SERVER['KONEKSI_DB']->query($sql);
                                                ?>
                                                @foreach ($exec as $data)
                                                    <option value="<?= $data['username'] ?>"><?= $data['username'] ?> - <?= $data['name'] ?></option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-dark btn-block btn-sm py-2 font-weight-bold mt-auto">Login as CDC</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- 2. Dosen Koordinator --}}
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="role-card border-l-koor">
                        <div class="role-header">
                            <i class="fas fa-chalkboard-teacher mr-2" style="color: #27ae60;"></i> Dosen Koordinator
                        </div>
                        <div class="role-body">
                            <form action="index.php?form=flogin" method="POST"> 
                                <?= csrf() ?> <?= input_hidden('role', 'Dosen') ?>
                                <div class="form-group px-0 mb-3">
                                    <select class="form-control select2" name="username" style="width:100%">
                                        <option></option>
                                        <?php 
                                        $sql1 = "SELECT id_kampus, nama_kampus FROM tb_kampus WHERE id_kampus IN( SELECT DISTINCT(id_kampus) FROM lecturer WHERE is_koor = 1 )";
                                        $exec1 = $_SERVER['KONEKSI_DB']->query($sql1); 
                                        ?>
                                        @foreach ($exec1 as $data1)
                                            <optgroup label="<?= $data1['nama_kampus'] ?>">
                                                <?php
                                                $sql = "SELECT * FROM lecturer LEFT JOIN program_study ON program_study.kode_prodi = lecturer.prodi_koor WHERE lecturer.is_koor = 1 AND lecturer.id_kampus = '" . $data1['id_kampus'] . "'";
                                                $exec = $_SERVER['KONEKSI_DB']->query($sql);
                                                ?>
                                                @foreach ($exec as $data)
                                                    <option value="<?= $data['nim_nik_unit'] ?>"><?= $data['prodi'] ?> | <?= $data['name'] ?></option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-block btn-sm py-2 font-weight-bold mt-auto" style="background:#27ae60; border:none;">Login as Koor</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- 3. Dosen Pembimbing --}}
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="role-card border-l-dpm">
                        <div class="role-header">
                            <i class="fas fa-user-tie mr-2" style="color: #2ecc71;"></i> Dosen Pembimbing
                        </div>
                        <div class="role-body">
                            <form action="index.php?form=flogin" method="POST"> 
                                <?= csrf() ?> <?= input_hidden('role', 'Dosen') ?>
                                <div class="form-group px-0 mb-3">
                                    <select class="form-control select2" name="username" style="width:100%">
                                        <option></option>
                                        <?php 
                                        $sql1 = "SELECT id_kampus, nama_kampus FROM tb_kampus WHERE id_kampus IN( SELECT DISTINCT(id_kampus) FROM lecturer WHERE is_koor = 1 )";
                                        $exec1 = $_SERVER['KONEKSI_DB']->query($sql1); 
                                        ?>
                                        @foreach ($exec1 as $data1)
                                            <optgroup label="<?= $data1['nama_kampus'] ?>">
                                                <?php
                                                $sql = "SELECT * FROM lecturer WHERE lecturer.is_koor NOT IN (1) AND lecturer.id_kampus = '" . $data1['id_kampus'] . "'";
                                                $exec = $_SERVER['KONEKSI_DB']->query($sql);
                                                ?>
                                                @foreach ($exec as $data)
                                                    <option value="<?= $data['nim_nik_unit'] ?>"><?= $data['name'] ?> (<?= $data['nim_nik_unit'] ?>)</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-block btn-sm py-2 font-weight-bold mt-auto" style="background:#2ecc71; border:none;">Login as DPM</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- 4. Mahasiswa --}}
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="role-card border-l-mhs">
                        <div class="role-header">
                            <i class="fas fa-user-graduate mr-2" style="color: #3498db;"></i> Mahasiswa
                        </div>
                        <div class="role-body">
                            <form action="index.php?form=flogin" method="POST"> 
                                <?= csrf() ?> <?= input_hidden('role', 'Mahasiswa') ?>
                                <div class="form-group px-0 mb-3">
                                    <select class="form-control select2" name="username" style="width:100%">
                                        <option></option>
                                        <?php 
                                        // 1. Ambil daftar Kampus yang aktif
                                        $sql1 = "SELECT id_kampus, nama_kampus FROM tb_kampus WHERE id_kampus IN( SELECT DISTINCT(id_kampus) FROM student_internship )";
                                        $exec1 = $_SERVER['KONEKSI_DB']->query($sql1); 
                                        ?>

                                        @foreach ($exec1 as $data1)
                                            <?php
                                            $id_k = $data1['id_kampus'];
                                            $nama_k = strtoupper($data1['nama_kampus']);
                                            
                                            // 2. Query data mahasiswa. Order wajib dimulai dari Prodi
                                            if ($id_k == '1') {
                                                // Berdasarkan NIM 3311801013, tahun '18' ada di posisi ke-4 dan ke-5
                                                // Jika datamu mengharuskan SUBSTRING(nim, 3, 2), silakan sesuaikan angkanya
                                                $sql = "SELECT *, SUBSTRING(nim, 4, 2) as code_year 
                                                        FROM student_internship 
                                                        WHERE id_kampus = '1' 
                                                        ORDER BY program_study ASC, code_year DESC, name ASC";
                                            } else {
                                                $sql = "SELECT * FROM student_internship 
                                                        WHERE id_kampus = '$id_k' 
                                                        ORDER BY program_study ASC, name ASC";
                                            }
                                            
                                            $exec = $_SERVER['KONEKSI_DB']->query($sql);
                                            $current_prodi = ""; 
                                            ?>

                                            {{-- Divider Visual untuk Kampus --}}
                                            <option disabled class="bg-light font-weight-bold text-center">
                                                --- {{ $nama_k }} ---
                                            </option>

                                            @foreach ($exec as $data)
                                                <?php 
                                                // Cek apakah ganti Prodi untuk membuat Grouping Baru
                                                if ($data['program_study'] !== $current_prodi) {
                                                    if ($current_prodi !== "") echo "</optgroup>"; // Tutup grup sebelumnya
                                                    
                                                    // Buka grup baru dengan label Prodi
                                                    echo "<optgroup label='" . htmlspecialchars($data['program_study']) . "'>";
                                                    $current_prodi = $data['program_study'];
                                                }

                                                // Ekstrak angkatan dari NIM
                                                // Gunakan substr sesuai posisi tahun di DB-mu
                                                $angkatan = "20" . substr($data['nim'], 3, 2); 
                                                ?>
                                                
                                                <option value="<?= $data['nim'] ?>">
                                                    [<?= $angkatan ?>] <?= $data['name'] ?>
                                                </option>
                                            @endforeach
                                            
                                            <?php if ($current_prodi !== "") echo "</optgroup>"; // Tutup grup terakhir di kampus ini ?>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block btn-sm py-2 font-weight-bold mt-auto">Login as Student</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- 5. HRD Industri --}}
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="role-card border-l-hrd">
                        <div class="role-header">
                            <i class="fas fa-building mr-2" style="color: #e67e22;"></i> HRD Industri
                        </div>
                        <div class="role-body">
                            <form action="index.php?form=flogin" method="POST"> 
                                <?= csrf() ?> <?= input_hidden('role', 'Perusahaan') ?>
                                <div class="form-group px-0 mb-3">
                                    <select class="form-control select2" name="username" style="width:100%">
                                        <option></option>
                                        <?php
                                        $sql = "SELECT * FROM user_company LEFT JOIN company ON company.id_company = user_company.id_company WHERE user_company.user_type = 'HRD' ORDER BY user_company.id_company";
                                        $exec = $_SERVER['KONEKSI_DB']->query($sql);
                                        ?>
                                        @foreach ($exec as $data)
                                            <option value="<?= $data['username'] ?>"><?= $data['name'] ?> (<?= $data['username'] ?>)</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-warning btn-block btn-sm py-2 text-white font-weight-bold mt-auto" style="background:#e67e22; border:none;">Login as HRD</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- 6. Supervisor Industri --}}
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="role-card border-l-spv">
                        <div class="role-header">
                            <i class="fas fa-user-cog mr-2" style="color: #f1c40f;"></i> Supervisor Industri
                        </div>
                        <div class="role-body">
                            <form action="index.php?form=flogin" method="POST"> 
                                <?= csrf() ?> <?= input_hidden('role', 'Perusahaan') ?>
                                <div class="form-group px-0 mb-3">
                                    <select class="form-control select2" name="username" style="width:100%">
                                        <option></option>
                                        <?php 
                                        $sql1 = "SELECT * FROM company WHERE id_company IN( SELECT DISTINCT(id_company) FROM user_company WHERE user_company.user_type NOT IN ('HRD') )";
                                        $exec1 = $_SERVER['KONEKSI_DB']->query($sql1); 
                                        ?>
                                        @foreach ($exec1 as $data1)
                                            <optgroup label="<?= $data1['name'] ?>">
                                                <?php
                                                $sql = "SELECT * FROM user_company LEFT JOIN company ON company.id_company = user_company.id_company WHERE user_company.id_company = '" .$data1['id_company']. "' AND user_company.user_type NOT IN ('HRD') ORDER BY user_company.id_company";
                                                $exec = $_SERVER['KONEKSI_DB']->query($sql);
                                                ?>
                                                @foreach ($exec as $data)
                                                    <option value="<?= $data['username'] ?>"><?= $data['username'] ?></option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-warning btn-block btn-sm py-2 text-dark font-weight-bold mt-auto" style="background:#f1c40f; border:none;">Login as SPV</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div> {{-- End Row --}}

        </div>
    </div>

@endif

@endsection

@push('footer-scripts')
{{-- Load Script Select2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="./core/component/soloalert.js"></script>

<script>
    $(document).ready(function() {
        // Init semua select2 dengan tema bootstrap4
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: "Select User to Impersonate",
            allowClear: true,
            width: '100%'
        });
    });

    function logout_confirm() {
        SoloAlert.confirm({
            title: "End Session?",
            body: "Are you sure you want to logout from Developer Console?",
            icon: "warning",
            theme: "light",
            onOk: () => {
                // Gunakan URL Absolut dari fungsi PHP base_url() untuk keamanan redirect
                window.location.href = "{{ base_url('index.php?action=logout') }}"; 
            }
        });
    }
</script>
@endpush