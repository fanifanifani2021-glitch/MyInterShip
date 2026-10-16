<?php

switch ($method) :
    /**
     * Case: Index
     * Menampilkan daftar riwayat magang mahasiswa yang sedang login.
     */
    case 'index':
        $nim = Session::get('nim');

        $internships = DB::table('internship')
            ->select(
                'internship.*', 
                'company.name as company_name', 
                'company.logo as company_logo',
                'offer.position',
                'offer.offer_type'
            )
            ->join('company', 'internship.id_company', '=', 'company.id_company')
            // Gunakan applicant sebagai jembatan karena internship tidak punya id_offer
            ->leftJoin('applicant', 'internship.id_internship', '=', 'applicant.id_internship')
            ->leftJoin('offer', 'applicant.id_offer', '=', 'offer.id_offer')
            ->where('internship.nim', '=', $nim)
            ->orderBy('internship.id_internship', 'DESC')
            ->get();

        return renderView('student.my_internship.index', [
            'title'       => 'My Internship History',
            'internships' => $internships,
            'routes'      => 'my_internship'
        ]);
    break;

    case 'data':
        $nim    = Session::get('nim');
        $draw   = Request::input('draw');
        $start  = Request::input('start', 0);
        $length = Request::input('length', 10);
        $search = Request::input('search.value');

        // Base Query
        $query = DB::table('internship')
            ->select('internship.*', 'company.name as company_name')
            ->join('company', 'internship.id_company', '=', 'company.id_company')
            ->where('internship.nim', '=', $nim);

        // Filter Kompleks
        if ($filterCompany = Request::input('filter_company')) {
            $query->where('internship.id_company', '=', $filterCompany);
        }
        if ($filterStatus = Request::input('filter_status')) {
            $query->where('internship.status', '=', $filterStatus);
        }

        // Global Search
        if (!empty($search)) {
            $query->whereGroup(function($q) use ($search) {
                $q->where('company.name', 'LIKE', "%$search%")
                ->orWhere('internship.status', 'LIKE', "%$search%");
            });
        }

        $recordsTotal    = DB::table('internship')->where('nim', '=', $nim)->count();
        $recordsFiltered = $query->count();

        // Order & Limit
        $dataRows = $query->orderBy('internship.id_internship', 'DESC')
                        ->limit($length)
                        ->offset($start)
                        ->get();

        $data = [];
        $no = $start + 1;
        foreach ($dataRows as $row) {
            // 1. Normalisasi status ke lowercase untuk pengecekan aman
            $statusRaw = strtolower($row['status']); 

            // 2. Pemisahan logika warna yang lebih spesifik
            if ($statusRaw === 'yes') {
                $statusClass = 'badge-primary'; // Biru: Menandakan sedang berjalan (Ongoing)
                $statusLabel = 'Ongoing';
            } elseif ($statusRaw === 'finish') {
                $statusClass = 'badge-success'; // Hijau: Menandakan sudah selesai (Selesai)
                $statusLabel = 'Finished';
            } elseif (in_array($statusRaw, ['no', 'reject'])) {
                $statusClass = 'badge-danger';  // Merah: Ditolak/Dibatalkan
                $statusLabel = 'Rejected';
            } else {
                $statusClass = 'badge-warning'; // Kuning: Menunggu antrian/approval
                $statusLabel = 'Waiting for Approval';
            }
            // 3. Enkripsi ID untuk Action
            $encryptedId = Encrypter::encrypt($row['id_internship']);

            $action = '<button type="button" onclick="viewDetail(\''.$encryptedId.'\')" class="btn btn-secondary btn-sm shadow-sm">';
            $action .= '<i class="fas fa-info-circle mr-1"></i> Detail</button>';

            $data[] = [
                $no++,
                "<b>" . $row['company_name'] . "</b>",
                '<span class="badge ' . $statusClass . '">' . $statusLabel . '</span>',
                date('d M Y', strtotime($row['start_date'])),
                date('d M Y', strtotime($row['end_date'])),
                $action
            ];
        }

        return Response::json([
            "draw"            => intval($draw),
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data"            => $data
        ]);
    break;

    /**
     * Case: Detail
     * Menampilkan detail satu record magang secara spesifik.
     */
    case 'detail':
        try {
            $id_internship = Encrypter::decrypt($params['id']);
            $nim = Session::get('nim');

            // 1. Ambil Data Internship & Company
            $internship = DB::table('internship')
                ->select('internship.*', 'company.name as company_name', 'company.logo as company_logo')
                ->join('company', 'internship.id_company', '=', 'company.id_company')
                ->where('internship.id_internship', '=', $id_internship)
                ->where('internship.nim', '=', $nim)
                ->first();

            if (!$internship) {
                Notification::swal2('error', 'Not Allowed', 'Internship Data is not found!');
                Response::redirect(router('student.internship'))->send();
                exit;
            }

            // 2. Ambil Data Dosen Pembimbing (dari student_internship)
            $student = DB::table('student_internship')->where('nim', '=', $nim)->first();
            $dospem = null;
            if (!empty($student['nik_dospem']) && $student['nik_dospem'] !== '-') {
                $dospem = DB::table('lecturer')
                    ->where('nim_nik_unit', '=', $student['nik_dospem'])
                    ->first();
            }

            // 3. Ambil Data Supervisor Industri
            $supervisor = null;
            if (!empty($internship['id_user_company']) && $internship['id_user_company'] !== '-') {
                $supervisor = DB::table('user_company')
                    ->where('id_user_company', '=', $internship['id_user_company'])
                    ->where('id_company', '=', $internship['id_company'])
                    ->first();
            }

            // 4. Mapping Status (Ongoing, Finish, Rejected, Waiting)
            $statusRaw = strtolower($internship['status']);
            $statusMap = [
                'yes'     => ['label' => 'Intern Accepted (Ongoing)', 'accepted' => 1, 'class' => 'badge-primary'],
                'finish'  => ['label' => 'Intern Accepted and Finished', 'accepted' => 1, 'class' => 'badge-success'],
                'no'      => ['label' => 'Intern Not Accepted', 'accepted' => 0, 'class' => 'badge-danger'],
                'reject'  => ['label' => 'Intern Rejected', 'accepted' => 0, 'class' => 'badge-danger'],
                'waiting' => ['label' => 'Waiting for validation', 'accepted' => 0, 'class' => 'badge-warning'],
            ];

            $currentStatus = $statusMap[$statusRaw] ?? ['label' => 'Not Registered', 'accepted' => null, 'class' => 'badge-secondary'];

            return renderView('student.my_internship.detail', [
                'title'      => 'Internship Detail',
                'routes'      => 'my_internship',
                'internship' => $internship,
                'dospem'     => $dospem ? $dospem['name'] : null,
                'supervisor' => $supervisor ? $supervisor['user_fullname'] : null,
                'status'     => $currentStatus,
                'id_enc'     => $params['id'] // Terusan ID terenkripsi untuk link
            ]);

        } catch (Exception $e) {
            Notification::swal2('warning', 'Security Warning', 'Invalid Access.');
            Response::redirect(router('student/internship'))->send();
            exit;
        }
    break;
endswitch;