<?php

trait CanDataTable
{
    /**
     * @param callable $formatCallback Untuk memformat baris (UI)
     * @param callable|null $filterCallback Untuk filter custom tambahan
     */
    public static function toDataTable(callable $formatCallback, callable $filterCallback = null)
    {
        $request = Request::instance();
        $instance = new static;

        // 1. Ambil Parameter DataTables
        $draw   = $request->integer('draw', 1);
        $start  = $request->integer('start', 0);
        $length = $request->integer('length', 10);
        $search = $_POST['search']['value'] ?? '';

        // Sorting Logic
        $orderIndex = $_POST['order'][0]['column'] ?? 0;
        $orderDir   = $_POST['order'][0]['dir'] ?? 'desc';
        $orderColumn = $_POST['columns'][$orderIndex]['data'] ?? $instance->primaryKey;

        // 2. Inisialisasi Query Dasar
        $query = self::query();

        // 3. Global Search (Otomatis berdasarkan fillable)
        $searchableColumns = array_filter($instance->fillable ?? [], function($col) {
            return !in_array($col, ['id', 'id_kampus', 'created_at', 'updated_at']);
        });

        if (!empty($search) && !empty($searchableColumns)) {
            $query->where(function($q) use ($search, $searchableColumns) {
                foreach ($searchableColumns as $index => $column) {
                    if ($index === 0) {
                        $q->where($column, 'LIKE', "%$search%");
                    } else {
                        $q->orWhere($column, 'LIKE', "%$search%");
                    }
                }
            });
        }

        // 4. Custom Filter Hook (Jika ada)
        if ($filterCallback) {
            $filterCallback($query);
        }

        // 5. [PENTING] Clone Query untuk Hitung Records Filtered
        // Agar count tidak merusak state query utama
        $countQuery = clone $query;
        $recordsFiltered = $countQuery->count();
        $recordsTotal    = self::count();

        // 6. Sorting, Limit, & Offset pada Query Utama
        if (in_array($orderColumn, $instance->fillable ?? [])) {
            $query->orderBy($orderColumn, $orderDir);
        } else {
            $query->orderBy($instance->primaryKey, 'DESC');
        }

        $data = $query->limit($length)->offset($start)->get();

        // 7. Formatting Output
        $formattedData = [];
        $no = $start + 1;

        if (!empty($data)) {
            foreach ($data as $row) {
                // Pastikan callback menerima data yang sudah bersih (objek/array)
                $formattedData[] = $formatCallback($row, $no++);
            }
        }

        header('Content-Type: application/json');
        return json_encode([
            "draw"            => $draw,
            "recordsTotal"    => (int)$recordsTotal,
            "recordsFiltered" => (int)$recordsFiltered,
            "data"            => $formattedData
        ]);
    }
}