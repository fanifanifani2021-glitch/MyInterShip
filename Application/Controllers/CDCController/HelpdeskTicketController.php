<?php
namespace Application\Controllers\CDCController;

use Controller;
use HelpdeskTicket;
use HelpdeskCategory;
use HelpdeskReply;
use HelpdeskAttachment;
use Request;
use Validator;
use Hashid;
use File; // Gunakan Helper File
use Exception;
use Str;

class HelpdeskTicketController extends Controller
{
    /**
     * Halaman Dashboard Utama Helpdesk CDC
     */
    public function index()
    {
        $categories = HelpdeskCategory::all();

        $stats = [
            'total'       => HelpdeskTicket::query()->count(),
            'open'        => HelpdeskTicket::where('status', 'Open')->count(),
            'in_progress' => HelpdeskTicket::where('status', 'In Progress')->count(),
            'resolved'    => HelpdeskTicket::where('status', 'Resolved')->count(),
            'closed'      => HelpdeskTicket::where('status', 'Closed')->count(),
        ];

        renderView('cdc.helpdesk.index', [
            'title'      => 'Helpdesk Ticket Management',
            'route'      => 'helpdesk',
            'categories' => $categories,
            'stats'      => $stats
        ]);
    }

    /**
     * DataTables Provider untuk CDC Management
     */
    public function data()
    {
        header('Content-Type: application/json');

        try {
            $draw         = Request::integer('draw', 1);
            $start        = Request::integer('start', 0);
            $length       = Request::integer('length', 10);
            $search       = Request::post('search')['value'] ?? '';
            $filterStatus = Request::post('status', 'All');
            $filterCat    = Request::post('category', 'All');
            $filterPrio   = Request::post('priority', 'All');

            $query = HelpdeskTicket::query();

            if (!empty($filterStatus) && $filterStatus !== 'All') {
                $query->where('status', '=', $filterStatus);
            }
            if (!empty($filterCat) && $filterCat !== 'All') {
                $query->where('id_category', '=', (int)$filterCat);
            }
            if (!empty($filterPrio) && $filterPrio !== 'All') {
                $query->where('priority', '=', $filterPrio);
            }

            $totalData = (clone $query)->count();

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('ticket_code', 'LIKE', "%{$search}%")
                      ->orWhere('subject', 'LIKE', "%{$search}%")
                      ->orWhere('user_name', 'LIKE', "%{$search}%")
                      ->orWhere('user_id', 'LIKE', "%{$search}%");
                });
            }

            $totalFiltered = (clone $query)->count();

            $tickets = $query->orderBy('id_ticket', 'DESC')
                             ->offset($start)
                             ->limit($length)
                             ->get();

            $data = [];
            $no   = $start + 1;

            foreach ($tickets as $row) {
                $hashId = \Hashid::encode($row->id_ticket);
                $categoryName = $row->category ? htmlspecialchars($row->category->category_name) : 'General';

                $actionBtn = '
                <a href="' . router('cdc.helpdesk.detail', ['id' => $hashId]) . '" class="btn btn-sm btn-primary btn-round shadow-sm">
                    <i class="fas fa-search mr-1"></i> Review & Reply
                </a>';

                $data[] = [
                    'no'          => $no++ . '.',
                    'ticket_code' => '<strong class="text-primary">' . htmlspecialchars($row->ticket_code) . '</strong>',
                    'user_info'   => '<div><strong>' . htmlspecialchars($row->user_name) . '</strong><br><small class="text-muted">' . htmlspecialchars($row->user_id) . ' (' . htmlspecialchars($row->user_type) . ')</small></div>',
                    'subject'     => htmlspecialchars($row->subject),
                    'category'    => '<span class="badge badge-count">' . $categoryName . '</span>',
                    'priority'    => $row->getPriorityBadge(),
                    'status'      => $row->getStatusBadge(),
                    'created_at'  => date('d M Y, H:i', strtotime($row->created_at)),
                    'aksi'        => $actionBtn
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
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * Detail Tiket & Thread Balasan
     */
    public function detail()
    {
        $hashParam = $this->params['id'] ?? null;
        $id = is_numeric($hashParam) ? (int)$hashParam : \Hashid::decode($hashParam);

        if (!$id) {
            \Router::handleError(404, 'Ticket not found.');
        }

        $ticket = HelpdeskTicket::find($id);

        if (!$ticket) {
            \Router::handleError(404, 'Ticket record not found.');
        }

        renderView('cdc.helpdesk.detail', [
            'title'  => 'Review Ticket #' . $ticket->ticket_code,
            'route'  => 'helpdesk',
            'ticket' => $ticket
        ]);
    }

    /**
     * Submit Balasan (Reply) CDC -> Mengembalikan Respon Notifikasi JSON untuk Frontend
     */
    public function reply()
    {
        header('Content-Type: application/json');
        try {
            $hashParam = $this->params['id'] ?? null;
            $id = is_numeric($hashParam) ? (int)$hashParam : \Hashid::decode($hashParam);

            $ticket = HelpdeskTicket::find($id);
            if (!$ticket) {
                echo json_encode(['status' => 'error', 'message' => 'Ticket record not found.']);
                exit;
            }

            $message = Request::string('message');
            if (empty($message)) {
                echo json_encode(['status' => 'error', 'message' => 'Response message cannot be empty.']);
                exit;
            }

            $adminId   = $_SESSION['username'] ?? ($_SESSION['name'] ?? 'CDC Admin');
            $adminName = $_SESSION['name'] ?? 'CDC Support Staff';

            // 1. Simpan Record Reply
            $reply = HelpdeskReply::create([
                'id_ticket' => $ticket->id_ticket,
                'user_id'   => (string)$adminId,
                'user_type' => 'CDC',
                'user_name' => $adminName,
                'message'   => $message,
                'created_at'=> date('Y-m-d H:i:s')
            ]);

            // 2. Simpan Lampiran Gambar Menggunakan File::upload()
            if (!empty($_FILES['image_proofs']['name']) && is_array($_FILES['image_proofs']['name'])) {
                $fileCount = count($_FILES['image_proofs']['name']);
                $destination = 'upload/helpdesk/';

                for ($i = 0; $i < $fileCount; $i++) {
                    if ($_FILES['image_proofs']['error'][$i] === UPLOAD_ERR_OK) {
                        $singleFile = [
                            'name'     => $_FILES['image_proofs']['name'][$i],
                            'type'     => $_FILES['image_proofs']['type'][$i],
                            'tmp_name' => $_FILES['image_proofs']['tmp_name'][$i],
                            'error'    => $_FILES['image_proofs']['error'][$i],
                            'size'     => $_FILES['image_proofs']['size'][$i],
                        ];

                        $uploadedFileName = File::upload($singleFile, $destination);

                        if ($uploadedFileName !== false) {
                            HelpdeskAttachment::create([
                                'id_ticket' => $ticket->id_ticket,
                                'id_reply'  => $reply->id_reply,
                                'file_name' => $_FILES['image_proofs']['name'][$i],
                                'file_path' => $destination . $uploadedFileName,
                                'file_size' => $_FILES['image_proofs']['size'][$i],
                                'created_at'=> date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
            }

            // 3. Update Status Tiket
            $newStatus = Request::string('status', 'In Progress');
            $ticket->update([
                'status'      => $newStatus,
                'assigned_to' => $adminName
            ]);

            // 4. Return JSON Response dengan Payload Notifikasi Lengkap
            echo json_encode([
                'status'  => 'success',
                'title'   => 'Response Sent Successfully!',
                'message' => 'Your reply has been posted and ticket status changed to [' . $newStatus . '].'
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * Quick Update Status & Priority -> Mengembalikan Respon Notifikasi JSON untuk Frontend
     */
    public function updateStatus()
    {
        header('Content-Type: application/json');
        try {
            $hashParam = $this->params['id'] ?? null;
            $id = is_numeric($hashParam) ? (int)$hashParam : \Hashid::decode($hashParam);

            $ticket = HelpdeskTicket::find($id);
            if (!$ticket) {
                echo json_encode(['status' => 'error', 'message' => 'Ticket not found.']);
                exit;
            }

            $status   = Request::string('status');
            $priority = Request::string('priority');

            $updateData = [];
            if (!empty($status))   $updateData['status'] = $status;
            if (!empty($priority)) $updateData['priority'] = $priority;

            $updateData['assigned_to'] = $_SESSION['name'] ?? 'CDC Staff';

            $ticket->update($updateData);

            echo json_encode([
                'status'  => 'success',
                'title'   => 'Status Updated!',
                'message' => 'Ticket status changed to [' . $status . '] and priority set to [' . $priority . '].'
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }
}