<?php

/**
 * Class DB
 * Wrapper statis untuk manajemen database.
 * Memudahkan eksekusi Raw Query dan akses ke QueryBuilder.
 */
class DB
{
    /**
     * Mendapatkan instance koneksi PDO.
     * @return \PDO
     */
    public static function connection()
    {
        return Database::getInstance()->getConnection();
    }

    /**
     * Memulai Query Builder pada tabel tertentu.
     * * Contoh: DB::table('users')->where('id', 1)->get();
     * * @param string $table Nama tabel.
     * @return QueryBuilder
     */
    public static function table($table)
    {
        return new QueryBuilder($table);
    }

    // =========================================================================
    // RAW QUERY EXECUTION
    // =========================================================================

    public static function raw($value)
    {
        return new Expression($value);
    }

    /**
     * Menjalankan statement SQL Raw (SELECT) dan mengembalikan array object.
     * * Contoh: DB::select("SELECT * FROM users WHERE id = ?", [1]);
     * * @param string $sql String SQL.
     * @param array $bindings Parameter binding.
     * @return array Hasil query (Array of Assoc).
     */
    public static function select($sql, $bindings = [])
    {
        $stmt = self::query($sql, $bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Menjalankan statement SQL Raw dan mengambil SATU baris pertama.
     * * Contoh: DB::selectOne("SELECT count(*) as total FROM users");
     * * @param string $sql String SQL.
     * @param array $bindings Parameter binding.
     * @return array|false Baris data atau false.
     */
    public static function selectOne($sql, $bindings = [])
    {
        $stmt = self::query($sql, $bindings);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Menjalankan statement SQL Raw untuk INSERT/UPDATE/DELETE.
     * Mengembalikan true/false keberhasilan.
     * * @param string $sql String SQL.
     * @param array $bindings Parameter binding.
     * @return bool
     */
    public static function statement($sql, $bindings = [])
    {
        return self::query($sql, $bindings)->rowCount() > 0;
    }

    /**
     * Eksekusi perintah INSERT mentah dan mengembalikan ID terakhir.
     * * @param string $sql String SQL.
     * @param array $bindings Parameter binding.
     * @return string|int Last Insert ID.
     */
    public static function insertGetId($sql, $bindings = [])
    {
        self::query($sql, $bindings);
        return self::connection()->lastInsertId();
    }

    /**
     * Fungsi Core untuk mengeksekusi query PDO.
     * * @param string $sql
     * @param array $bindings
     * @return \PDOStatement
     */
    public static function query($sql, $bindings = [])
    {
        try {
            $pdo = self::connection();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($bindings);
            return $stmt;
        } catch (PDOException $e) {
            // Log error jika fitur tersedia
            if (function_exists('dump_to_file')) {
                dump_to_file('db_raw_error', "SQL: $sql | Error: " . $e->getMessage());
            }
            
            // Tampilkan error saat development
            if (defined('SERVER_MODE') && SERVER_MODE === 'DEVELOPMENT') {
                die("<h3>DB Raw Query Error</h3><b>SQL:</b> $sql<br><b>Error:</b> " . $e->getMessage());
            }
            die("Database Error.");
        }
    }

    // =========================================================================
    // TRANSACTIONS
    // =========================================================================

    /**
     * Memulai Database Transaction.
     */
    public static function beginTransaction()
    {
        self::connection()->beginTransaction();
    }

    /**
     * Menyimpan perubahan (Commit).
     */
    public static function commit()
    {
        self::connection()->commit();
    }

    /**
     * Membatalkan perubahan (Rollback).
     */
    public static function rollBack()
    {
        self::connection()->rollBack();
    }

    /**
     * Menjalankan callback dalam transaction otomatis.
     * Jika ada error di dalam closure, otomatis rollback.
     * * @param callable $callback
     * @return mixed
     */
    public static function transaction(Closure $callback)
    {
        self::beginTransaction();

        try {
            $result = $callback();
            self::commit();
            return $result;
        } catch (Exception $e) {
            self::rollBack();
            throw $e;
        }
    }
}