<?php

/**
 * Class QueryBuilder
 * * Menyediakan antarmuka (Fluent Interface) untuk membangun dan mengeksekusi 
 * query SQL database dengan aman menggunakan PDO Prepared Statements.
 */
class QueryBuilder
{
    /** @var \PDO Instance koneksi database */
    protected $pdo;

    /** @var string Nama tabel target */
    protected $table;

    // Query Components
    protected $selects = ['*'];
    protected $distinct = false;
    protected $joins = [];
    protected $wheres = [];
    protected $groups = [];
    protected $havings = [];
    protected $orders = [];
    protected $limit = null;
    protected $offset = null;

    // Binding Parameters
    protected $bindings = [
        'select' => [],
        'join'   => [],
        'where'  => [],
        'having' => [],
        'order'  => [],
    ];

    protected $modelClass = null; // [BARU] Menyimpan nama class Model (misal: StudentInternship)

    protected static $queryLog = [];

    /**
     * [BARU] Set Class Model target untuk output result.
     */
    public function setModel($class)
    {
        $this->modelClass = $class;
        return $this;
    }

    /**
     * Inisialisasi QueryBuilder.
     * * @param string $table Nama tabel database.
     */
    public function __construct($table)
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->table = $table;
    }


    /**
     * Method untuk insert data dan mengembalikan ID terakhir yang dibuat.
     * * @param array $data ['column' => 'value']
     * @return string|int ID dari record yang baru diinsert
     */
    public function insertGetId(array $data)
    {
        // 1. Ambil instance PDO dari server
        $pdo = $_SERVER['PDO'];

        // 2. Siapkan bagian kolom dan placeholder
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        // 3. Bangun query SQL (Pastikan $this->table sudah diisi lewat DB::table('nama'))
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        try {
            $stmt = $pdo->prepare($sql);
            
            // 4. Eksekusi query dengan data yang di-binding otomatis oleh PDO
            $stmt->execute($data);

            // 5. Kembalikan ID terakhir
            return $pdo->lastInsertId();

        } catch (PDOException $e) {
            // Log error jika diperlukan atau throw kembali
            throw new Exception("Insert failed: " . $e->getMessage());
        }
    }


    // =========================================================================
    // SELECTS & JOINS
    // =========================================================================

    /**
     * Menentukan kolom yang akan diambil (SELECT).
     * * @param array|string $columns Array kolom atau string nama kolom.
     * @return self
     * * @example ->select('id', 'name')
     * @example ->select(['id', 'name', 'email'])
     */
    public function select($columns = ['*'])
    {
        $this->selects = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * Menambahkan ekspresi SELECT mentah (Raw SQL).
     * Berguna untuk fungsi agregat atau subquery.
     * * @param string $expression String SQL mentah.
     * @param array $bindings Parameter binding (opsional).
     * @return self
     * * @example ->selectRaw('count(*) as total')
     */
    public function selectRaw($expression, array $bindings = [])
    {
        $this->selects[] = $expression;
        $this->bindings['select'] = array_merge($this->bindings['select'], $bindings);
        return $this;
    }

    /**
     * Menggabungkan tabel (INNER JOIN).
     * * @param string $table Nama tabel yang akan di-join.
     * @param string $first Kolom kunci dari tabel utama (Foreign Key).
     * @param string $operator Operator perbandingan (misal: '=', '>', '!=').
     * @param string $second Kolom kunci dari tabel yang di-join (Primary Key).
     * @param string $type Tipe join (INNER, LEFT, RIGHT).
     * @return self
     * * @example ->join('users', 'posts.user_id', '=', 'users.id')
     */
    public function join($table, $first, $operator = null, $second = null, $type = 'INNER')
    {
        if ($first instanceof Closure) {
            // Jika Closure (contoh: leftJoin('table', function($join){ ... }))
            $this->joins[] = "{$type} JOIN {$table}";
            return $this;
        }

        // Jika hanya dikirim 2 argumen (seperti: ->join('attendance', 'attendance.id = internship.id'))
        if ($operator === null && $second === null) {
            $this->joins[] = "{$type} JOIN {$table} ON {$first}";
            return $this;
        }

        // Jika argumen $operator diisi langsung dengan nama kolom kedua (seperti: ->join('table', 'col1', 'col2'))
        if ($second === null) {
            $second = $operator;
            $operator = '=';
        }

        $this->joins[] = "{$type} JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    /**
     * Menggabungkan tabel (LEFT JOIN).
     */
    public function leftJoin($table, $first = null, $operator = null, $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    /**
     * Menggabungkan tabel (RIGHT JOIN).
     */
    public function rightJoin($table, $first = null, $operator = null, $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }



    /**
     * Menghasilkan hasil query yang unik (DISTINCT).
     * @return self
     */
    public function distinct()
    {
        $this->distinct = true;
        return $this;
    }



    // =========================================================================
    // WHERE CLAUSES
    // =========================================================================

    /**
     * Menambahkan kondisi WHERE.
     * * @param string $column Nama kolom.
     * @param string|mixed $operator Operator (misal: '=', '>', 'LIKE') ATAU Value jika operator dihilangkan.
     * @param mixed $value Nilai yang dicari (Opsional jika argumen ke-2 adalah value).
     * @param string $boolean Logika penggabung ('AND', 'OR').
     * @return self
     * * @example ->where('id', 1) 
     * @example ->where('age', '>=', 18)
     */
    public function where($column, $operator = null, $value = null, $boolean = 'AND')
    {
        if ($column instanceof Closure) {
            $query = new static($this->table);
            $column($query);
            $this->wheres[] = ['type' => 'Nested', 'query' => $query, 'boolean' => $boolean];
            return $this;
        }

        if (func_num_args() == 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'Basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value, // Simpan di sini
            'boolean' => $boolean
        ];

        return $this;
    }


    /**
     * Menambahkan kondisi OR WHERE.
     * * @param string $column Nama kolom.
     * @param string|mixed $operator Operator atau Value.
     * @param mixed $value Nilai.
     * @return self
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        if (func_num_args() == 2) {
            $value = $operator;
            $operator = '=';
        }
        return $this->where($column, $operator, $value, 'OR');
    }


    /**
     * Menambahkan kondisi OR WHERE mentah (Raw SQL).
     *
     * @param string $sql String SQL where.
     * @param array $bindings Array nilai untuk binding.
     * @return self
     */
    public function orWhereRaw($sql, array $bindings = [])
    {
        return $this->whereRaw($sql, $bindings, 'OR');
    }

    /**
     * Menambahkan kondisi WHERE IN (Mencocokkan nilai dalam array).
     * * @param string $column Nama kolom.
     * @param array $values Array nilai yang dicari.
     * @return self
     * * @example ->whereIn('role', ['admin', 'editor'])
     */
    public function whereIn($column, array $values, $boolean = 'AND', $not = false)
    {
        $type = $not ? 'NotIn' : 'In';
        $placeholders = implode(', ', array_fill(0, count($values), '?'));

        $this->wheres[] = [
            'type' => $type,
            'column' => $column,
            'placeholders' => $placeholders,
            'values' => $values, // Simpan di sini
            'boolean' => $boolean
        ];

        return $this;
    }

    /**
     * Menambahkan kondisi WHERE NOT IN.
     * @return self
     */
    public function whereNotIn($column, array $values, $boolean = 'AND')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    /**
     * Menambahkan kondisi WHERE IS NULL.
     * * @param string $column Nama kolom.
     * @return self
     */
    public function whereNull($column, $boolean = 'AND', $not = false)
    {
        $type = $not ? 'NotNull' : 'Null';

        $this->wheres[] = [
            'type' => $type,
            'column' => $column,
            'boolean' => $boolean
        ];

        return $this;
    }

    /**
     * Menambahkan kondisi WHERE IS NOT NULL.
     * @return self
     */
    public function whereNotNull($column, $boolean = 'AND')
    {
        return $this->whereNull($column, $boolean, true);
    }

    /**
     * Shortcut untuk kondisi WHERE LIKE.
     * * @param string $column Nama kolom.
     * @param string $value Nilai (biasanya mengandung %).
     * @return self
     * * @example ->whereLike('name', '%John%')
     */
    public function whereLike($column, $value, $boolean = 'AND')
    {
        return $this->where($column, 'LIKE', $value, $boolean);
    }

    /**
     * Membungkus kondisi ke dalam kurung (AND Group).
     * @param Closure $callback
     * @return self
     */
    public function whereGroup(Closure $callback)
    {
        return $this->where($callback);
    }

    /**
     * Membungkus kondisi ke dalam kurung dengan awalan OR (OR Group).
     * @param Closure $callback
     * @return self
     */
    public function orWhereGroup(Closure $callback)
    {
        return $this->where($callback, null, null, 'OR');
    }

    /**
     * Menambahkan kondisi WHERE mentah (Raw SQL).
     * Hati-hati terhadap SQL Injection, gunakan parameter binding!
     * * @param string $sql String SQL where.
     * @param array $bindings Array nilai untuk binding.
     * @return self
     * * @example ->whereRaw('YEAR(created_at) = ?', [2023])
     */
    public function whereRaw($sql, array $bindings = [], $boolean = 'AND')
    {
        $this->wheres[] = [
            'type' => 'Raw',
            'sql' => $sql,
            'bindings' => $bindings, // Simpan di sini
            'boolean' => $boolean
        ];

        return $this;
    }

    // =========================================================================
    // GROUP, HAVING, ORDER, LIMIT, OFFSET
    // =========================================================================

    /**
     * Mengelompokkan hasil query (GROUP BY).
     * * @param string|array $groups Nama kolom atau array kolom.
     * @return self
     */
    public function groupBy($groups)
    {
        $groups = is_array($groups) ? $groups : func_get_args();
        $this->groups = array_merge($this->groups, $groups);
        return $this;
    }

    /**
     * Menambahkan kondisi HAVING (filter setelah Group By).
     * * @param string $column Nama kolom atau Agregat.
     * @param string|mixed $operator Operator atau Value.
     * @param mixed $value Nilai.
     * @return self
     */
    public function having($column, $operator = null, $value = null, $boolean = 'AND')
    {
        if (func_num_args() == 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->havings[] = compact('column', 'operator', 'boolean');
        $this->bindings['having'][] = $value;

        return $this;
    }

    /**
     * Mengurutkan hasil query (ORDER BY).
     * * @param string $column Nama kolom.
     * @param string $direction Arah urutan ('ASC' atau 'DESC'). Default 'ASC'.
     * @return self
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $this->orders[] = "{$column} {$direction}";
        return $this;
    }


    /**
     * Menambahkan pengurutan berdasarkan SQL mentah (Raw).
     * Berguna untuk pengurutan kustom seperti CASE WHEN.
     * 
     * @param string $sql String SQL mentah.
     * @param array $bindings Parameter binding jika ada.
     * @return self
     */
    public function orderByRaw($sql, array $bindings = [])
    {
        $this->orders[] = $sql;
        
        // Simpan bindings jika ada (meskipun di order by jarang pakai, tapi untuk jaga-jaga)
        if (!empty($bindings)) {
            $this->bindings['order'] = array_merge($this->bindings['order'], $bindings);
        }
        
        return $this;
    }


    /**
     * Membatasi jumlah baris yang diambil (LIMIT).
     * * @param int $value Jumlah baris.
     * @return self
     */
    public function limit($value)
    {
        $this->limit = $value;
        return $this;
    }

    /**
     * Melewati sejumlah baris awal (OFFSET).
     * * @param int $value Jumlah baris yang dilewati.
     * @return self
     */
    public function offset($value)
    {
        $this->offset = $value;
        return $this;
    }

    // =========================================================================
    // EXECUTION & AGGREGATES
    // =========================================================================

    /**
     * Menjalankan query dan mengambil SEMUA hasil.
     * * @return array Array of Associative Arrays (List data).
     */
    public function get()
    {
        $sql = $this->buildSelectQuery();
        $results = $this->executeQuery($sql)->fetchAll();

        // [BARU] Jika ini dipanggil dari Model, konversi array ke Object Model
        if ($this->modelClass) {
            $objects = [];
            foreach ($results as $row) {
                // Buat instance object baru (misal: new StudentInternship($row))
                $objects[] = new $this->modelClass($row);
            }
            return $objects;
        }

        // Jika DB::table(), kembalikan array biasa
        return $results;
    }

    /**
     * Menjalankan query dan mengambil SATU baris pertama.
     * * @return array|false Associative Array data atau false jika kosong.
     */
    public function first()
    {
        // Clone agar limit(1) tidak merusak query asli
        $clone = clone $this;
        $clone->limit(1);

        $sql = $clone->buildSelectQuery();
        $result = $clone->executeQuery($sql)->fetch();

        if (!$result) return null;

        if ($this->modelClass) {
            return new $this->modelClass($result);
        }

        return $result;
    }

    /**
     * Menghitung jumlah baris (COUNT).
     * * @param string $column Kolom yang dihitung (default '*').
     * @return int Jumlah baris.
     */
    public function count($column = '*')
    {
        return $this->aggregate('COUNT', $column);
    }

    /**
     * Mengambil nilai maksimum (MAX).
     * @return mixed
     */
    public function max($column)
    {
        return $this->aggregate('MAX', $column);
    }

    /**
     * Mengambil nilai minimum (MIN).
     * @return mixed
     */
    public function min($column)
    {
        return $this->aggregate('MIN', $column);
    }

    /**
     * Mengambil nilai rata-rata (AVG).
     * @return mixed
     */
    public function avg($column)
    {
        return $this->aggregate('AVG', $column);
    }

    /**
     * Mengambil jumlah total nilai (SUM).
     * @return mixed
     */
    public function sum($column)
    {
        return $this->aggregate('SUM', $column);
    }

    /**
     * Helper internal untuk fungsi agregat.
     */
    protected function aggregate($function, $column)
    {
        // Clone agar selects ["COUNT(*)"] tidak merusak query asli
        $clone = clone $this;
        $clone->selects = ["{$function}({$column}) as aggregate"];

        $result = $clone->first();

        // Jika first() mengembalikan object Model (karena setModel), 
        // kita ambil atributnya via ArrayAccess atau property
        if (is_object($result)) {
            return $result['aggregate'] ?? 0;
        }

        return $result['aggregate'] ?? 0;
    }

    /**
     * Memeriksa apakah data ditemukan (exists).
     * * @return bool True jika ada data, False jika kosong.
     */
    public function exists()
    {
        $this->selects = [1];
        $this->limit(1);
        $result = $this->first();
        return !empty($result);
    }

    // =========================================================================
    // CRUD OPERATIONS
    // =========================================================================

    /**
     * Menyimpan data baru (INSERT).
     * * @param array $data Array asosiatif ['kolom' => 'nilai'].
     * @return string|int ID dari data yang baru dimasukkan (Last Insert ID).
     */
    public function insert(array $data)
    {
        $keys = array_keys($data);
        $columns = implode(', ', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $this->executeQuery($sql, array_values($data));

        return $this->pdo->lastInsertId();
    }

    /**
     * Mengupdate data (UPDATE) berdasarkan kondisi WHERE.
     * * @param array $data Array asosiatif kolom yang akan diupdate.
     * @return int Jumlah baris yang berubah (Affected Rows).
     */
    public function update(array $data)
    {
        $setParts = [];
        $values = [];

        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = ?";
            $values[] = $value;
        }

        $setString = implode(', ', $setParts);
        $whereString = $this->buildWhereString();

        $sql = "UPDATE {$this->table} SET {$setString} {$whereString}";

        // Gabungkan binding UPDATE (depan) dengan binding WHERE (belakang)
        $bindings = array_merge($values, $this->getFlattenedBindings());

        return $this->executeQuery($sql, $bindings)->rowCount();
    }

    /**
     * Menghapus data (DELETE) berdasarkan kondisi WHERE.
     * * @return int Jumlah baris yang terhapus.
     */
    public function delete()
    {
        // PROTEKSI: Jangan biarkan delete jalan tanpa WHERE
        if (empty($this->wheres)) {
            throw new Exception("Potentially unsafe operation: DELETE without WHERE clause is prohibited.");
        }

        $whereString = $this->buildWhereString();
        $sql = "DELETE FROM {$this->table} {$whereString}";

        // Eksekusi dengan bindings yang ada
        return $this->executeQuery($sql, $this->getFlattenedBindings())->rowCount();
    }
    // =========================================================================
    // QUERY BUILDING INTERNALS (PROTECTED)
    // =========================================================================

    /**
     * Membangun string query SELECT lengkap.
     */
    protected function buildSelectQuery()
    {
        $cols = implode(', ', $this->selects);

        $selectKeyword = $this->distinct ? "SELECT DISTINCT" : "SELECT";
        
        $sql = "{$selectKeyword} {$cols} FROM {$this->table}";

        // Joins
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }

        // Wheres
        $sql .= $this->buildWhereString();

        // Groups
        if (!empty($this->groups)) {
            $sql .= " GROUP BY " . implode(', ', $this->groups);
        }

        // Having
        if (!empty($this->havings)) {
            $sql .= " HAVING ";
            foreach ($this->havings as $index => $have) {
                $prefix = ($index === 0) ? '' : $have['boolean'] . ' ';
                $sql .= $prefix . "{$have['column']} {$have['operator']} ? ";
            }
        }

        // Orders
        if (!empty($this->orders)) {
            $sql .= " ORDER BY " . implode(', ', $this->orders);
        }

        // Limit
        if (!is_null($this->limit)) {
            $sql .= " LIMIT " . (int) $this->limit;
        }

        // Offset
        if (!is_null($this->offset)) {
            $sql .= " OFFSET " . (int) $this->offset;
        }

        return $sql;
    }

    /**
     * Membangun string kondisi WHERE.
     */
    protected function buildWhereString()
    {
        if (empty($this->wheres)) {
            return '';
        }

        $sqlParts = [];

        foreach ($this->wheres as $index => $where) {
            $prefix = ($index === 0) ? '' : $where['boolean'] . ' ';

            switch ($where['type']) {
                case 'Nested':
                    // [BARU] Ambil string WHERE dari sub-query dan bungkus kurung
                    $nestedSql = substr($where['query']->buildWhereString(), 7); // Potong kata " WHERE "
                    $sqlParts[] = "{$prefix}({$nestedSql})";
                    break;
                case 'Basic':
                    $sqlParts[] = "{$prefix}{$where['column']} {$where['operator']} ?";
                    break;

                case 'In':
                    $sqlParts[] = "{$prefix}{$where['column']} IN ({$where['placeholders']})";
                    break;
                case 'NotIn':
                    $sqlParts[] = "{$prefix}{$where['column']} NOT IN ({$where['placeholders']})";
                    break;
                case 'Null':
                    $sqlParts[] = "{$prefix}{$where['column']} IS NULL";
                    break;
                case 'NotNull':
                    $sqlParts[] = "{$prefix}{$where['column']} IS NOT NULL";
                    break;
                case 'Raw':
                    $sqlParts[] = "{$prefix}{$where['sql']}";
                    break;
            }
        }

        return " WHERE " . implode(' ', $sqlParts);
    }

    protected function getFlattenedBindings()
    {
        return array_merge(
            $this->bindings['select'],
            $this->bindings['join'],
            $this->collectWhereBindings(), // Hanya gunakan ini
            $this->bindings['having'],
            $this->bindings['order']
        );
    }
    /**
     * Helper untuk mengumpulkan semua binding WHERE termasuk nested
     */
    protected function collectWhereBindings()
    {
        $bindings = [];
        foreach ($this->wheres as $where) {
            switch ($where['type']) {
                case 'Nested':
                    $bindings = array_merge($bindings, $where['query']->collectWhereBindings());
                    break;
                case 'Basic':
                    $bindings[] = $where['value'];
                    break;
                case 'In':
                case 'NotIn':
                    $bindings = array_merge($bindings, $where['values']);
                    break;
                case 'Raw':
                    $bindings = array_merge($bindings, $where['bindings']);
                    break;
            }
        }
        return $bindings;
    }


    /**
     * Mengeksekusi Statement SQL (Low-level).
     */
    protected function executeQuery($sql, $bindings = null)
    {

        if (defined('SERVER_MODE') && SERVER_MODE === 'DEVELOPMENT') {
            self::$queryLog[] = [
                'sql' => $sql,
                'params' => $bindings,
                'time' => date('H:i:s')
            ];
        }
    
        try {
            // Gunakan bindings yang di-pass atau ambil default flatten bindings
            $params = $bindings ?? $this->getFlattenedBindings();

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt;
        } catch (PDOException $e) {
            if (function_exists('dump_to_file')) {
                dump_to_file('query_error', "SQL: $sql | Params: " . json_encode($params) . " | Error: " . $e->getMessage());
            }

            // Tampilkan error jika di development mode, atau generic jika production
            if (defined('SERVER_MODE') && SERVER_MODE === 'DEVELOPMENT') {
                die("<h3>Database Error</h3><b>Message:</b> " . $e->getMessage() . "<br><b>SQL:</b> " . $sql);
            } else {
                die("Database Error. Please check logs.");
            }
        }
    }


    public static function getLog() { return self::$queryLog; }
}
