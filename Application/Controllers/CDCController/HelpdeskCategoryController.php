<?php
namespace Application\Controllers\CDCController;

use Controller;
use HelpdeskCategory;
use Request;
use Validator;
use Exception;
use Str;

class HelpdeskCategoryController extends Controller
{
    public function index()
    {
        renderView('cdc.helpdesk.categories.index', [
            'route' => 'helpdesk_categories'
        ]);
    }

    /**
     * DataTables Server-Side Response
     */
    public function data()
    {
        $draw   = Request::integer('draw', 1);
        $start  = Request::integer('start', 0);
        $length = Request::integer('length', 10);
        $search = Request::post('search')['value'] ?? '';

        $query = HelpdeskCategory::query();
        $totalData = (clone $query)->count();

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('category_name', 'LIKE', "%{$search}%")
                  ->orWhere('category_description', 'LIKE', "%{$search}%");
            });
        }

        $totalFiltered = (clone $query)->count();

        $categories = $query->orderBy('id_category', 'DESC')
                           ->offset($start)
                           ->limit($length)
                           ->get();

        $data = [];
        $no   = $start + 1;

        foreach ($categories as $row) {
            $statusBadge = $row->is_active 
                ? '<span class="badge-status badge-active"><i class="fas fa-check-circle mr-1"></i> Active</span>'
                : '<span class="badge-status badge-expired"><i class="fas fa-minus-circle mr-1"></i> Inactive</span>';

            $aksi = '
            <div class="text-center">
                <button onclick="edit_data(' . $row->id_category . ')" class="btn btn-xs btn-primary shadow-sm" title="Edit"><i class="fas fa-pen"></i></button>
                <button onclick="toggle_status(' . $row->id_category . ')" class="btn btn-xs btn-warning shadow-sm" title="Toggle Status"><i class="fas fa-sync-alt"></i></button>
                <button onclick="delete_data(' . $row->id_category . ')" class="btn btn-xs btn-danger shadow-sm" title="Delete"><i class="fas fa-trash"></i></button>
            </div>';

            $data[] = [
                'no'                   => $no++,
                'category_name'        => '<strong>' . htmlspecialchars($row->category_name ?? '-') . '</strong>',
                'category_description' => htmlspecialchars($row->category_description ?: '-'),
                'status'               => $statusBadge,
                'aksi'                 => $aksi
            ];
        }

        header('Content-Type: application/json');
        echo json_encode([
            "draw"            => $draw,
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        ]);
        exit;
    }

    /**
     * AJAX Save (Create & Update)
     */
    public function save()
    {
        header('Content-Type: application/json');
        try {
            $id = Request::integer('id');

            // 1. Validasi Input
            $rules = [
                'category_name' => ['required', 'min:3']
            ];

            $messages = [
                'category_name.required' => 'Category name is required.',
                'category_name.min'      => 'Category name must be at least 3 characters.'
            ];

            $validator = Validator::make(Request::all(), $rules, [], $messages);

            if (!$validator->validate()) {
                echo json_encode([
                    'status' => 'validation_error',
                    'errors' => $validator->errors()
                ]);
                exit;
            }

            // 2. Data Preparation
            $data = [
                'category_name'        => Str::sanitize(Request::string('category_name')),
                'category_description' => Str::sanitize(Request::string('category_description')),
                'is_active'            => Request::integer('is_active', 1)
            ];

            // 3. Insert or Update
            if ($id > 0) {
                $category = HelpdeskCategory::find($id);
                if (!$category) throw new Exception("Category record not found.");
                $category->update($data);
                $message = "Category updated successfully.";
            } else {
                HelpdeskCategory::create($data);
                $message = "New category created successfully.";
            }

            echo json_encode(['status' => 'success', 'message' => $message]);
            exit;

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * AJAX Get Detail
     */
    public function detail()
    {
        header('Content-Type: application/json');
        try {
            $id = Request::integer('id');

            if (!$id) throw new Exception("Parameter 'id' is missing.");

            $category = HelpdeskCategory::find($id);

            if (!$category) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Category not found.']);
                exit;
            }

            echo json_encode($category);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * AJAX Destroy (Hard Delete)
     */
    public function destroy()
    {
        header('Content-Type: application/json');
        try {
            $id = Request::integer('id');
            $category = HelpdeskCategory::find($id);

            if (!$category) {
                echo json_encode(['status' => 'error', 'message' => 'Category not found!']);
                exit;
            }

            if ($category->delete()) {
                echo json_encode(['status' => 'success', 'message' => 'Category deleted successfully.']);
            } else {
                throw new Exception("Failed to delete category from database.");
            }
            exit;

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * AJAX Toggle Active Status
     */
    public function toggleStatus()
    {
        header('Content-Type: application/json');
        try {
            $id = Request::integer('id');
            $category = HelpdeskCategory::find($id);

            if (!$category) {
                echo json_encode(['status' => 'error', 'message' => 'Category not found!']);
                exit;
            }

            $newStatus = ((int)$category->is_active === 1) ? 0 : 1;
            $category->update(['is_active' => $newStatus]);

            echo json_encode(['status' => 'success', 'message' => 'Category status updated successfully.']);
            exit;

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }
}