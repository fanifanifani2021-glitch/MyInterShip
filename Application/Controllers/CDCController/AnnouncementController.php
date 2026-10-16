<?php
namespace Application\Controllers\CDCController;

use Controller;
use Announcement;
use Hashid;
use Request;
use Validator;
use Exception;
use Str;

class AnnouncementController extends Controller
{
    /**
     * Halaman Utama (Daftar Pengumuman)
     */
    public function index()
    {
        renderView('cdc.announcement_management.index', [
            'route' => 'announcement_management'
        ]);
    }

    /**
     * Halaman Content Manager (Form Editor Add/Edit)
     */
    public function content_manager()
    {
        // Dekode ID singkat Hashid (misal: "aX8k") kembali menjadi integer ID asli
        $id = !empty($this->params['id']) ? Hashid::decode($this->params['id']) : null;
        $announcement = null;

        if ($id) {
            $announcement = Announcement::find($id);
            if (!$announcement) {
                \make_swal2('error', 'Not Allowed', 'Announcement Data not found');
                \redirect_back(1); exit;
            }
        }

        renderView('cdc.announcement_management.content_manager', [
            'title'        => 'Content Manager',
            'route'        => 'announcement_management',
            'announcement' => $announcement
        ]);
    }

    /**
     * DataTables Server-Side JSON Provider
     */
    public function data()
    {
        // Pastikan Session Aktif
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        try {
            $draw         = Request::integer('draw', 1);
            $start        = Request::integer('start', 0);
            $length       = Request::integer('length', 10);
            $search       = Request::post('search')['value'] ?? '';
            $filterStatus = Request::post('status', 'All');

            // Safe Session Read (Object / Array Handling)
            $cdcSession = $_SESSION['CDC_Data'] ?? ($_SESSION['CDC-Data'] ?? null);
            if (is_object($cdcSession)) {
                $userCampusId = $cdcSession->id_kampus ?? '1';
            } elseif (is_array($cdcSession)) {
                $userCampusId = $cdcSession['id_kampus'] ?? '1';
            } else {
                $userCampusId = '1';
            }

            $query = Announcement::query()->where('id_kampus', '=', $userCampusId);

            if (!empty($filterStatus) && $filterStatus !== 'All') {
                $query->where('status', '=', $filterStatus);
            }

            $totalData = (clone $query)->count();

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('category', 'LIKE', "%{$search}%")
                      ->orWhere('created_by', 'LIKE', "%{$search}%");
                });
            }

            $totalFiltered = (clone $query)->count();

            $announcements = $query->orderBy('id_announcement', 'DESC')
                                   ->offset($start)
                                   ->limit($length)
                                   ->get();

            $data = [];
            $no   = $start + 1;

            foreach ($announcements as $row) {
                if ($row->status === 'Publish') {
                    $statusBadge = '<button type="button" class="btn btn-sm btn-success">Publish</button>';
                } elseif ($row->status === 'Draft') {
                    $statusBadge = '<button type="button" class="btn btn-sm btn-danger">Draft</button>';
                } else {
                    $statusBadge = '<button type="button" class="btn btn-sm btn-dark">' . htmlspecialchars($row->status) . '</button>';
                }

                $hashId = Hashid::encode($row->id_announcement);

                $actionBtn = '
                <div class="btn-group btn-round">
                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Action
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="' . router('announcement.detail', ['id' => $hashId]) . '" target="_blank">
                            <i class="fas fa-info-circle mr-2 text-info"></i>Announcement Detail
                        </a>';

                if ($row->id_kampus == $userCampusId) {
                    $actionBtn .= '
                        <a class="dropdown-item" href="' . router('cdc.announcement.content_manager', ['id' => $hashId]) . '">
                            <i class="fas fa-edit mr-2 text-warning"></i>Content Manager
                        </a>
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item text-danger" onclick="delete_data(' . $row->id_announcement . ')">
                            <i class="fas fa-trash-alt mr-2"></i>Delete Data
                        </button>';
                }

                $actionBtn .= '
                    </div>
                </div>';

                $data[] = [
                    'no'            => $no++ . '.',
                    'status'        => $statusBadge,
                    'title'         => htmlspecialchars($row->title ?? '-'),
                    'category'      => htmlspecialchars($row->category ?? 'Information'),
                    'visitor_count' => (int)$row->visitor_count,
                    'created_by'    => htmlspecialchars($row->created_by ?? '-'),
                    'aksi'          => $actionBtn
                ];
            }

            echo json_encode([
                "draw"            => $draw,
                "recordsTotal"    => $totalData,
                "recordsFiltered" => $totalFiltered,
                "data"            => $data
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ]);
            exit;
        }
    }

    /**
     * AJAX Save (Simpan/Update dari Content Manager)
     */
    public function save()
    {
        header('Content-Type: application/json');
        try {
            $id = Request::integer('id');

            $rules = [
                'title'    => ['required', 'min:5'],
                'category' => ['required'],
                'content'  => ['required']
            ];

            $validator = Validator::make(Request::all(), $rules);

            if (!$validator->validate()) {
                echo json_encode([
                    'status' => 'validation_error',
                    'errors' => $validator->errors()
                ]);
                exit;
            }

            $now = date('Y-m-d H:i:s');
            $userCampusId = (string)($_SESSION['CDC_Data']['id_kampus'] ?? ($_SESSION['CDC-Data']['id_kampus'] ?? '1'));

            $data = [
                'title'    => Str::sanitize(Request::string('title')),
                'category' => Str::sanitize(Request::string('category', 'Information')),
                'status'   => Str::sanitize(Request::string('status', 'Draft')),
                'content'  => Request::string('content'), // Biarkan HTML dari CKEditor
            ];

            if ($id > 0) {
                $announcement = Announcement::find($id);
                if (!$announcement) throw new Exception("Announcement record not found.");
                
                $data['updated_at'] = $now;
                $announcement->update($data);
                $message = "Announcement updated successfully.";
            } else {
                $data['picture']             = '-';
                $data['have_attachment']     = 0;
                $data['attachment']          = '-';
                $data['visitor_count']       = 0;
                $data['created_by']          = $_SESSION['name'] ?? 'CDC Admin';
                $data['created_by_username'] = $_SESSION['username'] ?? 'cdc';
                $data['created_by_role']     = $_SESSION['role'] ?? 'UPKPK';
                $data['created_at']          = $now;
                $data['updated_at']          = '-';
                $data['id_kampus']           = $userCampusId;

                Announcement::create($data);
                $message = "New announcement created successfully.";
            }

            echo json_encode(['status' => 'success', 'message' => $message]);
            exit;

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * AJAX Destroy
     */
    public function destroy()
    {
        header('Content-Type: application/json');
        try {
            $id = Request::integer('id');
            $announcement = Announcement::find($id);

            if (!$announcement) {
                echo json_encode(['status' => 'error', 'message' => 'Announcement not found!']);
                exit;
            }

            if ($announcement->delete()) {
                echo json_encode(['status' => 'success', 'message' => 'Announcement deleted successfully.']);
            } else {
                throw new Exception("Failed to delete record from database.");
            }
            exit;

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }
}