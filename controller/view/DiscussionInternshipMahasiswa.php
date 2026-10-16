<?php

$id_internship = base64_decode($_GET['id_internship']);
$nim = $_SESSION['nim'];

// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = :id_internship AND nim = :nim LIMIT 1";
$exec = $_SERVER['PDO']->prepare($sql);
$exec->execute([':nim' => $nim, ':id_internship' => $id_internship]);

if ($exec->rowCount() != 1) {
    make_swal2('error', 'Not Allowed', 'Internship Data is not found !');
    redirect_back(1);
    exit();
}

$internship_data = $exec->fetch(PDO::FETCH_ASSOC);

switch ($method):
    default:
        http_response_code(404);

        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'XMLHttpRequest') {
            renderView('errors.error_404', ['title' => '404 - Page Not Found']);
        } else {
            echo json_encode([
                'welcome_message' => $_SERVER['API_WELCOME'],
                'status' => 0,
                'message' => 'Invalid request !'
            ], JSON_PRETTY_PRINT);
        }
        break;

    case 'index':


        $sql = "SELECT * FROM discussion WHERE id_internship = :id_internship ORDER BY id_discussion DESC";

        $stmt = $_SERVER['PDO']->prepare($sql);
        $stmt->bindValue(':id_internship', $id_internship, PDO::PARAM_INT);

        $stmt->execute();
        $exec = $stmt->fetchAll(PDO::FETCH_ASSOC);


        renderView('student.my_internship.discussion.index', [
            'title' => 'Discussion',
            'routes' => 'my_internship',
            'id_internship' => $id_internship,
            'internship_data' => $internship_data,
            'exec' => $exec
        ]);

        break;

endswitch;
