<?php defined('BASE_PATH') or header('Location: /');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

require_once('./vendor/autoload.php');

// === Hak akses ===
if (!in_array(strtolower($_SESSION['role']), ['upkpk', 'cdc', 'lecturer'])) {
    renderView('errors.error_404', ['title' => '404 - Not Found']);
    exit;
}

$pdo = $_SERVER['PDO'];

// === Query ambil data company dan user_company ===
$stmt = $pdo->query("
    SELECT 
        c.id_company, c.name, c.type, c.type_other, c.phone, c.email, c.website, 
        c.facebook, c.twitter, c.instagram, c.linkedin, c.address, 
        c.province, c.city, c.description, c.status, c.access_type, c.id_kampus,
        uc.user_fullname, uc.user_phone, uc.user_email, uc.username, uc.password, uc.user_type
    FROM 
        company c
    LEFT JOIN 
        user_company uc ON c.id_company = uc.id_company
    ORDER BY 
        c.id_company ASC
");

$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $company_id = $row['id_company'];

    if (!isset($data[$company_id])) {
        $data[$company_id] = [
            'name' => $row['name'],
            'type' => $row['type'],
            'phone' => $row['phone'],
            'email' => $row['email'],
            'website' => $row['website'],
            'facebook' => $row['facebook'],
            'twitter' => $row['twitter'],
            'instagram' => $row['instagram'],
            'linkedin' => $row['linkedin'],
            'address' => $row['address'],
            'province' => $row['province'],
            'city' => $row['city'],
            'users' => []
        ];
    }

    if (!empty($row['user_fullname'])) {
        $user_display = "[" . $row['user_type'] . "] | Nama : " . $row['user_fullname'] . " | Username : " . $row['username'] . " | Email : " . $row['user_email'] . " | Nomor Telepon : " . $row['user_phone'];
        $data[$company_id]['users'][] = $user_display;
    }
}

// === Spreadsheet init
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

// === Informasi Generated On dan Server Name
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A1', 'Generated on : ' . date('Y-m-d H:i:s'));
$sheet->mergeCells('A2:C2');
$sheet->setCellValue('A2', 'From : ' . $_SERVER['APP_NAME'] . ' (' . base_url() . ')');

// === Judul Export
$sheet->mergeCells('A4:N4');
$sheet->setCellValue('A4', 'Company Export');
$styleA4 = $sheet->getStyle('A4');
$styleA4->getFont()->setBold(true)->setSize(16);
$styleA4->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

// === Sub Header Merge
$sheet->mergeCells('A5:D5')->setCellValue('A5', 'Data Perusahaan');
$sheet->mergeCells('E5:I5')->setCellValue('E5', 'Media Sosial Perusahaan');
$sheet->mergeCells('J5:L5')->setCellValue('J5', 'Alamat');
$sheet->mergeCells('M5:N5')->setCellValue('M5', 'Akun Perusahaan');

$sheet->getStyle('A5:N5')->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
]);

// === Header Kolom (Row 6)
$headers = [
    'No', 'Nama Perusahaan', 'Jenis Perusahaan', 'Nomor Telepon',
    'Email', 'Website', 'Facebook', 'Twitter', 'Instagram', 'Linkedin',
    'Alamat', 'Provinsi', 'Kota', 'Users'
];
$sheet->fromArray($headers, NULL, 'A6');

// === Styling Header
$headerStyle = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'BDD7EE']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];
$sheet->getStyle('A6:N6')->applyFromArray($headerStyle);

// === Isi data mulai dari baris 7
$rowIndex = 7;
$counter = 1;
foreach ($data as $company) {
    $users = count($company['users']) > 0 ? implode(", ", $company['users']) : "(kosong)";

    $rowData = [
        $counter++,
        $company['name'],
        $company['type'],
        $company['phone'],
        $company['email'],
        $company['website'],
        $company['facebook'],
        $company['twitter'],
        $company['instagram'],
        $company['linkedin'],
        $company['address'],
        $company['province'],
        $company['city'],
        $users
    ];
    $sheet->fromArray($rowData, NULL, 'A' . $rowIndex);
    $rowIndex++;
}

// === Styling semua data
$lastRow = $rowIndex - 1;
$sheet->getStyle("A6:N$lastRow")->getAlignment()->setWrapText(true);
$sheet->getStyle("A6:N$lastRow")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle("A6:N$lastRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// === Auto width
foreach (range('A', 'N') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// === Freeze header row
$sheet->freezePane('A7');

// === Save dan download
$location = 'excel/generate/';
if (!is_dir($location)) { mkdir($location, 0777, true); }

$excel_name = 'company_exported_' . date('Ymd_His') . '.xlsx';
$file_path = $location . $excel_name;

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save($file_path);

// Download
if (file_exists($file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    unlink($file_path);
    exit;
} else {
    echo "Failed to Export";
}
