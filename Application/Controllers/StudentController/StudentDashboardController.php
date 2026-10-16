<?php
namespace Application\Controllers\StudentController;

use Controller;
use Request;
use Response;
use Session;
use Internship;
use StudentProfile;
use Maintenance;
use DB;
use Encrypter;
use Application\Helpers\Student\StudentLayoutHelper;
use DateTime;
use Exception;

class StudentDashboardController extends Controller
{
    private $nim;

    public function __construct()
    {
        $this->nim = Session::get('nim') ?? ($_SESSION['nim'] ?? null);

        if (!$this->nim) {
            Response::redirect(router('login.student'))->send();
            exit;
        }
    }

    public function index()
    {
        $today = date('Y-m-d');

        // 1. Upcoming Maintenance
        $upcomingMaintenance = class_exists('Maintenance') ? Maintenance::getUpcoming(1) : [];
        $upcoming = !empty($upcomingMaintenance) ? $upcomingMaintenance[0] : null;

        // 2. Daftar Riwayat Magang Mahasiswa via Model Internship + Company
        $internships = Internship::where('nim', $this->nim)
            ->orderByRaw("FIELD(LOWER(status), 'yes', 'finish', 'waiting', 'no')")
            ->get();

        // Enkripsi ID untuk tiap magang & pastikan nama perusahaan aman
        foreach ($internships as $i) {
            $i->encoded_id = Encrypter::encrypt($i->id_internship);
            
            // Query fallback jika relasi ORM company tidak terpanggil
            if (empty($i->company_name)) {
                $comp = DB::table('company')->where('id_company', $i->id_company)->first();
                $i->company_name = $comp ? (is_object($comp) ? $comp->name : $comp['name']) : 'Company';
            }
        }

        // 3. Attendance Hari Ini untuk tiap Magang
        $attendanceTodayRows = DB::table('attendance')
            ->select('attendance.*')
            ->join('internship', 'internship.id_internship', '=', 'attendance.id_internship')
            ->where('internship.nim', '=', $this->nim)
            ->whereRaw("DATE(attendance.date) = ?", [$today])
            ->get();

        $attendanceToday = [];
        foreach ($attendanceTodayRows as $row) {
            $idInt = is_object($row) ? $row->id_internship : $row['id_internship'];
            $attendanceToday[$idInt] = (array) $row;
        }

        // 4. Student Profile Single Truth Check
        $isProfileComplete = StudentLayoutHelper::isProfileComplete();
        $studentProfile    = StudentProfile::where('nim', $this->nim)->first();

        // 5. Active Internship Hari Ini (Status 'Yes' & dalam rentang tanggal)
        $activeInternship = DB::table('internship')
            ->select(
                'internship.id_internship',
                'attendance.id_attendance',
                'attendance.check_in',
                'attendance.check_out',
                'attendance.attendance_type'
            )
            ->leftJoin('attendance', 'attendance.id_internship', '=', 'internship.id_internship')
            ->where('internship.nim', '=', $this->nim)
            ->whereRaw("LOWER(internship.status) = 'yes'")
            ->whereRaw("? BETWEEN internship.start_date AND internship.end_date", [$today])
            ->whereGroup(function($q) use ($today) {
                $q->whereNull('attendance.date')
                  ->orWhereRaw("DATE(attendance.date) = ?", [$today]);
            })
            ->first();

        if ($activeInternship) {
            $activeInternship = (array) $activeInternship;
            $activeInternship['encoded_id'] = Encrypter::encrypt($activeInternship['id_internship']);
        }

        // 6. Generate Dynamic Alerts (dengan style, text color & action text dinamis)
        $alerts = [];
        
        // Alert Profil Identity
        if (!$isProfileComplete) {
            $alerts[] = [
                'title'       => 'Student Profile & Documents Incomplete',
                'type'        => 'warning',
                'text_color'  => 'text-danger',
                'action_text' => 'Action required',
                'url'         => router('student.identity.index'),
                'icon'        => 'fa-exclamation-triangle'
            ];
        } else {
            $alerts[] = [
                'title'       => 'Student Profile & Documents Completed',
                'type'        => 'success',
                'text_color'  => 'text-success',
                'action_text' => 'All set',
                'url'         => router('student.identity.index'),
                'icon'        => 'fa-check-circle'
            ];
        }

        // Alert Attendance
        if ($activeInternship) {
            $attStatus = $this->getAttendanceStatus($activeInternship);

            if ($attStatus['canIn']) {
                $alerts[] = [
                    'title'       => 'You have not checked in today',
                    'type'        => 'warning',
                    'text_color'  => 'text-warning',
                    'action_text' => 'Action required',
                    'url'         => 'index.php?page=check_in_attendance_internship&id_internship=' . $activeInternship['encoded_id'],
                    'icon'        => 'fa-sign-in-alt'
                ];
            }

            if ($attStatus['canOut']) {
                $alerts[] = [
                    'title'       => 'You have not checked out yet',
                    'type'        => 'info',
                    'text_color'  => 'text-info',
                    'action_text' => 'Action required',
                    'url'         => 'index.php?page=check_out_attendance_internship&id_internship=' . $activeInternship['encoded_id'] . '&id_attendance=' . base64_encode($activeInternship['id_attendance'] ?? ''),
                    'icon'        => 'fa-sign-out-alt'
                ];
            }

            if ($attStatus['hasIn'] && $attStatus['hasOut']) {
                $alerts[] = [
                    'title'       => 'Attendance completed for today',
                    'type'        => 'success',
                    'text_color'  => 'text-success',
                    'action_text' => 'Completed',
                    'url'         => 'javascript:void(0)',
                    'icon'        => 'fa-check-double'
                ];
            }
        } else {
            $alerts[] = [
                'title'       => 'No active internship schedule today',
                'type'        => 'secondary',
                'text_color'  => 'text-muted',
                'action_text' => 'Information only',
                'url'         => 'javascript:void(0)',
                'icon'        => 'fa-calendar'
            ];
        }

        // 7. Data Announcements
        $announcements = DB::table('announcement')
            ->orderBy('id_announcement', 'DESC')
            ->limit(3)
            ->get();

        return renderView('student.dashboard', [
            'title'             => 'Student Dashboard',
            'routes'            => 'dashboard',
            'upcoming'          => $upcoming,
            'internships'       => $internships,
            'attendanceToday'   => $attendanceToday,
            'student_profile'   => $studentProfile,
            'isProfileComplete' => $isProfileComplete,
            'activeInternship'  => $activeInternship,
            'alerts'            => $alerts,
            'announcements'     => $announcements
        ]);
    }

    private function getAttendanceStatus(?array $attendance): array
    {
        if (!$attendance) {
            return [
                'hasIn'  => false,
                'hasOut' => false,
                'canIn'  => true,
                'canOut' => false,
            ];
        }

        $hasIn  = !empty($attendance['check_in']) && strtotime($attendance['check_in']);
        $hasOut = !empty($attendance['check_out']) && $attendance['check_out'] !== '-' && strtotime($attendance['check_out']);
        $type   = strtolower($attendance['attendance_type'] ?? '');

        return [
            'hasIn'  => $hasIn,
            'hasOut' => $hasOut,
            'canIn'  => !$hasIn,
            'canOut' => $hasIn && !$hasOut && ($type === 'present' || empty($type)),
        ];
    }
}