<?php

switch ($method):
    default:
        abort(404, 'No Found');
        break;

    case 'list':
        $db = $_SERVER['PDO'];

        // DataTables POST params
        $draw = $_POST['draw'] ?? 1;
        $start = intval($_POST['start'] ?? 0);
        $length = intval($_POST['length'] ?? 10);
        $searchValue = $_POST['search']['value'] ?? '';
        $orderColumnIndex = $_POST['order'][0]['column'] ?? 1; // default nama_kampus
        $orderDir = strtoupper($_POST['order'][0]['dir'] ?? 'ASC');

        // Map kolom index ke nama kolom db
        $columns = ['id_kampus', 'nama_kampus', 'website', 'logo', 'created_at', 'updated_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'id_kampus';

        // Total records
        $stmtTotal = $db->query("SELECT COUNT(*) FROM tb_kampus");
        $recordsTotal = (int) $stmtTotal->fetchColumn();

        // Filtering
        $where = '';
        $params = [];
        if ($searchValue !== '') {
            $where = "WHERE nama_kampus LIKE :search OR website LIKE :search";
            $params[':search'] = "%$searchValue%";
        }

        // Total records after filtering
        $stmtFiltered = $db->prepare("SELECT COUNT(*) FROM tb_kampus $where");
        $stmtFiltered->execute($params);
        $recordsFiltered = (int) $stmtFiltered->fetchColumn();

        // Fetch data
        $stmt = $db->prepare("
            SELECT id_kampus, nama_kampus, website, logo 
            FROM tb_kampus
            $where
            ORDER BY $orderColumn $orderDir
            LIMIT :start, :length
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':length', $length, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Response untuk DataTables
        Router::json(200, 'Success', $data, [
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        ]);
    break;


    case 'detail':

        if (!$id_kampus) {
            Router::json(400, 'id_kampus is required');
        }

        $stmt = $_SERVER['PDO']->prepare("SELECT id_kampus, nama_kampus, website, logo FROM tb_kampus WHERE id_kampus = :id LIMIT 1");
        $stmt->execute([':id' => $id_kampus]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            Router::json(404, 'Campus not found');
        }

        Router::json(200, 'Success', $row);
    break;

endswitch;