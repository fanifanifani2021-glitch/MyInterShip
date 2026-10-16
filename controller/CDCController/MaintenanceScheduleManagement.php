<?php
defined('BASE_PATH') or die('Direct access not allowed');
switch ($method) :

    default:
        Router::handleError(404, 'Method not found');
    break;

    case 'index':
        renderView('cdc.maintenance_schedule_management.index', ['route' => 'maintenance_schedule_management']);
    break;

    case 'data':
        echo Maintenance::toDataTable(function($row, $no) {
            $now = date('Y-m-d H:i:s');

            // Karena Model sudah punya __get, kita panggil seperti object
            $start = !empty($row->start_date) ? date('d M Y, H:i', strtotime($row->start_date)) : '-';
            $end   = !empty($row->end_date) ? date('d M Y, H:i', strtotime($row->end_date)) : '-';

            if (!$row->is_active) {
                $statusBadge = '<span class="badge-status badge-expired">Disabled</span>';
            } elseif ($now < $row->start_date) {
                $statusBadge = '<span class="badge-status badge-pending">Scheduled</span>';
            } elseif ($now >= $row->start_date && $now <= $row->end_date) {
                $statusBadge = '<span class="badge-status badge-active">On Progress</span>';
            } else {
                $statusBadge = '<span class="badge-status badge-expired">Expired</span>';
            }

            return [
                'no'         => $no,
                'title'      => '<strong>' . htmlspecialchars($row->title ?? '-') . '</strong>',
                'start_date' => $start,
                'end_date'   => $end,
                'status'     => $statusBadge,
                'aksi'       => '
                    <div class="text-center">
                        <button onclick="edit_data(' . $row->id . ')" class="btn btn-xs btn-primary shadow-sm"><i class="fas fa-pen"></i></button>
                        <button onclick="delete_data(' . $row->id . ')" class="btn btn-xs btn-danger shadow-sm"><i class="fas fa-trash"></i></button>
                    </div>'
            ];
        });
        exit;
    break;

    case 'save':
        try {
            $id = Request::integer('id');
            
            // 1. Jalankan Validasi
            $rules = [
                'title'       => ['required', 'min:5'],
                'description' => ['required'],
                'start_date'  => ['required'],
                'end_date'    => ['required'],
            ];

            $validator = Validator::make( Request::all(), $rules);


            if (!$validator->validate()) {
                echo json_encode([
                    'status' => 'validation_error',
                    'errors' => $validator->errors()
                ]);
                exit;
            }

            // 2. Siapkan Data
            $data = [
                'title'       => Request::string('title'),
                'description' => Request::string('description'),
                'start_date'  => Request::input('start_date'),
                'end_date'    => Request::input('end_date'),
                'allowed_ips' => Request::string('allowed_ips'),
                'is_active'   => Request::integer('is_active')
            ];

            // 3. Simpan Menggunakan Model (Update or Create)
            if ($id > 0) {
                $maintenance = Maintenance::find($id);
                if (!$maintenance) throw new Exception("Data not found.");
                $maintenance->update($data);
                $message = "Maintenance schedule updated successfully.";
            } else {
                Maintenance::create($data);
                $message = "New maintenance schedule created successfully.";
            }

            echo json_encode(['status' => 'success', 'message' => $message]);
            exit;

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    break;

    case 'destroy':
        try {
            $id = Request::integer('id');
            $maintenance = Maintenance::find($id);

            if (!$maintenance) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Schedule not found!']);
                exit;
            }

            // Eksekusi hapus
            if ($maintenance->delete()) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Schedule deleted successfully.']);
            } else {
                throw new Exception("Failed to delete from database.");
            }
            exit;

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    break;

    case 'detail':
        try {
            // 1. Ambil ID dari parameter GET/POST
            $id = Request::integer('id');

            if (!$id) {
                throw new Exception("Required parameter 'id' is missing.");
            }

            // 2. Cari data menggunakan Model
            $maintenance = Maintenance::find($id);

            // 3. Cek keberadaan data
            if (!$maintenance) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Maintenance record not found.'
                ]);
                exit;
            }

            // 4. Kirim data (otomatis menjalankan jsonSerialize dari Model)
            header('Content-Type: application/json');
            echo json_encode($maintenance);
            exit;

        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    break;


endswitch;