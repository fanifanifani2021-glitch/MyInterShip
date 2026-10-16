<?php

switch ($method):
    default:
        view('exception/error_404');
        break;

    case 'assign_supervisor_lecturer':

        // Get Internship
        $internship = base64_decode($_GET['internship']);


        $sql = "SELECT * FROM internship WHERE id_internship = :id_internship LIMIT 1";


        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_internship', $internship, PDO::PARAM_INT);

        $stmt->execute();

        if ($stmt->rowCount() != 1) {
            make_soloalert('error', 'Not Allowed', 'Internship data is not found !');
            redirect_back(1);
            exit;
        }

        $internship_data = $stmt->fetch(PDO::FETCH_ASSOC);


        // Get Compant Data
        $sql = "SELECT * FROM company WHERE id_company = :id_company LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_company', $internship_data['id_company'], PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() != 1) {
            $company_data = [];
        } else {
            $company_data = $stmt->fetch(PDO::FETCH_ASSOC);
        }


        // Get User Company
        $sql = "SELECT * FROM user_company WHERE id_company = :id_company AND user_type NOT IN ('HRD')";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_company', $internship_data['id_company'], PDO::PARAM_INT);
        $stmt->execute();

        if ( $stmt->rowCount() < 1) { 
            $supervisor_industry = [];
        } else {
            $supervisor_industry = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }



        $redirect = end(explode('index.php?page=', $_SERVER['REQUEST_URI']));
        $coor = $_GET['coor'];


        $nim = $internship_data['nim'];
        $student_data = $_SERVER['KONEKSI_DB']->query("SELECT * FROM student_internship WHERE nim = '$nim'")->fetch_assoc();

        // Get Coor Data
        $prodi = $student_data['program_study'];
        $coordinator_data = $_SERVER['KONEKSI_DB']->query("SELECT * FROM lecturer WHERE is_koor = 1 AND prodi_koor = '$prodi'")->fetch_assoc();


        // Get All University Supervisor
        $sql = "SELECT * FRoM lecturer WHERE id_kampus = :id_kampus";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam('id_kampus', $student_data['id_kampus'], PDO::PARAM_INT);

        $stmt->execute();

        if ( $stmt->rowCount() < 1 ) {
            $supervisor_university = [];
        } else {
            $supervisor_university = $stmt->fetchAll( PDO::FETCH_ASSOC );
        }



        view(
            'upkpk/internship_management/assign_supervisor_lecturer',
            [
                'title' => 'Assign Supervisor', 'data_internship' => $internship_data,
                'company_data' => $company_data, 'supervisor_industry' => $supervisor_industry,

                'redirect' => $redirect, 'coor' => $coor, 
                'student_data' => $student_data, 'coordinator_data' => $coordinator_data,
                'supervisor_university' => $supervisor_university
            ]
        );
        break;
endswitch;
