<?php
    $fmtEng = new IntlDateFormatter(
        'id_ID',
        IntlDateFormatter::LONG,
        IntlDateFormatter::NONE,
        'Asia/Jakarta',
        IntlDateFormatter::GREGORIAN,
        'd MMMM yyyy'
    );

    $start_date = $fmtEng->format(new DateTime($letter['expected_start']));
    $end_date   = $fmtEng->format(new DateTime($letter['expected_end']));
    $dateStr = $fmtEng->format(new DateTime($letter['letter_date']));

    $periode    = $start_date . ' - ' . $end_date;

    $year = date('Y');
    $letterNumber = $letter['full_letter_number'] ?? '___';
    // $nomorFormatted = "{$letterNumber}/Magang/SuratPengantar/{$year}";
    $nomorFormatted = $letterNumber ;
    $document_id = $nomorFormatted;
    $client_ip = Router::getClientIp();

    $company_name = $company['name'] ?? 'Company Name';
    
    $company_address = $company['address'] ?? 'Company Address';
    $company_city = $company['city'] ?? 'Company City';
    $company_province = $company['province'] ?? 'Company Address';

    $student_name = $student['name'] ?? 'Student Name';
    $student_nim  = $student['nim'] ?? '0000000000';
    $program_study = ($student['jenjang'] ?? '') . ' ' . ($student['prodi'] ?? '');


    $logoPolibatam = base_url('assets/img/polibatam.png');
    $logoUKAS = base_url('assets/img/ukas.png');
    $stempel = base_url('assets/img/ttd.png');
?>

