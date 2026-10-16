


<?php $__env->startPush('head-scripts'); ?>
<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
    #check-all.indeterminate~.custom-control-label::before {
        content: "-";
    }

</style>
<?php $__env->stopPush(); ?>


<?php $__env->startSection('main-content'); ?>
<?php

$id_kampus = $_SESSION['Lecturer_Data']['id_kampus'];

// Mengganti penggunaan $_SERVER['KONEKSI_DB']->query() dengan PDO
$sql_kampus = 'SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus';
$exec_kampus = $_SERVER['PDO']->prepare($sql_kampus);
$exec_kampus->bindParam(':id_kampus', $id_kampus);
$exec_kampus->execute();
$data_kampus = $exec_kampus->fetch(PDO::FETCH_ASSOC);

// Menghitung jumlah baris menggunakan PDO
$count_university = $_SERVER['PDO']
->query('SELECT COUNT(id_kampus) AS Count_Kampus FROM tb_kampus')
->fetch(PDO::FETCH_ASSOC)['Count_Kampus'];

$count_cdc = $_SERVER['PDO']
->query('SELECT COUNT(id_upkpk) AS Count_upkpk FROM upkpk')
->fetch(PDO::FETCH_ASSOC)['Count_upkpk'];

$count_student = $_SERVER['PDO']->prepare(
'SELECT COUNT(*) AS Count_Student FROM student_internship WHERE id_kampus = :id_kampus',
);
$count_student->bindParam(':id_kampus', $id_kampus);

$count_student->execute();
$count_student = $count_student->fetch(PDO::FETCH_ASSOC)['Count_Student'];

$count_lecturer = $_SERVER['PDO']->prepare(
'SELECT COUNT(*) AS Count_Lecturer FROM lecturer WHERE id_kampus = :id_kampus',
);
$count_lecturer->bindParam(':id_kampus', $id_kampus);
$count_lecturer->execute();
$count_lecturer = $count_lecturer->fetch(PDO::FETCH_ASSOC)['Count_Lecturer'];

$count_prodi = $_SERVER['PDO']->prepare(
'SELECT COUNT(*) AS Count_Prodi FROM program_study WHERE id_kampus = :id_kampus',
);
$count_prodi->bindParam(':id_kampus', $id_kampus);
$count_prodi->execute();
$count_prodi = $count_prodi->fetch(PDO::FETCH_ASSOC)['Count_Prodi'];

$count_total_company = $_SERVER['PDO']
->query('SELECT COUNT(*) AS Count_Total_Company FROM company')
->fetch(PDO::FETCH_ASSOC)['Count_Total_Company'];

$count_company = $_SERVER['PDO']
->query("SELECT COUNT(*) AS Count_Company FROM company WHERE status NOT IN ('Not Verified', 'Blacklist')")
->fetch(PDO::FETCH_ASSOC)['Count_Company'];

$count_blacklist_company = $_SERVER['PDO']
->query("SELECT COUNT(*) AS Count_Blacklist_Company FROM company WHERE status IN ('Blacklist')")
->fetch(PDO::FETCH_ASSOC)['Count_Blacklist_Company'];

$count_new_company = $_SERVER['PDO']
->query("SELECT COUNT(*) AS Count_New_Company FROM company WHERE status IN ('Not Verified')")
->fetch(PDO::FETCH_ASSOC)['Count_New_Company'];

?>



<?php
$pdo = $_SERVER['PDO'];
$nik = $_SESSION['nim_nik_unit'];
$sql = 'SELECT DISTINCT(nim) AS nim FROM student_internship WHERE nik_dospem = :nik';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':nik', $nik, PDO::PARAM_STR);
$stmt->execute();

$sql_student = $stmt->fetchAll(PDO::FETCH_ASSOC);

$program_study_data = [];

$sql = "
SELECT
si.nim,
si.name,
ps.kode_prodi,
ps.jenjang,
ps.study_program
FROM
student_internship si
INNER JOIN
program_study ps ON si.program_study = ps.kode_prodi
WHERE
si.nik_dospem = :nik_dospem
ORDER BY
ps.kode_prodi,
si.name
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['nik_dospem' => $_SESSION['nim_nik_unit']]);
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
'name' => $student['name'],
];
}

?>


