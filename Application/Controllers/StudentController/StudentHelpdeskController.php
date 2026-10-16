<?php
namespace Application\Controllers\StudentController;

use Controller;
use HelpdeskTicket;
use HelpdeskCategory;
use HelpdeskReply;
use HelpdeskAttachment;
use Request;
use Validator;
use Hashid;
use File;
use Exception;
use Str;

class StudentHelpdeskController extends Controller
{
    /**
     * Render Halaman Utama Helpdesk Mahasiswa
     */
    public function index()
    {
        renderView('student.helpdesk.index', [
            'title'  => 'Helpdesk Support',
            'routes' => 'helpdesk'
        ]);
    }

    /**
     * Render Halaman Form Buat Tiket Baru
     */
    public function create()
    {
        // Ambil hanya kategori yang aktif dari DB
        $categories = HelpdeskCategory::where('is_active', 1)->get();

        renderView('student.helpdesk.create', [
            'title'      => 'Create Support Ticket',
            'routes'     => 'helpdesk',
            'categories' => $categories
        ]);
    }

    /**
     * Render Halaman Detail & Diskusi Thread Tiket
     */
    public function detail()
    {
        $hashParam = $this->params['id'] ?? null;
        $id = is_numeric($hashParam) ? (int)$hashParam : \Hashid::decode($hashParam);

        if (!$id) {
            \Router::handleError(404, 'Ticket not found.');
        }

        $userId = $_SESSION['nim'] ?? ($_SESSION['username'] ?? null);
        $ticket = HelpdeskTicket::find($id);

        if (!$ticket || $ticket->user_id !== $userId) {
            \Router::handleError(403, 'You do not have access to this ticket.');
        }

        renderView('student.helpdesk.detail', [
            'title'  => 'Ticket Detail #' . $ticket->ticket_code,
            'routes' => 'helpdesk',
            'ticket' => $ticket
        ]);
    }

    /**
     * DataTables Server-Side JSON Provider
     */
    public function data()
    {
        header('Content-Type: application/json');

        $draw   = Request::integer('draw', 1);
        $start  = Request::integer('start', 0);
        $length = Request::integer('length', 10);
        $search = Request::post('search')['value'] ?? '';

        $userId = $_SESSION['nim'] ?? ($_SESSION['username'] ?? '');

        $query = HelpdeskTicket::query()->where('user_id', '=', $userId);
        $totalData = (clone $query)->count();

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('ticket_code', 'LIKE', "%{$search}%")
                  ->orWhere('subject', 'LIKE', "%{$search}%");
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

            $actionBtn = '
            <a href="' . router('student.helpdesk.detail', ['id' => $hashId]) . '" class="btn btn-xs btn-primary btn-round shadow-sm">
                <i class="fas fa-comments mr-1"></i> View & Reply
            </a>';

            $categoryName = $row->category ? htmlspecialchars($row->category->category_name) : 'General';

            $data[] = [
                'no'          => $no++ . '.',
                'ticket_code' => '<strong class="text-primary">' . htmlspecialchars($row->ticket_code) . '</strong>',
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
    }

    /**
     * AJAX Submit Tiket Baru + Simpan Banyak Lampiran (Multiple Attachments)
        * @return void
     */
    public function store()
    {
        header('Content-Type: application/json');
        try {
            $rules = [
                'id_category' => ['required'],
                'priority'    => ['required'],
                'subject'     => ['required', 'min:5'],
                'description' => ['required', 'min:10']
            ];

            $validator = Validator::make(Request::all(), $rules);

            if (!$validator->validate()) {
                echo json_encode([
                    'status' => 'validation_error',
                    'errors' => $validator->errors()
                ]);
                exit;
            }

            $userId   = $_SESSION['nim'] ?? ($_SESSION['username'] ?? '0');
            $userName = $_SESSION['name'] ?? 'Mahasiswa';
            $userEmail= $_SESSION['email'] ?? '-';

            // Auto Generate Unique Code: TKT-20260810-X9A2
            $ticketCode = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            // 1. Simpan Record Tiket Utama
            $ticket = HelpdeskTicket::create([
                'ticket_code' => $ticketCode,
                'id_category' => Request::integer('id_category'),
                'user_id'     => $userId,
                'user_type'   => 'Student',
                'user_name'   => $userName,
                'user_email'  => $userEmail,
                'priority'    => Str::sanitize(Request::string('priority', 'Medium')),
                'subject'     => Str::sanitize(Request::string('subject')),
                'description' => Request::string('description'),
                'status'      => 'Open',
                'assigned_to' => null
            ]);

            if (!$ticket) {
                throw new Exception("Failed to create ticket record in database.");
            }

            // 2. Simpan Lampiran Gambar Menggunakan Helper File::upload
            if (!empty($_FILES['image_proofs']['name']) && is_array($_FILES['image_proofs']['name'])) {
                $fileCount = count($_FILES['image_proofs']['name']);
                $destination = 'upload/helpdesk/';

                for ($i = 0; $i < $fileCount; $i++) {
                    if ($_FILES['image_proofs']['error'][$i] === UPLOAD_ERR_OK) {
                        
                        // Ekstrak struktur 1 file dari $_FILES array
                        $singleFile = [
                            'name'     => $_FILES['image_proofs']['name'][$i],
                            'type'     => $_FILES['image_proofs']['type'][$i],
                            'tmp_name' => $_FILES['image_proofs']['tmp_name'][$i],
                            'error'    => $_FILES['image_proofs']['error'][$i],
                            'size'     => $_FILES['image_proofs']['size'][$i],
                        ];

                        // Panggil Helper File::upload()
                        $uploadedFileName = File::upload($singleFile, $destination);

                        if ($uploadedFileName !== false) {
                            HelpdeskAttachment::create([
                                'id_ticket' => $ticket->id_ticket,
                                'id_reply'  => null,
                                'file_name' => $_FILES['image_proofs']['name'][$i],
                                'file_path' => $destination . $uploadedFileName,
                                'file_size' => $_FILES['image_proofs']['size'][$i],
                                'created_at'=> date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
            }

            echo json_encode([
                'status'  => 'success',
                'message' => 'Support ticket submitted successfully.'
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * AJAX Reply / Balas Diskusi Tiket dari Mahasiswa
     */
    public function reply()
    {
        header('Content-Type: application/json');
        try {
            $idTicket = Request::integer('id_ticket');
            $message  = Request::string('message');

            if (empty($message)) {
                echo json_encode(['status' => 'error', 'message' => 'Reply message cannot be empty.']);
                exit;
            }

            $ticket = HelpdeskTicket::find($idTicket);
            if (!$ticket) {
                echo json_encode(['status' => 'error', 'message' => 'Ticket not found.']);
                exit;
            }

            $userId   = $_SESSION['nim'] ?? ($_SESSION['username'] ?? '0');
            $userName = $_SESSION['name'] ?? 'Mahasiswa';

            // 1. Simpan Reply
            $reply = HelpdeskReply::create([
                'id_ticket' => $ticket->id_ticket,
                'user_id'   => $userId,
                'user_type' => 'Student',
                'user_name' => $userName,
                'message'   => $message,
                'created_at'=> date('Y-m-d H:i:s')
            ]);

            // Update status tiket kembali ke Open / Pending CDC
            $ticket->update(['status' => 'Open']);

            echo json_encode(['status' => 'success', 'message' => 'Reply posted successfully.']);
            exit;

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }
}