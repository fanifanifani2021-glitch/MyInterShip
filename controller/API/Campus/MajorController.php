<?php

switch ($method) :

    case 'list_prodi_kampus':
        if (!$id_kampus) {
            Router::json(400, 'id_kampus is required');
        }

        // cek apakah kampus ada
        $stmtKampus = $_SERVER['PDO']->prepare("
            SELECT COUNT(*) as total
            FROM tb_kampus
            WHERE id_kampus = :id_kampus
        ");
        $stmtKampus->execute(['id_kampus' => $id_kampus]);
        $kampusCount = (int)$stmtKampus->fetch(PDO::FETCH_ASSOC)['total'];

        if ($kampusCount === 0) {
            Router::json(404, 'Campus not found');
        }

        // ambil data jurusan
        $stmt = $_SERVER['PDO']->prepare("
            SELECT DISTINCT jurusan AS id, major AS en, major
            FROM program_study
            WHERE id_kampus = :id_kampus
            ORDER BY jurusan ASC
        ");
        $stmt->execute(['id_kampus' => $id_kampus]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Jika nanti mau filter DataTables
        $result = [
            'data' => $data,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
        ];

        Router::json(200, 'Success', $result);
    break;


    case 'list_prodi_jurusan':
        if (!$id_kampus) {
            Router::json(400, 'id_kampus is required');
        }

        if (!$jurusan) {
            Router::json(400, 'jurusan is required');
        }

        // cek apakah kampus ada
        $stmtKampus = $_SERVER['PDO']->prepare("
            SELECT COUNT(*) as total
            FROM tb_kampus
            WHERE id_kampus = :id_kampus
        ");
        $stmtKampus->execute(['id_kampus' => $id_kampus]);
        $kampusCount = (int)$stmtKampus->fetch(PDO::FETCH_ASSOC)['total'];

        if ($kampusCount === 0) {
            Router::json(404, 'Campus not found');
        }

        // ambil data program studi
        $stmt = $_SERVER['PDO']->prepare("
            SELECT kode_prodi AS id, study_program AS en, prodi, jurusan, major
    FROM program_study
    WHERE id_kampus = :id_kampus
      AND (jurusan LIKE :jurusan OR major LIKE :jurusan)
    ORDER BY study_program ASC
        ");

        $stmt->execute([
            'id_kampus' => $id_kampus,
            'jurusan'   => '%' . $jurusan . '%',
        ]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [
            'data' => $data,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
        ];

        Router::json(200, 'Success', $result);
    break;


endswitch;