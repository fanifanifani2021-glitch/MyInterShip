<?php
namespace Application\Controllers\LandingController;

use Controller;
use Announcement;
use Hashid; // Import helper Hashid dari Global Namespace
use Request;
use Str;

class PublicAnnouncementController extends Controller
{
    /**
     * Render Halaman Utama List Pengumuman
     */
    public function index()
    {
        renderView('landing.announcement.index', [
            'title'  => 'Announcements',
            'routes' => 'announcement'
        ]);
    }

    /**
     * Render Halaman Detail Pengumuman
     */
    public function detail()
    {
        $rawParam = $this->params['id'] ?? null;

        if (!$rawParam) {
            \Router::handleError(404, 'Announcement ID is required.');
        }

        // Cek apakah parameter berupa numeric ID atau Hashid string, lalu dekode
        $id = is_numeric($rawParam) ? (int)$rawParam : Hashid::decode($rawParam);

        if (!$id) {
            \Router::handleError(404, 'Invalid Announcement ID format.');
        }

        $announcement = Announcement::find($id);

        // Jika data tidak ada atau statusnya masih Draft
        if (!$announcement || strtolower($announcement->status) === 'draft') {
            \Router::handleError(404, 'Announcement not found or has been unpublished.');
        }

        // Increment jumlah pembaca (visitor count)
        $announcement->update([
            'visitor_count' => (int) $announcement->visitor_count + 1
        ]);

        renderView('landing.announcement.detail', [
            'title'        => $announcement->title,
            'routes'       => 'announcement',
            'announcement' => $announcement
        ]);
    }

    /**
     * AJAX Endpoint: JSON List Provider
     */
    public function list()
    {
        header('Content-Type: application/json');

        $page  = Request::integer('page', 1);
        $query = Str::sanitize(Request::string('query', ''));
        $limit = 6; // 6 kartu per halaman
        $offset = ($page - 1) * $limit;

        // Base Query: Hanya ambil yang bukan 'Draft'
        $builder = Announcement::query()->where('status', '!=', 'Draft');

        // Pencarian berdasarkan Judul atau Kategori
        if (!empty($query)) {
            $builder->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('category', 'LIKE', "%{$query}%");
            });
        }

        $totalData = (clone $builder)->count();
        $totalPages = ceil($totalData / $limit);

        $announcements = $builder->orderBy('id_announcement', 'DESC')
                                 ->offset($offset)
                                 ->limit($limit)
                                 ->get();

        $items = [];
        foreach ($announcements as $row) {
            // Bersihkan tag HTML dari CKEditor agar ringkasan bersih
            $cleanContent = strip_tags(html_entity_decode($row->content ?? ''));
            $excerpt = Str::limit($cleanContent, 120, '...');

            $createdDate = !empty($row->created_at) ? date('M d, Y', strtotime($row->created_at)) : 'Recently';

            // Enkode ID integer menjadi hashid unik pendek (e.g. 56 -> "aX8k")
            $hashId = Hashid::encode($row->id_announcement);

            $items[] = [
                'id'              => $hashId,
                'title'           => htmlspecialchars($row->title ?? 'Untitled'),
                'category'        => htmlspecialchars($row->category ?? 'Information'),
                'excerpt'         => $excerpt,
                'created_at'      => $createdDate,
                'created_by'      => htmlspecialchars($row->created_by ?? 'CDC Polibatam'),
                'visitor_count'   => (int) $row->visitor_count,
                'have_attachment' => (int) $row->have_attachment === 1,
                'detail_url'      => router('announcement.detail', ['id' => $hashId])
            ];
        }

        echo json_encode([
            'status'       => 'success',
            'total_data'   => $totalData,
            'current_page' => $page,
            'total_pages'  => $totalPages,
            'data'         => $items
        ]);
        exit;
    }
}