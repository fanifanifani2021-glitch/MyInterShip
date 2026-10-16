<?php
use PhpOffice\PhpWord\TemplateProcessor;

// --- 0. HELPER FUNCTION (Definisikan di atas agar aman) ---
function prepareCheckmarks($row) {
    $map = [];
    $fields = ['posisi' => 'posisi_', 'ilmu_dari_poltek' => 'ilmu_', 'ilmu_baru' => 'baru_', 'data_magang' => 'data_'];
    foreach ($fields as $dbKey => $prefix) {
        for ($i = 1; $i <= 4; $i++) {
            $map[$prefix . $i] = ($row[$dbKey] == $i) ? '✓' : '';
        }
    }
    return $map;
}

// --- 1. KEAMANAN DASAR ---
try {
    $id_internship = Encrypter::decrypt($id);
    $nim = $_SESSION['nim'];

    $internship = DB::table('internship')
        ->where('id_internship', $id_internship)->where('nim', $nim)->first();

    if (!$internship) {
        Notification::swal2('Denied', 'Internship data not found!', 'error');
        Response::redirect(router('student.internship'))->send(); exit;
    }
} catch (Exception $e) {
    Notification::swal2('Security Warning', 'Invalid Identifier.', 'warning');
    Response::redirect(router('student.internship'))->send(); exit;
}


switch ($method) :

    case 'download' :
        $feedback = DB::table('student_feedback')->where('id_internship', $id_internship)->first();

        if (!$feedback) {
            Notification::swal2('Not Found', 'Feedback data not found.', 'error');
            // PERBAIKAN: Gunakan Response::redirect
            Response::redirect(router('student.feedback.index', ['id' => $id]))->send(); exit;
        }

        $student = DB::table('student_internship as si')
            ->join('program_study as ps', 'ps.kode_prodi', '=', 'si.program_study')
            ->where('si.nim', $nim)
            ->select('si.nim', 'si.name', 'ps.jenjang', 'ps.prodi')
            ->first();

        $company = DB::table('company')->where('id_company', $internship['id_company'])->first();

        try {
            require_once './vendor/autoload.php';
            $templateProcessor = new TemplateProcessor('./word/student_feedback_template.docx');

            $templateProcessor->setValues([
                'current_url'     => base_url(),
                'current_time'    => date('Y-m-d H:i:s'),
                'tahun'           => date('Y', strtotime($feedback['date'])),
                'nama_mahasiswa'  => $student['name'],
                'nim_mahasiswa'   => $student['nim'],
                'prodi_mahasiswa' => $student['jenjang'] . ' ' . $student['prodi'],
                'tempat_magang'   => $company['name'],
                'kesan'           => htmlspecialchars($feedback['kesan']),
                'kendala'         => htmlspecialchars($feedback['kendala']),
                'masukan'         => htmlspecialchars($feedback['masukan']),
            ]);

            $templateProcessor->setValues(prepareCheckmarks($feedback));

            $tempName = uniqid() . '-generate.docx';
            $path = './word/generate/' . $tempName;
            $templateProcessor->saveAs($path);

            

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="StudentFeedback_' . $student['nim'] . '.docx"');
            readfile($path);

            if (!isset($_GET['bulk_download'])) unlink($path);
            exit;

        } catch (Exception $e) {
            Notification::snackbar("Export error: " . $e->getMessage());
            Redirect::back(1); exit;
        }
    break;

    case 'index' :
        $id_internship = Encrypter::decrypt($id);
        $nim = $_SESSION['nim'];

        // 1. Ambil atau Buat Data Feedback
        $row = DB::table('student_feedback')
            ->where('id_internship', $id_internship)
            ->first();

        if (!$row) {
            $insert_id = DB::table('student_feedback')->insertGetId([
                'id_internship' => $id_internship,
                'nim' => $nim,
                'date' => date('Y-m-d H:i:s'),
                'posisi' => '-', 'ilmu_dari_poltek' => '-', 'ilmu_baru' => '-', 
                'data_magang' => '-', 'kesan' => '-', 'kendala' => '-', 'masukan' => '-'
            ]);
            $row = DB::table('student_feedback')->where('id_student_feedback', $insert_id)->first();
        }

        return renderView('student.my_internship.student_feedback.index', [
            'title' => 'Student Feedback',
            'routes' => 'my_internship',
            'id_enc' => $id,
            'row' => $row
        ]);
    break;

    case 'store' :
        $id_internship = Encrypter::decrypt($id);
        
        $data = [
            'posisi' => Request::post('posisi'),
            'ilmu_dari_poltek' => Request::post('ilmu_dari_poltek'),
            'ilmu_baru' => Request::post('ilmu_baru'),
            'data_magang' => Request::post('data_magang'),
            'kesan' => Request::post('kesan'),
            'kendala' => Request::post('kendala'),
            'masukan' => Request::post('masukan'),
            'date' => date('Y-m-d H:i:s')
        ];

        try {
            DB::table('student_feedback')->where('id_internship', $id_internship)->update($data);
            Notification::swal2('Saved!', 'Your feedback has been updated.', 'success');
            Redirect::back(1); exit;
        } catch (Exception $e) {
            Notification::snackbar("Error: Failed to save feedback.");
            Redirect::back(1); exit;
        }
    break;

endswitch;