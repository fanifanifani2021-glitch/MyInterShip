<?php
try {
    // 1. Dekripsi ID Internship (Keamanan Dasar)
    $id_internship = Encrypter::decrypt($id);
    $nim = Session::get('nim');

    // 2. Verifikasi Kepemilikan Data
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


switch ( $method ) :

    case 'index': 
        $id_internship = Encrypter::decrypt($id);
        $nim = $_SESSION['nim'];

        // 1. Ambil Data Internship
        $row = DB::table('internship')
            ->where('id_internship', '=', $id_internship)
            ->where('nim', '=', $nim)
            ->first();

        // 2. Validasi Keberadaan & Status
        if (!$row) {
            Notification::swal2('Not Found', 'Internship data is not found.', 'error');
            Redirect::back(1); exit;
        }

        // Cek apakah status sudah 'Yes' atau 'Finish'
        if (!in_array(strtolower($row['status']), ['yes', 'finish'])) {
            Notification::swal2('Not Available', 'Only accepted internship is allowed to upload final report.', 'error');
            Redirect::back(1); exit;
        }

        return renderView('student.my_internship.final_report.index', [
            'title'      => 'Submit Final Report',
            'routes'     => 'my_internship',
            'id_enc'     => $id,
            'internship' => $row
        ]);
    break;

    /**
     * Memproses upload file laporan
     */
    case 'store': 
        $id_internship = Encrypter::decrypt($id);
        $timestamp = date('Y-m-d H:i:s');
        $uploadDirectory = "upload/lampiran_internship/";

        // 1. Set Aturan Validasi
        set_rules('final_report', 'Final Report', 
            ['required', 'allowed_types:pdf', 'max_size:10MB'], 
            [
                'required'      => 'Please select a file to upload!',
                'allowed_types' => 'Only PDF files are allowed!',
                'max_size'      => 'Maximum file size is 10MB!'
            ]
        );

        if (form_validation() === false) {
            Notification::snackbar('Validation failed. Please check your file.');
            Redirect::back(1); exit;
        }

        // 2. Eksekusi Database dengan Transaksi
        DB::beginTransaction();
        try {
            // Update timestamp laporan terlebih dahulu
            DB::table('internship')->where('id_internship', $id_internship)->update([
                'date_finalreport' => $timestamp
            ]);

            // 3. Proses Upload File Fisik
            // Fungsi upload() diasumsikan mengembalikan nama file jika sukses
            $uploadedFileName = upload($uploadDirectory, 'final_report');

            if ($uploadedFileName === false) {
                throw new Exception("Failed to move uploaded file to storage.");
            }

            // 4. Update nama file di database
            DB::table('internship')->where('id_internship', $id_internship)->update([
                'final_report' => $uploadedFileName
            ]);

            DB::commit();
            reflash_all();
            Notification::swal2('Uploaded!', 'Your final report has been submitted successfully.', 'success');
            Redirect::back(1); exit;

        } catch (Exception $e) {
            DB::rollBack();
            Notification::snackbar("Error: " . $e->getMessage());
            Redirect::back(1); exit;
        }
    break;

endswitch;