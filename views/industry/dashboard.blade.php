@extends('templates.industry')

@push('head-scripts')
<style>
    /* ================================
   SELECT2 BOOTSTRAP 4 STYLING
   ================================ */

    /* Container */
    .select2-container--bootstrap4 .select2-selection {
        min-height: 42px;
        padding: 6px 12px;
        border-radius: .25rem;
        border: 1px solid #ced4da;
        font-size: 14px;
        line-height: 1.5;
    }

    /* Single select */
    .select2-container--bootstrap4 .select2-selection--single {
        background-color: #fff;
    }

    /* Selected text */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        color: #495057;
        line-height: 28px;
        padding-left: 0;
    }

    /* Arrow */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: 100%;
        right: 10px;
    }

    /* Focus & Open */
    .select2-container--bootstrap4.select2-container--focus .select2-selection,
    .select2-container--bootstrap4.select2-container--open .select2-selection {
        border-color: #007bff;
        box-shadow: 0 0 0 .2rem rgba(0, 123, 255, .25);
    }

    /* Disabled */
    .select2-container--bootstrap4 .select2-selection--single[aria-disabled="true"] {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    /* Dropdown */
    .select2-container--bootstrap4 .select2-dropdown {
        border-radius: .25rem;
        border: 1px solid #ced4da;
    }

    /* Search box */
    .select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {
        border-radius: .25rem;
        padding: 6px 10px;
        border: 1px solid #ced4da;
    }

    /* Option */
    .select2-container--bootstrap4 .select2-results__option {
        padding: 8px 12px;
        font-size: 14px;
    }

    /* Hover option */
    .select2-container--bootstrap4 .select2-results__option--highlighted {
        background-color: #007bff;
        color: #fff;
    }

    /* Selected option */
    .select2-container--bootstrap4 .select2-results__option[aria-selected="true"] {
        background-color: #e9ecef;
        color: #495057;
    }

    /* Optgroup label */
    .select2-container--bootstrap4 .select2-results__group {
        padding: 6px 12px;
        font-weight: 600;
        font-size: 13px;
        background-color: #f8f9fa;
        color: #6c757d;
    }

    .dashboard-menu {
        border: 1px solid #f1f1f1;
        padding: 20px 10px;
        transition: .2s;
    }

    .dashboard-menu a {
        color: #007bff;
        display: block;
        text-decoration: none;
    }

    .dashboard-menu i {
        font-size: 28px;
        margin-bottom: 8px;
        display: block;
    }

    .dashboard-menu span {
        font-size: 14px;
        font-weight: 500;
    }

    .dashboard-menu:hover {
        background: #f8f9fa;
    }

    .alert-item {
        border: 1px solid #f1f1f1;
        border-radius: 4px;
        background: #fff;
        align-items: center;
    }

    .alert-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }



    .guide-item {
        border: 1px solid #f1f1f1;
        border-radius: 4px;
        background: #fff;
    }

    .guide-icon {
        width: 34px;
        height: 34px;
        border-radius: 4px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .stat-card {
        border: 1px solid #f1f1f1;
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 6px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .stat-value {
        font-size: 20px;
        font-weight: bold;
    }
</style>
@endpush
@section('main-content')

@php
$pdo = $_SERVER['PDO'];
$idCompany = $_SESSION['id_company'];
$userType  = $_SESSION['user_type'];
$idUser    = $_SESSION['id_user_company'] ?? null;
@endphp

@php
$statsQuery = [
    'count_university' => "SELECT COUNT(id_kampus) FROM tb_kampus",
    'count_company'    => "SELECT COUNT(id_company) FROM company",
    'count_lecturer'   => "SELECT COUNT(*) FROM lecturer",
    'count_prodi'      => "SELECT COUNT(*) FROM program_study",
    'count_offer'      => "SELECT COUNT(*) FROM offer WHERE id_company = :id_company",
];

$stats = [];

foreach ($statsQuery as $key => $sql) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute(strpos($sql, ':id_company') !== false ? ['id_company' => $idCompany] : []);
    $stats[$key] = (int) $stmt->fetchColumn();
}

/* Student aktif */
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT nim)
    FROM internship
    WHERE status = 'Yes' AND id_company = :id_company
