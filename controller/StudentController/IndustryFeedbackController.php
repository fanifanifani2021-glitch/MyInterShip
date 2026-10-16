<?php
use PhpOffice\PhpWord\TemplateProcessor;

// --- 0. HELPER FUNCTION ---
function prepareIndustryCheckmarks($row) {
    $map = [];
    $fields = [
        'etika' => 'etika_', 
        'keahlian_kompetensi' => 'komp_', 
        'keahlian_bahasa' => 'lang_', 
        'penggunaan_ti' => 'it_', 
        'komunikasi' => 'comm_', 
        'kerjasama' => 'team_', 
        'pengembangan_diri' => 'dev_'
    ];

    foreach ($fields as $dbKey => $prefix) {
        for ($i = 1; $i <= 4; $i++) {
            $map[$prefix . $i] = ($row[$dbKey] == $i) ? '✓' : '';
        }
    }
    return $map;
}

// --- 1. KEAMANAN DASAR ---
try {
    $id_internship = Encrypter::decrypt($id);
    $nim = $_SESSION['nim'];

    $internship = DB::table('internship')
        ->where('id_internship', $id_internship)->where('nim', $nim)->first();

    if (!$internship) {
        Notification::swal2('Denied', 'Internship data not found!', 'error');
        Response::redirect(router('student.internship'))->send(); exit;
    }
} catch (Exception $e) {
    Notification::swal2('Security Warning', 'Invalid Identifier.', 'warning');
    Response::redirect(router('student.internship'))->send(); exit;
}

