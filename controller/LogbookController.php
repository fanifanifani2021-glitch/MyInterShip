<?php
session_start();

switch ($method):

    default:
        response_json(404);
        break;

    case 'bulk_approval':

        $selectedIds = $_POST['ids'];
        $approvalValue = $_POST['approval'];

        // Lakukan validasi data jika diperlukan

        switch ( $_GET['action'] ) :
            default :
            make_swal2('error', 'Failed to save Bulk Approval', 'Please try again later.');
            response_json(400, $response);
            break;

            case 'supervisor' :

                try {
                    $pdo->beginTransaction();
        
        
                    // Proses penyimpanan data ke database dalam transaksi
                    $sql = "UPDATE logbook SET approval_spv = :approval WHERE id_logbook = :id_logbook";
                    $stmt = $pdo->prepare($sql);
        
                    foreach ($selectedIds as $idLogbook) {
                        $stmt->bindValue(':approval', $approvalValue);
                        $stmt->bindValue(':id_logbook', $idLogbook);
                        $stmt->execute();
                    }
        
                    // Commit transaksi
                    $pdo->commit();
        
                    // Respon berhasil
                    $response = ['status' => 'success', 'message' => 'Approval data berhasil disimpan.'];
                    make_snackbar('Bulk Approval has been saved');
                    response_json(200, $response);
                } catch (PDOException $e) {
                    // Rollback transaksi jika terjadi kesalahan
                    $pdo->rollBack();
        
                    // Tangani kesalahan PDO
                    $response = ['status' => 'error', 'message' => 'Terjadi kesalahan dalam penyimpanan data.'];
                    // make_snackbar('Failed to save Bulk Approval has been saved');
                    make_swal2('error', 'Failed to save Bulk Approval', 'Please try again later.');
                    response_json(400, $response);
                }
            break;


            case 'lecturer' :
                try {
                    $pdo->beginTransaction();
        
        
                    // Proses penyimpanan data ke database dalam transaksi
                    $sql = "UPDATE logbook SET approval_dpm = :approval WHERE id_logbook = :id_logbook";
                    $stmt = $pdo->prepare($sql);
        
                    foreach ($selectedIds as $idLogbook) {
                        $stmt->bindValue(':approval', $approvalValue);
                        $stmt->bindValue(':id_logbook', $idLogbook);
                        $stmt->execute();
                    }
        
                    // Commit transaksi
                    $pdo->commit();
        
                    // Respon berhasil
                    $response = ['status' => 'success', 'message' => 'Approval data berhasil disimpan.'];
                    make_snackbar('Bulk Approval has been saved');
                    response_json(200, $response);
                } catch (PDOException $e) {
                    // Rollback transaksi jika terjadi kesalahan
                    $pdo->rollBack();
        
                    // Tangani kesalahan PDO
                    $response = ['status' => 'error', 'message' => 'Terjadi kesalahan dalam penyimpanan data.'];
                    // make_snackbar('Failed to save Bulk Approval has been saved');
                    make_swal2('error', 'Failed to save Bulk Approval', 'Please try again later.');
                    response_json(400, $response);
                }
            break;



        endswitch;

        


        break;

    case 'bulk_delete':

        // Get Student NIM
        $nim = $_SESSION['nim'];
        $id_internship = base64_decode($_POST['id_internship']);


        // Pengecekan data di tabel internship sebelum penghapusan
        $sqlCheckInternship = "SELECT * FROM internship WHERE id_internship = :id_internship AND nim = :nim";
        $stmtCheckInternship = $pdo->prepare($sqlCheckInternship);
        $stmtCheckInternship->bindValue(':id_internship', $id_internship);
        $stmtCheckInternship->bindValue(':nim', $nim);
        $stmtCheckInternship->execute();

        if ($stmtCheckInternship->rowCount() !== 1) {
            // Jika rowcount != 1, batalkan penghapusan
            make_swal2('error', 'Failed to Bulk Delete ', 'Internship data not found or does not match the criteria.');
            response_json(400, $response);
        }


        // Cek Internship
        $selectedIds = $_POST['ids'];

        try {

            // Memulai transaksi
            $pdo->beginTransaction();



            // Hapus logbook yang sesuai dengan id_logbook dari tabel logbook
            foreach ($selectedIds as $idLogbook) {
                $sqlDeleteLogbook = "DELETE FROM logbook WHERE id_logbook = :id_logbook AND id_internship = :id_internship";
                $stmtDeleteLogbook = $pdo->prepare($sqlDeleteLogbook);
                $stmtDeleteLogbook->bindValue(':id_logbook', $idLogbook);
                $stmtDeleteLogbook->bindValue(':id_internship', $id_internship);
                $stmtDeleteLogbook->execute();
            }


            // Commit transaksi
            $pdo->commit();

            make_swal2('success', 'Bulk Delete Success', 'Logbook data has been deleted successfully.');
            response_json(200, $response);
        } catch (PDOException $e) {

            // Commit transaksi
            $pdo->commit();

            make_swal2('error', 'Failed to Bulk Delete', 'Please try again later.');
            response_json(400, $response);
        }

        break;


endswitch;
