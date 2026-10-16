<?php
defined('BASE_DIR') or die('Direct script access is Not Allowed');

switch ($method):

    default:
        http_response_code(404);
        break;

    case 'data':
            $id_kampus = $_POST['university'] ?? $_SESSION['CDC_Data']['id_kampus'];
        $columns = ['ps.kode_prodi', 'ps.jenjang', 'ps.prodi', 'ps.study_program', 'ps.jurusan', 'ps.major'];

        $limit  = $_POST['length'];
        $offset = $_POST['start'];
        $search = $_POST['search']['value'];

        // Jika program_study bernilai 'All', maka tidak terapkan filter id_kampus
        $isAll = ($id_kampus === 'All');

        $where = !$isAll ? "WHERE ps.id_kampus = :id_kampus" : "WHERE 1=1";

        if (!empty($search)) {
            $where .= " AND (
                    ps.kode_prodi LIKE :search OR
                    ps.jenjang LIKE :search OR
                    ps.prodi LIKE :search OR
                    ps.study_program LIKE :search OR
                    ps.jurusan LIKE :search OR
                    ps.major LIKE :search
                )";
        }

        // Total semua data tanpa filter
        $totalQuery = $isAll
            ? "SELECT COUNT(*) FROM program_study"
            : "SELECT COUNT(*) FROM program_study WHERE id_kampus = :id_kampus";
        $stmtTotal = $_SERVER['PDO']->prepare($totalQuery);
        if (!$isAll) {
            $stmtTotal->bindParam(':id_kampus', $id_kampus);
        }
        $stmtTotal->execute();
        $totalData = $stmtTotal->fetchColumn();

        // Total data setelah filter
        $filteredQuery = "
                SELECT COUNT(*)
                FROM program_study ps
                LEFT JOIN tb_kampus tk ON tk.id_kampus = ps.id_kampus
                $where
            ";
        $stmtFiltered = $_SERVER['PDO']->prepare($filteredQuery);
        if (!$isAll) {
            $stmtFiltered->bindParam(':id_kampus', $id_kampus);
        }
        if (!empty($search)) {
            $searchParam = "%$search%";
            $stmtFiltered->bindParam(':search', $searchParam, PDO::PARAM_STR);
        }
        $stmtFiltered->execute();
        $totalFiltered = $stmtFiltered->fetchColumn();

        // Ambil data dengan filter + limit
        $dataQuery = "
                SELECT ps.*, tk.nama_kampus
                FROM program_study ps
                LEFT JOIN tb_kampus tk ON tk.id_kampus = ps.id_kampus
                $where
                ORDER BY ps.major ASC
                LIMIT :offset, :limit
            ";
        $stmt = $_SERVER['PDO']->prepare($dataQuery);
        if (!$isAll) {
            $stmt->bindParam(':id_kampus', $id_kampus);
        }
        if (!empty($search)) {
            $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
        }
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $data = [];
        $no = $offset + 1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id_encoded = base64_encode(json_encode(['kode_prodi' => $row['kode_prodi'], 'id_kampus' => $row['id_kampus']]));
            $data[] = [
                'no' => $no++,
                'nama_kampus' => htmlspecialchars($row['nama_kampus']),
                'kode_prodi' => htmlspecialchars($row['kode_prodi']),
                'study_program' => htmlspecialchars($row['study_program']),
                'major' => htmlspecialchars($row['major']),
                'stats' => "<a class='btn btn-sm btn-info' target='_blank' href=\"" . base_url('index.php?page=stats_study_program&data=' . base64_encode(json_encode(['kode_prodi' => $row['kode_prodi'], 'id_kampus' => $row['id_kampus']]))) . "\"><i class='fas fa-chart-bar mr-2'></i>Stats</a>",
                'aksi' => "<div class='btn-group btn-round'>
                        <button type='button' class='btn btn-primary btn-sm dropdown-toggle' data-toggle='dropdown'>Action</button>
                        <div class='dropdown-menu'>
                            <button class='dropdown-item text-warning' onclick=\"window.location.href='" . base_url('cdc/study_program/edit/' . base64_encode(json_encode(['kode_prodi' => $row['kode_prodi'], 'id_kampus' => $row['id_kampus']]))) . "'\">
                                <i class='fas fa-edit mr-2'></i>Edit
                            </button>
                            <button class='dropdown-item text-danger' onclick=\"delete_data('$id_encoded')\">
                                <i class='fas fa-trash-alt mr-2'></i>Delete
                            </button>
                        </div>
                    </div>"
            ];
        }

        echo json_encode([
            'draw' => intval($_POST['draw']),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    break;

    case 'store':
        set_rules('kode_prodi', 'kode_prodi', ['required', 'is_unique:program_study.kode_prodi'], ['required' => 'This field is required !', 'is_unique' => 'Program Study Code already taken !']);
        set_rules('major_en', 'major', ['required'], ['required' => 'This field is required !', 'allowed_values' => 'This value is not allowed !']);
        set_rules('major_id', 'major', ['required'], ['required' => 'This field is required !', 'allowed_values' => 'This value is not allowed !']);
        set_rules('jenjang', 'jenjang', ['required', 'allowed_values:' . $allowed_jenjang], ['required' => 'This field is required !', 'allowed_values' => 'This value is not allowed !']);
        set_rules('prodi_en', 'prodi_en', ['required'], ['required' => 'This field is required !']);
        set_rules('prodi_id', 'prodi_id', ['required'], ['required' => 'This field is required !']);

        $cek_form = form_validation();

        if ($cek_form === false) {

            make_snackbar('Check your submitted data and try again later');
            redirect_back(1);
            exit;
        }

        try {
            $stmt = $_SERVER['PDO']->prepare("
                INSERT INTO program_study (
                    kode_prodi, jenjang, prodi, study_program, major, jurusan, id_kampus
                ) VALUES (
                    :kode_prodi, :jenjang, :prodi, :study_program, :major, :jurusan, :id_kampus
                )
            ");

            $stmt->execute([
                ':kode_prodi'     => $_POST['kode_prodi'],
                ':jenjang'        => $_POST['jenjang'],
                ':prodi'          => $_POST['prodi_id'],
                ':study_program'  => $_POST['prodi_en'],
                ':major'          => $_POST['major_en'],
                ':jurusan'        => $_POST['major_id'],
                ':id_kampus'      => $_POST['id_kampus'] ?? $_SESSION['CDC_Data']['id_kampus'] ?? '1',
            ]);

            reflash_all();
            make_swal2('success', 'Success', 'New Study Program has been added successfully !');
            // make_snackbar('New Study Program has been added successfully !');
            redirect( router('study_program.management'), 1);

            exit();
        } catch (PDOException $e) {
            error_log("Insert Error (program_study): " . $e->getMessage());
            make_snackbar('Check your submitted data and try again later');
            redirect_back(1);
            exit();
        }

    break;

endswitch;