");
$stmt->execute(['id_company' => $idCompany]);
$count_student = (int) $stmt->fetchColumn();
@endphp


@php
$notif = [
    'attendance' => [
        'sql' => "
            SELECT COUNT(a.id_attendance)
            FROM attendance a
            JOIN internship i ON a.id_internship = i.id_internship
            WHERE i.id_company = :id_company
            AND a.approval_spv = 'Waiting'
        ",
    ],
    'logbook' => [
        'sql' => "
            SELECT COUNT(l.id_logbook)
            FROM logbook l
            JOIN internship i ON l.id_internship = i.id_internship
            WHERE i.id_company = :id_company
            AND l.approval_spv = 'Waiting'
        ",
    ],
    'discussion' => [
        'sql' => "
            SELECT COUNT(d.id_discussion)
            FROM discussion d
            JOIN internship i ON d.id_internship = i.id_internship
            WHERE i.id_company = :id_company
            AND i.status = 'Yes'
            AND d.discussion_status = 'Open'
        ",
    ],
];

foreach ($notif as $key => &$item) {
    if ($userType === 'spv') {
        $item['sql'] .= " AND i.id_user_company = :id_user_company";
    }

    $stmt = $pdo->prepare($item['sql']);
    $params = ['id_company' => $idCompany];

    if ($userType === 'spv') {
        $params['id_user_company'] = $idUser;
    }

    $stmt->execute($params);
    $notif[$key]['count'] = (int) $stmt->fetchColumn();
}
@endphp

@php
$alerts = [];

if ($notif['attendance']['count'] > 0) {
    $alerts[] = [
        'title' => $notif['attendance']['count'] . ' Attendance need validation',
        'type'  => 'warning',
        'url'   => base_url('index.php?page=intern_attendance'),
    ];
}

if ($notif['logbook']['count'] > 0) {
    $alerts[] = [
        'title' => $notif['logbook']['count'] . ' Logbook need validation',
        'type'  => 'danger',
        'url'   => base_url('index.php?page=intern_logbook'),
    ];
}
@endphp

@php
$sqlBaseInternship = "
    FROM internship i
    WHERE i.id_company = :id_company
";

if ($userType === 'spv') {
    $sqlBaseInternship .= " AND i.id_user_company = :id_user_company";
}

$params = ['id_company' => $idCompany];
if ($userType === 'spv') {
    $params['id_user_company'] = $idUser;
}

/* Active Intern */
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT i.nim) " . $sqlBaseInternship . " AND i.status = 'Yes'");
$stmt->execute($params);
$stat_active_intern = (int) $stmt->fetchColumn();

/* Pending Attendance */
$stmt = $pdo->prepare("
    SELECT COUNT(a.id_attendance)
    FROM attendance a
    JOIN internship i ON a.id_internship = i.id_internship
    WHERE a.approval_spv = 'Waiting'
    AND i.id_company = :id_company
    " . ($userType === 'spv' ? "AND i.id_user_company = :id_user_company" : '')
);
$stmt->execute($params);
$stat_pending_attendance = (int) $stmt->fetchColumn();

/* Pending Logbook */
$stmt = $pdo->prepare("
    SELECT COUNT(l.id_logbook)
    FROM logbook l
    JOIN internship i ON l.id_internship = i.id_internship
    WHERE l.approval_spv = 'Waiting'
    AND i.id_company = :id_company
    " . ($userType === 'spv' ? "AND i.id_user_company = :id_user_company" : '')
);
$stmt->execute($params);
$stat_pending_logbook = (int) $stmt->fetchColumn();
@endphp

@php
$sql = "
    SELECT
        i.id_internship,
        si.nim,
        si.name,
        ps.jenjang,
        ps.study_program
    FROM student_internship si
    JOIN internship i ON si.nim = i.nim
    JOIN program_study ps ON si.program_study = ps.kode_prodi
    WHERE i.id_company = :id_company
    AND i.status = 'Yes'
    ORDER BY ps.kode_prodi, si.name
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id_company' => $idCompany]);

$program_study_data = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $key = $row['jenjang'].' '.$row['study_program'];
    $program_study_data[$key][] = $row;
}
@endphp



