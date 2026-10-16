<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Feedback - <?php echo e($student['nim']); ?></title>
    <style>
        /* Desain A4 Presisi */
        body { font-family: "Times New Roman", Times, serif; background: #525659; padding: 30px; }
        .page { background: white; width: 210mm; min-height: 297mm; padding: 20mm; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.5); }
        
        /* Header Logo Politeknik */
        .header-table { width: 100%; border-bottom: 2px solid #000; margin-bottom: 20px; }
        .logo-img { width: 80px; }
        .doc-title { font-size: 14pt; font-weight: bold; }

        /* Table Styling [cite: 3, 5] */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-data td { padding: 5px; vertical-align: top; }
        .table-border, .table-border th, .table-border td { border: 1px solid black; padding: 8px; font-size: 10pt; }
        .text-center { text-align: center; }
        
        .check-cell { width: 40px; text-align: center; font-weight: bold; font-size: 14pt; }
        @media  print { body { background: none; padding: 0; } .page { box-shadow: none; margin: 0; } .no-print { display: none; } }
    </style>

    <style>
    /* Kontainer Utama */
    #print-area {
        width: 210mm; /* Kunci lebar standar A4 */
        min-height: 297mm;
        margin: 0 auto;
        padding: 20mm;
        background: white;
        box-sizing: border-box; /* Penting agar padding tidak menambah lebar */
        overflow: hidden; /* Mencegah konten meluber keluar */
    }

    /* Hindari Pemotongan Baris Tengah */
    .table-border tr, .narrative-section {
        page-break-inside: avoid;
    }

    /* Sembunyikan Scrollbar saat proses capture */
    body { overflow-x: hidden; }

    @media  print {
        body { background: none; }
        #print-area { margin: 0; border: none; }
    }
</style>
</head>
<body>

<div class="no-print" style="text-align: center; margin-bottom: 20px;">
    <button onclick="generatePDF()" style="padding: 10px 20px; cursor: pointer; background: #1572e8; color: white; border: none; border-radius: 5px;">
        📥 DOWNLOAD PDF NOW
    </button>
</div>

<div class="page" id="print-area">
    <table class="header-table">
        <tr>
            <td width="15%"><img src="<?php echo e(base_url('assets/img/logo_poltek.png')); ?>" class="logo-img"></td>
            <td>
                <div class="doc-title">No.BO.8.4.3.3-V1 Borang Umpan Balik Magang dari Mahasiswa</div>
                <div style="font-size: 11pt;">12 September 2017</div>
            </td>
        </tr>
    </table>

    <p style="text-align: center; font-weight: bold;">Tahun <?php echo e(date('Y', strtotime($row['date']))); ?></p>

    <p><b>A. Data Peserta Magang</b></p>
    <table class="table-data">
        <tr><td width="25%">Nama</td><td width="2%">:</td><td><?php echo e($student['name']); ?></td></tr>
        <tr><td>NIM</td><td>:</td><td><?php echo e($student['nim']); ?></td></tr>
        <tr><td>Program Studi</td><td>:</td><td><?php echo e($student['jenjang']); ?> <?php echo e($student['prodi']); ?></td></tr>
        <tr><td>Tempat Magang</td><td>:</td><td><?php echo e($company['name']); ?></td></tr>
    </table>

    <p><b>B. Parameter Penilaian</b></p>
    <table class="table-border">
        <thead>
            <tr style="background: #f2f2f2;">
                <th rowspan="2" width="5%">No</th>
                <th rowspan="2">Parameter</th>
                <th colspan="4">Tanggapan</th>
            </tr>
            <tr style="background: #f2f2f2;">
                <th>Sangat Setuju (4)</th><th>Setuju (3)</th><th>Kurang Setuju (2)</th><th>Tidak Setuju (1)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $criteria = [
                    'posisi' => 'Posisi tempat magang sesuai dengan bidang ilmu',
                    'ilmu_dari_poltek' => 'Ilmu yang didapat di kampus dapat mengimplementasikan di tempat magang',
                    'ilmu_baru' => 'Mendapat ilmu baru yang tidak di dapat di kampus',
                    'data_magang' => 'Mendapatkan data dari tempat magang yang digunakan untuk laporan Magang atau TA'
                ];
                $no = 1;
            ?>
            <?php $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($no++); ?></td>
                <td><?php echo e($label); ?></td>
                <td class="check-cell"><?php echo e($row[$key] == 4 ? '✓' : ''); ?></td>
                <td class="check-cell"><?php echo e($row[$key] == 3 ? '✓' : ''); ?></td>
                <td class="check-cell"><?php echo e($row[$key] == 2 ? '✓' : ''); ?></td>
                <td class="check-cell"><?php echo e($row[$key] == 1 ? '✓' : ''); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <p><b>C. Kesan dan Pesan terhadap tempat magang</b></p>
    <div style="margin-left: 15px;">
        <p>1. Kesan Terhadap Tempat Magang :</p>
        <p style="font-style: italic; border-bottom: 1px solid #ccc; padding-bottom: 5px;"><?php echo e($row['kesan']); ?></p>
        
        <p>2. Kendala Ketika Magang :</p>
        <p style="font-style: italic; border-bottom: 1px solid #ccc; padding-bottom: 5px;"><?php echo e($row['kendala']); ?></p>
        
        <p>3. Masukan Bagi Politeknik Negeri Batam :</p>
        <p style="font-style: italic; border-bottom: 1px solid #ccc; padding-bottom: 5px;"><?php echo e($row['masukan']); ?></p>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function generatePDF() {
        const element = document.getElementById('print-area');
        
        // Konfigurasi Anti-Potong
        const opt = {
            margin:       0,
            filename:     'StudentFeedback_<?php echo e($student["nim"]); ?>.pdf',
            image:        { type: 'jpeg', quality: 1.0 },
            html2canvas:  { 
                scale: 3, // Skala tinggi untuk mencegah blur dan clipping
                useCORS: true,
                letterRendering: true,
                width: 794, // Lebar pixel standar A4 (96 DPI)
            },
            jsPDF:        { 
                unit: 'mm', 
                format: 'a4', 
                orientation: 'portrait',
                compress: true
            }
        };

        // Jalankan proses generate
        html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
            // Opsional: Anda bisa menambahkan nomor halaman di sini
        }).save();
    }
</script>
</body>
</html><?php /**PATH C:\laragon\www\myinternship\views/student/my_internship/student_feedback/print.blade.php ENDPATH**/ ?>