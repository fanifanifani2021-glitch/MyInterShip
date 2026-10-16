<?php

if (!is_ajax_request()) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid request.']);
    exit;
}

$pdo = $_SERVER['PDO'];

switch ($method):

    default:
        http_response_code(404);
        break;

    case 'getLectureByCampus':
        $id_kampus = $_POST['id_kampus'] ?? 0;

        $stmt = $pdo->prepare("SELECT nim_nik_unit, name FROM lecturer WHERE id_kampus = :id_kampus");
        $stmt->execute([':id_kampus' => $id_kampus]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $lecturers = [];

        foreach ($result as $row) {
            $lecturers[] = [
                'nim_nik_unit' => $row['nim_nik_unit'],
                'name' => $row['name']
            ];
        }

        echo json_encode($lecturers);
        break;

    case 'assignSupervisor':
        $nim = base64_decode($_POST['nim'] ?? '');
        $id_kampus = $_POST['id_kampus'] ?? '';
        $nik_dospem = $_POST['nik_dospem'] ?? '';

        if (!$nim || !$id_kampus || !$nik_dospem) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Incomplete data.']);
            exit;
        }

        try {
            // Validasi apakah mahasiswa ada
            $stmt = $pdo->prepare("SELECT * FROM `student_internship` WHERE nim = :nim LIMIT 1");
            $stmt->execute([':nim' => $nim]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$student) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Student not found.']);
                exit;
            }

            // Update dosen pembimbing
            if ($nik_dospem === 'Unassigned') {
                $stmt = $pdo->prepare("UPDATE student_internship SET nik_dospem = NULL WHERE nim = :nim");
            } else {
                $stmt = $pdo->prepare("UPDATE student_internship SET nik_dospem = :nik_dospem WHERE nim = :nim");
                $stmt->bindParam(':nik_dospem', $nik_dospem);
            }
            $stmt->bindParam(':nim', $nim);
            $stmt->execute();

            echo json_encode(['status' => 'success', 'message' => 'Supervisor assigned successfully.']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database error.', 'error' => $e->getMessage()]);
        }
        break;
endswitch;
