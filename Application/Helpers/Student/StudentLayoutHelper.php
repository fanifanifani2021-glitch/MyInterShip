<?php

namespace Application\Helpers\Student;

use Session;
use Internship;
use StudentInternship;
use StudentProfile;
use Campus;
use DB;

class StudentLayoutHelper
{
    /**
     * Mendapatkan data Kampus mahasiswa yang sedang login.
     */
    public static function getUniversityName()
    {
        $nim = Session::get('nim') ?? ($_SESSION['nim'] ?? null);
        
        if ($nim) {
            try {
                $student = StudentInternship::where('nim', $nim)->first();
                if ($student && !empty($student->id_kampus)) {
                    if (class_exists('Campus')) {
                        $campus = Campus::where('id_kampus', $student->id_kampus)->first();
                        if ($campus) {
                            return $campus->nama_kampus ?? $campus->name ?? 'Politeknik Negeri Batam';
                        }
                    }

                    $campus = DB::table('tb_kampus')->where('id_kampus', $student->id_kampus)->first();
                    if ($campus) {
                        return is_object($campus) ? ($campus->nama_kampus ?? 'Politeknik Negeri Batam') : ($campus['nama_kampus'] ?? 'Politeknik Negeri Batam');
                    }
                }
            } catch (\Exception $e) {
                error_log("StudentLayoutHelper University Error: " . $e->getMessage());
            }
        }

        return 'Politeknik Negeri Batam';
    }

    /**
     * 🔍 SINGLE SOURCE OF TRUTH: CEK KELENGKAPAN PROFIL MAHASISWA (DATABASE BASED)
     * Syarat Lengkap: Record profil ada, Talenthub CV terisi, dan Transkrip Nilai terisi.
     */
    public static function isProfileComplete()
    {
        $nim = Session::get('nim') ?? ($_SESSION['nim'] ?? null);
        if (!$nim) return false;

        try {
            // Query langsung ke tabel student_profile
            $profile = StudentProfile::where('nim', $nim)->first();
            
            if (!$profile) return false;

            // Handle baik bila $profile berupa Model/Object atau Array
            $cv         = is_object($profile) ? ($profile->talenthub_cv ?? '') : ($profile['talenthub_cv'] ?? '');
            $transcript = is_object($profile) ? ($profile->academic_transcript ?? '') : ($profile['academic_transcript'] ?? '');

            $hasCv         = !empty(trim((string)$cv));
            $hasTranscript = !empty(trim((string)$transcript));

            return ($hasCv && $hasTranscript);
        } catch (\Exception $e) {
            error_log("isProfileComplete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghitung magang aktif / ongoing mahasiswa saat ini
     */
    public static function activeInternshipCount()
    {
        $nim = Session::get('nim') ?? ($_SESSION['nim'] ?? null);
        if (!$nim) return 0;

        try {
            return Internship::where('nim', $nim)
                ->whereIn('status', ['Yes', 'yes', 'YES'])
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Menghitung Surat Pengantar Magang yang disetujui (Approved) tapi belum diisi konfirmasi balasan
     */
    public static function approvedLetterCount()
    {
        $nim = Session::get('nim') ?? ($_SESSION['nim'] ?? null);
        if (!$nim) return 0;

        try {
            return DB::table('internship_letter')
                ->where('student_nim', $nim)
                ->where('status', 'APPROVED')
                ->where(function($q) {
                    $q->whereNull('acceptance_status')
                      ->orWhere('acceptance_status', '');
                })
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Menghitung jumlah tiket Helpdesk milik mahasiswa yang berstatus Open/Replied
     */
    public static function helpdeskCount()
    {
        $nim = Session::get('nim') ?? ($_SESSION['nim'] ?? null);
        if (!$nim) return 0;

        try {
            return DB::table('helpdesk_tickets')
                ->where('user_id', $nim)
                ->whereIn('status', ['Replied', 'Open', 'OPEN', 'REPLIED'])
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}