// --- 2. SWITCH METHOD ---
switch ($method) :

    case 'index': 
        $row = DB::table('industry_feedback')->where('id_internship', $id_internship)->first();

        return renderView('student.my_internship.industry_feedback.index', [
            'title'      => 'Industry Feedback',
            'routes'    => 'my_internship',
            'id_enc'     => $id,
            'row'        => $row,
            'internship' => $internship
        ]);
    break;
    case 'download' :
        $feedback = DB::table('industry_feedback')->where('id_internship', $id_internship)->first();
        if (!$feedback || $feedback['terisi'] == 0) {
            Notification::swal2('Empty', 'Industry has not filled the evaluation yet.', 'info');
            Redirect::back(1); exit;
        }

        // Ambil parameter bahasa (Lowercase untuk konsistensi key array)
        $lang = strtolower($language ?? 'id'); 
        
        $student = DB::table('student_internship as si')
            ->join('program_study as ps', 'ps.kode_prodi', '=', 'si.program_study')
            ->where('si.nim', $nim)->first();
        $company = DB::table('company')->where('id_company', $internship['id_company'])->first();

        // 1. Definisikan Sumber Data Bahasa (Localization Dictionary)
        $dict = [
            'title' => [
                'id' => 'BORANG EVALUASI INDUSTRI TERHADAP MAHASISWA MAGANG',
                'en' => 'INDUSTRY EVALUATION FORM FOR INTERNSHIP STUDENT'
            ],
            'section' => [
                'a' => ['id' => 'A. Data Peserta Magang', 'en' => 'A. Intern Data'],
                'b' => ['id' => 'B. Komentar Keseluruhan', 'en' => 'B. Overall Comments'],
                'c' => ['id' => 'C. Parameter Penilaian', 'en' => 'C. Evaluation Parameters'],
            ],
            'label' => [
                'name_nim'  => ['id' => 'Nama / NIM', 'en' => 'Name / NIM'],
                'prodi'     => ['id' => 'Program Studi', 'en' => 'Study Program'],
                'company'   => ['id' => 'Perusahaan', 'en' => 'Company'],
                'cat_mhs'   => ['id' => 'Catatan untuk Mahasiswa:', 'en' => 'Comments for Intern:'],
                'cat_pt'    => ['id' => 'Catatan untuk Politeknik:', 'en' => 'Comments for Polytechnic:'],
                'rec'       => ['id' => 'Kelayakan Rekrutmen:', 'en' => 'Recruitment Eligibility:'],
                'final'     => ['id' => 'NILAI AKHIR', 'en' => 'FINAL SCORE'],
                'criteria'  => ['id' => 'Kriteria Penilaian', 'en' => 'Evaluation Criteria'],
                'generated' => ['id' => 'Dicetak pada:', 'en' => 'Generated on:'],
            ],
            'params' => [
                'etika' => [
                    'id' => 'Etika, sikap, dan perilaku di lingkungan profesional.',
                    'en' => 'Ethics, attitude, and behavior in professional environment.'
                ],
                'keahlian_kompetensi' => [
                    'id' => 'Keahlian kompetensi inti selama bekerja.',
                    'en' => 'Core competency skills demonstrated during work.'
                ],
                'keahlian_bahasa' => [
                    'id' => 'Kecakapan bahasa asing dan komunikasi.',
                    'en' => 'Foreign language proficiency and communication.'
                ],
                'penggunaan_ti' => [
                    'id' => 'Literasi dan penggunaan teknologi informasi.',
                    'en' => 'Information technology literacy and application.'
                ],
                'komunikasi' => [
                    'id' => 'Keterampilan komunikasi dengan rekan kerja.',
                    'en' => 'Communication skills with coworkers and clients.'
                ],
                'kerjasama' => [
                    'id' => 'Kemampuan kerjasama dan kolaborasi tim.',
                    'en' => 'Teamwork and collaboration ability.'
                ],
                'pengembangan_diri' => [
                    'id' => 'Inisiatif dan kemauan pengembangan diri.',
                    'en' => 'Initiative and personal development willingness.'
                ]
            ]
        ];

        try {

            // 2. Konstruksi HTML menggunakan helper dictionary di atas
            $html = '
            <style>
                body { font-family: "Arial", sans-serif; font-size: 10pt; color: #333; }
                .header { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
                .table-border { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .table-border th, .table-border td { border: 1px solid #000; padding: 8px; font-size: 9pt; }
                .bg-gray { background-color: #f2f2f2; font-weight: bold; }
                .text-center { text-align: center; }
                .comment-box { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; font-style: italic; min-height: 40px; }
                .check { font-family: "DejaVu Sans", sans-serif; font-weight: bold; color: #1572e8; }
            </style>

            <div class="header">
                <table width="100%">
                    <tr>
                        <td width="15%"><img src="assets/img/polibatam.png" width="60"></td>
                        <td><b style="font-size: 12pt;">' . $dict['title'][$lang] . '</b></td>
                    </tr>
                </table>
            </div>

            <h4>' . $dict['section']['a'][$lang] . '</h4>
            <table width="100%" style="margin-bottom: 20px;">
                <tr><td width="25%">' . $dict['label']['name_nim'][$lang] . '</td><td width="2%">:</td><td>' . $student['name'] . ' / ' . $student['nim'] . '</td></tr>
                <tr><td>' . $dict['label']['prodi'][$lang] . '</td><td>:</td><td>' . $student['jenjang'] . ' ' . $student['prodi'] . '</td></tr>
                <tr><td>' . $dict['label']['company'][$lang] . '</td><td>:</td><td>' . $company['name'] . '</td></tr>
            </table>

            <h4>' . $dict['section']['b'][$lang] . '</h4>
            <b>' . $dict['label']['cat_mhs'][$lang] . '</b><div class="comment-box">' . ($feedback['catatan_utk_mahasiswa'] ?: '-') . '</div>
            <b>' . $dict['label']['cat_pt'][$lang] . '</b><div class="comment-box">' . ($feedback['catatan_utk_poltek'] ?: '-') . '</div>
            <b>' . $dict['label']['rec'][$lang] . '</b><div class="comment-box">' . ($feedback['layak_diterima'] ?: '-') . '</div>

            <h4>' . $dict['section']['c'][$lang] . '</h4>
            <table class="table-border">
                <thead>
                    <tr class="bg-gray">
                        <th width="5%">No</th>
                        <th>' . $dict['label']['criteria'][$lang] . '</th>
                        <th width="10%">4</th><th width="10%">3</th><th width="10%">2</th><th width="10%">1</th>
                    </tr>
                </thead>
                <tbody>';
            
            $i = 1;
            foreach ($dict['params'] as $db_key => $texts) {
                $html .= '<tr>
                    <td class="text-center">' . $i++ . '</td>
                    <td>' . $texts[$lang] . '</td>';
                for ($v = 4; $v >= 1; $v--) {
                    $tick = ($feedback[$db_key] == $v) ? '<span class="check">✓</span>' : '';
                    $html .= '<td class="text-center">' . $tick . '</td>';
                }
                $html .= '</tr>';
            }

            $html .= '
                    <tr class="bg-gray">
                        <td colspan="2" style="text-align: right;">' . $dict['label']['final'][$lang] . '</td>
                        <td colspan="4" class="text-center" style="font-size: 14pt;">' . $feedback['nilai_akhir'] . '</td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 30px; text-align: right;">
                <p>' . $dict['label']['generated'][$lang] . ' ' . date('d M Y') . '</p>
            </div>';

            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
            $mpdf->WriteHTML($html);
            $mpdf->Output("IndustryFeedback_" . $lang . "_" . $student['nim'] . ".pdf", "D");
            exit;

        } catch (Exception $e) {
            Notification::snackbar("Export Error: " . $e->getMessage());
            Redirect::back(1); exit;
        }
    break;

    case 'download_word' :
        // 1. Ambil data dengan Query Builder (Lebih aman dari PDO manual)
        $id_internship = Encrypter::decrypt($id);
        $feedback = DB::table('industry_feedback')->where('id_internship', $id_internship)->first();

        // Validasi ketersediaan data
        if (!$feedback || $feedback['terisi'] == 0) {
            Notification::swal2('error', 'Not Found', 'Industry feedback not found or not yet filled!');
            Redirect::back(1); exit;
        }

        // Ambil Data Pendukung (Internship, Student, Company, Supervisor)
        $internship = DB::table('internship')->where('id_internship', $id_internship)->first();
        $student = DB::table('student_internship as si')
            ->join('program_study as ps', 'ps.kode_prodi', '=', 'si.program_study')
            ->where('si.nim', $internship['nim'])->first();
        $company = DB::table('company')->where('id_company', $internship['id_company'])->first();
        $supervisor = DB::table('user_company')->where('id_user_company', $internship['id_user_company'])->first();

        // 2. Pilih Template berdasarkan bahasa (ID / EN)
        $lang = strtoupper($language ?? 'EN');
        $templateFile = ($lang == 'ID') ? 'industry_feedback_template_ID.docx' : 'industry_feedback_template_EN.docx';

        try {
            require_once './vendor/autoload.php';
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('./word/' . $templateFile);

            // 3. Set Values (Mapping Data ke Template Word)
            $templateProcessor->setValues([
                'current_url'     => base_url(),
                'current_time'    => date('Y-m-d H:i:s'),
                'current_date'    => date('d M Y', strtotime($feedback['date'])),
                'tahun'           => date('Y', strtotime($feedback['date'])),
                'nama_mahasiswa'  => $student['name'],
                'nim_mahasiswa'   => $student['nim'],
                'prodi_mahasiswa' => $student['jenjang'] . ' ' . $student['prodi'],
                'nama_perusahaan' => $company['name'],
                'nama_supervisor' => $supervisor['user_fullname'] ?? 'N/A',
                'nilai_akhir'     => $feedback['nilai_akhir'],
                'catatan_utk_mahasiswa' => htmlspecialchars($feedback['catatan_utk_mahasiswa']),
                'catatan_utk_poltek'    => htmlspecialchars($feedback['catatan_utk_poltek']),
                'layak_diterima'        => htmlspecialchars($feedback['layak_diterima']),
                'langsung_diterima'     => htmlspecialchars($feedback['langsung_diterima']),
            ]);

            // 4. Logika Centang (✓) Otomatis
            $criteria_fields = [
                'etika' => 'etika_',
                'keahlian_kompetensi' => 'keahlian_kompetensi_',
                'keahlian_bahasa' => 'keahlian_bahasa_',
                'penggunaan_ti' => 'penggunaan_ti_',
                'komunikasi' => 'komunikasi_',
                'kerjasama' => 'kerjasama_',
                'pengembangan_diri' => 'pengembangan_diri_'
            ];

            $centang = [];
            foreach ($criteria_fields as $dbKey => $prefix) {
                for ($i = 1; $i <= 4; $i++) {
                    // Jika nilai database sesuai dengan iterasi (1-4), beri centang
                    $centang[$prefix . $i] = ($feedback[$dbKey] == $i) ? '✓' : '';
                }
            }
            $templateProcessor->setValues($centang);

            // 5. Eksekusi Download & Cleanup
            $fileName = "IndustryFeedback_" . $student['nim'] . ".docx";
            $tempPath = './word/generate/' . uniqid() . '_' . $fileName;
            $templateProcessor->saveAs($tempPath);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Length: ' . filesize($tempPath));
            
            readfile($tempPath);

            // Hapus file sementara jika bukan bulk download
            if (!isset($_GET['bulk_download'])) {
                unlink($tempPath);
            }
            exit;

        } catch (Exception $e) {
            Notification::snackbar("Word Export error: " . $e->getMessage());
            Redirect::back(1); exit;
        }
    break;

endswitch;