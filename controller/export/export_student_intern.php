<?php defined('BASE_PATH' or header('Location: /'));
error_reporting(false);
session_start();

use alhimik1986\PhpExcelTemplator\PhpExcelTemplator;

require_once('./vendor/autoload.php'); // if you don't use framework

$no = [];
$nim = [];
$student_name = [];
$study_program = [];
$nik_dospem = [];
$nama_dospem = [];
$angkatan = [];
$jurusan = [];
$jenjang = [];

$internship_status2 = [];
$internship_date2 = [];
$internship_period2 = [];
$internship_company2 = [];
$internship_supervisor2 = [];


$no = [];
$nim = [];
$student_name = [];
$study_program = [];
$nik_dospem = [];
$nama_dospem = [];
$angkatan = [];
$jurusan = [];
$jenjang = [];

$internship_status2 = [];
$internship_date2 = [];
$internship_period2 = [];
$internship_company2 = [];
$internship_supervisor2 = [];

$internship_industry_feedback = [];
$internship_student_feedback = [];

try {
    $sql2 = $_SESSION['EXPORT']['STUDENT_INTERN']['QUERY'];
    $stmt = $_SERVER['PDO']->prepare($sql2);
    $bindings = $_SESSION['EXPORT']['STUDENT_INTERN']['BINDING'];

    $title_en = "Major " . (!empty($bindings[':major']) ? $bindings[':major'] : '-') . " -- Study Program " . (!empty($bindings[':program_study']) ? $bindings[':program_study'] : 'All Study Program');
    $year_en = ($bindings[':generation'] != '') ? ('20' . $bindings[':generation']) : 'All Generation';

    $prefered_name = "Internship Data Recapitulation of Students from $title_en Class of $year_en";

    // Bind semua parameter
    foreach ($bindings as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
} catch (PDOException $e) {
    make_swal2('error', 'Error', "Encountered an error when exporting data");
    close(2000);
    die;
}

$get_student = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Looping data hasil query
foreach ($get_student as $index => $data) :

    $sql2 = "SELECT internship.*, company.name AS company_name, user_company.user_fullname AS industry_supervisor, user_company.user_email AS email_supervisor 
            FROM internship 
	        LEFT JOIN user_company ON user_company.id_user_company = internship.id_user_company 
	        LEFT JOIN company ON company.id_company = internship.id_company 
	        WHERE internship.nim = :nim AND internship.status IN ('Yes', 'Finish') 
	        ORDER BY internship.id_internship";

    $stmt2 = $_SERVER['PDO']->prepare($sql2);
    $stmt2->bindParam(':nim', $data['nim'], PDO::PARAM_INT);
    $stmt2->execute();

    $internship_status = 'Not Yet Submitted';
    $internship_date = '-';
    $internship_period = '-';
    $internship_company = '-';
    $internship_supervisor = '-';

    $student_feedback = '-';
    $industry_feedback = '-';

    $generation = '20' . substr($data['nim'], 3, 2);

    if ($stmt2->rowCount() >= 1) {
        $get_internship = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        foreach ($get_internship as $internship) {
            $status = strtolower($internship['status']);

            if (in_array($status, ['waiting'])) {
                $internship_status = 'Currently Submitting';
            } elseif (is_null($internship['status'])) {
                $internship_status = 'None';
            } elseif (in_array($status, ['yes', 'finish', 1, 3])) {
                $internship_status = 'Internship Accepted';
                $internship_period = range_time_elapsed_string($internship['start_date'], $internship['end_date']);
                $internship_date = date('d-M-Y', strtotime($internship['start_date'])) . " until " . date('d-M-Y', strtotime($internship['end_date'])) . " ($internship_period)";
                $internship_company = $internship['company_name'];
                $internship_supervisor = $internship['industry_supervisor'] ?? 'None';

                // $internship_supervisor .= $internship['email_supervisor'] ?? 'None';
            } elseif (in_array($status, ['no', 0])) {
                $internship_status = 'Rejected by Company';
            }

            // Query pertama untuk menghitung student_feedback
            $sql_sf = "SELECT * FROM student_feedback WHERE id_internship = :id_internship LIMIT 1";
            $stmt_sf = $pdo->prepare($sql_sf);

            // Query kedua untuk menghitung industry_feedback
            $sql_if = "SELECT * FROM industry_feedback WHERE id_internship = :id_internship LIMIT 1";
            $stmt_if = $pdo->prepare($sql_if);

            // Bind parameter untuk kedua query
            $stmt_sf->bindParam(':id_internship', $internship['id_internship'], PDO::PARAM_INT);
            $stmt_if->bindParam(':id_internship', $internship['id_internship'], PDO::PARAM_INT);

            // Eksekusi kedua statement
            $stmt_sf->execute();
            $stmt_if->execute();

            // Ambil hasil eksekusi
            $student_feedback = $stmt_sf->fetch(PDO::FETCH_ASSOC);
            $industry_feedback = $stmt_if->fetch(PDO::FETCH_ASSOC);

            // Cek feedback dari student
            if ($student_feedback) {
                $student_feedback_link = base_url('index.php?page=print_student_feedback&id_student_feedback=' . base64_encode($student_feedback['id_student_feedback']));
                $student_feedback = '=HYPERLINK("' . $student_feedback_link . '", "View Student Feedback")';
            } else {
                $student_feedback = 'No Feedback Available';
            }

            // Cek feedback dari industry
            if ($industry_feedback) {
                $industry_feedback_link = base_url('index.php?page=print_industry_feedback&id=' . encrypt($internship['id_internship'])) . '&exportLanguage=ID';
                $industry_feedback = '=HYPERLINK("' . $industry_feedback_link . '", "View Industry Feedback")';
            } else {
                $industry_feedback = 'No Feedback Available';
            }



            // Student Data
            array_push($no, $index + 1);
            array_push($nim, $data['nim']);
            array_push($student_name, $data['name']);
            array_push($angkatan, $generation);
            array_push($jurusan, $data['major']);
            array_push($jenjang, $data['jenjang']);
            array_push($study_program, $data['study_program']);
            array_push($nik_dospem, $data['nik_dospem'] ?? '-');
            array_push($nama_dospem, $data['nama_dosen'] ?? '-');


            // Internship Data
            array_push($internship_status2, $internship_status);
            array_push($internship_date2, $internship_date);
            array_push($internship_period2, $internship_period);
            array_push($internship_company2, $internship_company);
            array_push($internship_supervisor2, $internship_supervisor);

            // Internship Feedback
            array_push($internship_student_feedback, $student_feedback);
            array_push($internship_industry_feedback, $industry_feedback);
        }
    } else {
        // Student Data
        array_push($no, $index + 1);
        array_push($nim, $data['nim']);
        array_push($student_name, $data['name']);
        array_push($angkatan, $generation);
        array_push($jurusan, $data['major']);
        array_push($jenjang, $data['jenjang']);
        array_push($study_program, $data['study_program']);
        array_push($nik_dospem, $data['nik_dospem'] ?? '-');
        array_push($nama_dospem, $data['nama_dosen'] ?? '-');


        array_push($internship_status2, $internship_status);
        array_push($internship_date2, $internship_date);
        array_push($internship_period2, $internship_period);
        array_push($internship_company2, $internship_company);
        array_push($internship_supervisor2, $internship_supervisor);

        // Internship Feedback
        array_push($internship_student_feedback, $student_feedback);
        array_push($internship_industry_feedback, $industry_feedback);
    }



endforeach;

$location = 'excel/generate/';
// $excel_name = 'internship_student.xlsx';
$excel_name = 'internship_student' . uniqid() . '.xlsx';
$file_name = $excel_name;



$a = PhpExcelTemplator::saveToFile('excel/export_student.xlsx', $location . $excel_name, [
    '{generated_on}' => date('Y-m-d H:i:s'),
    '{server_name}' => $_SERVER['APP_NAME'] . ' (' . base_url() . ')',
    '{title_en}' =>
    "Major : " . (!empty($bindings[':major']) ? $bindings[':major'] : '-') .
        " Study Program : " . (!empty($bindings[':program_study']) ? $bindings[':program_study'] : 'All Study Program'),
    '{year_en}' => ($bindings[':generation'] != '') ? ('20' . $bindings[':generation']) : 'All Generation',

    '{title_id}' =>
    "Jurusan : " . (!empty($bindings[':major']) ? $bindings[':major'] : '-') .
        " Program Studi : " . (!empty($bindings[':program_study']) ? $bindings[':program_study'] : 'All Study Program'),
    '{year_id}' => ($bindings[':generation'] != '') ? ('20' . $bindings[':generation']) : 'Semua Angkatan',


    '{no}' => $no,
    '{nim}' => $nim,
    '{student_name}' => $student_name,
    '{generation}' => $angkatan,
    '{major}' => $jurusan,
    '{jenjang}' => $jenjang,

    '{study_program}' => $study_program,
    '{nik_dospem}' => $nik_dospem,
    '{lecturer_name}' => $nama_dospem,


    '{internship_status}' => $internship_status2,
    '{internship_date}' => $internship_date2,
    '{internship_period}' => $internship_period2,

    '{internship_company}' => $internship_company2,
    '{internship_supervisor}' => $internship_supervisor2,


    '{student_feedback}' => $internship_student_feedback,
    '{industry_feedback}' => $internship_industry_feedback,
]);




ignore_user_abort();

set_time_limit(0);
echo "<script>
    window.setTimeout(function() {
        window.location = 'index.php?viewer=excel_generate&location=" . base64_encode($excel_name) . "&prefname=" . base64_encode($prefered_name) . "';
    }, 100);
</script>";
exit;
