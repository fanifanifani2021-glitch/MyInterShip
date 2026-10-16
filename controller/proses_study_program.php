<?php
error_reporting(false);
session_start();
$title = "Process Study Program";
include "./controller/controller-init.php";
$aksi = $_GET['aksi'];


role_checker('role', ['UPKPK', 'CDC'], 'halaman');


// $allowed_major = 'Informatics Engineering,Electrical Engineering,Mechanical Engineering,Business Management';
$allowed_jenjang = 'D2,D3,D4,S1,S2,S3,Profesi';


if ($aksi == "add") {

   
} else

if ($aksi == "edit") {
    try {
        set_rules('kode_prodi', 'kode_prodi', ['required',], ['required' => 'This field is required !', 'is_unique' => 'Program Study Code already taken !']);
        set_rules('major_en', 'major', ['required'], ['required' => 'This field is required !', 'allowed_values' => 'This value is not allowed !']);
        set_rules('major_id', 'major', ['required'], ['required' => 'This field is required !', 'allowed_values' => 'This value is not allowed !']);
        set_rules('jenjang', 'jenjang', ['required', 'allowed_values:' . $allowed_jenjang], ['required' => 'This field is required !', 'allowed_values' => 'This value is not allowed !']);
        set_rules('prodi_en', 'prodi_en', ['required'], ['required' => 'This field is required !']);
        set_rules('prodi_id', 'prodi_id', ['required'], ['required' => 'This field is required !']);

        if (form_validation() === false) {
            make_snackbar('Check your submitted data and try again later');
            redirect_back(1);
            exit;
        }

        $stmt = $_SERVER['PDO']->prepare("
            UPDATE program_study SET 
                kode_prodi = :kode_prodi,
                jenjang = :jenjang,
                prodi = :prodi,
                study_program = :study_program,
                major = :major,
                jurusan = :jurusan,
                id_kampus = :id_kampus
            WHERE kode_prodi = :kode_prodi_lama AND id_kampus = :old_id_kampus
        ");

        $stmt->execute([
            ':kode_prodi'      => $_POST['kode_prodi'],
            ':jenjang'         => $_POST['jenjang'],
            ':prodi'           => $_POST['prodi_id'],
            ':study_program'   => $_POST['prodi_en'],
            ':major'           => $_POST['major_en'],
            ':jurusan'         => $_POST['major_id'],
            ':id_kampus'       => $_POST['id_kampus'] ?? '1',
            ':old_id_kampus'       => $_POST['old_id_kampus'] ?? '1',
            ':kode_prodi_lama' => $_POST['kode_prodi_lama'],
        ]);

        reflash_all();
        make_snackbar('Study Program has been edited successfully !');
        redirect( base_url('cdc/study_program/management'), 1);
        exit();
    } catch (Exception $e) {

        var_dump( $e );
        error_log("Update Error (program_study): " . $e->getMessage());
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1);
        exit();
    }
} else


if ($aksi == "delete") {
    try {
        // Validasi input
        if (empty($_POST['id'])) {
            throw new Exception("Data ID tidak boleh kosong.");
        }

        // Decode data ID
        $decoded = json_decode(base64_decode($_POST['id']), true);
        if (!is_array($decoded) || empty($decoded['kode_prodi']) || empty($decoded['id_kampus'])) {
            throw new Exception("Data ID tidak valid.");
        }

        $kode_prodi = $decoded['kode_prodi'];
        $id_kampus  = $decoded['id_kampus'];

        // Persiapkan dan jalankan query DELETE
        $stmt = $_SERVER['PDO']->prepare("
            DELETE FROM program_study 
            WHERE kode_prodi = :kode_prodi AND id_kampus = :id_kampus
        ");
        $stmt->execute([
            ':kode_prodi' => $kode_prodi,
            ':id_kampus'  => $id_kampus
        ]);

        // Cek apakah ada baris yang terhapus
        if ($stmt->rowCount() > 0) {
            http_response_code(204); // No Content
            exit();
        } else {
            http_response_code(404); // Not Found
            echo json_encode(['error' => 'Data tidak ditemukan atau sudah dihapus.']);
            exit();
        }
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        error_log("PDO Delete Error: " . $e->getMessage());
        echo json_encode(['error' => 'Terjadi kesalahan saat menghapus data.', 'detail' => $e->getMessage()]);
        exit();
    } catch (Exception $e) {
        http_response_code(400); // Bad Request
        error_log("Delete Validation Error: " . $e->getMessage());
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
} else {
    http_response_code(400); // Bad Request
    make_snackbar('You do not have permission to perform this action');
    redirect_back(1);
    exit;
}
