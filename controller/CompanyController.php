<?php

switch ($method):

    default:
        response_json(404);
        break;

    case 'company_stats':

        $id = base64_decode($_POST['id']);

        // Cek Company
        $sql = "SELECT * FROM company WHERE id_company = :id LIMIT 1";
        $stmt = $_SERVER['PDO']->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() != 1) {
            response_json(400);
        }

        $company_data = $stmt->fetch(PDO::FETCH_ASSOC);



        // Count Student
        $sql_total_internship = "SELECT COUNT(id_internship) as total_internship FROM intersnhip WHERE id_company = :id_company";
        $sql_accepted_internship = "SELECT COUNT(id_internship) as accepted_internship FROM intersnhip WHERE id_company = :id_company AND `status` IN ('YES', 'Yes')";
        $sql_finished_internship = "SELECT COUNT(id_internship) as finished_internship FROM intersnhip WHERE id_company = :id_company AND `status` IN ('FINISH', 'Finish')";
        $sql_waiting_internship = "SELECT COUNT(id_internship) as waiting_internship FROM internship WHERE id_company = :id_company AND `status` IN ('WAITING', 'Waiting')";

        $sql_company_stats = "SELECT COUNT(id_internship) as total_internship,
            SUM(CASE WHEN `status` IN ('YES', 'Yes') THEN 1 ELSE 0 END) as accepted_internship,
            SUM(CASE WHEN `status` IN ('FINISH', 'Finish') THEN 1 ELSE 0 END) as finished_internship,
            SUM(CASE WHEN `status` IN ('WAITING', 'Waiting') THEN 1 ELSE 0 END) as waiting_internship
            FROM internship
            WHERE id_company = :id_company
        ";

        $stmt_stats = $_SERVER['PDO']->prepare($sql_company_stats);
        $stmt_stats->bindParam(':id_company', $company_data['id_company'], PDO::PARAM_INT);
        $stmt_stats->execute();
        $company_stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

        $total_internship = $company_stats['total_internship'];
        $accepted_internship = $company_stats['accepted_internship'];
        $finished_internship = $company_stats['finished_internship'];
        $waiting_internship = $company_stats['waiting_internship'];


        // Student Feedback
        $sql = "
    SELECT
        AVG(posisi) as rata_posisi,
        AVG(ilmu_dari_poltek) as rata_ilmu_dari_poltek,
        AVG(ilmu_baru) as rata_ilmu_baru,
        AVG(data_magang) as rata_data_magang
    FROM student_feedback sf
    JOIN internship i ON sf.id_internship = i.id_internship
    WHERE i.id_company = :id_company;
";

$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->bindParam(':id_company', $company_data['id_company'], PDO::PARAM_INT);
$stmt->execute();

$averages = $stmt->fetch(PDO::FETCH_ASSOC);

// Extract the averages
$rata_posisi = $averages['rata_posisi'] ?? 0;
$rata_ilmu_dari_poltek = $averages['rata_ilmu_dari_poltek'] ?? 0;
$rata_ilmu_baru = $averages['rata_ilmu_baru'] ?? 0;
$rata_data_magang = $averages['rata_data_magang'] ?? 0;


        controller('CompanyStats', ['company_data' => $company_data, 
        'company_stats' => $company_stats, 'averages' => $averages]);
        break;

endswitch;