<?php
// Query pertama untuk menghitung jumlah attendance
$notif_attendance = "SELECT COUNT(attendance.id_attendance) AS count_attendance
FROM attendance
INNER JOIN internship ON attendance.id_internship = internship.id_internship
INNER JOIN student_internship ON internship.nim = student_internship.nim
LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
WHERE student_internship.nik_dospem = :nik_dospem
AND attendance.approval_dpm = :approval_dpm";

// Query kedua untuk menghitung jumlah logbook
$notif_logbook = "SELECT COUNT(logbook.id_logbook) AS count_logbook
FROM logbook
INNER JOIN internship ON logbook.id_internship = internship.id_internship
INNER JOIN student_internship ON internship.nim = student_internship.nim
LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
WHERE student_internship.nik_dospem = :nik_dospem
AND logbook.approval_dpm = :approval_dpm";


$notif_discussion = "SELECT COUNT(discussion.id_discussion) AS count_discussion
FROM discussion
INNER JOIN internship ON discussion.id_internship = internship.id_internship
INNER JOIN student_internship ON internship.nim = student_internship.nim
LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
WHERE student_internship.nik_dospem = :nik_dospem
AND internship.status = :internship_status AND discussion.discussion_status = :discussion_status";

// Persiapan untuk eksekusi query pertama
$stmt_attendance = $_SERVER['PDO']->prepare($notif_attendance);
$stmt_attendance->execute([':nik_dospem' => $_SESSION['nim_nik_unit'], ':approval_dpm' => 'Waiting']);
$result_attendance = $stmt_attendance->fetch( PDO::FETCH_ASSOC );
$notif['count_attendance'] = $result_attendance['count_attendance'];

// Persiapan untuk eksekusi query kedua
$stmt_logbook = $_SERVER['PDO']->prepare($notif_logbook);
$stmt_logbook->execute([':nik_dospem' => $_SESSION['nim_nik_unit'], ':approval_dpm' => 'Waiting']);
$result_logbook = $stmt_logbook->fetch( PDO::FETCH_ASSOC );
$notif['count_logbook'] = $result_logbook['count_logbook'];

// Persiapan untuk eksekusi query ketiga
$stmt_discussion = $_SERVER['PDO']->prepare($notif_discussion);
$stmt_discussion->execute([':nik_dospem' => $_SESSION['nim_nik_unit'], ':discussion_status' => 'Open', 'internship_status' => 'Yes']);
$result_discussion = $stmt_discussion->fetch( PDO::FETCH_ASSOC );
$notif['count_discussion'] = $result_discussion['count_discussion'];


