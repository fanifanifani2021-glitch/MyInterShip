<?php
defined('BASE_DIR') or die('Direct script access is Not Allowed');

switch ($method):

    default:
        http_response_code(404);
        break;

    case 'submit':
        // 1. Set Header JSON
        header('Content-Type: application/json');

        $pdo = $_SERVER['PDO'];
        
        // Ambil data input
        $nama_kampus = $_POST['university'] ?? '';

        // Validasi sederhana
        if (empty($nama_kampus)) {
            http_response_code(400);
            echo json_encode(['status' => 0, 'message' => 'University name is required']);
            exit();
        }

        try {
            $sql = "
                INSERT INTO tb_kampus (nama_kampus, created_at, updated_at)
                VALUES (:nama_kampus, :created_at, :updated_at)
            ";

            $stmt = $pdo->prepare($sql);

            $params = [
                ':nama_kampus' => $nama_kampus,
                ':created_at'  => date('Y-m-d H:i:s'),
                ':updated_at'  => null,
            ];

            $insert = $stmt->execute($params);

            if ($insert) {
                http_response_code(200);
                echo json_encode([
                    'status'  => 1,
                    'message' => 'New University Successfully Added!'
                ]);
            } else {
                throw new Exception("Failed to execute INSERT query.");
            }

        } catch (Exception $e) {
            // 2. LOGGING ERROR
            // 'id' diisi nama kampus karena ID (Auto Increment) belum terbentuk saat error insert
            Router::log("Database_Error", [
                'message' => $e->getMessage(), 
                'id'      => $nama_kampus 
            ]);

            // 3. Return JSON Error
            http_response_code(400);
            echo json_encode([
                'status'  => 0,
                'message' => 'System Error: ' . $e->getMessage()
            ]);
        }

        exit();

    break;


// ==========================================
    // DELETE DATA (DESTROY)
    // ==========================================
    case 'destroy':
        // 1. Set Header JSON
        header('Content-Type: application/json');

        $pdo = $_SERVER['PDO'];
        $id_kampus = $_POST['id_kampus'] ?? null;

        if (empty($id_kampus)) {
            http_response_code(400);
            echo json_encode(['status' => 0, 'message' => 'ID Kampus is required']);
            exit();
        }

        try {
            $sql = "DELETE FROM tb_kampus WHERE id_kampus = :id_kampus";
            $stmt = $pdo->prepare($sql);

            $delete = $stmt->execute([':id_kampus' => $id_kampus]);

            if ($delete) {
                http_response_code(200);
                echo json_encode([
                    'status'  => 1,
                    'message' => 'University Successfully Deleted!'
                ]);
            } else {
                throw new Exception("Failed to execute DELETE query.");
            }

        } catch (Exception $e) {
            // 2. LOGGING
            Router::log("Database_Error", [
                'message' => $e->getMessage(), 
                'id'      => $id_kampus
            ]);

            // 3. Return Error JSON
            http_response_code(400);
            echo json_encode([
                'status'  => 0,
                'message' => 'System Error: ' . $e->getMessage()
            ]);
        }
        exit();
    break;


    // ==========================================
    // EDIT DATA (UPDATE)
    // ==========================================
    case 'update':
        // 1. Set Header JSON
        header('Content-Type: application/json');

        $pdo = $_SERVER['PDO'];
        
        $id_kampus   = $_POST['id_kampus'] ?? null;
        $nama_kampus = $_POST['university'] ?? null;

        // Validasi Input
        if (empty($id_kampus) || empty($nama_kampus)) {
            http_response_code(400);
            echo json_encode(['status' => 0, 'message' => 'ID and University Name are required']);
            exit();
        }

        try {
            $sql = "
                UPDATE tb_kampus
                SET 
                    nama_kampus = :nama_kampus,
                    updated_at  = :updated_at
                WHERE id_kampus = :id_kampus
            ";

            $stmt = $pdo->prepare($sql);

            $update = $stmt->execute([
                ':nama_kampus' => $nama_kampus,
                ':updated_at'  => date('Y-m-d H:i:s'),
                ':id_kampus'   => $id_kampus,
            ]);

            if ($update) {
                http_response_code(200);
                echo json_encode([
                    'status'  => 1,
                    'message' => 'University Successfully Updated!'
                ]);
            } else {
                throw new Exception("Failed to execute UPDATE query.");
            }

        } catch (Exception $e) {
            // 2. LOGGING
            Router::log("Database_Error", [
                'message' => $e->getMessage(), 
                'id'      => "Update ID: $id_kampus"
            ]);

            // 3. Return Error JSON
            http_response_code(400);
            echo json_encode([
                'status'  => 0,
                'message' => 'System Error: ' . $e->getMessage()
            ]);
        }
        exit();
    break;

endswitch;
