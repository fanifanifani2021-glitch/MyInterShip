<?php
namespace Application\Controllers\StudentController;

use Controller;
use Request;
use Response;
use Session;
use Validator;
use Notification;
use DB;
use File;
use Exception;
use DateTime;
use DateTimeZone;
use PDO;

class StudentInternshipLetterController extends Controller
{
    private string $nim;

    public function __construct()
    {
        $this->nim = Session::get('nim') ?? ($_SESSION['nim'] ?? null);

        if (!$this->nim) {
            Notification::swal2('Session Expired', 'Your session has expired. Please log in again.', 'warning');
            Response::redirect(router('login.student'))->send();
            exit;
        }
    }

    /**
     * 1. Halaman List Surat Pengantar Magang
     */
    public function index()
    {
        renderView('student.my_internship_letter.index', [
            'title'  => 'Internship Letter Management',
            'routes' => 'my_internship_letter'
        ]);
    }

    /**
     * 2. Halaman Detail Surat Pengantar Magang
     */
    public function detail()
    {
        $id = $this->params['id'] ?? Request::get('id');

        if (!$id) {
            Notification::swal2('Error', 'Invalid Internship Letter ID.', 'error');
            Response::redirect(router('student.internship_letter.index'))->send();
            exit;
        }

        try {
            $db = DB::connection();

            // Query Detail Surat
            $sqlLetter = "
                SELECT 
                    il.*,
                    s.name AS student_name,
                    s.email,
                    s.phone,
                    ps.study_program,
                    ps.jenjang,
                    ps.jurusan,
                    c.name AS company_name,
                    c.address AS company_address,
                    c.email AS company_email,
                    c.phone AS company_phone,
                    c.city AS company_city,
                    c.province AS company_province
                FROM internship_letter il
                JOIN student_internship s ON il.student_nim = s.nim
                JOIN program_study ps ON s.program_study = ps.kode_prodi
                LEFT JOIN company c ON il.id_company = c.id_company
                WHERE il.id_internship_letter = :id AND il.student_nim = :nim
                LIMIT 1
            ";
            $stmt = $db->prepare($sqlLetter);
            $stmt->execute(['id' => $id, 'nim' => $this->nim]);
            $letter = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$letter) {
                Notification::swal2('Not Found', 'Internship letter not found or access restricted.', 'error');
                Response::redirect(router('student.internship_letter.index'))->send();
                exit;
            }

            // 🔑 GENERATE PRINT TOKEN UNTUK IFRAME PRINT VIEWER
            $printToken = bin2hex(random_bytes(16));
            $_SESSION['PRINT_TOKEN'] = $printToken;
            Session::set('PRINT_TOKEN', $printToken);

            // Query Approval History
            $sqlApproval = "
                SELECT role, role_username, role_name, status_approval, remark, created_at
                FROM internship_letter_approval
                WHERE id_internship_letter = :id
                ORDER BY created_at ASC
            ";
            $stmt = $db->prepare($sqlApproval);
            $stmt->execute(['id' => $id]);
            $approvals = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Query Status History
            $sqlHistory = "
                SELECT status, created_at
                FROM internship_letter_history
                WHERE id_internship_letter = :id
                ORDER BY id_internship_letter_history DESC
            ";
            $stmt = $db->prepare($sqlHistory);
            $stmt->execute(['id' => $id]);
            $histories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            renderView('student.my_internship_letter.detail', [
                'title'       => 'Internship Letter Detail',
                'routes'      => 'my_internship_letter',
                'letter'      => $letter,
                'approvals'   => $approvals,
                'histories'   => $histories,
                'print_token' => $printToken // Passing token ke Blade
            ]);

        } catch (Exception $e) {
            error_log("Detail Letter Error: " . $e->getMessage());
            Notification::swal2('Error', 'An internal error occurred while fetching letter details.', 'error');
            Response::redirect(router('student.internship_letter.index'))->send();
            exit;
        }
    }

    /**
     * 3. Halaman Form Pengajuan Surat Magang Baru
     */
    public function create()
    {
        try {
            $db = DB::connection();

            // Cek Pengajuan Terbaru Mahasiswa
            $sqlCheck = "
                SELECT id_internship_letter, status, coor_approval, cdc_approval, acceptance_status
                FROM internship_letter
                WHERE student_nim = :nim
                ORDER BY created_at DESC
                LIMIT 1
            ";
            $stmt = $db->prepare($sqlCheck);
            $stmt->execute(['nim' => $this->nim]);
            $letter = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($letter) {
                $coorApp = strtolower($letter['coor_approval'] ?? '');
                $cdcApp  = strtolower($letter['cdc_approval'] ?? '');

                // Bila sudah disetujui tapi belum isi konfirmasi balasan (Acceptance)
                if ($coorApp === 'approved' && $cdcApp === 'approved' && empty($letter['acceptance_status'])) {
                    Notification::swal2(
                        'Action Required!',
                        'Please complete the Acceptance Response Form for your approved letter before creating a new request.',
                        'warning'
                    );
                    Response::redirect(router('student.internship_letter.detail', ['id' => $letter['id_internship_letter']]))->send();
                    exit;
                }

                // Bila surat terdahulu masih diproses
                if (in_array($letter['status'], ['WAITING', 'PROCESSING'])) {
                    Notification::swal2(
                        'Request In Progress',
                        'Your previous internship letter is still being reviewed by the Coordinator and CDC.',
                        'warning'
                    );
                    Response::redirect(router('student.internship_letter.detail', ['id' => $letter['id_internship_letter']]))->send();
                    exit;
                }
            }

            // Ambil Daftar Perusahaan
            $stmtComp = $db->prepare("SELECT id_company, name FROM company ORDER BY name ASC");
            $stmtComp->execute();
            $companies = $stmtComp->fetchAll(PDO::FETCH_ASSOC);

            // Ambil Data Mahasiswa + Prodi
            $sqlStudent = "
                SELECT si.*, ps.kode_prodi, ps.jenjang, ps.prodi, ps.study_program, ps.jurusan, ps.major
                FROM student_internship si
                INNER JOIN program_study ps ON ps.kode_prodi = si.program_study
                WHERE si.nim = :nim
                LIMIT 1
            ";
            $stmtStud = $db->prepare($sqlStudent);
            $stmtStud->execute(['nim' => $this->nim]);
            $studentData = $stmtStud->fetch(PDO::FETCH_ASSOC);

            renderView('student.my_internship_letter.add', [
                'title'              => 'Request New Internship Letter',
                'routes'             => 'my_internship_letter',
                'student_internship' => $studentData,
                'companies'          => $companies,
                'minimal_start_date' => date('Y-m-d', strtotime('-7 days'))
            ]);

        } catch (Exception $e) {
            error_log("Create Letter Form Error: " . $e->getMessage());
            Notification::swal2('Error', 'Unable to load letter request form.', 'error');
            Response::redirect(router('student.internship_letter.index'))->send();
            exit;
        }
    }

    /**
     * 4. Proses Submit Pengajuan Surat Magang Baru
     */
    public function store()
    {
        $isNewCompany = (int) Request::input('company_not_listed', 0);

        $rules = [
            'class'       => 'required',
            'semester'    => 'required',
            'start_date'  => 'required',
            'end_date'    => 'required'
        ];

        if ($isNewCompany === 0) {
            $rules['company_id'] = 'required';
        } else {
            $rules['newCompanyName']    = 'required';
            $rules['newCompanyEmail']   = 'required|email';
            $rules['newCompanyPhone']   = 'required';
            $rules['newCompanyAddress'] = 'required';
        }

        $messages = [
            'class.required'             => 'Class name is required.',
            'semester.required'          => 'Semester is required.',
            'start_date.required'        => 'Expected start date is required.',
            'end_date.required'          => 'Expected end date is required.',
            'company_id.required'        => 'Please select a company.',
            'newCompanyName.required'    => 'Company name is required.',
            'newCompanyEmail.required'   => 'Company email is required.',
            'newCompanyPhone.required'   => 'Company phone number is required.',
            'newCompanyAddress.required' => 'Company address is required.'
        ];

        $validator = Validator::make(Request::all(), $rules, [], $messages);

        if ($validator->fails()) {
            Session::flash('validation_errors', $validator->errors());
            Session::flash('old', Request::all());
            Notification::swal2('Validation Error', 'Please check your inputs and try again.', 'error');
            Response::back()->send();
            exit;
        }

        try {
            $db = DB::connection();
            $db->beginTransaction();

            $companyToCheck = ($isNewCompany === 1) ? null : Request::input('company_id');

            // Cek apakah mahasiswa sudah punya surat aktif ke perusahaan yang sama
            if ($companyToCheck) {
                $sqlCheckActive = "
                    SELECT id_internship_letter
                    FROM internship_letter
                    WHERE student_nim = :nim AND id_company = :company
                    AND (
                        status IN ('WAITING', 'PROCESSING')
                        OR (coor_approval IN ('waiting','approved') AND cdc_approval IN ('pending','approved') AND (acceptance_status IS NULL OR acceptance_status = ''))
                    )
                    LIMIT 1
                ";
                $stmt = $db->prepare($sqlCheckActive);
                $stmt->execute(['nim' => $this->nim, 'company' => $companyToCheck]);
                $lastLetter = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($lastLetter) {
                    $db->rollBack();
                    Session::flash('old', Request::all());
                    Notification::swal2(
                        'Active Letter Exists',
                        'You already have an active letter request for this company. Please complete its acceptance form first.',
                        'warning'
                    );
                    Response::redirect(router('student.internship_letter.detail', ['id' => $lastLetter['id_internship_letter']]))->send();
                    exit;
                }
            }

            // Jika Perusahaan Baru -> Simpan ke Tabel Company Terlebih Dahulu
            if ($isNewCompany === 1) {
                $stmtStudent = $db->prepare("SELECT id_kampus FROM student_internship WHERE nim = :nim LIMIT 1");
                $stmtStudent->execute(['nim' => $this->nim]);
                $studentInfo = $stmtStudent->fetch(PDO::FETCH_ASSOC);

                $sqlCompany = "
                    INSERT INTO company (name, email, phone, address, status, province, city, access_type, id_kampus)
                    VALUES (:name, :email, :phone, :address, 'Not Verified', :province, :city, '1', :id_kampus)
                ";
                $stmtComp = $db->prepare($sqlCompany);
                $stmtComp->execute([
                    'name'      => Request::input('newCompanyName'),
                    'email'     => Request::input('newCompanyEmail'),
                    'phone'     => Request::input('newCompanyPhone'),
                    'address'   => Request::input('newCompanyAddress'),
                    'province'  => Request::input('newCompanyProvince', ''),
                    'city'      => Request::input('newCompanyCity', ''),
                    'id_kampus' => $studentInfo['id_kampus'] ?? 1
                ]);

                $idCompany = $db->lastInsertId();
            } else {
                $idCompany = Request::input('company_id');
            }

            // Insert Record `internship_letter`
            $letterId = generateUUIDv4();
            $sqlInsertLetter = "
                INSERT INTO internship_letter (
                    id_internship_letter, id_company, student_nim, class, semester,
                    is_new_company, expected_start, expected_end, letter_languange,
                    status, coor_approval, cdc_approval, created_at
                ) VALUES (
                    :id, :id_company, :nim, :class, :semester,
                    :is_new, :start, :end, :lang,
                    'WAITING', 'waiting', 'pending', :created_at
                )
            ";
            $stmtLetter = $db->prepare($sqlInsertLetter);
            $stmtLetter->execute([
                'id'         => $letterId,
                'id_company' => $idCompany,
                'nim'        => $this->nim,
                'class'      => Request::input('class'),
                'semester'   => Request::input('semester'),
                'is_new'     => $isNewCompany,
                'start'      => Request::input('start_date'),
                'end'        => Request::input('end_date'),
                'lang'       => Request::input('language', 'EN'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Insert History Log Status
            $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            $dateFormatted = $now->format('F j, Y \a\t H:i');
            $userName = Session::get('name') ?? ($_SESSION['name'] ?? 'Student');
            $historyMsg = "Internship Letter Submitted on {$dateFormatted}, status changed to Waiting for Internship Coordinator Approval by {$this->nim} | {$userName}.";

            $sqlHistory = "
                INSERT INTO internship_letter_history (id_internship_letter, status, created_at)
                VALUES (:id, :status, :created_at)
            ";
            $stmtHist = $db->prepare($sqlHistory);
            $stmtHist->execute([
                'id'         => $letterId,
                'status'     => $historyMsg,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $db->commit();

            Notification::swal2('Success!', 'Internship letter request submitted successfully.', 'success');
            Response::redirect(router('student.internship_letter.detail', ['id' => $letterId]))->send();
            exit;

        } catch (Exception $e) {
            $db->rollBack();
            error_log("Submit Letter Request Error: " . $e->getMessage());
            Session::flash('old', Request::all());
            Notification::swal2('Error', 'An error occurred while submitting your letter request: ' . $e->getMessage(), 'error');
            Response::back()->send();
            exit;
        }
    }

    /**
     * 5. Halaman Form Konfirmasi Balasan Perusahaan (Acceptance Form)
     */
    public function acceptanceForm()
    {
        $id = $this->params['id'] ?? Request::get('id');

        if (!$id) {
            Notification::swal2('Error', 'Invalid Internship Letter ID.', 'error');
            Response::redirect(router('student.internship_letter.index'))->send();
            exit;
        }

        try {
            $db = DB::connection();

            $sql = "
                SELECT 
                    il.*,
                    c.id_company, c.name AS company_name, c.address AS company_address, c.city AS company_city, c.province AS company_province,
                    s.name AS student_name, s.nim AS student_nim, ps.jenjang, ps.study_program, ps.prodi, ps.jurusan
                FROM internship_letter il
                LEFT JOIN company c ON il.id_company = c.id_company
                LEFT JOIN student_internship s ON il.student_nim = s.nim
                LEFT JOIN program_study ps ON s.program_study = ps.kode_prodi
                WHERE il.id_internship_letter = :id AND il.student_nim = :nim
                LIMIT 1
            ";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $id, 'nim' => $this->nim]);
            $letter = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$letter) {
                Notification::swal2('Not Found', 'Internship letter not found or access restricted.', 'error');
                Response::redirect(router('student.internship_letter.index'))->send();
                exit;
            }

            // Hitung 10 hari kerja dari tanggal penerbitan surat
            $letterDate = new DateTime($letter['created_at'] ?? 'now');
            $today      = new DateTime();
            $businessDays = 0;
            $cursor     = clone $letterDate;

            while ($cursor <= $today) {
                if ($cursor->format('N') < 6) {
                    $businessDays++;
                }
                $cursor->modify('+1 day');
            }

            $canSubmitWithoutReply = $businessDays >= 10;

            renderView('student.my_internship_letter.acceptance_form', [
                'title'                 => 'Internship Acceptance Form',
                'routes'                => 'my_internship_letter',
                'letter'                => $letter,
                'company'               => [
                    'id'       => $letter['id_company'],
                    'name'     => $letter['company_name'],
                    'address'  => $letter['company_address'],
                    'city'     => $letter['company_city'],
                    'province' => $letter['company_province'],
                ],
                'student'               => [
                    'name'          => $letter['student_name'],
                    'nim'           => $letter['student_nim'],
                    'jenjang'       => $letter['jenjang'],
                    'study_program' => $letter['study_program'],
                    'jurusan'       => $letter['jurusan'],
                ],
                'canSubmitWithoutReply' => $canSubmitWithoutReply
            ]);

        } catch (Exception $e) {
            error_log("Acceptance Form Error: " . $e->getMessage());
            Notification::swal2('Error', 'Unable to load acceptance response form.', 'error');
            Response::redirect(router('student.internship_letter.index'))->send();
            exit;
        }
    }

    /**
     * 6. Proses Submit Form Konfirmasi Balasan Perusahaan
     */
    public function submitAcceptance()
    {
        $letterId    = Request::input('id_internship_letter');
        $hasReply    = Request::input('has_reply');
        $isAccepted  = Request::input('is_accepted');
        $changedDate = Request::input('changed_date');

        if (!$letterId) {
            Notification::swal2('Error', 'Invalid Internship Letter request.', 'error');
            Response::back()->send();
            exit;
        }

        if (!$hasReply) {
            Notification::swal2('Validation Error', 'Please select whether you have received a reply from the company.', 'error');
            Response::back()->send();
            exit;
        }

        try {
            $db = DB::connection();

            // Verifikasi Kepemilikan Surat
            $stmt = $db->prepare("SELECT * FROM internship_letter WHERE id_internship_letter = :id AND student_nim = :nim LIMIT 1");
            $stmt->execute(['id' => $letterId, 'nim' => $this->nim]);
            $letter = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$letter) {
                Notification::swal2('Forbidden', 'Unauthorized access to this internship letter.', 'error');
                Response::back()->send();
                exit;
            }

            $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            $timestamp = $now->format('F j, Y \a\t H:i');
            $userName  = Session::get('name') ?? ($_SESSION['name'] ?? 'Student');

            // -----------------------------------------------------------------
            // OPSEN A: HAS REPLY = NO (TIDAK ADA BALASAN SETELAH 10 HARI KERJA)
            // -----------------------------------------------------------------
            if ($hasReply === 'no') {
                $letterDate = new DateTime($letter['created_at'] ?? 'now');
                $today      = new DateTime();
                $businessDays = 0;
                $cursor     = clone $letterDate;

                while ($cursor <= $today) {
                    if ($cursor->format('N') < 6) {
                        $businessDays++;
                    }
                    $cursor->modify('+1 day');
                }

                if ($businessDays < 10) {
                    Notification::swal2('Action Blocked', 'You cannot submit "No Reply" until 10 business days have passed since letter issuance.', 'warning');
                    Response::back()->send();
                    exit;
                }

                // Update Status Surat
                $db->prepare("
                    UPDATE internship_letter 
                    SET has_reply = 'NO', acceptance_status = 'NO REPLY', updated_at = NOW() 
                    WHERE id_internship_letter = :id
                ")->execute(['id' => $letterId]);

                // Insert History
                $histMsg = "Internship Letter - Company Response submitted on {$timestamp} — Internship Acceptance Status changed to NO REPLY FROM COMPANY by {$this->nim} | {$userName}";
                $db->prepare("
                    INSERT INTO internship_letter_history (id_internship_letter, status, created_at) 
                    VALUES (:id, :status, NOW())
                ")->execute(['id' => $letterId, 'status' => $histMsg]);

                Notification::swal2('Saved', 'No reply response status confirmed.', 'success');
                Response::redirect(router('student.internship_letter.detail', ['id' => $letterId]))->send();
                exit;
            }

            // -----------------------------------------------------------------
            // OPSEN B: HAS REPLY = YES (ADA BALASAN SURAT DARI PERUSAHAAN)
            // -----------------------------------------------------------------
            if (!$isAccepted) {
                Notification::swal2('Validation Error', 'Please select whether the company accepted your internship application.', 'error');
                Response::back()->send();
                exit;
            }

            // Validasi & Upload File Surat Balasan Perusahaan (PDF Wajib)
            if (empty($_FILES['response_pdf']['tmp_name']) || $_FILES['response_pdf']['error'] !== UPLOAD_ERR_OK) {
                Notification::swal2('File Required', 'Please upload the company response letter in PDF format.', 'error');
                Response::back()->send();
                exit;
            }

            $ext = strtolower(pathinfo($_FILES['response_pdf']['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                Notification::swal2('Invalid Format', 'The company response letter file must be a PDF document.', 'error');
                Response::back()->send();
                exit;
            }

            // Strict Magic Bytes Check Biner PDF
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $realMime = finfo_file($finfo, $_FILES['response_pdf']['tmp_name']);
            finfo_close($finfo);

            if ($realMime !== 'application/pdf') {
                Notification::swal2('Security Alert', 'The uploaded response file is not a real PDF document.', 'error');
                Response::back()->send();
                exit;
            }

            $uploadedFileName = File::upload($_FILES['response_pdf'], 'upload/internship_letter/company_reply/');
            if (!$uploadedFileName) {
                throw new Exception("Failed to save response letter file to server.");
            }

            // -----------------------------------------------------------------
            // B.1: PERUSAHAAN MENOLAK (REJECTED)
            // -----------------------------------------------------------------
            if ($isAccepted === 'no') {
                $db->prepare("
                    UPDATE internship_letter 
                    SET has_reply = 'yes', acceptance_status = 'rejected', company_reply_letter = :file, updated_at = NOW() 
                    WHERE id_internship_letter = :id
                ")->execute(['file' => $uploadedFileName, 'id' => $letterId]);

                $histMsg = "Internship Letter - Company Response submitted on {$timestamp} — Internship Acceptance Status changed to REJECTED WITH COMPANY REPLY by {$this->nim} | {$userName}";
                $db->prepare("
                    INSERT INTO internship_letter_history (id_internship_letter, status, created_at) 
                    VALUES (:id, :status, NOW())
                ")->execute(['id' => $letterId, 'status' => $histMsg]);

                Notification::swal2('Saved', 'Rejection response recorded successfully.', 'info');
                Response::redirect(router('student.internship_letter.detail', ['id' => $letterId]))->send();
                exit;
            }

            // -----------------------------------------------------------------
            // B.2: PERUSAHAAN MENERIMA (ACCEPTED) -> KLAIM / DAFTAR MAGANG
            // -----------------------------------------------------------------
            $finalStart = $letter['expected_start'];
            $finalEnd   = $letter['expected_end'];

            if ($changedDate === 'yes') {
                $newStart = Request::input('new_start_date');
                $newEnd   = Request::input('new_end_date');

                if (!$newStart || !$newEnd) {
                    Notification::swal2('Validation Error', 'Please specify the updated start and end dates provided by the company.', 'error');
                    Response::back()->send();
                    exit;
                }

                $finalStart = $newStart;
                $finalEnd   = $newEnd;
            }

            // Hitung Durasi Bulan Magang
            $startDateObj = new DateTime($finalStart);
            $endDateObj   = new DateTime($finalEnd);
            $diff         = $startDateObj->diff($endDateObj);
            $totalMonths  = ($diff->y * 12) + $diff->m;

            // Update Table `internship_letter`
            $db->prepare("
                UPDATE internship_letter 
                SET has_reply = 'yes', acceptance_status = 'accepted', company_reply_letter = :file, updated_at = NOW() 
                WHERE id_internship_letter = :id
            ")->execute(['file' => $uploadedFileName, 'id' => $letterId]);

            // Insert Table Claim `internship`
            $stmtClaim = $db->prepare("
                INSERT INTO `internship` (
                    `nim`, `id_company`, `start_date`, `end_date`, `status`, 
                    `internship_position`, `internship_period`, `timestamp_register`, 
                    `id_user_company`, `nik`, `file_1`, `file_2`, `file_3`, `date_finalreport`, `final_report`
                ) VALUES (
                    :nim, :id_company, :start_date, :end_date, 'Yes', 
                    :position, :period, CURRENT_TIMESTAMP, 
                    '', '', '', '', '', '', ''
                )
            ");
            $stmtClaim->execute([
                'nim'                 => $this->nim,
                'id_company'          => $letter['id_company'],
                'start_date'          => $finalStart,
                'end_date'            => $finalEnd,
                'position'            => Request::input('internship_position', 'Intern'),
                'period'              => $totalMonths
            ]);

            // Insert History Log
            $histMsg = "Internship Letter - Company Response submitted on {$timestamp} — Internship Acceptance Status changed to ACCEPTED WITH COMPANY REPLY by {$this->nim} | {$userName}";
            $db->prepare("
                INSERT INTO internship_letter_history (id_internship_letter, status, created_at) 
                VALUES (:id, :status, NOW())
            ")->execute(['id' => $letterId, 'status' => $histMsg]);

            Notification::swal2('Accepted!', 'Congratulations! Your internship acceptance response has been registered.', 'success');
            Response::redirect(router('student.internship_letter.detail', ['id' => $letterId]))->send();
            exit;

        } catch (Exception $e) {
            error_log("Submit Acceptance Error: " . $e->getMessage());
            Notification::swal2('Error', 'Failed to save acceptance response: ' . $e->getMessage(), 'error');
            Response::back()->send();
            exit;
        }
    }



/**
     * DataTables Server-Side JSON Endpoint untuk List Surat Pengantar Magang
     */
    public function data()
    {
        header('Content-Type: application/json');

        try {
            $db = DB::connection();

            $start          = Request::integer('start', 0);
            $length         = Request::integer('length', 10);
            $draw           = Request::integer('draw', 1);
            $search         = Request::post('search')['value'] ?? '';
            $statusFilter   = Request::post('status', '');
            $langFilter     = Request::post('language', '');
            $classFilter    = Request::post('class', '');
            $semesterFilter = Request::post('semester', '');

            $where = "WHERE il.student_nim = :nim";
            $params = ['nim' => $this->nim];

            // Dynamic Filters
            if (!empty($statusFilter) && $statusFilter !== 'All') {
                $where .= " AND il.status = :status";
                $params['status'] = $statusFilter;
            }

            if (!empty($langFilter) && $langFilter !== 'All') {
                $where .= " AND il.letter_languange = :language";
                $params['language'] = $langFilter;
            }

            if (!empty($classFilter) && $classFilter !== 'All') {
                $where .= " AND il.class = :class";
                $params['class'] = $classFilter;
            }

            if (!empty($semesterFilter) && $semesterFilter !== 'All') {
                $where .= " AND il.semester = :semester";
                $params['semester'] = (int)$semesterFilter;
            }

            if (!empty($search)) {
                $where .= " AND (il.student_nim LIKE :search OR s.name LIKE :search OR c.name LIKE :search)";
                $params['search'] = "%{$search}%";
            }

            // Count Total Filtered
            $sqlTotal = "
                SELECT COUNT(*) 
                FROM internship_letter il
                JOIN student_internship s ON il.student_nim = s.nim
                LEFT JOIN company c ON il.id_company = c.id_company
                {$where}
            ";
            $stmtCount = $db->prepare($sqlTotal);
            $stmtCount->execute($params);
            $totalFiltered = (int) $stmtCount->fetchColumn();

            // Query Main Data
            $sqlData = "
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
                    il.acceptance_status,
                    c.name AS company_name
                FROM internship_letter il
                JOIN student_internship s ON il.student_nim = s.nim
                JOIN program_study ps ON s.program_study = ps.kode_prodi
                LEFT JOIN company c ON il.id_company = c.id_company
                {$where}
                ORDER BY il.created_at DESC, il.id_internship_letter DESC
                LIMIT {$start}, {$length}
            ";

            $stmtData = $db->prepare($sqlData);
            $stmtData->execute($params);
            $letters = $stmtData->fetchAll(PDO::FETCH_ASSOC);

            $data = [];
            $no   = $start + 1;

            foreach ($letters as $l) {
                // Letter Status Badge
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

                // Coordinator Approval Badge
                switch (strtolower($l['coor_approval'])) {
                    case 'approved':
                        $coorApproval = '<span class="badge badge-success">Approved</span>';
                        break;
                    case 'waiting':
                        $coorApproval = '<span class="badge badge-warning">Waiting</span>';
                        break;
                    case 'rejected':
                        $coorApproval = '<span class="badge badge-danger">Rejected</span>';
                        break;
                    default:
                        $coorApproval = '<span class="badge badge-secondary">Pending</span>';
                        break;
                }

                // CDC Approval Badge
                switch (strtolower($l['cdc_approval'])) {
                    case 'approved':
                        $cdcApproval = '<span class="badge badge-success">Approved</span>';
                        break;
                    case 'waiting':
                        $cdcApproval = '<span class="badge badge-warning">Waiting</span>';
                        break;
                    case 'rejected':
                        $cdcApproval = '<span class="badge badge-danger">Rejected</span>';
                        break;
                    default:
                        $cdcApproval = '<span class="badge badge-secondary">Pending</span>';
                        break;
                }

                $languageLabel = ($l['letter_languange'] === 'EN') ? 'English' : 'Bahasa Indonesia';
                $periode = '';
                if (!empty($l['expected_start']) && !empty($l['expected_end'])) {
                    $periode = date('M d, Y', strtotime($l['expected_start'])) . ' - ' . date('M d, Y', strtotime($l['expected_end']));
                }

                // Acceptance Status
                if ($l['status'] === 'APPROVED') {
                    if (empty($l['acceptance_status'])) {
                        $acceptanceStatus = '<span class="badge badge-warning">Waiting Response</span>';
                    } else {
                        switch (strtolower($l['acceptance_status'])) {
                            case 'accepted':
                                $acceptanceStatus = '<span class="badge badge-success">Accepted</span>';
                                break;
                            case 'rejected':
                                $acceptanceStatus = '<span class="badge badge-danger">Rejected</span>';
                                break;
                            case 'no reply':
                                $acceptanceStatus = '<span class="badge badge-secondary">No Reply</span>';
                                break;
                            default:
                                $acceptanceStatus = '<span class="badge badge-info">' . htmlspecialchars($l['acceptance_status']) . '</span>';
                                break;
                        }
                    }
                } else {
                    $acceptanceStatus = '<span class="badge badge-secondary">No Status</span>';
                }

                // Action Link Detail
                $detailUrl = router('student.internship_letter.detail', ['id' => $l['id_internship_letter']]);
                $actionBtn = '
                <a class="btn btn-outline-info btn-xs btn-round shadow-sm" href="' . $detailUrl . '">
                    <i class="fas fa-eye mr-1"></i> Detail
                </a>';

                $data[] = [
                    'no'                   => $no++ . '.',
                    'id_internship_letter' => $l['id_internship_letter'],
                    'nim'                  => $l['student_nim'],
                    'student_name'         => htmlspecialchars($l['student_name']),
                    'program'              => trim($l['jenjang'] . ' ' . ($l['study_program'] ?? '')),
                    'jurusan'              => htmlspecialchars($l['jurusan']),
                    'company_name'         => htmlspecialchars($l['company_name'] ?? '-'),
                    'acceptance_status'    => $acceptanceStatus,
                    'class'                => htmlspecialchars($l['class']),
                    'semester'             => $l['semester'],
                    'language'             => $languageLabel,
                    'status'               => $statusBadge,
                    'coor_approval'        => $coorApproval,
                    'cdc_approval'         => $cdcApproval,
                    'periode'              => $periode,
                    'created_at'           => !empty($l['created_at']) ? date('d M Y', strtotime($l['created_at'])) : '-',
                    'actions'              => $actionBtn
                ];
            }

            echo json_encode([
                "draw"            => $draw,
                "recordsTotal"    => $totalFiltered,
                "recordsFiltered" => $totalFiltered,
                "data"            => $data
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }
}