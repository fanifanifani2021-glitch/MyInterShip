<?php

switch ($method):
    default:
        echo "Not Found";
        break;

    case 'destroy':
        $nimEncoded = $_POST['nim'] ?? null;

        if (!$nimEncoded) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing student NIM.']);
            exit;
        }

        $nim = base64_decode($nimEncoded);

        try {
            $stmt = $_SERVER['PDO']->prepare("DELETE FROM student_internship WHERE nim = :nim");
            $stmt->execute([':nim' => $nim]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Student deleted successfully.']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Student not found or already deleted.']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'System Error']);
        }
        break;


endswitch;