<?php
    // Formatter untuk tanggal cetak Indonesia
    $fmtIndo = new IntlDateFormatter(
        'id_ID',
        IntlDateFormatter::LONG,
        IntlDateFormatter::SHORT, // Menampilkan jam juga
        'Asia/Jakarta',
        IntlDateFormatter::GREGORIAN
    );
    $printed_date_id = $fmtIndo->format(new DateTime());
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Permohonan Magang Industri</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/pdf.css') ?>">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<body>

    <!-- ========================================================= -->
    <!--                SURAT PERMOHONAN MAGANG                    -->
    <!-- ========================================================= -->

    <div class="container-permohonan" id="content-permohonan">

        <div class="header-permohonan">
            <div class="logo-left">
                <img src="<?= $logoPolibatam ?>" alt="Logo Polibatam">
            </div>

            <div class="header-text-permohonan">
                <h1>KEMENTERIAN PENDIDIKAN TINGGI,</h1>
                <h1>SAINS, DAN TEKNOLOGI</h1>
                <h2>POLITEKNIK NEGERI BATAM</h2>

                <p>Jalan Ahmad Yani, Batam Centre, Batam 29461, Kepulauan Riau</p>
                <p>Telepon +62 778 469626 - 469660 • Faks +62 778 463620</p>
                <p>Laman: www.polibatam.ac.id • Surel: info@polibatam.ac.id</p>
            </div>

            <div class="logo-right">
                <img src="<?= $logoUKAS ?>" alt="Logo UKAS">
            </div>
        </div>

        <div class="header-border"></div>

        <div class="date">
            Batam, <?= $dateStr ?>
        </div>

        <div class="letter-info">
            <table>
                <tr>
                    <td style="width: 80px;">Nomor</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($nomorFormatted) ?></td>
                </tr>
                <tr>
                    <td>Lampiran</td>
                    <td>:</td>
                    <td>1 (satu) berkas</td>
                </tr>
                <tr>
                    <td>Hal</td>
                    <td>:</td>
                    <td><strong><u>Permohonan Magang Industri</u></strong></td>
                </tr>
            </table>
        </div>

        <div class="recipient">
            <strong>Yth. Pimpinan</strong><br> <strong><?php echo e($company_name); ?></strong><br>
            <strong><?php echo e($company_address); ?></strong><br>
            <strong><?php echo e($company_city); ?>, <?php echo e($company_province); ?></strong>
        </div>

        <div class="content-permohonan">
            <p>Dengan hormat,</p> <p>
                Kami sampaikan apresiasi yang sebesar-besarnya kepada 
                <em><strong><?= htmlspecialchars($company_name) ?></strong></em>
                atas kerja samanya selama ini dalam kegiatan magang industri mahasiswa. 
                Pelaksanaan magang memberikan kontribusi yang signifikan dalam meningkatkan keterampilan kerja,
                serta menumbuhkan pemahaman terhadap lingkungan, proses, dan budaya kerja di perusahaan/instansi.
                Pada akhirnya, kegiatan magang akan membentuk kedisiplinan dan perilaku baik mahasiswa, yang krusial
                dalam mempersiapkan generasi muda yang kompeten dan siap kerja.
            </p>

            <p>
                Melalui surat ini, kami memohon agar mahasiswa berikut dapat diterima untuk melaksanakan magang industri
                di perusahaan/instansi yang Bapak/Ibu pimpin:
            </p>

            <table class="student-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Program Studi</th>
                        <th>Periode Magang</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td style="text-transform: uppercase; font-weight: bold;"><?= $student_name ?></td>
                        <td><?= $student_nim ?></td>
                        <td><?= $program_study ?></td>
                        <td><?= $periode ?></td>
                    </tr>
                </tbody>
            </table>

            <p>
                Informasi mengenai <strong>profil kompetensi serta rekomendasi posisi magang</strong> yang relevan bagi mahasiswa tersebut 
                (seperti <em>Software Development, IT Support,</em> atau bidang terkait lainnya) dapat diakses melalui 
                tautan: <a href="https://polibatam.id/KompetensiMagangPolibatam" style="color: blue; text-decoration: underline;">https://polibatam.id/KompetensiMagangPolibatam</a>. 
                Kami juga menyertakan draf surat balasan untuk memudahkan Bapak/Ibu dalam proses konfirmasi penerimaan.
            </p>

            <p>
                Konfirmasi penerimaan magang mohon dikirim melalui surel ke 
                <a href="mailto:cdc@polibatam.ac.id" style="color: blue;">cdc@polibatam.ac.id</a> 
                paling lambat 10 hari kerja setelah surat ini diterima. Apabila tidak ada konfirmasi sampai batas waktu tersebut,
                kami menganggap perusahaan Bapak/Ibu belum dapat menerima mahasiswa magang untuk periode ini.
            </p>

            <p>
                Apabila perusahaan membutuhkan posisi magang selain yang kami ajukan, Bapak/Ibu dapat mengisi formulir kebutuhan pada tautan berikut: 
                <a href="https://polibatam.id/FormKebutuhanMagang" style="color: blue;">https://polibatam.id/FormKebutuhanMagang</a>.
            </p>
        </div>

        <div class="closing">
            <p>Demikian permohonan ini kami sampaikan. Atas perhatian dan kerja sama yang baik, kami ucapkan terima kasih.</p>
        </div>

        <div class="signature-section">
            <div class="tembusan-box">
                <div class="tembusan-title">Tembusan:</div>
                <div class="tembusan-content">Direktur Politeknik Negeri Batam</div>
            </div>

            <div class="signature-box">
                <div class="signature-title">Wakil Direktur III</div>
                <div class="signature-subtitle">Bidang Kemahasiswaan, Alumni dan Kerja Sama</div>
                <div class="signature-image">
                    <img src="<?= $stempel ?>" alt="Tanda Tangan dan Stempel Resmi">
                </div>
                <div class="signature-name">Dr. H. Muhammad Zaenuddin, S.Si., M.Sc.</div>
            </div>
        </div>

        <div class="footer-generated">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="text-align: left; width: 63%; vertical-align: top; border: none; padding: 0;">
                        Pemohon: <?php echo e($student_name); ?> (<?php echo e($student_nim); ?>)<br>
                        Diterbitkan oleh: CDC Politeknik Negeri Batam<br>
                        ID Verifikasi: <code style="font-family: monospace; font-weight: bold;"><?php echo e($document_id); ?></code>
                    </td>
                    <td style="text-align: right; width: 37%; vertical-align: top; border: none; padding: 0;">
                        Dicetak pada: <?php echo e($printed_date_id); ?> WIB<br>
                        Alamat IP: <?php echo e($client_ip); ?><br>
                        Sumber: <em>myinternship.polibatam.ac.id</em>
                    </td>
                </tr>
            </table>
            <div style="margin-top: 8px; border-top: 0.5px solid #ccc; padding-top: 5px; font-style: italic; font-size: 6.5pt;">
                * Ini adalah dokumen resmi yang dihasilkan secara otomatis oleh sistem MyInternship. Verifikasi keaslian dapat dilakukan dengan memvalidasi ID unik dokumen pada sistem atau menghubungi unit CDC Politeknik Negeri Batam.
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!--                SURAT BALASAN PERMOHONAN MAGANG            -->
    <!-- ========================================================= -->

    <div class="container-balasan" id="content-balasan">

        <div class="header-balasan">
            <div class="logo-balasan">
                <img src="<?= $logoPolibatam ?>" alt="Logo Polibatam">
            </div>
            <div class="header-text-balasan">
                <div class="header-title">No.FO.8.4.1.1-V0 Format Surat Balasan Permohonan Magang</div>
                <div class="header-date">23 Maret 2020</div>
            </div>
        </div>

        <div class="location">
            Batam, ..............................
        </div>

        <div class="letter-meta">
            <table>
                <tr><td>Nomor</td><td>:</td><td></td></tr>
                <tr><td>Lampiran</td><td>:</td><td></td></tr>
                <tr><td>Hal</td><td>:</td><td>Balasan Surat Permohonan Magang Industri</td></tr>
            </table>
        </div>

        <div class="letter-meta" style="margin-top:20px;">
            <div>Kepada Yth.</div>
            <div>Kepala CDC Politeknik Negeri Batam</div>
        </div>

        <div class="greeting">Dengan hormat,</div>

        <div class="content-balasan">
            Berdasarkan Surat Permohonan Magang Industri Nomor .................... yang kami terima,
            bersama ini kami sampaikan bahwa mahasiswa berikut:
        </div>

        <table class="student-table-balasan">
            <thead>
                <tr>
                    <th rowspan="2" style="width:40px;">No.</th>
                    <th rowspan="2" style="width:180px;">Nama</th>
                    <th rowspan="2" style="width:100px;">NIM</th>
                    <th rowspan="2" style="width:120px;">Program Studi</th>
                    <th rowspan="2" style="width:110px;">*Periode Magang</th>
                    <th colspan="2">*Keterangan</th>
                    <th rowspan="2" style="width:130px;">Bagian Penempatan</th>
                </tr>
                <tr>
                    <th>Diterima</th>
                    <th>Tidak Diterima</th>
                </tr>
            </thead>

            <tbody id="studentTable">
                <?php for($i=1; $i<=9; $i++): ?>
                <tr>
                    <td><?= $i ?>.</td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><div class="checkbox-container" onclick="toggleCheckbox(this)"></div></td>
                    <td><div class="checkbox-container" onclick="toggleCheckbox(this)"></div></td>
                    <td><input type="text"></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <div class="note">
            *Bagian ini diisi oleh perusahaan.
        </div>

        <div class="content-balasan">
            Demikian surat ini kami sampaikan. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.
        </div>

        <div class="closing-balasan">
            Hormat kami,

            <div class="signature-balasan">
                <div class="signature-circle">
                    Cap<br>& Tanda<br>Tangan
                </div>
                <div class="signature-text">
                    <div>Nama</div>
                    <div>Jabatan</div>
                    <div>Nama Perusahaan</div>
                </div>
            </div>
        </div>

        <div class="footer-note">
            <strong>Catatan:</strong> Kop surat boleh diganti dengan kop surat perusahaan.
        </div>

    </div>

</body>
</html>
<?php /**PATH C:\laragon\www\myinternship\views/print/internship_letter_id.blade.php ENDPATH**/ ?>