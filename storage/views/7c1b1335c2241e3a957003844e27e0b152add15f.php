<?php
    $fmt = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Asia/Jakarta');
    $tgl_sekarang = $fmt->format(new DateTime());
    
    $student_name = "Gilang Bagus Ramadhan";
    $student_nim  = "3311801013";
    $student_phone = "0812-XXXX-XXXX";
    $company_name = "PT. Inovasi Teknologi Batam";
    $company_address = "Kawasan Industri Batamindo, Muka Kuning, Batam";
    $supervisor_name = "Budi Santoso, S.T.";
    $supervisor_phone = "0813-YYYY-YYYY";
    $lecture_name = "Arna Fariza, S.Kom., M.Kom.";
    $lecture_nik = "198501012015012001";

    $logoPolibatam = base_url('assets/img/polibatam.png');
    $tte_placeholder = "https://placehold.co/120x80/fdfdfd/a0a0a0?text=TTE+VERIFIED";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Preview Logbook - <?php echo e($student_name); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* 1. KONFIGURASI MARGIN WORD SESUAI IMAGE_79FD37.PNG */
        @page  { 
            size: A4; 
            margin: 0; /* Margin dikelola oleh padding agar preview & print sinkron */
        }

        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-print-color-adjust: exact; }
        
        /* 2. STYLING PREVIEW (SCREEN ONLY) */
        body { 
            background-color: #525659; 
            display: flex; flex-direction: column; align-items: center;
            padding: 40px 0; font-family: 'Times New Roman', Times, serif;
        }

        /* Container Kertas A4 */
        .paper-sheet {
            background: white;
            width: 210mm;
            min-height: 297mm;
            padding: 0.96cm 0.69cm 1.42cm 1.78cm; /* Margin Word */
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            position: relative;
            /* Simulasi Garis Potong Halaman (Dashed Line) di Preview */
            background-image: linear-gradient(to bottom, transparent 296mm, #ccc 296mm, #ccc 297mm, transparent 297mm);
            background-size: 100% 297mm;
        }

        /* 3. TABLE SYSTEM UNTUK HEADER/FOOTER BERULANG */
        .master-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        
        .header-official { 
            display: flex; align-items: center; border-bottom: 2.5px solid black; 
            padding-bottom: 8px; margin-bottom: 15px; 
        }
        .header-logo { width: 68px; margin-right: 15px; }
        .header-text h5 { font-size: 14pt; font-weight: bold; margin-bottom: 2px; }
        .header-text p { font-size: 10pt; margin: 0; }

        /* 4. IDENTITAS & KONTEN */
        .table-identity { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10.5pt; }
        .table-identity td { border: 1px solid black !important; padding: 7px 10px; vertical-align: middle; }
        .bg-grey { background: #f2f2f2 !important; font-weight: bold; }
        .col-label { width: 35%; }

        .content-box { border: 1px solid black; padding: 12px; font-size: 10.5pt; text-align: justify; line-height: 1.4; }

        /* 5. PARALLEL SIGNATURE */
        .signature-container { display: flex; justify-content: space-between; margin-top: 30px; page-break-inside: avoid; }
        .sig-box { text-align: center; width: 45%; font-size: 10.5pt; }

        /* 6. FOOTER BERULANG DI DASAR SETIAP HALAMAN */
        .print-footer { 
            width: 100%; border-top: 1px solid black; padding-top: 5px; 
            font-size: 8pt; font-weight: bold; color: black;
            display: flex; justify-content: space-between;
        }

        /* 7. FAB & PRINT RULES */
        .fab-container { position: fixed; bottom: 30px; right: 30px; display: flex; flex-direction: column; gap: 10px; z-index: 999; }
        .btn-fab { width: 55px; height: 55px; border-radius: 12px; border: none; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
        .bg-print { background: #2ecc71; } .bg-back { background: #34495e; }

        @media  print {
            body { background: none; padding: 0; }
            .paper-sheet { 
                box-shadow: none; margin: 0; width: 100%; padding: 0; 
                background-image: none; /* Hilangkan garis bantu saat print */
            }
            .fab-container { display: none; }
            thead { display: table-header-group; } /* KUNCI: Ulangi header di tiap halaman fisik */
            tfoot { display: table-footer-group; } /* KUNCI: Ulangi footer di tiap halaman fisik */
            .page-break { page-break-before: always; padding-top: 10px; }
        }
    </style>
</head>
<body>

    <div class="fab-container">
        <button class="btn-fab bg-back" onclick="history.back()" title="Kembali"><i class="fas fa-arrow-left"></i></button>
        <button class="btn-fab bg-print" onclick="window.print()" title="Cetak / Simpan PDF"><i class="fas fa-print"></i></button>
    </div>

    <div class="paper-sheet">
        <table class="master-table">
            <thead>
                <tr>
                    <td>
                        <div class="header-official">
                            <img src="<?php echo e($logoPolibatam); ?>" class="header-logo">
                            <div class="header-text">
                                <h5>No.BO.8.4.3.1-V2 Borang Logbook Magang</h5>
                                <p>23 Maret 2020</p>
                            </div>
                        </div>
                    </td>
                </tr>
            </thead>
            
            <tbody>
                <tr>
                    <td>
                        <div style="text-align: center; margin-bottom: 15px;">
                            <h3 style="text-transform: uppercase; font-weight: bold; font-size: 13pt;">Logbook Magang Industri Minggu 6</h3>
                            <p style="font-size: 10.5pt;">tgl. 02 Feb 2026 s/d 08 Feb 2026</p>
                        </div>

                        <table class="table-identity">
                            <tr><td colspan="3" class="bg-grey">Identitas Mahasiswa</td></tr>
                            <tr><td class="col-label">Nama Mahasiswa</td><td style="width:5%; text-align:center;">:</td><td><?php echo e($student_name); ?></td></tr>
                            <tr><td>NIM</td><td style="text-align:center;">:</td><td><?php echo e($student_nim); ?></td></tr>
                            <tr><td colspan="3" class="bg-grey">Identitas Perusahaan dan Pembimbing di Perusahaan</td></tr>
                            <tr><td>Nama Perusahaan</td><td style="text-align:center;">:</td><td><?php echo e($company_name); ?></td></tr>
                            <tr><td>Pembimbing</td><td style="text-align:center;">:</td><td><?php echo e($supervisor_name); ?></td></tr>
                        </table>

                        <p style="font-weight: bold; margin-bottom: 5px;">Rincian Kegiatan Magang:</p>
                        <div class="content-box">
                            <?php for($i = 0; $i < 40; $i++): ?>
                                <p><b>Senin - Minggu:</b> Melakukan koordinasi tim pengembang, optimasi database Laravel, implementasi antarmuka Flutter, serta penyusunan dokumentasi teknis sistem logbook Politeknik Negeri Batam.</p>
                            <?php endfor; ?>
                        </div>

                        <div class="signature-container">
                            <div class="sig-box">
                                <p>Mengetahui,</p>
                                <p>Pembimbing di Perguruan Tinggi</p>
                                <div style="height: 80px; display: flex; align-items: center; justify-content: center;">
                                    <img src="<?php echo e($tte_placeholder); ?>" style="height: 60px;">
                                </div>
                                <p><b>( Arna Fariza, M.Kom )</b></p>
                                <p>NIK. <?php echo e($lecture_nik); ?></p>
                            </div>
                            <div class="sig-box">
                                <p>Batam, <?php echo e($tgl_sekarang); ?></p>
                                <p>Pembimbing di Perusahaan</p>
                                <div style="height: 80px; display: flex; align-items: center; justify-content: center;">
                                    <img src="<?php echo e($tte_placeholder); ?>" style="height: 60px;">
                                </div>
                                <p><b>( <?php echo e($supervisor_name); ?> )</b></p>
                                <p>Jabatan: Pembimbing Magang</p>
                            </div>
                        </div>

                        <div class="page-break">
                            <p style="font-weight: bold; border-bottom: 1.5px solid black; padding-bottom: 3px; margin-top: 20px;">Dokumentasi Kegiatan Magang</p>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                                <div style="border: 1px solid #ddd; padding: 5px; text-align: center;">
                                    <div style="height: 180px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; color: #999;">[FOTO KEGIATAN 1]</div>
                                    <p style="font-size: 8.5pt; margin-top: 5px;">Aktivitas Workspace</p>
                                </div>
                                <div style="border: 1px solid #ddd; padding: 5px; text-align: center;">
                                    <div style="height: 180px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; color: #999;">[FOTO KEGIATAN 2]</div>
                                    <p style="font-size: 8.5pt; margin-top: 5px;">Diskusi Koordinasi</p>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <td>
                        <div class="print-footer">
                            <span>Milik: <?php echo e($student_name); ?> (<?php echo e($student_nim); ?>)</span>
                            <span style="font-weight: normal; font-size: 7.5pt;">myinternship.id | <?php echo e(date('d/m/Y H:i')); ?></span>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

</body>
</html><?php /**PATH C:\laragon\www\myinternship\views/student/my_internship/logbook/print.blade.php ENDPATH**/ ?>