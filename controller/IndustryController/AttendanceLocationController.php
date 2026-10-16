<?php

switch ($method) :

    default:
        Router::handleError('404', 'Not Found');
    break;

    case 'data':
        $pdo = $_SERVER['PDO'];

        // =============================
        // DataTables params
        // =============================
        $draw   = (int)($_POST['draw'] ?? 1);
        $start  = (int)($_POST['start'] ?? 0);
        $length = (int)($_POST['length'] ?? 10);
        $search = $_POST['search']['value'] ?? '';

        $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
        $orderDir         = $_POST['order'][0]['dir'] ?? 'asc';

        // Mapping kolom (WAJIB sesuai urutan tabel frontend)
        $columns = [
            'id_location',
            'location',
            'latitude',
            'longitude',
            'radius_meters',
            'created_at'
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'id_location';

        // =============================
        // Base WHERE
        // =============================
        $where = 'WHERE id_company = :id_company AND is_deleted = :is_deleted ';
        $params = [
            ':id_company' => $_SESSION['id_company'],
            'is_deleted' => false
        ];

        // Search
        if (!empty($search)) {
            $where .= ' AND (
                location LIKE :search
                OR latitude LIKE :search
                OR longitude LIKE :search
                OR radius_meters LIKE :search
            )';
            $params[':search'] = "%{$search}%";
        }


        // =============================
        // Total records
        // =============================
        $totalStmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM attendance_location 
            WHERE id_company = :id_company AND is_deleted = :is_deleted
        ");
        $totalStmt->execute([':id_company' => $_SESSION['id_company'], 'is_deleted' => false]);
        $recordsTotal = (int)$totalStmt->fetchColumn();

        // =============================
        // Filtered records
        // =============================
        $filteredStmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM attendance_location 
            {$where}
        ");
        $filteredStmt->execute($params);
        $recordsFiltered = (int)$filteredStmt->fetchColumn();

        // =============================
        // Main query
        // =============================
        $sql = "
            SELECT 
                id_location,
                location,
                latitude,
                longitude,
                radius_meters,
                created_at
            FROM attendance_location
            {$where}
            ORDER BY {$orderColumn} {$orderDir}
            LIMIT :start, :length
        ";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':length', $length, PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // =============================
        // Format data
        // =============================
        $data = [];
        foreach ($rows as $i => $row) {
            $data[] = [
                'no' => $start + $i + 1, // ✅ nomor mengikuti page & filter
                'location' => htmlspecialchars($row['location']),
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'radius_meters' => $row['radius_meters'] . ' m',
                'action' => '
                    <button class="btn btn-sm btn-warning btn-edit" data-id="'.$row['id_location'].'"><i class="fas fa-edit mr-1"></i>Edit</button>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="'.$row['id_location'].'"><i class="fas fa-trash mr-1"></i>Delete</button>
                '
            ];
        }


        // =============================
        // Response
        // =============================
        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data
        ]);
        exit;
    break;

    case 'data_recycle':
        $pdo = $_SERVER['PDO'];

        // =============================
        // DataTables params
        // =============================
        $draw   = (int)($_POST['draw'] ?? 1);
        $start  = (int)($_POST['start'] ?? 0);
        $length = (int)($_POST['length'] ?? 10);
        $search = $_POST['search']['value'] ?? '';

        $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
        $orderDir         = $_POST['order'][0]['dir'] ?? 'asc';

        // Mapping kolom (WAJIB sesuai urutan tabel frontend)
        $columns = [
            'id_location',
            'location',
            'latitude',
            'longitude',
            'radius_meters',
            'created_at'
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'id_location';

        // =============================
        // Base WHERE
        // =============================
        $where = 'WHERE id_company = :id_company AND is_deleted = :is_deleted ';
        $params = [
            ':id_company' => $_SESSION['id_company'],
            'is_deleted' => true
        ];

        // Search
        if (!empty($search)) {
            $where .= ' AND (
                location LIKE :search
                OR latitude LIKE :search
                OR longitude LIKE :search
                OR radius_meters LIKE :search
            )';
            $params[':search'] = "%{$search}%";
        }


        // =============================
        // Total records
        // =============================
        $totalStmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM attendance_location 
            WHERE id_company = :id_company AND is_deleted = :is_deleted
        ");
        $totalStmt->execute([':id_company' => $_SESSION['id_company'], 'is_deleted' => true]);
        $recordsTotal = (int)$totalStmt->fetchColumn();

        // =============================
        // Filtered records
        // =============================
        $filteredStmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM attendance_location 
            {$where}
        ");
        $filteredStmt->execute($params);
        $recordsFiltered = (int)$filteredStmt->fetchColumn();

        // =============================
        // Main query
        // =============================
        $sql = "
            SELECT 
                id_location,
                location,
                latitude,
                longitude,
                radius_meters,
                created_at,
                deleted_by,
                deleted_at
            FROM attendance_location
            {$where}
            ORDER BY {$orderColumn} {$orderDir}
            LIMIT :start, :length
        ";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':length', $length, PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // =============================
        // Format data
        // =============================
        $data = [];
        foreach ($rows as $i => $row) {
            $data[] = [
                'no' => $start + $i + 1, // nomor mengikuti page & filter
                'location' => htmlspecialchars($row['location']),
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'deleted_properties' => $row['deleted_by']. " on " .$row['deleted_at'],
                'radius_meters' => $row['radius_meters'] . ' m',
                'action' => '
                    <button 
                        class="btn btn-sm btn-success btn-restore"
                        data-id="'.$row['id_location'].'">
                        <i class="fas fa-undo mr-1"></i> Restore
                    </button>

                    <button 
                        class="btn btn-sm btn-danger btn-force-delete"
                        data-id="'.$row['id_location'].'">
                        <i class="fas fa-trash-alt mr-1"></i> Delete Permanently
                    </button>
                '
            ];
        }



        // =============================
        // Response
        // =============================
        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data
        ]);
        exit;
    break;

    case 'submit':

        $pdo = $_SERVER['PDO'];

        // =============================
        // Ambil & validasi input
        // =============================
        $location       = trim($_POST['location'] ?? '');
        $latitude       = $_POST['latitude'] ?? null;
        $longitude      = $_POST['longitude'] ?? null;
        $radius_meters  = $_POST['radius_meters'] ?? null;
        $id_company     = $_SESSION['id_company'] ?? null;

        if (
            empty($location) ||
            empty($latitude) ||
            empty($longitude) ||
            empty($radius_meters) ||
            empty($id_company)
        ) {
            echo json_encode([
                'status'  => false,
                'message' => 'Incomplete Data '
            ]);
            exit;
        }

        // Validasi numerik
        if (
            !is_numeric($latitude) ||
            !is_numeric($longitude) ||
            !is_numeric($radius_meters)
        ) {
            echo json_encode([
                'status'  => false,
                'message' => 'Invalid Coordinate or Radius'
            ]);
            exit;
        }

        // =============================
        // Insert data
        // =============================
        $sql = "
            INSERT INTO attendance_location
            (
                id_company,
                location,
                latitude,
                longitude,
                radius_meters,
                is_deleted,
                created_at

            )
            VALUES
            (
                :id_company,
                :location,
                :latitude,
                :longitude,
                :radius_meters,
                :is_deleted,
                NOW()
            )
        ";

        $stmt = $pdo->prepare($sql);

        $result = $stmt->execute([
            ':id_company'    => $id_company,
            ':location'      => $location,
            ':latitude'      => $latitude,
            ':longitude'     => $longitude,
            ':radius_meters' => (int)$radius_meters,
            ':is_deleted'    => (int) 0
        ]);

        if (!$result) {
            echo json_encode([
                'status'  => false,
                'message' => 'Failed to save attendance location'
            ]);
            exit;
        }

        // =============================
        // Success response
        // =============================
        echo json_encode([
            'status'  => true,
            'message' => 'Attendance Location has been created successfully'
        ]);
        exit;

    break;


    case 'destroy':
        $pdo = $_SERVER['PDO'];

        if (empty($_POST['id_location'])) {
            echo json_encode([
                'status'  => false,
                'message' => 'Invalid location ID'
            ]);
            exit;
        }

        $id_location = (int) $_POST['id_location'];

        // Pastikan data milik company yg login
        $stmt = $pdo->prepare("
            UPDATE attendance_location
            SET is_deleted = :is_deleted,
            deleted_at = :deleted_at,
            deleted_by = :deleted_by
            WHERE id_location = :id
            AND id_company = :company
        ");

        $success = $stmt->execute([
            ':id'      => $id_location,
            ':is_deleted' => true,
            ':deleted_at' => date('Y-m-d H:i:s'),
            ':deleted_by' => $_SESSION['username']. " | " .$_SESSION['user_fullname'],
            ':company' => $_SESSION['id_company']
        ]);

        if ($success && $stmt->rowCount() > 0) {
            echo json_encode([
                'status'  => true,
                'message' => 'Attendance location deleted successfully'
            ]);
        } else {
            echo json_encode([
                'status'  => false,
                'message' => 'Data not found or access denied'
            ]);
        }

        exit;
    break;

    case 'restore':

        $pdo = $_SERVER['PDO'];

        $id = (int)($_POST['id_location'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid ID']);
            exit;
        }

        $stmt = $pdo->prepare("
            UPDATE attendance_location
            SET 
                is_deleted = 0,
                deleted_at = NULL,
                deleted_by = NULL
            WHERE id_location = :id
            AND id_company  = :company
            AND is_deleted  = 1
        ");

        $stmt->execute([
            ':id'       => $id,
            ':company'  => $_SESSION['id_company']
        ]);

        echo json_encode([
            'status'  => true,
            'message' => 'Location restored successfully'
        ]);
    exit;


    case 'force_delete':

        $pdo = $_SERVER['PDO'];

        $id = (int)($_POST['id_location'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid ID']);
            exit;
        }

        // 🔒 pastikan hanya data recycle milik company
        $stmt = $pdo->prepare("
            DELETE FROM attendance_location
            WHERE id_location = :id
            AND id_company  = :company
            AND is_deleted  = 1
        ");

        $stmt->execute([
            ':id'      => $id,
            ':company' => $_SESSION['id_company']
        ]);

        echo json_encode([
            'status'  => true,
            'message' => 'Location permanently deleted'
        ]);
    exit;


    case 'detail':
        $pdo = $_SERVER['PDO'];

        $stmt = $pdo->prepare("
            SELECT id_location, location, latitude, longitude, radius_meters
            FROM attendance_location
            WHERE id_location = :id
            AND id_company = :company
            AND is_deleted = 0
            LIMIT 1
        ");

        $stmt->execute([
            ':id'      => $_POST['id_location'],
            ':company' => $_SESSION['id_company']
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            echo json_encode([
                'status'  => false,
                'message' => 'Attendance location not found'
            ]);
            exit;
        }

        echo json_encode([
            'status' => true,
            'data'   => $row
        ]);
        exit;
    break;


    case 'update':
    $pdo = $_SERVER['PDO'];

    $stmt = $pdo->prepare("
        UPDATE attendance_location SET
            location       = :location,
            latitude       = :latitude,
            longitude      = :longitude,
            radius_meters  = :radius,
            updated_at     = NOW()
        WHERE id_location = :id
          AND id_company  = :company
    ");

    $stmt->execute([
        ':location' => $_POST['location'],
        ':latitude' => $_POST['latitude'],
        ':longitude'=> $_POST['longitude'],
        ':radius'   => $_POST['radius_meters'],
        ':id'       => $_POST['id_location'],
        ':company'  => $_SESSION['id_company']
    ]);

    echo json_encode([
        'status'  => true,
        'message' => 'Attendance location updated successfully'
    ]);
    exit;
break;



endswitch;