# Get University Data
$sql_university = "SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1";
$stmt_university = $_SERVER['PDO']->prepare($sql_university);
$stmt_university->execute( ['id_kampus' => $_SESSION['Lecturer-Data']['id_kampus']] );
$lecturer_university = $stmt_university->fetch( PDO::FETCH_ASSOC );


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


            <div class="col-sm-12">
                <div class="card">

                    <div class="card-header">

                        <div class="card-title">Student Internship Quick Access</div>
                    </div>
                    <div class="card-body">




                        <div class="d-flex flex-column flex-lg-row justify-content-center">

                            <a href="<?php echo e(base_url('index.php?page=logbook_student')); ?>" id="discussion-link" class="btn btn-primary mb-1 mb-lg-0 mr-lg-1 d-flex align-items-center justify-content-center col-sm-6 col-lg-3" style="min-height: 50px; max-height: 50px;">
                                <i class="fas fa-book notification-icon mr-2">
                                    <?php if( $notif['count_logbook'] > 0 ): ?>
                                    <span class="custom-notification-badge bg-success"><?php echo e($notif['count_logbook']); ?></span>
                                    <span class="sr-only"><?php echo e($notif['count_logbook']); ?> Logbook Waiting for Validation</span>
                                    <?php endif; ?>

                                </i> Student Logbook
                            </a>
                            <a href="<?php echo e(base_url('index.php?page=attendance_student')); ?>" id="logbook-link" class="btn btn-primary mb-1 mb-lg-0 mr-lg-1 d-flex align-items-center justify-content-center col-sm-6 col-lg-3" style="min-height: 50px; max-height: 50px;">
                                
                                <i class="fas fa-calendar notification-icon mr-2">
                                    <?php if( $notif['count_attendance'] > 0 ): ?>
                                    <span class="custom-notification-badge bg-success"><?php echo e($notif['count_attendance']); ?></span>
                                    <span class="sr-only"><?php echo e($notif['count_attendance']); ?> Attendance Waiting for Validation</span>
                                    <?php endif; ?>
                                </i>Student Attendance
                            </a>

                            <a href="<?php echo e(base_url('index.php?page=discussion_student')); ?>" id="attendance-link" class="btn btn-primary mb-1 mb-lg-0 mr-lg-1 d-flex align-items-center justify-content-center col-sm-6 col-lg-3" style="min-height: 50px; max-height: 50px;">
                                <i class="fas fa-comments notification-icon mr-2">
                                    <?php if( $notif['count_discussion'] > 0 ): ?>
                                    <span class="custom-notification-badge bg-success"><?php echo e($notif['count_discussion']); ?></span>
                                    <span class="sr-only"><?php echo e($notif['count_discussion']); ?> Discussion to Join</span>
                                    <?php endif; ?>
                                </i>Student Discussion
                            </a>

                            <a href="<?php echo e(base_url('index.php?page=administration_student')); ?>" id="attendance-link" class="btn btn-primary mb-1 d-flex align-items-center justify-content-center col-sm-6 col-lg-3" style="min-height: 50px; max-height: 50px;">
                                <i class="fas fa-file mr-1"></i> Student Administration
                            </a>
                        </div>

                        <hr>
                        <div class="form-group">
                            <label for="internship-select">Data per Student</label>

                            <select name="student" id="student-select" class="form-control" onchange="navigateToDetail()">

                                <option value="">- Select Student -</option>

                                <?php foreach ($program_study_data as $program => $students): ?>
                                <optgroup label="<?= htmlspecialchars($program) ?>">
                                    <?php foreach ($students as $student): ?>
                                    <option value="<?= htmlspecialchars($student['nim']) ?>">
                                        <?= htmlspecialchars($student['nim']) . ' | ' . htmlspecialchars($student['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <?php endforeach; ?>
                            </select>


                            <!-- Indikator loading -->
                            <div id="loading-indicator" style="display: none;">
                                <hr />
                                <h6><i class="fas fa-spin fa-spinner fa-2x"></i> Redirecting . . .</h6> <!-- Anda bisa mengganti ini dengan spinner CSS atau gambar -->
                            </div>

                        </div>




                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card full-height">
                    <div class="card-body">
                        <div class="card-title">My Internship Statistics</div>
                        <div class="card-category">Daily information about statistics in system</div>
                        <div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
                            <div class="px-2 pb-2 pb-md-0 text-center">
                                <div id="circles-0"></div>
                                <h6 class="fw-bold mt-3 mb-0">University</h6>
                            </div>
                            <div class="px-2 pb-2 pb-md-0 text-center">
                                <div id="circles-1"></div>
                                <h6 class="fw-bold mt-3 mb-0">Company</h6>
                            </div>

                            <div class="px-2 pb-2 pb-md-0 text-center">
                                <div id="circles-5"></div>
                                <h6 class="fw-bold mt-3 mb-0">CDC User</h6>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <!-- Politeknik Statistik -->
            <div class="col-md-6">
                <div class="card full-height">
                    <div class="card-body">
                        <div class="card-title"><?= $data_kampus['nama_kampus'] ?> statistics</div>
                        <div class="card-category">Daily information about your university statistics in system</div>
                        <div class="d-flex flex-wrap justify-content-around pb-2 pt-4">

                            <div class="px-2 pb-2 pb-md-0 text-center">
                                <div id="circles-2"></div>
                                <h6 class="fw-bold mt-3 mb-0">Lecturer</h6>
                            </div>
                            <div class="px-2 pb-2 pb-md-0 text-center">
                                <div id="circles-3"></div>
                                <h6 class="fw-bold mt-3 mb-0">Student</h6>
                            </div>

                            <div class="px-2 pb-2 pb-md-0 text-center">
                                <div id="circles-4"></div>
                                <h6 class="fw-bold mt-3 mb-0">Study Program</h6>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <div class="col-md-12">
                <div class="card full-height">
                    <div class="card-body">
                        <div class="card-title">Manual Guide Application</div>
                        <div class="card-category">Download Manual Guide</div>
                        <div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
                            <div class="px-2 pb-2 pb-md-0 text-center">
                                <h4 class="fw-bold mt-3 mb-0 text-primary">
                                    <a style="text-decoration: none;" title="Download Manual Guide for UPKPK" aria-label="Download Manual Guide for UPKPK" href="viewer.php?src=manual_guide" download="" target="_blank"> <i class="fas fa-download"></i> Download Manual
                                        Guide </a>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- <div class="col-md-6">
                    <div class="card full-height">
                        <div class="card-body">
                            <div class="card-title">Manual Guide Application</div>
                            <div class="card-category">Download Manual Guide</div>
                            <div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
                                <div class="px-2 pb-2 pb-md-0 text-center">
                                    <h4 class="fw-bold mt-3 mb-0 text-primary">
                                        <a style="text-decoration: none;" title="Download Manual Guide for UPKPK" aria-label="Download Manual Guide for UPKPK" href="./system_requirement/manual_guide_upkpk.pdf" download="" target="_blank"> <i class="fas fa-download"></i> Download Manual Guide </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card full-height">
                        <div class="card-body">
                            <div class="card-title">Tutorial Video Application</div>
                            <div class="card-category">Watch Tutorial Video</div>
                            <div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
                                <div class="px-2 pb-2 pb-md-0 text-center">
                                    <h4 class="fw-bold mt-3 mb-0 text-primary">
                                        <a style="text-decoration: none;" title="Watch Video Tutorial in Youtube" aria-label="Watch Video Tutorial in Youtube" href="https://youtube.com/" download="" target="_blank"> <i class="fab fa-youtube"></i> Watch Video Tutorial</a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->


        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<!-- Chart Circle -->
<script src="./assets/js/plugin/chart-circle/circles.min.js"></script>


<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {

        $('#student-select').select2({
            theme: 'bootstrap4'
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
            var url = 'index.php?page=intern_student_detail&nim=' + encodedNim;

            // Redirect to the URL
            window.location.href = url;
        }
    }

</script>



<script>
    Circles.create({
        id: 'circles-0'
        , radius: 45
        , value: '<?= $count_university ?>'
        , maxValue: '<?= $count_university ?>'
        , width: 7
        , text: '<?= $count_university ?>'
        , colors: ['#f1f1f1', '#FE5D26']
        , duration: 400
        , wrpClass: 'circles-wrp'
        , textClass: 'circles-text'
        , styleWrapper: true
        , styleText: true
    })

    Circles.create({
        id: 'circles-1'
        , radius: 45
        , value: '<?= $count_company ?>'
        , maxValue: '<?= $count_company ?>'
        , width: 7
        , text: '<?= $count_company ?>'
        , colors: ['#f1f1f1', '#F2C078']
        , duration: 400
        , wrpClass: 'circles-wrp'
        , textClass: 'circles-text'
        , styleWrapper: true
        , styleText: true
    })

    Circles.create({
        id: 'circles-2'
        , radius: 45
        , value: '<?= $count_lecturer ?? 0 ?>'
        , maxValue: '<?= $count_lecturer ?? 0 ?>'
        , width: 7
        , text: '<?= $count_lecturer ?? 0 ?>'
        , colors: ['#f1f1f1', '#FAEDCA']
        , duration: 400
        , wrpClass: 'circles-wrp'
        , textClass: 'circles-text'
        , styleWrapper: true
        , styleText: true
    })

    Circles.create({
        id: 'circles-3'
        , radius: 45
        , value: '<?= $count_student ?? 0 ?>'
        , maxValue: '<?= $count_student ?? 0 ?>'
        , width: 7
        , text: '<?= $count_student ?? 0 ?>'
        , colors: ['#f1f1f1', '#c1dbb3']
        , duration: 400
        , wrpClass: 'circles-wrp'
        , textClass: 'circles-text'
        , styleWrapper: true
        , styleText: true
    })


    Circles.create({
        id: 'circles-4'
        , radius: 45
        , value: '<?= $count_prodi ?>'
        , maxValue: '<?= $count_prodi ?>'
        , width: 7
        , text: '<?= $count_prodi ?>'
        , colors: ['#f1f1f1', '#6861CE']
        , duration: 400
        , wrpClass: 'circles-wrp'
        , textClass: 'circles-text'
        , styleWrapper: true
        , styleText: true
    })

    Circles.create({
        id: 'circles-5'
        , radius: 45
        , value: '<?= $count_cdc ?>'
        , maxValue: '<?= $count_cdc ?>'
        , width: 7
        , text: '<?= $count_cdc ?>'
        , colors: ['#f1f1f1', '#7ebc89']
        , duration: 400
        , wrpClass: 'circles-wrp'
        , textClass: 'circles-text'
        , styleWrapper: true
        , styleText: true
    })

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.lecturer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp_new\htdocs\pblKita\views/lecturer/dashboard.blade.php ENDPATH**/ ?>