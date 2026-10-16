<?php
error_reporting(false);
session_start();

try {
    // Cek Internship
    $sql = "SELECT * FROM internship WHERE id_internship = :id_internship AND id_company = :id_company LIMIT 1";
    $stmt = $_SERVER['PDO']->prepare($sql);

    $stmt->execute( [':id_internship' => $_POST['id_internship'], ':id_company' => $_POST['id_company'] ?? $_SESSION['id_company'] ]);
    $internship_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (in_array(strtolower($internship_data['status']), ['no'])) {
        make_swal2('error', 'Not Allowed', 'Only Accepted Internship are allowed to be marked');
        redirect_referrer(1);
        die;
    }


    // Define the status as a variable
    $internship_status = 'Finish';

    // Ubah Status nya
    $sql_update = "UPDATE internship SET `status` = :internship_status WHERE id_internship = :id_internship AND id_company = :id_company";
    $stmt_update = $_SERVER['PDO']->prepare($sql_update);
    $stmt_update->execute( [':id_internship' => $_POST['id_internship'], ':id_company' => $_POST['id_company'] ?? $_SESSION['id_company'], ':internship_status' => $internship_status]);

    // Check if the update was successful
    if ($stmt_update) {
        // Success - Redirect with a success message
        make_swal2('success', 'Internship Finished', 'The internship status has been updated to "Finish".');
        // Redirect to the internship details page
        redirect('detail_intern_management&id_internship=' . base64_encode($_POST['id_internship']), 1);
        die;
    } else {
        
            // var_dump( $sql_update, [':id_internship' => $_POST['id_internship'], ':id_company' => $_POST['id_company'] ?? $_SESSION['id_company'], ':internship_status' => $internship_status] ); die;
            
        // Failure - Redirect with an error message
        make_swal2('error', 'Update Failed', 'Failed to update the internship status.');
        redirect_referrer(1);
        die;
    }


} catch (Exception $e) {
    
    var_dump( $e ); die;
    make_swal2('error', 'Update Failed', 'Failed to update the internship status.');
    redirect_referrer(1);
    die;
}