@php

$stmt = $_SERVER['PDO']->prepare("SELECT COUNT(id_kampus) AS Count_Kampus FROM tb_kampus");
$stmt->execute();
$count_university = $stmt->fetch(PDO::FETCH_ASSOC)['Count_Kampus'];

$stmt = $_SERVER['PDO']->prepare("SELECT COUNT(id_company) AS Count_company FROM company");
$stmt->execute();
$count_company = $stmt->fetch(PDO::FETCH_ASSOC)['Count_company'];

$stmt = $_SERVER['PDO']->prepare("SELECT COUNT(*) AS Count_Lecturer FROM lecturer");
$stmt->execute();
$count_lecturer = $stmt->fetch(PDO::FETCH_ASSOC)['Count_Lecturer'];

$stmt = $_SERVER['PDO']->prepare("SELECT COUNT(DISTINCT nim) AS Count_Student FROM internship WHERE `status` IN ('Yes',
'yes') AND id_company = :id_company");
$stmt->execute(['id_company' => $_SESSION['id_company']]);
$count_student = $stmt->fetch(PDO::FETCH_ASSOC)['Count_Student'];


$stmt = $_SERVER['PDO']->prepare("SELECT COUNT(*) AS Count_SPV FROM user_company WHERE user_type NOT IN ('HRD') AND
id_company = :id_company");
$stmt->execute(['id_company' => $_SESSION['id_company']]);
$count_spv = $stmt->fetch(PDO::FETCH_ASSOC)['Count_SPV'];

$stmt = $_SERVER['PDO']->prepare("SELECT COUNT(*) AS Count_HRD FROM user_company WHERE user_type = 'HRD' AND id_company
= :id_company");
$stmt->execute(['id_company' => $_SESSION['id_company']]);
$count_hrd = $stmt->fetch(PDO::FETCH_ASSOC)['Count_HRD'];


$stmt = $_SERVER['PDO']->prepare("SELECT COUNT(*) AS Count_Offer FROM offer WHERE id_company = :id_company");
$stmt->execute(['id_company' => $_SESSION['id_company']]);
$count_offer = $stmt->fetch(PDO::FETCH_ASSOC)['Count_Offer'];


$stmt = $_SERVER['PDO']->prepare("SELECT COUNT(*) AS Count_All_Student FROM student_internship");
$stmt->execute();
$count_all_student = $stmt->fetch(PDO::FETCH_ASSOC)['Count_All_Student'];

$stmt = $_SERVER['PDO']->prepare("SELECT COUNT(*) AS Count_Prodi FROM program_study");
$stmt->execute();
$count_prodi = $stmt->fetch(PDO::FETCH_ASSOC)['Count_Prodi'];

@endphp


@php


// Query pertama untuk menghitung jumlah attendance
$notif_attendance = "SELECT COUNT(attendance.id_attendance) AS count_attendance
FROM attendance
INNER JOIN internship ON attendance.id_internship = internship.id_internship
INNER JOIN student_internship ON internship.nim = student_internship.nim
LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
WHERE internship.id_company = :id_company
AND attendance.approval_spv = :approval_spv";

// Query kedua untuk menghitung jumlah logbook
$notif_logbook = "SELECT COUNT(logbook.id_logbook) AS count_logbook
FROM logbook
INNER JOIN internship ON logbook.id_internship = internship.id_internship
INNER JOIN student_internship ON internship.nim = student_internship.nim
LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
WHERE internship.id_company = :id_company
AND logbook.approval_spv = :approval_spv";

// Query ketiga untuk menghitung jumlah discussion
$notif_discussion = "SELECT COUNT(discussion.id_discussion) AS count_discussion
FROM discussion
INNER JOIN internship ON discussion.id_internship = internship.id_internship
INNER JOIN student_internship ON internship.nim = student_internship.nim
LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
WHERE internship.id_company = :id_company
AND internship.status = :internship_status AND discussion.discussion_status = :discussion_status";

// Menambahkan kondisi jika user_type adalah 'spv'
if ($_SESSION['user_type'] == 'spv') {
$notif_attendance .= " AND internship.id_user_company = :id_user_company";
$notif_logbook .= " AND internship.id_user_company = :id_user_company";
$notif_discussion .= " AND internship.id_user_company = :id_user_company";
}

// Persiapan untuk eksekusi query pertama
$stmt_attendance = $_SERVER['PDO']->prepare($notif_attendance);
$params_attendance = [':id_company' => $_SESSION['id_company'], ':approval_spv' => 'Waiting'];
if ($_SESSION['user_type'] == 'spv') {
$params_attendance[':id_user_company'] = $_SESSION['id_user_company'];
}
$stmt_attendance->execute($params_attendance);
$result_attendance = $stmt_attendance->fetch(PDO::FETCH_ASSOC);
$notif['count_attendance'] = $result_attendance['count_attendance'];

// Persiapan untuk eksekusi query kedua
$stmt_logbook = $_SERVER['PDO']->prepare($notif_logbook);
$params_logbook = [':id_company' => $_SESSION['id_company'], ':approval_spv' => 'Waiting'];
if ($_SESSION['user_type'] == 'spv') {
$params_logbook[':id_user_company'] = $_SESSION['id_user_company'];
}
$stmt_logbook->execute($params_logbook);
$result_logbook = $stmt_logbook->fetch(PDO::FETCH_ASSOC);
$notif['count_logbook'] = $result_logbook['count_logbook'];

// Persiapan untuk eksekusi query ketiga
$stmt_discussion = $_SERVER['PDO']->prepare($notif_discussion);
$params_discussion = [':id_company' => $_SESSION['id_company'], ':discussion_status' => 'Open', ':internship_status' =>
'Yes'];
if ($_SESSION['user_type'] == 'spv') {
$params_discussion[':id_user_company'] = $_SESSION['id_user_company'];
}
$stmt_discussion->execute($params_discussion);
$result_discussion = $stmt_discussion->fetch(PDO::FETCH_ASSOC);
$notif['count_discussion'] = $result_discussion['count_discussion'];



@endphp

<?php
$alerts = [];

/* ======================
   ATTENDANCE WAITING
   ====================== */
$sqlAttendance = "
    SELECT COUNT(a.id_attendance) AS total
    FROM attendance a
    INNER JOIN internship i ON a.id_internship = i.id_internship
    WHERE i.id_company = :id_company
    AND a.approval_spv = 'Waiting'
";

if ($_SESSION['user_type'] === 'spv') {
    $sqlAttendance .= " AND i.id_user_company = :id_user_company";
}

$stmt = $_SERVER['PDO']->prepare($sqlAttendance);
$params = ['id_company' => $_SESSION['id_company']];

if ($_SESSION['user_type'] === 'spv') {
    $params['id_user_company'] = $_SESSION['id_user_company'];
}

$stmt->execute($params);
$countAttendance = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

if ($countAttendance > 0) {
    $alerts[] = [
        'title' => "$countAttendance Attendance need validation",
        'type'  => 'warning',
        'url'   => base_url('index.php?page=intern_attendance')
    ];
}

/* ======================
   LOGBOOK WAITING
   ====================== */
$sqlLogbook = "
    SELECT COUNT(l.id_logbook) AS total
    FROM logbook l
    INNER JOIN internship i ON l.id_internship = i.id_internship
    WHERE i.id_company = :id_company
    AND l.approval_spv = 'Waiting'
";

if ($_SESSION['user_type'] === 'spv') {
    $sqlLogbook .= " AND i.id_user_company = :id_user_company";
}

$stmt = $_SERVER['PDO']->prepare($sqlLogbook);
$stmt->execute($params);
$countLogbook = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

if ($countLogbook > 0) {
    $alerts[] = [
        'title' => "$countLogbook Logbook need validation",
        'type'  => 'danger',
        'url'   => base_url('index.php?page=intern_logbook')

    ];
}
?>

<?php
$sqlAnnouncement = "
    SELECT 
        id_announcement,
        title,
        category,
        have_attachment,
        created_at
    FROM announcement
    WHERE status = 'Publish'
    ORDER BY id_announcement DESC
    LIMIT 5
";

$stmt = $_SERVER['PDO']->prepare($sqlAnnouncement);
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   ATTENDANCE TODAY
   =============================== */
$sqltotal_user_company = "
    SELECT COUNT(uc.id_user_company) AS total
    FROM user_company uc
    WHERE uc.id_company = :id_company
";

$stmt = $_SERVER['PDO']->prepare($sqltotal_user_company);
$stmt->execute($params);
$total_user_company = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

?>
<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h1 class="text-white pb-2 fw-bold">Welcome To My Internship</h1>
                    <h4 class="text-white op-7 mb-2">Managing Industrial Learning through Structured Internship</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">


            <!-- LEFT SIDE -->
            <div class="col-lg-8 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Internship Quick Access</div>
                    </div>

                    <div class="card-body p-0">
                        <div class="row no-gutters text-center">

                            <div class="col-6 col-md-3 dashboard-menu">
                                <a href="{{ base_url('index.php?page=intern_logbook') }}">
                                    <i class="fas fa-book"></i>
                                    <span>Logbook</span>
                                </a>
                            </div>

                            <div class="col-6 col-md-3 dashboard-menu">
                                <a href="{{ base_url('index.php?page=intern_attendance') }}">
                                    <i class="fas fa-calendar"></i>
                                    <span>Attendance</span>
                                </a>
                            </div>

                            <div class="col-6 col-md-3 dashboard-menu">
                                <a href="{{ base_url('index.php?page=intern_discussion') }}">
                                    <i class="fas fa-comments"></i>
                                    <span>Discussion</span>
                                </a>
                            </div>

                            <div class="col-6 col-md-3 dashboard-menu">
                                <a href="{{ base_url('index.php?page=intern_administration') }}">
                                    <i class="fas fa-file"></i>
                                    <span>Administration</span>
                                </a>
                            </div>

                        </div>
                        <div class="row mb-2 px-4 py-2">

                            @php

                            $program_study_data = [];

                            $sql = "
                            SELECT
                            internship.id_internship,
                            si.nim,
                            si.name,
                            ps.kode_prodi,
                            ps.jenjang,
                            ps.study_program
                            FROM
                            student_internship si

                            INNER JOIN
                            internship ON si.nim = internship.nim

                            INNER JOIN
                            program_study ps ON si.program_study = ps.kode_prodi
                            WHERE
                            internship.id_company = :id_company
                            AND
                            internship.status = :internship_status
                            ORDER BY
                            ps.kode_prodi,
                            si.name;
                            ";

                            $stmt = $_SERVER['PDO']->prepare($sql);
                            $stmt->execute(['id_company' => $_SESSION['id_company'], 'internship_status' => 'Yes']);
                            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Structure data by program_study
                            $program_study_data = [];
                            foreach ($students as $student) {
                            $program_study = $student['jenjang'] . ' ' . $student['study_program'];
                            if (!isset($program_study_data[$program_study])) {
                            $program_study_data[$program_study] = [];
                            }
                            $program_study_data[$program_study][] = [
                            'nim' => $student['nim'],
                            'id_internship' => $student['id_internship'],
                            'name' => $student['name'],
                            ];
                            }

                            @endphp

                            <hr>
                            <div class="col-12 col-md-12">
                                <label for="internship-select">Data per Student</label>

                                <select name="student" id="student-select" class="form-control"
                                    onchange="navigateToDetail()">

                                    <option value="">- Select Student -</option>

                                    <?php foreach ($program_study_data as $program => $students): ?>
                                    <optgroup label="<?= htmlspecialchars($program) ?>">
                                        <?php foreach ($students as $student): ?>
                                        <option value="<?= htmlspecialchars($student['id_internship']) ?>">
                                            <?= htmlspecialchars($student['nim']) . ' | ' . htmlspecialchars($student['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                    <?php endforeach; ?>
                                </select>


                                <!-- Indikator loading -->
                                <div id="loading-indicator" style="display: none;">
                                    <hr />
                                    <h6><i class="fas fa-spin fa-spinner fa-2x"></i> Redirecting . . .</h6>
                                    <!-- Anda bisa mengganti ini dengan spinner CSS atau gambar -->
                                </div>

                            </div>
                        </div>

                    </div>
                </div>


                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-book"></i> User Guide & Resources
                        </div>
                    </div>

                    <div class="card-body p-2">

                        @php
                        $guides = [
                        [
                        'title' => 'Manual Guide (PDF)',
                        'type' => 'pdf',
                        'url' => 'viewer.php?src=manual_guide'
                        ],
                        ];
                        @endphp

                        @foreach ($guides as $guide)
                        <div class="guide-item d-flex align-items-center p-2 mb-2">

                            <div class="guide-icon bg-{{ $guide['type'] === 'pdf' ? 'danger' : 'info' }}">
                                <i class="fas {{ $guide['type'] === 'pdf' ? 'fa-file-pdf' : 'fa-play' }}"></i>
                            </div>

                            <div class="guide-content ml-2">
                                <div class="font-weight-bold">
                                    {{ $guide['title'] }}
                                </div>
                                <small class="text-muted">
                                    {{ strtoupper($guide['type']) }} Resource
                                </small>
                            </div>

                            <div class="ml-auto">
                                <a href="{{ $guide['url'] }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>

                        </div>
                        @endforeach

                    </div>
                </div>


                <div class="row">

                    @php
                    $stats = [
                    [
                    'title' => 'Active Interns',
                    'value' => $stat_active_intern,
                    'icon' => 'fa-user-graduate',
                    'color' => 'primary'
                    ],
                    [
                    'title' => 'User Company',
                    'value' => $total_user_company,
                    'icon' => 'fa-calendar-check',
                    'color' => 'info'
                    ],
                    [
                    'title' => 'Pending Attendance',
                    'value' => $stat_pending_attendance,
                    'icon' => 'fa-calendar-times',
                    'color' => 'warning'
                    ],
                    [
                    'title' => 'Pending Logbook',
                    'value' => $stat_pending_logbook,
                    'icon' => 'fa-book',
                    'color' => 'danger'
                    ],
                    ];
                    @endphp


                    @foreach ($stats as $stat)
                    <div class="col-md-3 col-sm-6">
                        <div class="card stat-card mb-3">
                            <div class="card-body d-flex align-items-center">
                                <div class="stat-icon bg-{{ $stat['color'] }}">
                                    <i class="fas {{ $stat['icon'] }}"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="stat-value">
                                        {{ $stat['value'] }}
                                    </div>
                                    <div class="stat-title text-muted">
                                        {{ $stat['title'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>






                {{-- @include('dashboard.partials.statistics') --}}
            </div>

            <!-- RIGHT SIDE -->
            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-bell"></i> Today Alert
                        </div>
                    </div>

                    <div class="card-body p-2">

                        @if(count($alerts) > 0)

                        @foreach ($alerts as $alert)
                        <a href="{{ $alert['url'] }}" class="text-decoration-none">
                            <div class="alert-item d-flex p-2 mb-2">
                                <div class="alert-icon bg-{{ $alert['type'] }}">
                                    <i class="fas fa-exclamation"></i>
                                </div>

                                <div class="alert-content ml-2 text-dark">
                                    <div class="font-weight-bold">
                                        {{ $alert['title'] }}
                                    </div>
                                    <small class="text-muted">
                                        Action required
                                    </small>
                                </div>
                            </div>
                        </a>
                        @endforeach

                        @else

                        {{-- EMPTY STATE --}}
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <div class="font-weight-bold">
                                All Clear
                            </div>
                            <small>No pending attendance or logbook validation</small>
                        </div>

                        @endif

                    </div>

                </div>



                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-info-circle"></i> Latest Announcement
                        </div>
                    </div>

                    <div class="card-body p-3">

                        @forelse ($announcements as $info)
                            <div class="info-item p-2 mb-2">

                                <div class="d-flex justify-content-between">
                                    <span class="badge badge-info">
                                        {{ $info['category'] }}
                                    </span>

                                    <small class="text-muted">
                                        {{ date('d M Y', strtotime($info['created_at'])) }}
                                    </small>
                                </div>

                                <div class="mt-1 font-weight-bold">
                                    {{ $info['title'] }}
                                </div>

                                <div class="mt-1 d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        @if ($info['have_attachment'])
                                            <i class="fas fa-paperclip"></i> Attachment
                                        @endif
                                    </small>

                                    <a href="announcement_detail.php?id={{ base64_encode($info['id_announcement']) }}"
                                    class="btn btn-sm btn-link p-0">
                                        View
                                    </a>
                                </div>

                            </div>

                        @empty
                            {{-- EMPTY STATE --}}
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-bullhorn fa-2x mb-2"></i>
                                <div class="font-weight-bold">
                                    No Announcement
                                </div>
                                <small>
                                    There is no published announcement yet
                                </small>
                            </div>
                        @endforelse

                    </div>

                    @if (count($announcements) > 0)
                        <div class="card-footer text-center p-2">
                            <a href="{{ router('announcement.index') }}"
                            class="btn btn-sm btn-outline-primary">
                                View All Information
                            </a>
                        </div>
                    @endif
                </div>



            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-scripts')
<!-- Chart Circle -->
<script src="./assets/js/plugin/chart-circle/circles.min.js"></script>


<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {

        $('#student-select').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: '- Select Student -',
            allowClear: true
        });

    });


    function navigateToDetail() {
        var select = document.getElementById('student-select');
        var nim = select.value;
        var loadingIndicator = document.getElementById('loading-indicator');

        if (nim) {
            // Disable select
            select.disabled = true;

            // Show loading indicator
            loadingIndicator.style.display = 'block';

            // Encode nim in base64
            var encodedNim = btoa(nim);
            var url = 'index.php?page=detail_intern_management&id_internship=' + encodedNim;

            // Redirect to the URL
            window.location.href = url;
        }
    }
</script>

<script>
    Circles.create({
        id: 'circles-0',
        radius: 45,
        value: '<?= $count_university ?>',
        maxValue: '<?= $count_university ?>',
        width: 7,
        text: '<?= $count_university ?>',
        colors: ['#f1f1f1', '#FE5D26'],
        duration: 400,
        wrpClass: 'circles-wrp',
        textClass: 'circles-text',
        styleWrapper: true,
        styleText: true
    })

    Circles.create({
        id: 'circles-1',
        radius: 45,
        value: '<?= $count_company ?>',
        maxValue: '<?= $count_company ?>',
        width: 7,
        text: '<?= $count_company ?>',
        colors: ['#f1f1f1', '#F2C078'],
        duration: 400,
        wrpClass: 'circles-wrp',
        textClass: 'circles-text',
        styleWrapper: true,
        styleText: true
    })

    Circles.create({
        id: 'circles-2',
        radius: 45,
        value: '<?= $count_spv ?? 0 ?>',
        maxValue: '<?= $count_spv ?? 0 ?>',
        width: 7,
        text: '<?= $count_spv ?? 0 ?>',
        colors: ['#f1f1f1', '#FAEDCA'],
        duration: 400,
        wrpClass: 'circles-wrp',
        textClass: 'circles-text',
        styleWrapper: true,
        styleText: true
    })

    Circles.create({
        id: 'circles-3',
        radius: 45,
        value: '<?= $count_student ?? 0 ?>',
        maxValue: '<?= $count_student ?? 0 ?>',
        width: 7,
        text: '<?= $count_student ?? 0 ?>',
        colors: ['#f1f1f1', '#c1dbb3'],
        duration: 400,
        wrpClass: 'circles-wrp',
        textClass: 'circles-text',
        styleWrapper: true,
        styleText: true
    })


    Circles.create({
        id: 'circles-4',
        radius: 45,
        value: '<?= $count_offer ?>',
        maxValue: '<?= $count_offer ?>',
        width: 7,
        text: '<?= $count_offer ?>',
        colors: ['#f1f1f1', '#6861CE'],
        duration: 400,
        wrpClass: 'circles-wrp',
        textClass: 'circles-text',
        styleWrapper: true,
        styleText: true
    })

    Circles.create({
        id: 'circles-5',
        radius: 45,
        value: '<?= $count_lecturer ?>',
        maxValue: '<?= $count_lecturer ?>',
        width: 7,
        text: '<?= $count_lecturer ?>',
        colors: ['#f1f1f1', '#7ebc89'],
        duration: 400,
        wrpClass: 'circles-wrp',
        textClass: 'circles-text',
        styleWrapper: true,
        styleText: true
    })
</script>

@endpush