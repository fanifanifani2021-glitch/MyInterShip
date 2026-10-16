<?php

switch ($method):

    case 'index':

        $companies = Company::select('id_company', 'name')
                    ->orderBy('name', 'ASC')->get();

        $cities = Company::select('city')->distinct()
            ->whereNotNull('city')->orderBy('city', 'ASC')
            ->get();

        renderView('student.internship_offer.index', [
            'title'  => 'Internship Offer', 
            'routes' => 'student_offer',
            'companies' => $companies,
            'cities' => $cities
        ]);
    break;
    case 'data':
        // 1. Ambil input
        $draw   = Request::input('draw', 1);
        $start  = Request::input('start', 0);
        $length = Request::input('length', 10);
        $search = Request::input('search.value');

        // Filter - Kita set default 'Open' jika filter_status kosong
        $filterStatus    = Request::input('filter_status', 'Open'); 
        $filterOfferType = Request::input('filter_offer_type', 'Internship');

        // 2. Hitung Total Data Asli (Default Internship/Magang yang Open)
        $recordsTotal = DB::table('offer')
            ->whereIn('offer_type', ['Internship', 'Magang'])
            ->where('status', '=', 'Open')
            ->count();

        // 3. Bangun Query Utama
        $query = DB::table('offer')
            ->select('offer.*', 'company.name as company_name')
            ->join('company', 'offer.id_company', '=', 'company.id_company');

        // 4. Terapkan Filter Kompleks
        if ($filterCompany = Request::input('filter_company')) {
            $query->where('offer.id_company', '=', $filterCompany);
        }
        if ($filterCity = Request::input('filter_city')) {
            $query->where('offer.city', '=', $filterCity);
        }
        
        // Logika Offer Type (Internship/Magang)
        if ($filterOfferType) {
            $query->where('offer.offer_type', '=', $filterOfferType);
        } else {
            // Default list jika tidak ada filter spesifik
            $query->whereIn('offer.offer_type', ['Internship', 'Magang']);
        }

        // Filter Status (Default: Open)
        $query->where('offer.status', '=', $filterStatus);

        // 5. Global Search
        if (!empty($search)) {
            $query->whereGroup(function($q) use ($search) {
                $q->where('company.name', 'LIKE', "%$search%")
                ->orWhere('offer.position', 'LIKE', "%$search%")
                ->orWhere('offer.city', 'LIKE', "%$search%");
            });
        }

        $recordsFiltered = $query->count();

        // 6. Sorting & Pagination
        $columns = ['id_offer', 'company.name', 'position', 'start_date', 'city', 'job_type', 'status'];
        $orderIndex = Request::input('order.0.column', 0);
        $orderDir   = Request::input('order.0.dir', 'asc');
        
        if (isset($columns[$orderIndex])) {
            $query->orderBy($columns[$orderIndex], $orderDir);
        }

        $dataRows = $query->limit($length)->offset($start)->get();

        // 7. Format Data
        $data = [];
        $no = (int)$start + 1;

        foreach ($dataRows as $row) {
            $statusBtn = $row['status'] == 'Open' 
                ? '<span class="badge badge-success">Open</span>' 
                : '<span class="badge badge-danger">Closed</span>';
            
            $action = '<div class="btn-group">';
            $action .= '<button class="btn btn-sm btn-info" title="Detail" onclick="window.location.href=\''.router('student/internship_offer.detail', ['id' => Encrypter::encrypt($row['id_offer'])]).'\'"><i class="fas fa-info-circle"></i></button>';
            
            // Tombol Apply hanya muncul jika Status OPEN dan Tipe adalah Internship/Magang
            $isInternship = in_array($row['offer_type'], ['Internship', 'Magang']);
            if ($row['status'] == 'Open' && $isInternship) {
                $action .= '<button class="btn btn-sm btn-primary ml-1" title="Apply" onclick="applyOffer(\''.Encrypter::encrypt($row['id_offer']).'\')"><i class="fab fa-wpforms"></i></button>';
            }
            $action .= '</div>';

            $data[] = [
                $no++,
                $row['company_name'],
                '<b>'.$row['position'].'</b><br><small class="text-muted">Type: '.$row['offer_type'].'</small>',
                'Start: '.date('d/m/y', strtotime($row['start_date'])).'<br>End: '.date('d/m/y', strtotime($row['end_date'])),
                $row['city'],
                $row['job_type'],
                $statusBtn,
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


    case 'detail':
        try {
            // 1. Dekripsi ID menggunakan kelas Encrypter kamu
            $id_offer = Encrypter::decrypt($id);

            // 2. Ambil data detail (Offer + Company)
            $offer = DB::table('offer')
                ->select(
                    'offer.*', 
                    'company.name as company_name', 
                    'company.logo as company_logo',
                    'company.website as company_website',
                    // Kita gabungkan Address, City, dan Province di sini
                    "CONCAT_WS(', ', company.address, company.city, company.province) as full_address"
                )
                ->join('company', 'offer.id_company', '=', 'company.id_company')
                ->where('offer.id_offer', '=', $id_offer)
                ->first();


            if (!$offer) {
                Notification::swal2('Error', 'Internship offer not found.', 'error');
                Response::redirect(router('student/internship_offer.list'))->send();
                exit;
            }


            // Logika Validasi & Format URL
            $website = $offer['website'] ?? null;
            $formattedWebsite = null;

            if (!empty($website)) {
                // 1. Cek apakah sudah punya protokol (http/https). Jika belum, tambahkan https://
                $formattedWebsite = (preg_match("~^(?:f|ht)tps?://~i", $website)) 
                                    ? $website 
                                    : "https://" . $website;

                // 2. Validasi apakah format URL-nya benar-benar valid secara sintaksis
                if (!filter_var($formattedWebsite, FILTER_VALIDATE_URL)) {
                    $formattedWebsite = null; // Set null jika link ngaco (misal cuma isi ttd/strip)
                }
            }

            // Simpan kembali ke array untuk dikirim ke view
            $offer['formatted_website'] = $formattedWebsite;


            // 3. Ambil List Pelamar (Applicant List)
            $applicantCount = DB::table('applicant')
                    ->where('id_offer', '=', $id_offer)
                    ->count();
            // 4. Render View Blade
            return renderView('student.internship_offer.detail', [
                'title'      => 'Offer Detail - ' . $offer['position'],
                'offer'      => $offer,
                'applicantCount' => $applicantCount
            ]);

        } catch (Exception $e) {
            Notification::swal2('Error', 'Internship offer not found.', 'error');
            Response::redirect(router('student/internship_offer.list'))->send();
            exit;
        }
    break;
endswitch;