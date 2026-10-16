<?php
defined('BASE_DIR') or die('Direct script access is Not Allowed');

error_reporting(false); session_start();
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



if (!is_ajax_request()) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid request.']);
    exit;
}


switch ($method):

    default:
        http_response_code(404);
        break;

    case 'my_student_lecturer':
        $pdo = $_SERVER['PDO'];

        $start = $_POST['start'];
        $length = $_POST['length'];
        $search = $_POST['search']['value'];
        $programStudy = $_POST['study_program'] ?? '';
        $internshipStatus = $_POST['internship_status'] ?? '';
        $nik = $_SESSION['nim_nik_unit'];

        // Awal filter WHERE
        $where = "WHERE si.nik_dospem = :nik";

        if (!empty($programStudy) && $programStudy !== 'All') {
            if (strpos($programStudy, 'All-') === 0) {
                $where .= " AND si.program_study IN (SELECT kode_prodi FROM program_study WHERE major = :major)";
            } else {
                $where .= " AND si.program_study = :program_study";
            }
        }

        if (!empty($search)) {
            $where .= " AND (si.nim LIKE :search OR si.name LIKE :search)";
        }

        // Total filtered
        $sql_total = "SELECT COUNT(*) FROM student_internship si $where";
        $stmt = $pdo->prepare($sql_total);
        $stmt->bindParam(':nik', $nik);

        if (!empty($search)) {
            $searchVal = "%$search%";
            $stmt->bindParam(':search', $searchVal);
        }

        if (!empty($programStudy) && strpos($programStudy, 'All-') === 0) {
            $major = str_replace('All-', '', $programStudy);
            $stmt->bindParam(':major', $major);
        } elseif (!empty($programStudy) && $programStudy !== 'All') {
            $stmt->bindParam(':program_study', $programStudy);
        }

        $stmt->execute();
        $totalFiltered = $stmt->fetchColumn();

        // Query data utama
        $sql_data = "
                SELECT 
                    si.nim, 
                    si.name, 
                    ps.jenjang, 
                    ps.kode_prodi, 
                    ps.study_program,
                    (
                        SELECT status 
                        FROM internship 
                        WHERE internship.nim = si.nim 
                          AND status IN ('Yes', 'Finish', 'No') 
                        ORDER BY FIELD(status, 'Yes', 'Finish', 'No') ASC, id_internship DESC 
                        LIMIT 1
                    ) AS internship_status
                FROM student_internship si
                JOIN program_study ps ON si.program_study = ps.kode_prodi
                $where
                LIMIT :start, :length
            ";

        $stmt = $pdo->prepare($sql_data);
        $stmt->bindParam(':nik', $nik);

        if (!empty($search)) {
            $stmt->bindParam(':search', $searchVal);
        }

        if (!empty($programStudy) && strpos($programStudy, 'All-') === 0) {
            $stmt->bindParam(':major', $major);
        } elseif (!empty($programStudy) && $programStudy !== 'All') {
            $stmt->bindParam(':program_study', $programStudy);
        }

        $stmt->bindParam(':start', $start, PDO::PARAM_INT);
        $stmt->bindParam(':length', $length, PDO::PARAM_INT);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        $no = $start + 1;

        foreach ($students as $s) {
            $status = $s['internship_status'];

            if ($status === 'Yes') {
                $label = 'Internship in Progress';
                $btn = 'btn-success';
                $priority = 1;
            } elseif ($status === 'Finish') {
                $label = 'Internship Completed';
                $btn = 'btn-info';
                $priority = 3;
            } elseif ($status === 'No') {
                $label = 'Internship Rejected';
                $btn = 'btn-danger';
                $priority = 4;
            } elseif (!$status) {
                $label = 'Not Applied Yet';
                $btn = 'btn-secondary';
                $priority = 5;
            } else {
                $label = 'Waiting for Validation';
                $btn = 'btn-warning';
                $priority = 2;
            }

            if (!empty($internshipStatus) && $label !== $internshipStatus) {
                continue;
            }

            $data[] = [
                "no" => $no++,
                "nim" => $s['nim'],
                "name" => $s['name'],
                "internship_status" => $label,
                "internship_btn" => $btn,
                "kode_prodi" => $s['kode_prodi'],
                "jenjang" => $s['jenjang'],
                "prodi" => $s['prodi'],
                "study_program" => $s['study_program'],
                "priority" => $priority
            ];
        }

        // Urutkan berdasarkan prioritas
        usort($data, fn($a, $b) => $a['priority'] <=> $b['priority']);

        $response = [
            "draw" => intval($_POST['draw']),
            "recordsTotal" => $totalFiltered,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
    break;



    case 'university2_data_cdc':
        $koneksi = $_SERVER['KONEKSI_DB'];
        $sql = "SELECT * FROM tb_customer";
        $start = isset($_POST['start']) ? $_POST['start'] : 1;
        $length = isset($_POST['length']) ? $_POST['length'] : 10;
        $total_records = mysqli_num_rows(mysqli_query($koneksi, $sql));
        $column_order = array('id', 'name', 'email', 'phone', 'city', 'country');
        $search = "";
        $search_query = "";
        if (isset($_POST['search']) && $_POST['search']['value'] != '') {
            $search_val = $_POST['search']['value'];
            $where = " WHERE ( name LIKE '%$search_val%' OR email LIKE '%$search_val%' OR phone LIKE '%$search_val%' OR city LIKE '%$search_val%' OR country LIKE '%$search_val%' )";
            $sql .= $where;
        }

        $order = ' ORDER BY ';
        if (isset($_POST['order'])) {
            $order_column_index = $_POST['order'][0]['column'];
            $order_dir = $_POST['order'][0]['dir'];
            $order_column = $column_order[$order_column_index];

            $order .= " $order_column $order_dir";
        }
        $sql .= " $order LIMIT $start, $length";
        $result = mysqli_query($koneksi, $sql);
        $sql_count = "SELECT COUNT(*) AS count FROM tb_customer";
        if (isset($_POST['search']) && $_POST['search']['value'] != '') {
            $search_val = $_POST['search']['value'];
            $sql_count .= $where;
        }
        $result_count = mysqli_query($koneksi, $sql_count);
        $row_count = mysqli_fetch_assoc($result_count)['count'];
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = array('order' => $_POST['order'], "id" => $row['id'], "name" => $row['name'], "email" => $row['email'], "phone" => $row['phone'], "city" => $row['city'], "country" => $row['country'], "aksi" => '<button class="btn btn-primary btn-sm" onclick="editRow(\'' . encrypt($row['id']) . '\')">Edit</button> <button class="btn btn-danger btn-sm btn-delete" data-id="' . encrypt($row['id']) . '">Hapus</button>', 'draw' => intval($_POST['draw']), 'search_val' => $search_val);
        }
        $output = array('welcome_message' => $_SERVER['API_WELCOME'], "draw" => intval($_POST['draw']), "recordsTotal" => intval($total_records), "recordsFiltered" => intval($row_count), "sql" => $sql, "data" => $data);
        echo json_encode($output, JSON_PRETTY_PRINT);
        $conn->close();
    break;


    case 'university_data_cdc':

        $columns = ['id_kampus', 'nama_kampus'];

        $limit  = $_POST['length'];
        $offset = $_POST['start'];
        $search = $_POST['search']['value'];

        $where = '';
        if (!empty($search)) {
            $where = " WHERE id_kampus LIKE :search OR nama_kampus LIKE :search";
        }

        $totalQuery = "SELECT COUNT(*) FROM tb_kampus";
        $stmtTotal = $_SERVER['PDO']->query($totalQuery);
        $totalData = $stmtTotal->fetchColumn();

        // $query = "SELECT * FROM tb_kampus $where LIMIT :offset, :limit";

        $query = "SELECT tb_kampus.*, (SELECT COUNT(*) FROM student_internship WHERE student_internship.id_kampus = tb_kampus.id_kampus) AS jumlah_mahasiswa FROM tb_kampus $where LIMIT :offset, :limit";

        $stmt = $_SERVER['PDO']->prepare($query);

        if (!empty($search)) {
            $searchParam = "%$search%";
            $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
        }
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $data = [];
        $no = $offset + 1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id_encoded = base64_encode($row['id_kampus']);
            $nama_encoded = base64_encode($row['nama_kampus']);

            $data[] = [
                'no' => $no++,
                'id_kampus' => htmlspecialchars($row['id_kampus']),
                'nama_kampus' => htmlspecialchars($row['nama_kampus']),
                'jumlah_mahasiswa' => $row['jumlah_mahasiswa'] . " Student(s)", // tambahkan ini
                'aksi' => "<div class='btn-group btn-round'> <button type='button' class='btn btn-primary btn-sm dropdown-toggle' data-toggle='dropdown'>Action</button><div class='dropdown-menu'><button class='dropdown-item text-warning' onclick=\"edit_data('$id_encoded', '$nama_encoded')\"><i class='fas fa-edit mr-2'></i>Edit Data</button><button class='dropdown-item text-danger' onclick=\"delete_data('$id_encoded')\"><i class='fas fa-trash-alt mr-2'></i>Delete Data</button></div></div>"
            ];
        }

        echo json_encode([
            'draw' => intval($_POST['draw']),
            'recordsTotal' => $totalData,
            'recordsFiltered' => !empty($search) ? count($data) : $totalData,
            'data' => $data
        ]);
    break;


    case 'cdc_account_data_cdc':
        $columns = ['name', 'username', 'status', 'nama_kampus'];

        $limit  = $_POST['length'];
        $offset = $_POST['start'];
        $search = $_POST['search']['value'];
        $current_user = $_SESSION['username'];

        $where = " WHERE upkpk.username != :current_user ";
        if (!empty($search)) {
            $where .= " AND (
                upkpk.name LIKE :search OR
                upkpk.username LIKE :search OR
                upkpk.status LIKE :search OR
                tb_kampus.nama_kampus LIKE :search
            )";
        }

        // Total semua data tanpa filter
        $totalQuery = "SELECT COUNT(*) FROM upkpk WHERE username != :current_user";
        $stmtTotal = $_SERVER['PDO']->prepare($totalQuery);
        $stmtTotal->bindParam(':current_user', $current_user);
        $stmtTotal->execute();
        $totalData = $stmtTotal->fetchColumn();

        // Total data setelah filter
        $filteredQuery = "
            SELECT COUNT(*) 
            FROM upkpk 
            LEFT JOIN tb_kampus ON tb_kampus.id_kampus = upkpk.id_kampus 
            $where
        ";
        $stmtFiltered = $_SERVER['PDO']->prepare($filteredQuery);
        $stmtFiltered->bindParam(':current_user', $current_user);
        if (!empty($search)) {
            $searchParam = "%$search%";
            $stmtFiltered->bindParam(':search', $searchParam, PDO::PARAM_STR);
        }
        $stmtFiltered->execute();
        $totalFiltered = $stmtFiltered->fetchColumn();

        // Ambil data hasil filter + paginasi
        $dataQuery = "
            SELECT upkpk.*, tb_kampus.nama_kampus 
            FROM upkpk 
            LEFT JOIN tb_kampus ON tb_kampus.id_kampus = upkpk.id_kampus 
            $where 
            LIMIT :offset, :limit
        ";
        $stmt = $_SERVER['PDO']->prepare($dataQuery);
        $stmt->bindParam(':current_user', $current_user);
        if (!empty($search)) {
            $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
        }
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $data = [];
        $no = $offset + 1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id_encoded = base64_encode($row['id_upkpk']);

            $data[] = [
                'no' => $no++,
                'name' => htmlspecialchars($row['name']),
                'username' => htmlspecialchars($row['username']),
                'status' => htmlspecialchars($row['status']),
                'nama_kampus' => htmlspecialchars($row['nama_kampus']),
                'aksi' => "<div class='btn-group btn-round'>
                    <button type='button' class='btn btn-primary btn-sm dropdown-toggle' data-toggle='dropdown'>Action</button>
                    <div class='dropdown-menu'>
                        <button class='dropdown-item text-primary' onclick=\"reset_password('$id_encoded')\">
                            <i class='fas fa-key mr-2'></i>Reset Password</button>
                        <div class='dropdown-divider'></div>
                        <button class='dropdown-item text-warning' onclick=\"window.location.href = 'index.php?page=edit_cdc&id=$id_encoded'\">
                            <i class='fas fa-edit mr-2'></i>Edit Data</button>
                        <button class='dropdown-item text-danger' onclick=\"delete_data('$id_encoded')\">
                            <i class='fas fa-trash-alt mr-2'></i>Delete Data</button>
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

    case 'study_program_data_cdc':
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


    case 'lecturer_data_cdc':
        $pdo = $_SERVER['PDO'];

        $id_kampus = $_POST['university'];


        $columns = ['nim_nik_unit', 'name', 'status', 'is_koor'];
        $start = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 10;
        $search = $_POST['search']['value'] ?? '';
        $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
        $orderDir = $_POST['order'][0]['dir'] ?? 'asc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'nim_nik_unit';

        // Total records
        $totalQuery = $pdo->prepare("SELECT COUNT(*) FROM lecturer WHERE id_kampus = ?");
        $totalQuery->execute([$id_kampus]);
        $totalData = $totalQuery->fetchColumn();

        // Filtered data
        $sql = "SELECT * FROM lecturer WHERE id_kampus = ?";
        $params = [$id_kampus];

        if (!empty($search)) {
            $sql .= " AND (nim_nik_unit LIKE ? OR name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $totalFilteredQuery = $pdo->prepare(str_replace('*', 'COUNT(*)', $sql));
        $totalFilteredQuery->execute(array_slice($params, 0, count($params)));
        $totalFiltered = $totalFilteredQuery->fetchColumn();

        $sql .= " ORDER BY $orderColumn $orderDir LIMIT $start, $length";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format for DataTables
        $results = [];
        foreach ($data as $i => $row) {
            $role = $row['status'];
            $supervised = $row['is_koor'] == 1 ? 'Internship Coordinator' : 'Internship Supervisor';


            $results[] = [
                'no' => $start + $i + 1,
                'nik' => $row['nim_nik_unit'],
                'name' => $row['name'],
                'role' => $role,
                'supervised_status' => $supervised,
                'aksi' => '<div class="btn-group btn-round">
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <button class="dropdown-item" onclick="reset_password(\'' . base64_encode($row['nim_nik_unit']) . '\')"><i class="fas fa-key mr-2"></i>Reset Password</button>

                                    <a class="dropdown-item" href="index.php?page=edit_lecturer&nik=' . base64_encode($row['nim_nik_unit']) . '"><i class="fas fa-edit mr-2"></i>Edit Data</a>

                                    <button class="dropdown-item" onclick="swal_confirm(\'' . base64_encode($row['nim_nik_unit']) . '\')"><i class="fas fa-trash-alt mr-2"></i>Delete Data</button>
                                </div>
                            </div>',
            ];
        }

        echo json_encode([
            'draw' => intval($_POST['draw']),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $results
        ]);
    break;


    case 'student_internship_data_cdc':

        $isExport = ($_GET['action'] ?? '') === 'export';
        $format = $_GET['format'] ?? 'xlsx';

        $draw = $_POST['draw'] ?? 0;
        $start = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 10;
        $searchValue = $_POST['search']['value'] ?? '';

        $major = $_POST['major'] ?? 'All';
        $program_study = $_POST['program_study'] ?? 'All';
        $generation = $_POST['generation'] ?? 'All';
        $id_kampus = $_POST['id_kampus'] ?? $_SESSION['CDC_Data']['id_kampus'];

        $baseQuery = "FROM student_internship 
        INNER JOIN program_study ON program_study.kode_prodi = student_internship.program_study
        LEFT JOIN lecturer ON lecturer.nim_nik_unit = student_internship.nik_dospem 
        WHERE student_internship.id_kampus = :id_kampus";

        $params = [':id_kampus' => $id_kampus];

        if ($major !== 'All') {
            $baseQuery .= " AND program_study.major = :major";
            $params[':major'] = $major;
        }

        if ($program_study !== 'All') {
            if (str_starts_with($program_study, 'All-')) {
                $prodiMajor = substr($program_study, 4);
                $baseQuery .= " AND (program_study.major = :prodiMajor OR program_study.jurusan = :prodiMajor)";
                $params[':prodiMajor'] = $prodiMajor;
            } else {
                $baseQuery .= " AND program_study.kode_prodi = :kode_prodi";
                $params[':kode_prodi'] = $program_study;
            }
        }

        if ($generation !== 'All') {
            $baseQuery .= " AND SUBSTRING(student_internship.nim, 4, 2) = :generation";
            $params[':generation'] = $generation;
        }

        if (!empty($searchValue) && !$isExport) {
            $baseQuery .= " AND (student_internship.nim LIKE :search OR student_internship.name LIKE :search 
            OR student_internship.nik_dospem LIKE :search OR lecturer.name LIKE :search)";
            $params[':search'] = "%$searchValue%";
        }

        $sqlData = "SELECT student_internship.*, lecturer.name AS dosen, program_study.* $baseQuery ORDER BY student_internship.nim ASC";
        if (!$isExport) {
            $sqlData .= " LIMIT :start, :length";
        }

        $stmt = $_SERVER['PDO']->prepare($sqlData);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        if (!$isExport) {
            $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
            $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        }
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($isExport) {
            $stmtKampus = $_SERVER['PDO']->prepare("SELECT nama_kampus FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1");
            $stmtKampus->execute([':id_kampus' => $id_kampus]);
            $nama_kampus = $stmtKampus->fetchColumn() ?: 'Unknown Campus';

            $labelProdi = 'All Program Studi';
            $labelMajor = $major !== 'All' ? $major : 'All Major';
            $labelGen = $generation !== 'All' ? $generation : 'All Generation';

            if ($program_study !== 'All' && !str_starts_with($program_study, 'All-')) {
                $stmtProdi = $_SERVER['PDO']->prepare("SELECT CONCAT(jenjang, ' ', prodi) AS nama_prodi FROM program_study WHERE kode_prodi = :kode_prodi LIMIT 1");
                $stmtProdi->execute([':kode_prodi' => $program_study]);
                $labelProdi = $stmtProdi->fetchColumn() ?: $program_study;
            }

            $headers = ['NIM', 'Name', 'Program Study', 'Generation', 'Lecturer', 'Status', 'Internship Date'];
            $dataExport = [];

            foreach ($result as $row) {
                $angkatan = substr($row['nim'], 3, 2);
                $tahunAngkatan = '20' . $angkatan;

                $stmtInt = $_SERVER['PDO']->prepare("SELECT * FROM internship WHERE nim = :nim ORDER BY id_internship DESC LIMIT 1");
                $stmtInt->execute([':nim' => $row['nim']]);
                $intern = $stmtInt->fetch(PDO::FETCH_ASSOC);

                if (!$intern) {
                    $status = 'No Record';
                    $tanggal = '-';
                } else {
                    switch (strtoupper($intern['status'])) {
                        case 'WAITING':
                            $status = 'Currently Submitting';
                            $tanggal = '-';
                            break;
                        case 'YES':
                        case '1':
                            $status = 'Internship Accepted';
                            $tanggal = date('d-M-Y', strtotime($intern['start_date'])) . " to " . date('d-M-Y', strtotime($intern['end_date']));
                            break;
                        case 'NO':
                        case '0':
                            $status = 'Rejected by Company';
                            $tanggal = '-';
                            break;
                        default:
                            $status = 'Unknown';
                            $tanggal = '-';
                    }
                }

                $dataExport[] = [
                    $row['nim'],
                    $row['name'],
                    $row['jenjang'] . ' ' . $row['prodi'],
                    $tahunAngkatan,
                    $row['dosen'] ?? 'No Lecturer',
                    $status,
                    $tanggal
                ];
            }

            if ($format === 'csv') {
                header("Content-Type: text/csv");
                header("Content-Disposition: attachment; filename=student_internship_export.csv");
                $output = fopen("php://output", "w");
                fputcsv($output, ["Export Data Mahasiswa: $nama_kampus | Program Studi: $labelProdi | Major: $labelMajor | Generation: $labelGen"]);
                fputcsv($output, []);
                fputcsv($output, $headers);
                foreach ($dataExport as $row) {
                    fputcsv($output, $row);
                }
                fclose($output);
                exit;
            }

            if ($format === 'xlsx') {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('A1', "Export Data Mahasiswa: $nama_kampus | Program Studi: $labelProdi | Major: $labelMajor | Generation: $labelGen");
                $sheet->setCellValue('A2', '');
                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue($col . '3', $header);
                    $col++;
                }
                $rowIndex = 4;
                foreach ($dataExport as $row) {
                    $sheet->fromArray($row, null, 'A' . $rowIndex++);
                }
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="student_internship_export.xlsx"');
                header('Cache-Control: max-age=0');
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit;
            }

            http_response_code(400);
            echo "Invalid export format.";
            exit;
        }

        // Non-export (DataTables JSON)
        $countFiltered = $_SERVER['PDO']->prepare("SELECT COUNT(*) $baseQuery");
        $countFiltered->execute($params);
        $totalFiltered = $countFiltered->fetchColumn();

        $countTotal = $_SERVER['PDO']->prepare("SELECT COUNT(*) FROM student_internship WHERE id_kampus = :id_kampus");
        $countTotal->execute([':id_kampus' => $id_kampus]);
        $totalData = $countTotal->fetchColumn();

        $data = [];
        $no = $start + 1;

        foreach ($result as $row) {
            $badgeReset = '';
            if (!empty($row['password_reset']) && $row['password_reset'] == 1) {
                $badgeReset = ' <span class="badge badge-info" style="font-size: 10px;">Default Password</span>';
            }

            $angkatan = substr($row['nim'], 3, 2);
            $tahunAngkatan = '20' . $angkatan;

            // Ambil data internship terakhir
            $stmt = $_SERVER['PDO']->prepare("SELECT * FROM internship WHERE nim = :nim ORDER BY id_internship DESC LIMIT 1");
            $stmt->execute([':nim' => $row['nim']]);

            if ($stmt->rowCount() < 1) {
                $status = '<span class="badge badge-warning">No Record</span>';
                $tanggal = '-';
            } else {
                $intern = $stmt->fetch(PDO::FETCH_ASSOC);
                if (in_array($intern['status'], ['WAITING', 'Waiting'])) {
                    $status = 'Currently Submitting';
                    $tanggal = '-';
                } elseif (in_array($intern['status'], ['YES', 'Yes', 1])) {
                    $status = 'Internship Accepted';
                    $tanggal = date('d-M-Y', strtotime($intern['start_date'])) . " to " . date('d-M-Y', strtotime($intern['end_date']));
                } elseif (in_array($intern['status'], ['NO', 'No', 0])) {
                    $status = 'Rejected by Company';
                    $tanggal = '-';
                } else {
                    $status = '<span class="badge badge-warning">No Record</span>';
                    $tanggal = '-';
                }
            }

            // Ambil nama dospem jika ada
            $namaDosen = '<span class="badge badge-warning">Not Set</span>';
            if (!empty($row['nik_dospem']) && $row['nik_dospem'] !== '-' && $row['nik_dospem'] !== '0') {
                $stmtDosen = $_SERVER['PDO']->prepare("SELECT name FROM lecturer WHERE nim_nik_unit = :nik LIMIT 1");
                $stmtDosen->execute([':nik' => $row['nik_dospem']]);
                $fetchDosen = $stmtDosen->fetch(PDO::FETCH_ASSOC);

                if ($fetchDosen && !empty($fetchDosen['name'])) {
                    $namaDosen = htmlspecialchars($fetchDosen['name']);
                }
            }

            $nimEncoded = base64_encode($row['nim']);

            $data[] = [
                'no' => $no++,
                'program_study' => htmlspecialchars($row['jenjang'] . ' ' . $row['prodi']),
                'nim' => htmlspecialchars($row['nim']),
                'nama' => htmlspecialchars($row['name']) . $badgeReset,
                'dosen' => $namaDosen,
                'generation' => $tahunAngkatan,
                'status' => $status,
                'tanggal' => $tanggal,
                'aksi' => '<div class="btn-group btn-round">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                    <div class="dropdown-menu">
                        <button class="dropdown-item" onclick="window.location.href = \'index.php?page=student_detail_intern_co&nim=' . $nimEncoded . '\'">
                            <i class="fa fa-info-circle mr-1"></i> Detail
                        </button>
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item" onclick="window.location.href = \'index.php?page=edit_student_intern&nim=' . $nimEncoded . '\'">
                            <i class="fa fa-edit mr-1 text-warning"></i> Edit
                        </button>
                        <button class="dropdown-item" onclick="swal_confirm(\'' . $nimEncoded . '\')">
                            <i class="fa fa-trash mr-1 text-danger"></i> Delete
                        </button>
                        <button class="dropdown-item" onclick="assign_supervisor(\'' . $nimEncoded . '\', \'' . $row['nik_dospem'] . '\', \'' . $row['id_kampus'] . '\')">
                            <i class="fa fa-user mr-1"></i> Assign Lecturer
                        </button>

                        <button class="dropdown-item" onclick="reset_password(\'' . $nimEncoded . '\')">
                            <i class="fa fa-key mr-1"></i> Reset Password
                        </button>
                    </div>
                </div>'
            ];
        }


        echo json_encode([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data
        ]);
    break;
    
    case 'student_internship_data_coor':

        $isExport = ($_GET['action'] ?? '') === 'export';
        $format = $_GET['format'] ?? 'xlsx';

        $draw = $_POST['draw'] ?? 0;
        $start = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 10;
        $searchValue = $_POST['search']['value'] ?? '';

        $major = $_POST['major'] ?? 'All';
        $program_study = $_POST['program_study'] ?? 'All';
        $generation = $_POST['generation'] ?? 'All';
        $id_kampus = $_POST['id_kampus'] ?? $_SESSION['Lecturer-Data']['id_kampus'] ?? 1;

        $baseQuery = "FROM student_internship 
            INNER JOIN program_study ON program_study.kode_prodi = student_internship.program_study
            LEFT JOIN lecturer ON lecturer.nim_nik_unit = student_internship.nik_dospem 
            WHERE student_internship.id_kampus = :id_kampus";

        $params = [':id_kampus' => $id_kampus];

        if ($major !== 'All') {
            $baseQuery .= " AND program_study.major = :major";
            $params[':major'] = $major;
        }

        if ($program_study !== 'All') {
            if (str_starts_with($program_study, 'All-')) {
                $prodiMajor = substr($program_study, 4);
                $baseQuery .= " AND (program_study.major = :prodiMajor OR program_study.jurusan = :prodiMajor)";
                $params[':prodiMajor'] = $prodiMajor;
            } else {
                $baseQuery .= " AND program_study.kode_prodi = :kode_prodi";
                $params[':kode_prodi'] = $program_study;
            }
        }

        if ($generation !== 'All') {
            $baseQuery .= " AND SUBSTRING(student_internship.nim, 4, 2) = :generation";
            $params[':generation'] = $generation;
        }

        if (!empty($searchValue) && !$isExport) {
            $baseQuery .= " AND (student_internship.nim LIKE :search OR student_internship.name LIKE :search 
                OR student_internship.nik_dospem LIKE :search OR lecturer.name LIKE :search)";
            $params[':search'] = "%$searchValue%";
        }

        $sqlData = "SELECT student_internship.*, lecturer.name AS dosen, program_study.* $baseQuery ORDER BY student_internship.nim ASC";
        if (!$isExport) {
            $sqlData .= " LIMIT :start, :length";
        }

        $stmt = $_SERVER['PDO']->prepare($sqlData);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        if (!$isExport) {
            $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
            $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        }
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($isExport) {
            $stmtKampus = $_SERVER['PDO']->prepare("SELECT nama_kampus FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1");
            $stmtKampus->execute([':id_kampus' => $id_kampus]);
            $nama_kampus = $stmtKampus->fetchColumn() ?: 'Unknown Campus';

            $labelProdi = 'All Program Studi';
            $labelMajor = $major !== 'All' ? $major : 'All Major';
            $labelGen = $generation !== 'All' ? $generation : 'All Generation';

            if ($program_study !== 'All' && !str_starts_with($program_study, 'All-')) {
                $stmtProdi = $_SERVER['PDO']->prepare("SELECT CONCAT(jenjang, ' ', prodi) AS nama_prodi FROM program_study WHERE kode_prodi = :kode_prodi LIMIT 1");
                $stmtProdi->execute([':kode_prodi' => $program_study]);
                $labelProdi = $stmtProdi->fetchColumn() ?: $program_study;
            }

            $headers = ['NIM', 'Name', 'Program Study', 'Generation', 'Lecturer', 'Status', 'Internship Date'];
            $dataExport = [];

            foreach ($result as $row) {
                $angkatan = substr($row['nim'], 3, 2);
                $tahunAngkatan = '20' . $angkatan;

                $stmtInt = $_SERVER['PDO']->prepare("SELECT * FROM internship WHERE nim = :nim ORDER BY id_internship DESC LIMIT 1");
                $stmtInt->execute([':nim' => $row['nim']]);
                $intern = $stmtInt->fetch(PDO::FETCH_ASSOC);

                if (!$intern) {
                    $status = 'No Record';
                    $tanggal = '-';
                } else {
                    switch (strtoupper($intern['status'])) {
                        case 'WAITING':
                            $status = 'Currently Submitting';
                            $tanggal = '-';
                            break;
                        case 'YES':
                        case '1':
                            $status = 'Internship Accepted';
                            $tanggal = date('d-M-Y', strtotime($intern['start_date'])) . " to " . date('d-M-Y', strtotime($intern['end_date']));
                            break;
                        case 'NO':
                        case '0':
                            $status = 'Rejected by Company';
                            $tanggal = '-';
                            break;
                        default:
                            $status = 'Unknown';
                            $tanggal = '-';
                    }
                }

                $dataExport[] = [
                    $row['nim'],
                    $row['name'],
                    $row['jenjang'] . ' ' . $row['prodi'],
                    $tahunAngkatan,
                    $row['dosen'] ?? 'No Lecturer',
                    $status,
                    $tanggal
                ];
            }

            if ($format === 'csv') {
                header("Content-Type: text/csv");
                header("Content-Disposition: attachment; filename=student_internship_export.csv");
                $output = fopen("php://output", "w");
                fputcsv($output, ["Export Data Mahasiswa: $nama_kampus | Program Studi: $labelProdi | Major: $labelMajor | Generation: $labelGen"]);
                fputcsv($output, []);
                fputcsv($output, $headers);
                foreach ($dataExport as $row) {
                    fputcsv($output, $row);
                }
                fclose($output);
                exit;
            }

            if ($format === 'xlsx') {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('A1', "Export Data Mahasiswa: $nama_kampus | Program Studi: $labelProdi | Major: $labelMajor | Generation: $labelGen");
                $sheet->setCellValue('A2', '');
                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue($col . '3', $header);
                    $col++;
                }
                $rowIndex = 4;
                foreach ($dataExport as $row) {
                    $sheet->fromArray($row, null, 'A' . $rowIndex++);
                }
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="student_internship_export.xlsx"');
                header('Cache-Control: max-age=0');
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit;
            }

            http_response_code(400);
            echo "Invalid export format.";
            exit;
        }

        // Non-export (DataTables JSON)
        $countFiltered = $_SERVER['PDO']->prepare("SELECT COUNT(*) $baseQuery");
        $countFiltered->execute($params);
        $totalFiltered = $countFiltered->fetchColumn();

        $countTotal = $_SERVER['PDO']->prepare("SELECT COUNT(*) FROM student_internship WHERE id_kampus = :id_kampus");
        $countTotal->execute([':id_kampus' => $id_kampus]);
        $totalData = $countTotal->fetchColumn();

        $data = [];
        $no = $start + 1;

        foreach ($result as $row) {
            $badgeReset = '';
            if (!empty($row['password_reset']) && $row['password_reset'] == 1) {
                $badgeReset = ' <span class="badge badge-info" style="font-size: 10px;">Default Password</span>';
            }

            $angkatan = substr($row['nim'], 3, 2);
            $tahunAngkatan = '20' . $angkatan;

            // Ambil data internship terakhir
            $stmt = $_SERVER['PDO']->prepare("SELECT * FROM internship WHERE nim = :nim ORDER BY id_internship DESC LIMIT 1");
            $stmt->execute([':nim' => $row['nim']]);

            if ($stmt->rowCount() < 1) {
                $status = '<span class="badge badge-warning">No Record</span>';
                $tanggal = '-';
            } else {
                $intern = $stmt->fetch(PDO::FETCH_ASSOC);
                if (in_array($intern['status'], ['WAITING', 'Waiting'])) {
                    $status = 'Currently Submitting';
                    $tanggal = '-';
                } elseif (in_array($intern['status'], ['YES', 'Yes', 1])) {
                    $status = 'Internship Accepted';
                    $tanggal = date('d-M-Y', strtotime($intern['start_date'])) . " to " . date('d-M-Y', strtotime($intern['end_date']));
                } elseif (in_array($intern['status'], ['NO', 'No', 0])) {
                    $status = 'Rejected by Company';
                    $tanggal = '-';
                } else {
                    $status = '<span class="badge badge-warning">No Record</span>';
                    $tanggal = '-';
                }
            }

            // Ambil nama dospem jika ada
            $namaDosen = '<span class="badge badge-warning">Not Set</span>';
            if (!empty($row['nik_dospem']) && $row['nik_dospem'] !== '-' && $row['nik_dospem'] !== '0') {
                $stmtDosen = $_SERVER['PDO']->prepare("SELECT name FROM lecturer WHERE nim_nik_unit = :nik LIMIT 1");
                $stmtDosen->execute([':nik' => $row['nik_dospem']]);
                $fetchDosen = $stmtDosen->fetch(PDO::FETCH_ASSOC);

                if ($fetchDosen && !empty($fetchDosen['name'])) {
                    $namaDosen = htmlspecialchars($fetchDosen['name']);
                }
            }

            $nimEncoded = base64_encode($row['nim']);


            $nimEncoded = base64_encode($row['nim']);
            $prodiKoor = $_SESSION['Lecturer-Data']['prodi_koor'] ?? '';
            $kodeProdi = $row['kode_prodi'] ?? '';
            $nikDospem = $row['nik_dospem'] ?? '';
            $idKampus = $row['id_kampus'] ?? '';

            $aksi = '
            <div class="btn-group btn-round">
                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action
                </button>
                <div class="dropdown-menu">
                    <button class="dropdown-item" onclick="window.location.href = \'index.php?page=student_intern_detail&nim=' . $nimEncoded . '\'">
                        <i class="fa fa-info-circle mr-1"></i> Detail
                    </button>
                    <div class="dropdown-divider"></div>';

                    if ($prodiKoor === $kodeProdi) {
                        $aksi .= '
                    <button class="dropdown-item text-warning" onclick="window.location.href = \'index.php?page=edit_student_intern&nim=' . $nimEncoded . '\'">
                        <i class="fa fa-edit mr-1"></i> Edit
                    </button>
                    <button class="dropdown-item text-danger" onclick="swal_confirm(\'' . $nimEncoded . '\')">
                        <i class="fa fa-trash mr-1"></i> Delete
                    </button>';
                    }

                    $aksi .= '
                    <button class="dropdown-item" onclick="assign_supervisor(\'' . $nimEncoded . '\', \'' . $nikDospem . '\', \'' . $idKampus . '\')">
                        <i class="fa fa-user mr-1"></i> Assign Lecturer
                    </button>
                    <button class="dropdown-item" onclick="reset_password(\'' . $nimEncoded . '\')">
                        <i class="fa fa-key mr-1"></i> Reset Password
                    </button>
                </div>
            </div>';


            $data[] = [
                'no' => $no++,
                'program_study' => htmlspecialchars($row['jenjang'] . ' ' . $row['prodi']),
                'nim' => htmlspecialchars($row['nim']),
                'nama' => htmlspecialchars($row['name']) . $badgeReset,
                'dosen' => $namaDosen,
                'generation' => $tahunAngkatan,
                'status' => $status,
                'tanggal' => $tanggal,
                'aksi' => $aksi
            ];
        }


        echo json_encode([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data
        ]);
    break;

    case 'my_internship_letter':
        $pdo = $_SERVER['PDO'];

        // Ambil parameter dari DataTables (aman)
        $start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $search = $_POST['search']['value'] ?? '';
        $statusFilter = $_POST['status'] ?? ''; // filter status surat
        $langFilter   = $_POST['language'] ?? ''; // filter bahasa
        $classFilter  = $_POST['class'] ?? ''; // filter kelas
        $semesterFilter = $_POST['semester'] ?? ''; // filter semester
        $nim = $_SESSION['nim'] ?? '';

        // ===== Awal query base =====
        $where = "WHERE il.student_nim = :nim";

        // ===== Filter dinamis =====
        if (!empty($statusFilter) && $statusFilter !== 'All') {
            $where .= " AND il.status = :status";
        }

        if (!empty($langFilter) && $langFilter !== 'All') {
            $where .= " AND il.letter_languange = :language";
        }

        if (!empty($classFilter) && $classFilter !== 'All') {
            $where .= " AND il.class = :class";
        }

        if (!empty($semesterFilter) && $semesterFilter !== 'All') {
            $where .= " AND il.semester = :semester";
        }

        if (!empty($search)) {
            $where .= " AND (il.student_nim LIKE :search OR s.name LIKE :search OR c.name LIKE :search)";
            $searchVal = "%{$search}%";
        }

        // ===== Hitung total data (filtered) =====
        $sql_total = "SELECT COUNT(*) 
                    FROM internship_letter il
                    JOIN student_internship s ON il.student_nim = s.nim
                    LEFT JOIN company c ON il.id_company = c.id_company
                    $where";

        $stmt = $pdo->prepare($sql_total);

        // Bind yang diperlukan
        $stmt->bindValue(':nim', $nim);

        if (!empty($statusFilter) && $statusFilter !== 'All') {
            $stmt->bindValue(':status', $statusFilter);
        }
        if (!empty($langFilter) && $langFilter !== 'All') {
            $stmt->bindValue(':language', $langFilter);
        }
        if (!empty($classFilter) && $classFilter !== 'All') {
            $stmt->bindValue(':class', $classFilter);
        }
        if (!empty($semesterFilter) && $semesterFilter !== 'All') {
            $stmt->bindValue(':semester', intval($semesterFilter), PDO::PARAM_INT);
        }
        if (!empty($search)) {
            $stmt->bindValue(':search', $searchVal);
        }

        $stmt->execute();
        $totalFiltered = (int) $stmt->fetchColumn();

        // ===== Query data utama =====
        $sql_data = "
            SELECT 
                il.id_internship_letter,
                il.student_nim,
                s.name AS student_name,
                ps.study_program,
                ps.jenjang,
                ps.jurusan,
                il.class,
                il.semester,
                il.letter_languange,
                il.status,
                il.coor_approval,
                il.cdc_approval,
                il.expected_start,
                il.expected_end,
                il.created_at,
                il.data_hash,
                il.acceptance_status,
                c.name AS company_name
            FROM internship_letter il
            JOIN student_internship s ON il.student_nim = s.nim
            JOIN program_study ps ON s.program_study = ps.kode_prodi
            LEFT JOIN company c ON il.id_company = c.id_company
            $where
            ORDER BY il.created_at DESC, il.id_internship_letter DESC
            LIMIT :start, :length
        ";

        $stmt = $pdo->prepare($sql_data);

        // Bind ulang untuk query data
        $stmt->bindValue(':nim', $nim);

        if (!empty($statusFilter) && $statusFilter !== 'All') {
            $stmt->bindValue(':status', $statusFilter);
        }
        if (!empty($langFilter) && $langFilter !== 'All') {
            $stmt->bindValue(':language', $langFilter);
        }
        if (!empty($classFilter) && $classFilter !== 'All') {
            $stmt->bindValue(':class', $classFilter);
        }
        if (!empty($semesterFilter) && $semesterFilter !== 'All') {
            $stmt->bindValue(':semester', intval($semesterFilter), PDO::PARAM_INT);
        }
        if (!empty($search)) {
            $stmt->bindValue(':search', $searchVal);
        }

        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':length', $length, PDO::PARAM_INT);
        $stmt->execute();

        $letters = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ===== Format data untuk DataTables =====
        $data = [];
        $no = $start + 1;

        foreach ($letters as $l) {
            // status badge (compatibel PHP7+)
            switch ($l['status']) {
                case 'WAITING':
                    $statusBadge = '<span class="badge badge-warning">Waiting</span>';
                    break;
                case 'APPROVED':
                    $statusBadge = '<span class="badge badge-success">Approved</span>';
                    break;
                case 'REJECTED':
                    $statusBadge = '<span class="badge badge-danger">Rejected</span>';
                    break;
                default:
                    $statusBadge = '<span class="badge badge-secondary">Unknown</span>';
                    break;
            }

            // approval badges
            $coorApproval = '<span class="badge badge-secondary">Pending</span>';
            switch ($l['coor_approval']) {
                case 'approved':
                    $coorApproval = '<span class="badge badge-success">Approved</span>';
                    break;
                case 'waiting':
                    $coorApproval = '<span class="badge badge-warning">Waiting</span>';
                    break;
                case 'rejected':
                    $coorApproval = '<span class="badge badge-danger">Rejected</span>';
                    break;
            }

            $cdcApproval = '<span class="badge badge-secondary">Pending</span>';
            switch ($l['cdc_approval']) {
                case 'approved':
                    $cdcApproval = '<span class="badge badge-success">Approved</span>';
                    break;
                case 'waiting':
                    $cdcApproval = '<span class="badge badge-warning">Waiting</span>';
                    break;
                case 'rejected':
                    $cdcApproval = '<span class="badge badge-danger">Rejected</span>';
                    break;
            }

            $languageLabel = ($l['letter_languange'] === 'EN') ? 'English' : 'Bahasa Indonesia';
            $periode = '';
            if (!empty($l['expected_start']) && !empty($l['expected_end'])) {
                $periode = date('M d, Y', strtotime($l['expected_start'])) . ' - ' . date('M d, Y', strtotime($l['expected_end']));
            }

            // ACCEPTANCE STATUS
            $acceptance_status = '-';

            // Hanya menampilkan jika surat sudah APPROVED
            if ($l['status'] === 'APPROVED') {

                if (empty($l['acceptance_status'])) {
                    $acceptance_status = '<span class="badge badge-warning">Waiting Response</span>';
                } else {
                    switch ($l['acceptance_status']) {
                        case 'accepted':
                            $acceptance_status = '<span class="badge badge-success">Accepted</span>';
                            break;

                        case 'rejected':
                            $acceptance_status = '<span class="badge badge-danger">Rejected</span>';
                            break;

                        default:
                            $acceptance_status = '<span class="badge badge-secondary">Unknown</span>';
                            break;
                    }
                }

            } else {
                // Selain APPROVED → tidak ada acceptance status
                $acceptance_status = '<span class="badge badge-secondary">No Status</span>';
            }

            $data[] = [
                'no' => $no++,
                'id_internship_letter' => $l['id_internship_letter'],
                'nim' => $l['student_nim'],
                'student_name' => $l['student_name'],
                'program' => trim($l['jenjang'] . ' ' . ($l['study_program'] ?? '')),
                'jurusan' => $l['jurusan'],
                'company_name' => $l['company_name'] ?? '-',
                'acceptance_status' => $acceptance_status,
                'class' => $l['class'],
                'semester' => $l['semester'],
                'language' => $languageLabel,
                'status' => $statusBadge,
                'coor_approval' => $coorApproval,
                'cdc_approval' => $cdcApproval,
                'periode' => $periode,
                'created_at' => !empty($l['created_at']) ? date('d M Y', strtotime($l['created_at'])) : '-',
                'actions' => '<button class="btn btn-sm btn-outline-primary view-letter" data-id="' . $l['id_internship_letter'] . '">Detail</button>'
            ];
        }

        // ===== Kirim response JSON =====
        $response = [
            "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
            "recordsTotal" => $totalFiltered,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    break;

    case 'internship_letter_coordinator':

        $pdo = $_SERVER['PDO'];

        // --- Ambil filter dari request (POST untuk AJAX/export) ---
        $cdcStatus    = isset($_POST['cdc_status']) ? $_POST['cdc_status'] : 'All';
        $coorStatus   = isset($_POST['coor_status']) ? $_POST['coor_status'] : 'All';
        $letterStatus = isset($_POST['letter_status']) ? $_POST['letter_status'] : 'All';
        $internStatus = isset($_POST['internship_status']) ? $_POST['internship_status'] : 'All';
        $prodiFilter  = isset($_POST['program_study']) ? $_POST['program_study'] : ''; // "", "All", "PS:xxx", "Major:xxx"

        $prodiKoor = isset($_SESSION['prodi_koor']) ? $_SESSION['prodi_koor'] : '';

        $generation = isset($_POST['generation']) ? trim($_POST['generation']) : 'All';

        $from_date = !empty($_POST['from_date']) ? trim($_POST['from_date']) : null;
        $to_date   = !empty($_POST['to_date'])   ? trim($_POST['to_date'])   : null;


        //
        // Helper: bangun where & params
        //
        $whereClauses = [];
        $params = [];

        // Program study filter behavior:
        // - '' or null => default to prodi_koor
        // - 'All' => no filter at all
        // - 'PS:xxxx' => specific kode_prodi
        // - 'Major:Name' => ps.major = Name
        if ($prodiFilter === '' || $prodiFilter === null) {
            $whereClauses[] = "s.program_study = :prodi_koor";
            $params[':prodi_koor'] = $prodiKoor;
        } elseif ($prodiFilter === 'All') {
            // no clause
        } elseif (strpos($prodiFilter, 'PS:') === 0) {
            $kodeProdi = substr($prodiFilter, 3);
            $whereClauses[] = "s.program_study = :prodi_selected";
            $params[':prodi_selected'] = $kodeProdi;
        } elseif (strpos($prodiFilter, 'Major:') === 0) {
            $majorSelected = substr($prodiFilter, 6);
            $whereClauses[] = "ps.major = :major_selected";
            $params[':major_selected'] = $majorSelected;
        }

        if ($generation !== 'All' && $generation !== '') {
            $whereClauses[] = "SUBSTRING(s.nim, 4, 2) = :generation";
            $params[':generation'] = $generation;
        }


        if ($from_date === null && $to_date === null) {
            // No date filter
        }// 2) Jika hanya from_date (start_date)
        elseif ($from_date !== null && $to_date === null) {
            $whereClauses[] = "il.created_at >= :from_date";
            $params['from_date'] = $from_date;
        }

        // 3) Jika dua-duanya ada (between)
        elseif ($from_date !== null && $to_date !== null) {
            $whereClauses[] = "il.created_at BETWEEN :from_date AND :to_date";
            $params['from_date'] = $from_date;
            $params['to_date']   = $to_date;
        }



        // Status filters (normalize to compare safely)
        if ($cdcStatus !== 'All') {
            // compare lowercase
            $whereClauses[] = "LOWER(il.cdc_approval) = :cdc_status";
            $params[':cdc_status'] = strtolower($cdcStatus);
        }
        if ($coorStatus !== 'All') {
            $whereClauses[] = "LOWER(il.coor_approval) = :coor_status";
            $params[':coor_status'] = strtolower($coorStatus);
        }
        if ($letterStatus !== 'All') {
            // il.status seems to be enums like 'WAITING','APPROVED' -> compare uppercase
            $whereClauses[] = "UPPER(il.status) = :letter_status";
            $params[':letter_status'] = strtoupper($letterStatus);
        }
        if ($internStatus !== 'All') {
            $whereClauses[] = "LOWER(il.acceptance_status) = :intern_status";
            $params[':intern_status'] = strtolower($internStatus);
        }

        // Search (DataTables)
        $search = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
        if ($search !== '') {
            $whereClauses[] = "(s.name LIKE :search OR s.nim LIKE :search OR c.name LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        // Combine where
        $where = '';
        if (!empty($whereClauses)) {
            $where = ' WHERE ' . implode(' AND ', $whereClauses);
        }

        // ===== EXPORT FLOW (no limit) =====
        if (isset($_GET['action']) && $_GET['action'] === 'export') {
            try {
                // Query export (no limit)
                $sql_export = "
                    SELECT 
                        il.id_internship_letter,
                        il.student_nim,
                        s.name AS student_name,
                        COALESCE(c.name, '-') AS company_name,
                        il.status,
                        il.coor_approval,
                        il.cdc_approval,
                        il.acceptance_status,
                        il.letter_languange,
                        il.expected_start,
                        il.expected_end,
                        il.created_at,
                        ps.jenjang,
                        ps.study_program,
                        ps.jurusan,
                        ps.major
                    FROM internship_letter il
                    JOIN student_internship s ON il.student_nim = s.nim
                    JOIN program_study ps ON s.program_study = ps.kode_prodi
                    LEFT JOIN company c ON il.id_company = c.id_company
                    $where
                    ORDER BY il.created_at DESC
                ";

                $stmt = $pdo->prepare($sql_export);

                // Bind all params
                foreach ($params as $k => $v) {
                    // detect param type
                    $type = PDO::PARAM_STR;
                    $stmt->bindValue($k, $v, $type);
                }

                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // PhpSpreadsheet usage (pastikan sudah require/autoload sebelumnya)
                $xls = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $xls->getActiveSheet();

                // Header row
                $headers = [
                    "No", "NIM", "Student", "Company", "Letter Status",
                    "Coordinator", "CDC", "Acceptance", "Language",
                    "Start", "End", "Created", "Jenjang", "Program Study", "Jurusan", "Major"
                ];
                $sheet->fromArray($headers, null, 'A1');

                // Fill data
                $r = 2;
                foreach ($rows as $i => $row) {
                    $sheet->fromArray([
                        $i + 1,
                        $row['student_nim'],
                        $row['student_name'],
                        $row['company_name'],
                        $row['status'],
                        $row['coor_approval'],
                        $row['cdc_approval'],
                        $row['acceptance_status'],
                        $row['letter_languange'],
                        $row['expected_start'],
                        $row['expected_end'],
                        $row['created_at'],
                        $row['jenjang'],
                        $row['study_program'],
                        $row['jurusan'],
                        $row['major']
                    ], null, "A$r");
                    $r++;
                }

                // Output headers
                $filename = "internship_letter_export_" . date('Ymd_His') . ".xlsx";
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: max-age=0');

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($xls);
                $writer->save('php://output');
                exit;

            } catch (Exception $e) {
                // Jika error export, tampilkan pesan teks (agar AJAX bisa debug)
                http_response_code(500);
                echo "Export error: " . $e->getMessage();
                exit;
            }
        }

        // ===== DATATABLES MODE (serverSide) =====

        // DataTables parameters
        $draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
        $start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;

        // 1) recordsTotal: total rows respecting the default prodi restriction (so UI sees correct total)
        // Build base WHERE for total: include only program constraint default (if prodiFilter blank) otherwise no filters
        $baseWhereClauses = [];
        $baseParams = [];
        if ($prodiFilter === '' || $prodiFilter === null) {
            $baseWhereClauses[] = "s.program_study = :prodi_koor";
            $baseParams[':prodi_koor'] = $prodiKoor;
        } elseif ($prodiFilter === 'All') {
            // no base filter
        } elseif (strpos($prodiFilter, 'PS:') === 0) {
            $baseWhereClauses[] = "s.program_study = :prodi_selected";
            $baseParams[':prodi_selected'] = $kodeProdi; // $kodeProdi set earlier if present
        } elseif (strpos($prodiFilter, 'Major:') === 0) {
            $majorSelected = substr($prodiFilter, 6);
            $baseWhereClauses[] = "ps.major = :major_selected";
            $baseParams[':major_selected'] = $majorSelected;
        }

        if ($generation !== 'All' && $generation !== '') {
            $baseWhereClauses[] = "SUBSTRING(s.nim, 4, 2) = :generation";
            $baseParams[':generation'] = $generation;
        }

        if ($from_date === null && $to_date === null) {
            // No date filter
        }// 2) Jika hanya from_date (start_date)
        elseif ($from_date !== null && $to_date === null) {
            $baseWhereClauses[] = "il.created_at >= :from_date";
            $baseParams['from_date'] = $from_date;
        }

        // 3) Jika dua-duanya ada (between)
        elseif ($from_date !== null && $to_date !== null) {
            $baseWhereClauses[] = "il.created_at BETWEEN :from_date AND :to_date";
            $baseParams['from_date'] = $from_date;
            $baseParams['to_date']   = $to_date;
        }




        $baseWhere = '';
        if (!empty($baseWhereClauses)) {
            $baseWhere = ' WHERE ' . implode(' AND ', $baseWhereClauses);
        }

        $sql_total_all = "
            SELECT COUNT(*) FROM internship_letter il
            JOIN student_internship s ON il.student_nim = s.nim
            JOIN program_study ps ON s.program_study = ps.kode_prodi
            LEFT JOIN company c ON il.id_company = c.id_company
            $baseWhere
        ";
        $stmtTotalAll = $pdo->prepare($sql_total_all);
        foreach ($baseParams as $k => $v) {
            $stmtTotalAll->bindValue($k, $v);
        }
        $stmtTotalAll->execute();
        $recordsTotal = (int) $stmtTotalAll->fetchColumn();

        // 2) recordsFiltered: count with all filters & search (use $where + $params built earlier)
        $sql_count_filtered = "
            SELECT COUNT(*) FROM internship_letter il
            JOIN student_internship s ON il.student_nim = s.nim
            JOIN program_study ps ON s.program_study = ps.kode_prodi
            LEFT JOIN company c ON il.id_company = c.id_company
            $where
        ";
        $stmtCount = $pdo->prepare($sql_count_filtered);
        foreach ($params as $k => $v) {
            $stmtCount->bindValue($k, $v);
        }
        $stmtCount->execute();
        $recordsFiltered = (int) $stmtCount->fetchColumn();

        // 3) data query with limit
        $sql_data = "
            SELECT 
                il.id_internship_letter,
                il.student_nim,
                s.name AS student_name,
                COALESCE(c.name, '-') AS company_name,
                il.status,
                il.coor_approval,
                il.cdc_approval,
                il.acceptance_status,
                il.letter_languange,
                il.expected_start,
                il.expected_end,
                il.created_at,
                ps.jenjang,
                ps.study_program,
                ps.jurusan,
                ps.major
            FROM internship_letter il
            JOIN student_internship s ON il.student_nim = s.nim
            JOIN program_study ps ON s.program_study = ps.kode_prodi
            LEFT JOIN company c ON il.id_company = c.id_company
            $where
            ORDER BY il.created_at DESC, il.id_internship_letter DESC
            LIMIT :start, :length
        ";
        $stmtData = $pdo->prepare($sql_data);

        // bind params for data
        foreach ($params as $k => $v) {
            $stmtData->bindValue($k, $v);
        }
        // bind start/length
        $stmtData->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmtData->bindValue(':length', (int)$length, PDO::PARAM_INT);

        $stmtData->execute();
        $rows = $stmtData->fetchAll(PDO::FETCH_ASSOC);

        // format rows for DataTables
        $dataArr = [];
        $no = $start + 1;
        foreach ($rows as $r) {
            // formatted badges/text (you can adapt to your front-end)
            $statusLabel = $r['status'];
            $coorLabel = $r['coor_approval'];
            $cdcLabel = $r['cdc_approval'];
            $acceptLabel = $r['acceptance_status'];

            // create visual badges (server returns HTML fragments)
            switch (strtoupper($statusLabel)) {
                case 'WAITING':
                    $statusBadge = '<span class="badge badge-warning">Waiting</span>';
                    break;
                case 'APPROVED':
                    $statusBadge = '<span class="badge badge-success">Approved</span>';
                    break;
                case 'REJECTED':
                    $statusBadge = '<span class="badge badge-danger">Rejected</span>';
                    break;
                default:
                    $statusBadge = '<span class="badge badge-secondary">'.htmlspecialchars($statusLabel).'</span>';
            }

            // coor
            $coorBadge = '<span class="badge badge-secondary">Pending</span>';
            if ($coorLabel === 'approved') $coorBadge = '<span class="badge badge-success">Approved</span>';
            elseif ($coorLabel === 'rejected') $coorBadge = '<span class="badge badge-danger">Rejected</span>';
            elseif ($coorLabel === 'waiting') $coorBadge = '<span class="badge badge-warning text-dark">Waiting</span>';

            // cdc
            $cdcBadge = '<span class="badge badge-secondary">Pending</span>';
            if ($cdcLabel === 'approved') $cdcBadge = '<span class="badge badge-success">Approved</span>';
            elseif ($cdcLabel === 'rejected') $cdcBadge = '<span class="badge badge-danger">Rejected</span>';
            elseif ($cdcLabel === 'waiting') $cdcBadge = '<span class="badge badge-warning text-dark">Waiting</span>';

            // acceptance
            if (strtoupper($r['status']) === 'APPROVED') {
                if (empty($r['acceptance_status'])) {
                    $acceptanceBadge = '<span class="badge badge-warning">Waiting Response</span>';
                } else {
                    if ($r['acceptance_status'] === 'accepted') $acceptanceBadge = '<span class="badge badge-success">Accepted</span>';
                    elseif ($r['acceptance_status'] === 'rejected') $acceptanceBadge = '<span class="badge badge-danger">Rejected</span>';
                    else $acceptanceBadge = '<span class="badge badge-secondary">'.htmlspecialchars($r['acceptance_status']).'</span>';
                }
            } else {
                $acceptanceBadge = '<span class="badge badge-secondary">No Status</span>';
            }

            $language = ($r['letter_languange'] === 'EN') ? 'English' : 'Bahasa';

            $periode = (!empty($r['expected_start']) && !empty($r['expected_end']))
                ? date('d M Y', strtotime($r['expected_start'])) . ' - ' . date('d M Y', strtotime($r['expected_end']))
                : '-';

            $dataArr[] = [
                "no" => $no++,
                "created_at" => !empty($r['created_at']) ? date('d M Y', strtotime($r['created_at'])) : '-',
                "student_name" => htmlspecialchars($r['student_name']),
                "company_name" => htmlspecialchars($r['company_name']),
                "status" => $statusBadge,
                "coor_approval" => $coorBadge,
                "cdc_approval" => $cdcBadge,
                "acceptance_status" => $acceptanceBadge,
                "language" => $language,
                "periode" => $periode,
                "id_internship_letter" => $r['id_internship_letter'],
                // additional raw fields if frontend needs
                "major" => $r['major'],
                "study_program" => $r['jenjang'] . ' - ' . $r['study_program'],
                "jurusan" => $r['jurusan']
            ];
        }

        // send response
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $dataArr
        ], JSON_UNESCAPED_UNICODE);
    break;


    case 'internship_letter_cdc':

        $pdo = $_SERVER['PDO'];

        // --- Ambil filter dari request (POST untuk AJAX/export) ---
        $cdcStatus    = isset($_POST['cdc_status']) ? $_POST['cdc_status'] : 'All';
        $coorStatus   = isset($_POST['coor_status']) ? $_POST['coor_status'] : 'All';
        $letterStatus = isset($_POST['letter_status']) ? $_POST['letter_status'] : 'All';
        $internStatus = isset($_POST['internship_status']) ? $_POST['internship_status'] : 'All';
        $prodiFilter  = isset($_POST['program_study']) ? $_POST['program_study'] : ''; // "", "All", "PS:xxx", "Major:xxx"
        $generation = isset($_POST['generation']) ? trim($_POST['generation']) : 'All';

        $from_date = !empty($_POST['from_date']) ? trim($_POST['from_date']) : null;
        $to_date   = !empty($_POST['to_date'])   ? trim($_POST['to_date'])   : null;


        //
        // Helper: bangun where & params
        //
        $whereClauses = [];
        $params = [];

        // Program study filter behavior:
        // - '' or null => default to prodi_koor
        // - 'All' => no filter at all
        // - 'PS:xxxx' => specific kode_prodi
        // - 'Major:Name' => ps.major = Name
        if ($prodiFilter === 'All') {
            // no clause
        } elseif (strpos($prodiFilter, 'PS:') === 0) {
            $kodeProdi = substr($prodiFilter, 3);
            $whereClauses[] = "s.program_study = :prodi_selected";
            $params[':prodi_selected'] = $kodeProdi;
        } elseif (strpos($prodiFilter, 'Major:') === 0) {
            $majorSelected = substr($prodiFilter, 6);
            $whereClauses[] = "ps.major = :major_selected";
            $params[':major_selected'] = $majorSelected;
        }

        if ($generation !== 'All' && $generation !== '') {
            $whereClauses[] = "SUBSTRING(s.nim, 4, 2) = :generation";
            $params[':generation'] = $generation;
        }


        if ($from_date === null && $to_date === null) {
            // No date filter
        }// 2) Jika hanya from_date (start_date)
        elseif ($from_date !== null && $to_date === null) {
            $whereClauses[] = "il.created_at >= :from_date";
            $params['from_date'] = $from_date;
        }

        // 3) Jika dua-duanya ada (between)
        elseif ($from_date !== null && $to_date !== null) {
            $whereClauses[] = "il.created_at BETWEEN :from_date AND :to_date";
            $params['from_date'] = $from_date;
            $params['to_date']   = $to_date;
        }



        // Status filters (normalize to compare safely)
        if ($cdcStatus !== 'All') {
            // compare lowercase
            $whereClauses[] = "LOWER(il.cdc_approval) = :cdc_status";
            $params[':cdc_status'] = strtolower($cdcStatus);
        }
        if ($coorStatus !== 'All') {
            $whereClauses[] = "LOWER(il.coor_approval) = :coor_status";
            $params[':coor_status'] = strtolower($coorStatus);
        }
        if ($letterStatus !== 'All') {
            // il.status seems to be enums like 'WAITING','APPROVED' -> compare uppercase
            $whereClauses[] = "UPPER(il.status) = :letter_status";
            $params[':letter_status'] = strtoupper($letterStatus);
        }
        if ($internStatus !== 'All') {
            $whereClauses[] = "LOWER(il.acceptance_status) = :intern_status";
            $params[':intern_status'] = strtolower($internStatus);
        }

        // Search (DataTables)
        $search = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
        if ($search !== '') {
            $whereClauses[] = "(s.name LIKE :search OR s.nim LIKE :search OR c.name LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        // Combine where
        $where = '';
        if (!empty($whereClauses)) {
            $where = ' WHERE ' . implode(' AND ', $whereClauses);
        }

        // ===== EXPORT FLOW (no limit) =====
        if (isset($_GET['action']) && $_GET['action'] === 'export') {
            try {
                // Query export (no limit)
                $sql_export = "
                    SELECT 
                        il.id_internship_letter,
                        il.student_nim,
                        s.name AS student_name,
                        COALESCE(c.name, '-') AS company_name,
                        il.status,
                        il.coor_approval,
                        il.cdc_approval,
                        il.acceptance_status,
                        il.letter_languange,
                        il.expected_start,
                        il.expected_end,
                        il.created_at,
                        ps.jenjang,
                        ps.study_program,
                        ps.jurusan,
                        ps.major
                    FROM internship_letter il
                    JOIN student_internship s ON il.student_nim = s.nim
                    JOIN program_study ps ON s.program_study = ps.kode_prodi
                    LEFT JOIN company c ON il.id_company = c.id_company
                    $where
                    ORDER BY il.created_at DESC
                ";

                $stmt = $pdo->prepare($sql_export);

                // Bind all params
                foreach ($params as $k => $v) {
                    // detect param type
                    $type = PDO::PARAM_STR;
                    $stmt->bindValue($k, $v, $type);
                }

                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // PhpSpreadsheet usage (pastikan sudah require/autoload sebelumnya)
                $xls = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $xls->getActiveSheet();

                // Header row
                $headers = [
                    "No", "NIM", "Student", "Company", "Letter Status",
                    "Coordinator", "CDC", "Acceptance", "Language",
                    "Start", "End", "Created", "Jenjang", "Program Study", "Jurusan", "Major"
                ];
                $sheet->fromArray($headers, null, 'A1');

                // Fill data
                $r = 2;
                foreach ($rows as $i => $row) {
                    $sheet->fromArray([
                        $i + 1,
                        $row['student_nim'],
                        $row['student_name'],
                        $row['company_name'],
                        $row['status'],
                        $row['coor_approval'],
                        $row['cdc_approval'],
                        $row['acceptance_status'],
                        $row['letter_languange'],
                        $row['expected_start'],
                        $row['expected_end'],
                        $row['created_at'],
                        $row['jenjang'],
                        $row['study_program'],
                        $row['jurusan'],
                        $row['major']
                    ], null, "A$r");
                    $r++;
                }

                // Output headers
                $filename = "internship_letter_export_" . date('Ymd_His') . ".xlsx";
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: max-age=0');

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($xls);
                $writer->save('php://output');
                exit;

            } catch (Exception $e) {
                // Jika error export, tampilkan pesan teks (agar AJAX bisa debug)
                http_response_code(500);
                echo "Export error: " . $e->getMessage();
                exit;
            }
        }

        // ===== DATATABLES MODE (serverSide) =====

        // DataTables parameters
        $draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
        $start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;

        // 1) recordsTotal: total rows respecting the default prodi restriction (so UI sees correct total)
        // Build base WHERE for total: include only program constraint default (if prodiFilter blank) otherwise no filters
        $baseWhereClauses = [];
        $baseParams = [];
        if ($prodiFilter === 'All') {
            // no base filter
        } elseif (strpos($prodiFilter, 'PS:') === 0) {
            $baseWhereClauses[] = "s.program_study = :prodi_selected";
            $baseParams[':prodi_selected'] = $kodeProdi; // $kodeProdi set earlier if present
        } elseif (strpos($prodiFilter, 'Major:') === 0) {
            $majorSelected = substr($prodiFilter, 6);
            $baseWhereClauses[] = "ps.major = :major_selected";
            $baseParams[':major_selected'] = $majorSelected;
        }

        if ($generation !== 'All' && $generation !== '') {
            $baseWhereClauses[] = "SUBSTRING(s.nim, 4, 2) = :generation";
            $baseParams[':generation'] = $generation;
        }

        if ($from_date === null && $to_date === null) {
            // No date filter
        }// 2) Jika hanya from_date (start_date)
        elseif ($from_date !== null && $to_date === null) {
            $baseWhereClauses[] = "il.created_at >= :from_date";
            $baseParams['from_date'] = $from_date;
        }

        // 3) Jika dua-duanya ada (between)
        elseif ($from_date !== null && $to_date !== null) {
            $baseWhereClauses[] = "il.created_at BETWEEN :from_date AND :to_date";
            $baseParams['from_date'] = $from_date;
            $baseParams['to_date']   = $to_date;
        }




        $baseWhere = '';
        if (!empty($baseWhereClauses)) {
            $baseWhere = ' WHERE ' . implode(' AND ', $baseWhereClauses);
        }

        $sql_total_all = "
            SELECT COUNT(*) FROM internship_letter il
            JOIN student_internship s ON il.student_nim = s.nim
            JOIN program_study ps ON s.program_study = ps.kode_prodi
            LEFT JOIN company c ON il.id_company = c.id_company
            $baseWhere
        ";
        $stmtTotalAll = $pdo->prepare($sql_total_all);
        foreach ($baseParams as $k => $v) {
            $stmtTotalAll->bindValue($k, $v);
        }
        $stmtTotalAll->execute();
        $recordsTotal = (int) $stmtTotalAll->fetchColumn();

        // 2) recordsFiltered: count with all filters & search (use $where + $params built earlier)
        $sql_count_filtered = "
            SELECT COUNT(*) FROM internship_letter il
            JOIN student_internship s ON il.student_nim = s.nim
            JOIN program_study ps ON s.program_study = ps.kode_prodi
            LEFT JOIN company c ON il.id_company = c.id_company
            $where
        ";
        $stmtCount = $pdo->prepare($sql_count_filtered);
        foreach ($params as $k => $v) {
            $stmtCount->bindValue($k, $v);
        }
        $stmtCount->execute();
        $recordsFiltered = (int) $stmtCount->fetchColumn();

        // 3) data query with limit
        $sql_data = "
            SELECT 
                il.id_internship_letter,
                il.student_nim,
                s.name AS student_name,
                COALESCE(c.name, '-') AS company_name,
                il.status,
                il.coor_approval,
                il.cdc_approval,
                il.acceptance_status,
                il.letter_languange,
                il.expected_start,
                il.expected_end,
                il.created_at,
                ps.jenjang,
                ps.study_program,
                ps.jurusan,
                ps.major
            FROM internship_letter il
            JOIN student_internship s ON il.student_nim = s.nim
            JOIN program_study ps ON s.program_study = ps.kode_prodi
            LEFT JOIN company c ON il.id_company = c.id_company
            $where
            ORDER BY il.created_at DESC, il.id_internship_letter DESC
            LIMIT :start, :length
        ";
        $stmtData = $pdo->prepare($sql_data);

        // bind params for data
        foreach ($params as $k => $v) {
            $stmtData->bindValue($k, $v);
        }
        // bind start/length
        $stmtData->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmtData->bindValue(':length', (int)$length, PDO::PARAM_INT);

        $stmtData->execute();
        $rows = $stmtData->fetchAll(PDO::FETCH_ASSOC);

        // format rows for DataTables
        $dataArr = [];
        $no = $start + 1;
        foreach ($rows as $r) {
            // formatted badges/text (you can adapt to your front-end)
            $statusLabel = $r['status'];
            $coorLabel = $r['coor_approval'];
            $cdcLabel = $r['cdc_approval'];
            $acceptLabel = $r['acceptance_status'];

            // create visual badges (server returns HTML fragments)
            switch (strtoupper($statusLabel)) {
                case 'WAITING':
                    $statusBadge = '<span class="badge badge-warning">Waiting</span>';
                    break;
                case 'APPROVED':
                    $statusBadge = '<span class="badge badge-success">Approved</span>';
                    break;
                case 'REJECTED':
                    $statusBadge = '<span class="badge badge-danger">Rejected</span>';
                    break;
                default:
                    $statusBadge = '<span class="badge badge-secondary">'.htmlspecialchars($statusLabel).'</span>';
            }

            // coor
            $coorBadge = '<span class="badge badge-secondary">Pending</span>';
            if ($coorLabel === 'approved') $coorBadge = '<span class="badge badge-success">Approved</span>';
            elseif ($coorLabel === 'rejected') $coorBadge = '<span class="badge badge-danger">Rejected</span>';
            elseif ($coorLabel === 'waiting') $coorBadge = '<span class="badge badge-warning text-dark">Waiting</span>';

            // cdc
            $cdcBadge = '<span class="badge badge-secondary">Pending</span>';
            if ($cdcLabel === 'approved') $cdcBadge = '<span class="badge badge-success">Approved</span>';
            elseif ($cdcLabel === 'rejected') $cdcBadge = '<span class="badge badge-danger">Rejected</span>';
            elseif ($cdcLabel === 'waiting') $cdcBadge = '<span class="badge badge-warning text-dark">Waiting</span>';

            // acceptance
            if (strtoupper($r['status']) === 'APPROVED') {
                if (empty($r['acceptance_status'])) {
                    $acceptanceBadge = '<span class="badge badge-warning">Waiting Response</span>';
                } else {
                    if ($r['acceptance_status'] === 'accepted') $acceptanceBadge = '<span class="badge badge-success">Accepted</span>';
                    elseif ($r['acceptance_status'] === 'rejected') $acceptanceBadge = '<span class="badge badge-danger">Rejected</span>';
                    else $acceptanceBadge = '<span class="badge badge-secondary">'.htmlspecialchars($r['acceptance_status']).'</span>';
                }
            } else {
                $acceptanceBadge = '<span class="badge badge-secondary">No Status</span>';
            }

            $language = ($r['letter_languange'] === 'EN') ? 'English' : 'Bahasa';

            $periode = (!empty($r['expected_start']) && !empty($r['expected_end']))
                ? date('d M Y', strtotime($r['expected_start'])) . ' - ' . date('d M Y', strtotime($r['expected_end']))
                : '-';

            $dataArr[] = [
                "no" => $no++,
                "created_at" => !empty($r['created_at']) ? date('d M Y', strtotime($r['created_at'])) : '-',
                "student_name" => htmlspecialchars($r['student_name']),
                "company_name" => htmlspecialchars($r['company_name']),
                "status" => $statusBadge,
                "coor_approval" => $coorBadge,
                "cdc_approval" => $cdcBadge,
                "acceptance_status" => $acceptanceBadge,
                "language" => $language,
                "periode" => $periode,
                "id_internship_letter" => $r['id_internship_letter'],
                // additional raw fields if frontend needs
                "major" => $r['major'],
                "study_program" => $r['jenjang'] . ' - ' . $r['study_program'],
                "jurusan" => $r['jurusan']
            ];
        }

        // send response
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $dataArr
        ], JSON_UNESCAPED_UNICODE);
    break;


    



    
endswitch;


