<?php
defined('BASE_DIR') or die('Direct script access is Not Allowed');

switch ($method) :

    # Ini adalah case lama yang dipanggil oleh Router
    case 'index':
        renderView('lecturer.company_list.index', [
            'title'   => 'Company List', 
            'routes'  => 'company_list',
        ]);
    break;

    case 'data':
        // 1. Inisialisasi Parameter Datatables
        $draw   = isset($_POST['draw']) ? (int)$_POST['draw'] : 0;
        $start  = isset($_POST['start']) ? (int)$_POST['start'] : 0;
        $length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
        $search = $_POST['search']['value'] ?? '';
        
        $orderColumnIndex = $_POST['order'][0]['column'] ?? 1;
        $orderDir = $_POST['order'][0]['dir'] ?? 'asc';
        
        $columns = [
            0 => 'id_company',
            1 => 'name',
            2 => 'type',
            3 => 'city',
            4 => 'status'
        ];
        $orderBy = $columns[$orderColumnIndex] ?? 'name';

        // 2. Hitung TOTAL DATA menggunakan Model
        $totalData = Company::count();

        // 3. Bangun Query menggunakan Model
        // (Asumsi base model kamu mendukung static call seperti Company::where)
        $query = Company::where('id_company', '>', 0); 

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('type', 'LIKE', "%{$search}%");
            });
        }

        // Hitung total data SETELAH difilter
        $totalFiltered = $query->count();

        // 4. Ambil Data (Result berupa Collection of Objects)
        $companies = $query->orderBy($orderBy, $orderDir)
                           ->offset($start)
                           ->limit($length)
                           ->get();

        // 5. Formatting Data menggunakan Helper Methods dari Model
        $data = [];
        foreach ($companies as $company) {
            
            // Menggunakan helper getDisplayType() dari model
            $typeDisplay = $company->getDisplayType();

            // Label Status
            $statusLabel = $company->getStatusBadge();

            $id_enc = Encrypter::encrypt($company->id_company);
            $actionBtn = '<a href="'.router('lecturer.company.detail', ['id' => $id_enc]).'" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Detail</a>';
            $data[] = [
                "id"           => $company->id_company,
                "name"         => '<b>' . ($company->name ?? '-') . '</b>',
                "type"         => $typeDisplay ?? '-', // Pakai helper model
                "city"         => $company->city ?? '-',
                "status_label" => $statusLabel,
                "aksi"         => $actionBtn
            ];
        }

        // 6. Return JSON Response
        header('Content-Type: application/json');
        echo json_encode([
            "draw"            => $draw,
            "recordsTotal"    => (int)$totalData,
            "recordsFiltered" => (int)$totalFiltered,
            "data"            => $data
        ]);
        exit;
    break;


    case 'detail':
        try {
            $id_company = Encrypter::decrypt($id);

            // 1. Ambil Data Perusahaan menggunakan Model
            $company = Company::where('id_company', $id_company)->first();

            if (!$company) {
                Notification::swal2('Not Found', 'Company data is not found!', 'error');
                Redirect::to( router('lecturer.company.index'), 100 ); exit;

            }

            // 2. Ambil Data User/Staff Company (Raw DB atau Model jika ada)
            $users = DB::table('user_company')->where('id_company', $id_company)->orderBy('id_user_company', 'ASC')->get();

            // 3. Ambil List Offer
            $offers = DB::table('offer')->where('id_company', $id_company)->get();

            // 4. Ambil List Mahasiswa Magang (Join)
            $students = DB::table('internship as i')
                ->join('student_internship as s', 'i.nim', '=', 's.nim')
                ->select('i.*', 's.name as student_name', 's.program_study', 's.email', 's.no_whatsapp')
                ->where('i.id_company', $id_company)
                ->get();

            renderView('lecturer.company_list.detail', [
                'title'      => 'Company Detail - ' . $company->name,
                'routes'     => 'company_list',
                'data'       => $company,
                'users'      => $users,
                'offers'     => $offers,
                'students'   => $students,
                'id_enc'     => $id
            ]);

        } catch (Exception $e) {
            Notification::swal2('Not Found', 'Company data is not found!', 'error');
            Redirect::to( router('lecturer.company.index'), 100 ); exit;
            
        }
    break;

endswitch;