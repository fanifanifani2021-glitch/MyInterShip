<?php 
use Dompdf\Dompdf;
use Dompdf\Options;

switch ($method) :
    default:
        http_response_code(404);
    break;

    case 'print':

        if (!isset($_GET['token']) || !isset($_SESSION['PRINT_TOKEN'])) {
            echo "This page can only be viewed inside the system viewer.";
            exit;
        }

        if ($_GET['token'] !== $_SESSION['PRINT_TOKEN']) {
            echo "Invalid token. Access denied.";
            exit;
        }

        unset($_SESSION['PRINT_TOKEN']);

        try {
        $pdo = $_SERVER['PDO'];

        // ================================
        // 1. Ambil ID dari GET
        // ================================
        $id = isset($_GET['id']) ? $_GET['id'] : 0;

        // ================================
        // 2. Ambil data letter
        // ================================
        $sqlLetter = "
            SELECT il.*, s.nim AS student_nim, s.name AS student_name,
                ps.jenjang, ps.study_program, ps.prodi, ps.jurusan,

                c.name AS company_name, c.address AS company_address,
                c.email AS company_email, c.phone AS company_phone,
                c.city AS company_city, c.province AS company_province
            FROM internship_letter il
            LEFT JOIN student_internship s ON s.nim = il.student_nim
            LEFT JOIN program_study ps ON s.program_study = ps.kode_prodi
            LEFT JOIN company c ON c.id_company = il.id_company
            WHERE il.id_internship_letter = :id
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sqlLetter);
        $stmt->execute([':id' => $id]);
        $letter = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$letter) {
            echo "Letter not found.";
            exit;
        }

        // ================================
        // 3. Validasi status
        // ================================
        if ($letter['status'] !== 'APPROVED') {
            echo "This internship letter is not approved and cannot be printed.";
            exit;
        }

        // ================================
        // 4. Tentukan language yang dipilih
        // ================================
        $language = isset($letter['letter_languange']) ? $letter['letter_languange'] : 'ID';

        if ($language === 'EN') {
            $view = 'print.internship_letter_en';
        } else {
            $view = 'print.internship_letter_id';
        }

        $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
                  . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


        $generated_by = [
            'username' => 'UNKNOWN',
            'name'     => 'UNKNOWN'
        ];

        // Jika mahasiswa login
        if (isset($_SESSION['Student-Data'])) {
            $generated_by['username'] = $_SESSION['Student-Data']['nim'] ?? 'UNKNOWN';
            $generated_by['name']     = $_SESSION['Student-Data']['name'] ?? 'UNKNOWN';
        }

        // Jika CDC login
        elseif (isset($_SESSION['CDC-Data'])) {
            $generated_by['username'] = $_SESSION['CDC-Data']['username'] ?? 'CDC';
            $generated_by['name']     = $_SESSION['CDC-Data']['name'] ?? 'CDC Administrator';
        }

        // Jika dosen login (lecturer)
        elseif (isset($_SESSION['Lecturer-Data'])) {
            $generated_by['username'] = $_SESSION['Lecturer-Data']['nik_nim_unit'] 
                                        ?? $_SESSION['Lecturer-Data']['nik'] 
                                        ?? 'LECTURER';
            $generated_by['name']     = $_SESSION['Lecturer-Data']['name'] ?? 'Lecturer';
        }


        // ================================
        // 5. Render ke view BladeOne
        // ================================
        renderView($view, [
            'title'   => 'Print Internship Letter',
            'routes'  => 'my_internship_letter',
            'letter'  => $letter,
            'currentUrl' => $currentUrl,
            'generated_by' => $generated_by,
            'student' => [
                'nim'           => $letter['student_nim'],
                'name'          => $letter['student_name'],
                'jenjang'       => $letter['jenjang'],
                'study_program' => $letter['study_program'],
                'prodi' => $letter['prodi'],
                'jurusan'       => $letter['jurusan'],
            ],
            'company' => [
                'name'    => $letter['company_name'],
                'address' => $letter['company_address'],
                'email'   => $letter['company_email'],
                'phone'   => $letter['company_phone'],
                'city'   => $letter['company_city'],
                'province'   => $letter['company_province'],
            ],
        ]);

    } catch ( Exception $e ) {
        var_dump( $e ); die;
    }

    break;


endswitch;