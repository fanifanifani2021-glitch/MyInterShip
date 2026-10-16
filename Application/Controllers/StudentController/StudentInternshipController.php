<?php
namespace Application\Controllers\StudentController;

use Controller;
use Request;
use Response;
use Session;
use Notification;
use Internship;
use StudentInternship;
use Company;
use Lecturer;
use DB;
use Encrypter;
use Exception;

class StudentInternshipController extends Controller
{
    private $nim;

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
     * 1. Halaman Riwayat Magang (Index View)
     */
    public function index()
    {
        // Ambil riwayat magang mahasiswa login menggunakan Model Internship
        $internships = Internship::where('nim', $this->nim)
            ->orderBy('id_internship', 'DESC')
            ->get();

        return renderView('student.my_internship.index', [
            'title'       => 'My Internship History',
            'internships' => $internships,
            'routes'      => 'my_internship'
        ]);
    }

    /**
     * 2. DataTables Server-Side Endpoint
     */
    public function data()
    {
        $draw   = (int) Request::input('draw', 1);
        $start  = (int) Request::input('start', 0);
        $length = (int) Request::input('length', 10);
        $search = Request::input('search.value');

        // Base Query menggunakan QueryBuilder dari Model Internship
        $query = Internship::where('nim', $this->nim);

        // Filter Perusahaan
        if ($filterCompany = Request::input('filter_company')) {
            $query->where('id_company', $filterCompany);
        }

        // Filter Status
        if ($filterStatus = Request::input('filter_status')) {
            $query->where('status', $filterStatus);
        }

        // Global Search
        if (!empty($search)) {
            $query->whereGroup(function($q) use ($search) {
                $q->where('internship_position', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%");
            });
        }

        $recordsTotal    = Internship::where('nim', $this->nim)->count();
        $recordsFiltered = $query->count();

        // Execution with Pagination
        $dataRows = $query->orderBy('id_internship', 'DESC')
                          ->limit($length)
                          ->offset($start)
                          ->get();

        $data = [];
        $no   = $start + 1;

        foreach ($dataRows as $row) {
            $statusRaw   = strtolower($row->status ?? '');
            $statusClass = $row->getStatusColor();
            $statusLabel = $row->getStatusLabel('en');

            // Enkripsi ID untuk tombol detail
            $encryptedId = Encrypter::encrypt($row->id_internship);
            $detailUrl   = router('student.internship.detail', ['id' => $encryptedId]);

            $action  = '<a href="' . $detailUrl . '" class="btn btn-secondary btn-sm shadow-sm">';
            $action .= '<i class="fas fa-info-circle mr-1"></i> Detail</a>';

            // Nama Perusahaan via Relasi Model ($row->company)
            $companyName = is_object($row->company) ? $row->company->name : '-';

            $data[] = [
                $no++,
                '<b>' . htmlspecialchars($companyName) . '</b>',
                '<span class="badge badge-' . $statusClass . '">' . htmlspecialchars($statusLabel) . '</span>',
                !empty($row->start_date) ? date('d M Y', strtotime($row->start_date)) : '-',
                !empty($row->end_date) ? date('d M Y', strtotime($row->end_date)) : '-',
                $action
            ];
        }

        return Response::json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data
        ]);
    }

    /**
     * 3. Halaman Detail Record Magang
     */
    public function detail()
    {
        try {
            $rawId = $this->params['id'] ?? Request::get('id');

            if (!$rawId) {
                Notification::swal2('Error', 'Invalid Internship ID.', 'error');
                Response::redirect(router('student.internship.index'))->send();
                exit;
            }

            // Dekripsi ID
            $idInternship = Encrypter::decrypt($rawId);

            // 1. Ambil Data Internship menggunakan Model Internship
            $internship = Internship::where('id_internship', $idInternship)
                ->where('nim', $this->nim)
                ->first();

            if (!$internship) {
                Notification::swal2('Not Found', 'Internship record not found or access denied.', 'error');
                Response::redirect(router('student.internship.index'))->send();
                exit;
            }

            // 2. Ambil Data Mahasiswa, Profil, & Dosen Pembimbing
            $student   = StudentInternship::where('nim', $this->nim)->first();
            $profile   = $student ? $student->profile() : null;
            $dospemObj = $student ? $student->getDospem() : null;

            // Logika Fallback Nama Dosen Pembimbing
            $dospemName = null;
            if ($dospemObj) {
                $dospemName = $dospemObj->name ?? $dospemObj->nama ?? $dospemObj->user_fullname ?? null;
            } elseif ($student && !empty($student->nik_dospem) && $student->nik_dospem !== '-') {
                $dospemName = "NIP/NIK: " . $student->nik_dospem;
            }

            // 3. Ambil Data Supervisor Industri
            $supervisor = null;
            if (!empty($internship->id_user_company) && $internship->id_user_company !== '-') {
                $supervisor = DB::table('user_company')
                    ->where('id_user_company', $internship->id_user_company)
                    ->where('id_company', $internship->id_company)
                    ->first();
            }

            // 4. Mapping Status Display
            $statusRaw = strtolower($internship->status ?? '');
            $statusMap = [
                'yes'     => ['label' => 'Intern Accepted (Ongoing)', 'accepted' => 1, 'class' => 'badge-primary', 'step' => 3],
                'finish'  => ['label' => 'Intern Accepted and Finished', 'accepted' => 1, 'class' => 'badge-success', 'step' => 4],
                'no'      => ['label' => 'Intern Not Accepted', 'accepted' => 0, 'class' => 'badge-danger', 'step' => 0],
                'reject'  => ['label' => 'Intern Rejected', 'accepted' => 0, 'class' => 'badge-danger', 'step' => 0],
                'waiting' => ['label' => 'Waiting for validation', 'accepted' => 0, 'class' => 'badge-warning', 'step' => 2],
            ];

            $currentStatus = $statusMap[$statusRaw] ?? ['label' => 'Not Registered', 'accepted' => null, 'class' => 'badge-secondary', 'step' => 1];

            return renderView('student.my_internship.detail', [
                'title'           => 'Internship Detail',
                'routes'          => 'my_internship',
                'internship'      => $internship,
                'company'         => $internship->company,
                'student_profile' => $profile,
                'dospem'          => $dospemName,
                'supervisor'      => is_array($supervisor) ? ($supervisor['user_fullname'] ?? null) : (is_object($supervisor) ? ($supervisor->user_fullname ?? null) : null),
                'status'          => $currentStatus,
                'id_enc'          => $rawId
            ]);

        } catch (Exception $e) {
            error_log("Internship Detail Error: " . $e->getMessage());
            Notification::swal2('Security Warning', 'Invalid or corrupted access parameters.', 'warning');
            Response::redirect(router('student.internship.index'))->send();
            exit;
        }
    }
}