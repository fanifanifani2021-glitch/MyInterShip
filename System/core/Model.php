<?php

// Pastikan implements ArrayAccess ada
abstract class Model implements JsonSerializable, ArrayAccess
{
    protected $table;
    protected $primaryKey = 'id';
    
    // Container data
    protected $attributes = [];
    
    /**
     * [BARU] Definisi Schema (Tipe Data & Length)
     * Format: ['nama_kolom' => ['type' => 'int', 'length' => 11]]
     */
    protected $schema = [];


    protected $useTimestamps = true;
    protected $useSoftDeletes = false; // Set true di model anak jika perlu


    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);

        // Cek dan lengkapi kolom database jika diperlukan
        $this->syncSchema();
    }

    protected function syncSchema()
    {
        // Hanya jalankan jika di mode Development untuk performa
        if (defined('SERVER_MODE') && SERVER_MODE !== 'DEVELOPMENT') return;

        $db = DB::connection();
        $table = $this->getTable();
        
        // 1. Ambil kolom yang benar-benar ada di DB saat ini
        $existingColumns = $db->query("DESCRIBE {$table}")->fetchAll(PDO::FETCH_COLUMN);

        // 2. Cek Timestamps (created_at & updated_at)
        if ($this->useTimestamps) {
            if (!in_array('created_at', $existingColumns)) {
                $db->exec("ALTER TABLE {$table} ADD COLUMN created_at TIMESTAMP NULL");
            }
            if (!in_array('updated_at', $existingColumns)) {
                $db->exec("ALTER TABLE {$table} ADD COLUMN updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP");
            }
        }

        // 3. Cek Soft Deletes (deleted_at)
        if ($this->useSoftDeletes) {
            if (!in_array('deleted_at', $existingColumns)) {
                $db->exec("ALTER TABLE {$table} ADD COLUMN deleted_at TIMESTAMP NULL");
            }
        }
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            // Kita ubah value-nya sesuai tipe data di schema sebelum disimpan
            $this->attributes[$key] = $this->castAttribute($key, $value);
        }
        return $this;
    }

    /**
     * [BARU] Fungsi Ajaib untuk Mengubah Tipe Data (Casting)
     */
    protected function castAttribute($key, $value)
    {
        // Jika value null, biarkan null
        if (is_null($value)) return null;

        // Jika tidak ada definisi schema untuk kolom ini, kembalikan aslinya
        if (!isset($this->schema[$key])) return $value;

        $type = $this->schema[$key]['type'] ?? 'string';

        switch ($type) {
            case 'int':
            case 'integer':
                return (int) $value;
            
            case 'float':
            case 'double':
                return (float) $value;

            case 'bool':
            case 'boolean':
                // Mengubah 1/0/'1'/'0' jadi true/false
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            
            case 'json':
            case 'array':
                // Jika database simpan JSON, otomatis jadi Array di PHP
                return is_string($value) ? json_decode($value, true) : $value;
            
            case 'date':
            case 'datetime':
                // Opsional: Otomatis jadi object DateTime? (sementara string dulu)
                return $value; 

            default:
                return $value;
        }
    }

    // =========================================================================
    // 1. OBJECT ACCESS HANDLER (->)
    // =========================================================================


    /**
     * Perbaikan Method belongsTo di Model.php (Abstract Class)
     */
    public function belongsTo($relatedModel, $foreignKey, $ownerKey = 'id')
    {
        // 1. Ambil nilai foreign key dari atribut (misal: 'TRPL')
        $ownerKeyValue = $this->attributes[$foreignKey] ?? null;
        
        if (!$ownerKeyValue) return null;

        // 2. Gunakan query manual agar bisa mencari berdasarkan $ownerKey 
        // meskipun itu bukan Primary Key asli di model target
        return $relatedModel::where($ownerKey, $ownerKeyValue)->first();
    }

    /**
     * Mendefinisikan relasi One-to-One (Orang Tua ke Anak)
     */
    public function hasOne($relatedModel, $foreignKey, $localKey = 'id')
    {
        $localKeyValue = $this->attributes[$localKey] ?? null;
        if (!$localKeyValue) return null;

        // Cari di tabel anak yang memiliki foreign_key = local_key kita
        return $relatedModel::where($foreignKey, $localKeyValue)->first();
    }


    /**
     * Mendefinisikan relasi One-to-Many (Orang Tua ke banyak Anak)
     * Tambahkan ini di Model.php
     */
    public function hasMany($relatedModel, $foreignKey, $localKey = null)
    {
        $localKey = $localKey ?: $this->primaryKey;
        $localValue = $this->attributes[$localKey] ?? null;
        
        if (!$localValue) return [];

        return $relatedModel::where($foreignKey, $localValue)->get();
    }

    // public function __get($key)
    // {
    //     return $this->attributes[$key] ?? null;
    // }

    /**
     * MODIFIKASI: Magic method __get agar bisa handle relasi otomatis
     */
    public function __get($key)
    {
        // 1. Cek apakah ada di attributes (data kolom asli)
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        // 2. Cek apakah ada method relasi dengan nama tersebut
        if (method_exists($this, $key)) {
            // Simpan hasilnya ke attributes agar tidak query berulang kali (Caching)
            $this->attributes[$key] = $this->$key();
            return $this->attributes[$key];
        }

        return null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    // =========================================================================
    // 2. ARRAY ACCESS HANDLER ([]) - VERSI PHP 7.4 COMPATIBLE
    // =========================================================================

    /**
     * Cek apakah key ada (isset($model['key']))
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Ambil data via array ($model['key'])
     * [PERBAIKAN] Hapus ': mixed' agar jalan di PHP 7.4
     * @param mixed $offset
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->attributes[$offset] ?? null;
    }

    /**
     * Set data via array ($model['key'] = 'value')
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->attributes[] = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    /**
     * Hapus data via array (unset($model['key']))
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset]);
    }

    // =========================================================================
    // 3. OTHER HELPERS
    // =========================================================================

    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * [PERBAIKAN] Hapus ': mixed' agar jalan di PHP 7.4
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function getTable()
    {
        if (isset($this->table)) {
            return $this->table;
        }
        return strtolower((new ReflectionClass($this))->getShortName());
    }

    public static function query()
    {
        $instance = new static;
        return (new QueryBuilder($instance->getTable()))->setModel(get_class($instance));
    }

    public static function __callStatic($method, $parameters)
    {
        $instance = new static;
        $builder = (new QueryBuilder($instance->getTable()))->setModel(get_class($instance));
        
        return call_user_func_array([$builder, $method], $parameters);
    }

    /**
     * Mencari satu data berdasarkan Primary Key
     */
    public static function find($id) {
        $instance = new static;
        $result = DB::table($instance->getTable())
                    ->where($instance->primaryKey, $id)
                    ->first();

        if (!$result) return null;

        // Isi attributes (Bukan properti dinamis)
        $instance->fill((array) $result); 
        
        return $instance;
    }
    /**
     * Membuat data baru ke Database
     */
    public static function create(array $data) {
        $instance = new static();
        
        // Filter data hanya yang ada di fillable
        $filteredData = array_intersect_key($data, array_flip($instance->fillable));
        
        // Jalankan insert via QueryBuilder
        $insertResult = DB::table($instance->getTable())->insert($filteredData);
        
        /**
         * LOGIKA PERBAIKAN:
         * Jika lastInsertId() mengembalikan 0 (karena bukan auto-increment),
         * maka ambil ID/NIM dari data yang kita input tadi.
         */
        $id = ($insertResult != 0) ? $insertResult : ($filteredData[$instance->primaryKey] ?? null);
        
        if (!$id) return null;

        return self::find($id);
    }

    /**
     * Update data pada instansi model saat ini
     */
    public function update(array $data) 
    {
        // 1. Filter data berdasarkan properti fillable
        $filteredData = array_intersect_key($data, array_flip($this->fillable));

        // 2. Tambahkan updated_at otomatis hanya jika kolomnya ada di schema
        if ($this->useTimestamps && isset($this->schema['updated_at'])) {
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }

        // 3. Ambil nilai PK (Primary Key) dari attributes
        $pkValue = $this->attributes[$this->primaryKey] ?? null;

        // 4. Pastikan PK ada nilainya sebelum eksekusi
        if (!$pkValue) {
            throw new Exception("Update failed: Primary key '{$this->primaryKey}' is missing.");
        }

        // 5. Jalankan Query melalui QueryBuilder
        $success = DB::table($this->getTable())
                    ->where($this->primaryKey, $pkValue)
                    ->update($filteredData);
                    
        if ($success) {
            // 6. Sinkronisasi data ke dalam instansi objek saat ini
            foreach ($filteredData as $key => $value) {
                $this->attributes[$key] = $value;
            }
        }
        return $success;
    }

    /**
     * Menghapus data berdasarkan Primary Key secara langsung
     * Contoh: Maintenance::destroy(5);
     */
    public static function destroy($id)
    {
        $instance = new static; // Buat instansi sementara untuk cek properti model
        
        // 1. Logika Soft Delete Statis
        if ($instance->useSoftDeletes && isset($instance->schema['deleted_at'])) {
            return DB::table($instance->getTable())
                        ->where($instance->primaryKey, $id)
                        ->update(['deleted_at' => date('Y-m-d H:i:s')]);
        }

        // 2. Logika Hard Delete Statis
        return self::query()
                    ->where($instance->primaryKey, $id)
                    ->delete();
    }

    /**
     * Menghapus instansi objek yang sedang aktif
     * Contoh: $m = Maintenance::find(1); $m->delete();
     */
    public function delete()
    {
        // 1. Pastikan Primary Key ada nilainya
        if (!isset($this->attributes[$this->primaryKey])) {
            return false;
        }

        // 2. Cek apakah harus Soft Delete: Fitur Aktif DAN Kolom ada di Schema
        if ($this->useSoftDeletes && isset($this->schema['deleted_at'])) {
            return $this->update(['deleted_at' => date('Y-m-d H:i:s')]);
        }

        // 3. Fallback ke Hard Delete (Permanen) jika syarat soft delete tidak terpenuhi
        return self::query()
                    ->where($this->primaryKey, $this->{$this->primaryKey})
                    ->delete();
    }

    public function save() 
    {
        $pk = $this->primaryKey;
        $now = date('Y-m-d H:i:s');

        if (isset($this->attributes[$pk])) {
            // --- LOGIKA UPDATE ---
            // Hanya isi updated_at jika fitur aktif DAN kolomnya ada di schema
            if ($this->useTimestamps && isset($this->schema['updated_at'])) {
                $this->attributes['updated_at'] = $now;
            }
            return $this->update($this->attributes);
        } else {
            // --- LOGIKA INSERT ---
            // Hanya isi created_at jika fitur aktif DAN kolomnya ada di schema
            if ($this->useTimestamps && isset($this->schema['created_at'])) {
                $this->attributes['created_at'] = $now;
            }
            // Sama untuk updated_at saat data baru dibuat
            if ($this->useTimestamps && isset($this->schema['updated_at'])) {
                $this->attributes['updated_at'] = $now;
            }
            return self::create($this->attributes);
        }
    }
}