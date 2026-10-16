<?php
    $fmtEng = new IntlDateFormatter(
        'en_US',
        IntlDateFormatter::LONG,
        IntlDateFormatter::NONE,
        'Asia/Jakarta',
        IntlDateFormatter::GREGORIAN,
        'MMMM d, yyyy'
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
    $program_study = ($student['jenjang'] ?? '') . ' ' . ($student['study_program'] ?? '');

    $logoPolibatam = base_url('assets/img/polibatam.png');
    $logoUKAS = base_url('assets/img/ukas.png');
    $stempel = base_url('assets/img/ttd.png');
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Industrial Internship Letter</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="<?php echo e(base_url('assets/css/pdf.css')); ?>" />
</head>

<body>
    <!-- SURAT PERMOHONAN MAGANG -->
    <div class="container-permohonan" id="content-permohonan">
        <div class="header-permohonan">
            <div class="logo-left">
                <img src="<?php echo e($logoPolibatam); ?>" alt="Batam State Polytechnic Logo">
            </div>
            <div class="header-text-permohonan">
                <h1>MINISTRY OF HIGHER EDUCATION,</h1>
                <h1>SCIENCE, AND TECHNOLOGY</h1>
                <h2>BATAM STATE POLYTECHNIC</h2>

                <p>Jalan Ahmad Yani, Batam Centre, Batam 29461, Riau Islands</p>
                <p>Phone +62 778 469626 - 469660 • Fax +62 778 463620</p>
                <p>Website: www.polibatam.ac.id • Email: info@polibatam.ac.id</p>
            </div>
            <div class="logo-right">
                <img src="<?php echo e($logoUKAS); ?>" alt="UKAS Logo">
            </div>
        </div>
        <div class="header-border"></div>

        <div class="date">
            Batam, <?php echo e($dateStr); ?>

        </div>

        <div class="letter-info">
            <table>
                <tr>
                    <td style="width: 80px;">Number</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($nomorFormatted) ?></td>
                </tr>
                <tr>
                    <td>Attachment</td>
                    <td>:</td>
                    <td>1 (one) file</td>
                </tr>
                <tr>
                    <td>Subject</td>
                    <td>:</td>
                    <td><strong><u>Industrial Internship Application</u></strong></td>
                </tr>
            </table>
        </div>

        <div class="recipient">
            <strong>To the Management of</strong><br>
            <strong><?php echo e($company_name); ?></strong><br>
            <strong><?php echo e($company_address); ?></strong><br>
            <strong><?php echo e($company_city); ?>, <?php echo e($company_province); ?></strong>
        </div>

        <div class="content-permohonan">
            <p>Dear Sir/Madam,</p>
            
            <p>We would like to express our deepest appreciation to <em><strong><?php echo e($company_name); ?></strong></em>
                for its continued cooperation in the student industrial internship program. The internship program has made a significant contribution to
                improving work skills and fostering knowledge about the professional environment, processes, and work culture within
                companies and institutions. Ultimately, this program fosters discipline and professional conduct among
                students, which is crucial in preparing a competent and job-ready younger generation.</p>

            <p>Through this letter, we kindly request the following student(s) to be accepted for an industrial internship
                at your company:</p>

            <table class="student-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>NIM</th>
                        <th>Study Program</th>
                        <th>Internship Period</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td style="text-transform: uppercase; font-weight: bold;"><?php echo e($student_name); ?></td>
                        <td><?php echo e($student_nim); ?></td>
                        <td><?php echo e($program_study); ?></td>
                        <td><?php echo e($periode); ?></td>
                    </tr>
                </tbody>
            </table>

            <p>
                Detailed information regarding the <strong>student's competencies and recommended internship roles</strong> 
                (such as <em>Software Development, IT Support,</em> or related fields) can be accessed via the following link: 
                <a href="https://polibatam.id/KompetensiMagangPolibatam" style="color: blue; text-decoration: underline;">https://polibatam.id/KompetensiMagangPolibatam</a>. 
                We have also provided a <strong>response letter template</strong> for your convenience to assist in the acceptance confirmation process.
            </p>

            <p>Please send the confirmation of internship acceptance via email to 
                <a href="mailto:cdc@polibatam.ac.id" style="color: blue;">cdc@polibatam.ac.id</a> 
                no later than 10 working days after receiving this letter. If no confirmation is received by the deadline, we will assume that your company is unable to accept interns for this period.
            </p>

            <p>Should your company require internship positions other than those we have proposed, please fill out the requirements form at the following link: 
                <a href="https://polibatam.id/FormKebutuhanMagang" style="color: blue;">https://polibatam.id/FormKebutuhanMagang</a>.
            </p>
        </div>

        <div class="closing">
            <p>Thank you for your kind attention and cooperation. We look forward to a positive response.</p>
        </div>

        <div class="signature-section">
            <div class="tembusan-box">
                <div class="tembusan-title">CC:</div>
                <div class="tembusan-content">Director of Batam State Polytechnic</div>
            </div>

            <div class="signature-box">
                <div class="signature-title">Deputy Director III</div>
                <div class="signature-subtitle">Student Affairs, Alumni, and Cooperation</div>
                <div class="signature-image">
                    <img src="<?php echo e($stempel); ?>" alt="Digital Signature and Official Stamp">
                </div>
                <div class="signature-name">Dr. H. Muhammad Zaenuddin, S.Si., M.Sc.</div>
            </div>
        </div>

        <div class="footer-generated">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="text-align: left; width: 63%; vertical-align: top; border: none; padding: 0;">
                        Requester: <?php echo e($student_name); ?> (<?php echo e($student_nim); ?>)<br>
                        Issued by: CDC Batam State Polytechnic<br>
                        Letter Number: <code style="font-family: monospace; font-weight: bold;"><?php echo e($document_id); ?></code>
                    </td>
                    <td style="text-align: right; width: 37%; vertical-align: top; border: none; padding: 0;">
                        Printed on: <?php echo e(date('d F Y, H:i:s')); ?> WIB<br>
                        IP Address: <?php echo e($client_ip); ?><br>
                        Source: <em>myinternship.id</em>
                    </td>
                </tr>
            </table>
            <div style="margin-top: 8px; border-top: 0.5px solid #ccc; padding-top: 5px; font-style: italic; font-size: 6.5pt;">
                * This is an official document generated automatically by the MyInternship system. Document authenticity can be verified by validating the unique ID through the system or by contacting the CDC Batam State Polytechnic unit.
            </div>
        </div>
    </div>





    <!-- SURAT BALASAN MAGANG -->
    <div class="container-balasan" id="content-balasan">
        <div class="header-balasan">
            <div class="logo-balasan">
                <img src="assets/img/polibatam.png" alt="Logo Politeknik Negeri Batam">
            </div>
            <div class="header-text-balasan">
                <div class="header-title">No.FO.8.4.1.1-V0 Internship Application Response Letter Format</div>
                <div class="header-date">March 23, 2020</div>
            </div>
        </div>

        <div class="location">
            Batam, ..............................
        </div>

        <div class="letter-meta">
            <table>
                <tr>
                    <td>No</td>
                    <td>:</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Attachment</td>
                    <td>:</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Subject</td>
                    <td>:</td>
                    <td>Response to Industrial Internship Application Letter</td>
                </tr>
            </table>
        </div>

        <div class="letter-meta" style="margin-top: 30px;">
            <div>Dear Sir/Madam</div>
            <div>Head of CDC Batam State Polytechnic</div>
        </div>

        <div class="greeting">
            Sincerely,
        </div>

        <div class="content-balasan">
            Based on the Industrial Internship Application Letter Number ................... submitted to our company,
            we hereby inform you that the following students:
        </div>


        <table class="student-table-balasan">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 40px;">No.</th>
                    <th rowspan="2" style="width: 180px;">Name</th>
                    <th rowspan="2" style="width: 100px;">NIM</th>
                    <th rowspan="2" style="width: 100px;">Study<br>Program</th>
                    <th rowspan="2" style="width: 110px;">*Internship Period</th>
                    <th colspan="2">*Description</th>
                    <th rowspan="2" style="width: 130px;">Section<br>Placement</th>
                </tr>
                <tr>
                    <th class="checkbox-cell">Accepted</th>
                    <th class="checkbox-cell">Not<br>Accepted</th>
                </tr>
            </thead>
            <tbody id="studentTable">
                <tr>
                    <td>1.</td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td class="checkbox-cell">
                        <div class="checkbox-container" onclick="toggleCheckbox(this)"></div>
                    </td>
                    <td class="checkbox-cell">
                        <div class="checkbox-container" onclick="toggleCheckbox(this)"></div>
                    </td>
                    <td><input type="text"></td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td class="checkbox-cell">
                        <div class="checkbox-container" onclick="toggleCheckbox(this)"></div>
                    </td>
                    <td class="checkbox-cell">
                        <div class="checkbox-container" onclick="toggleCheckbox(this)"></div>
                    </td>
                    <td><input type="text"></td>
                </tr>
                <tr>
                    <td>3.</td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td class="checkbox-cell">
                        <div class="checkbox-container"></div>
                    </td>
                    <td class="checkbox-cell">
                        <div class="checkbox-container"></div>
                    </td>
                    <td><input type="text"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                    <td><input type="text"></td>
                </tr>
            </tbody>
        </table>

        <div class="note">
            *This section is filled in by the company.
        </div>

        <div class="content-balasan">
            We hereby submit this letter and thank you for your attention and cooperation.
        </div>

        <div class="closing-balasan">
            Sincerely,

            <div class="signature-balasan">
                <div class="signature-circle">
                    Stamp<br>& Signature
                </div>
                <div class="signature-text">
                    <div>Name</div>
                    <div>Position</div>
                    <div>Company Name</div>
                </div>
            </div>
        </div>

        <div class="footer-note">
            <strong>Note:</strong> The letterhead may be replaced with the company letterhead.
        </div>

    </div>



</body>

</html><?php /**PATH C:\laragon\www\myinternship\views/print/internship_letter_en.blade.php ENDPATH**/ ?>