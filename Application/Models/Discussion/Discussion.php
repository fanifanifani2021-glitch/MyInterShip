<?php

class Discussion extends Model
{
    protected $table = 'discussion';
    protected $primaryKey = 'id_discussion';

    protected $fillable = [
        'id_internship', 'user_type', 'user_id', 'user_name', 
        'discussion_title', 'discussion_topic', 'discussion_tag', 
        'discussion_status', 'timestamp_update'
    ];

    protected $schema = [
        'id_discussion' => ['type' => 'int'],
        'id_internship' => ['type' => 'int'],
        'timestamp_add' => ['type' => 'datetime']
    ];

    protected $useTimestamps = false; // Karena menggunakan timestamp manual/DB default

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Relasi ke data Magang
     */
    public function internship()
    {
        return $this->belongsTo(Internship::class, 'id_internship', 'id_internship');
    }

    /**
     * Relasi ke komentar-komentar (Has Many)
     */
    public function comments()
    {
        // Mengambil semua komentar yang memiliki id_discussion sama dengan model ini
        return DiscussionComment::where('id_discussion', $this->id_discussion)
                                 ->orderBy('timestamp_add', 'ASC')
                                 ->get();
    }


    /**
     * Menghitung jumlah komentar langsung dari database
     * @return int
     */
    public function commentsCount()
    {
        // Ini akan menjalankan query: SELECT COUNT(*) FROM discussion_comment WHERE id_discussion = ?
        return DiscussionComment::where('id_discussion', $this->id_discussion)->count();
    }


    // =========================================================================
    // HELPERS
    // =========================================================================

    public function getTimeAgo($lang = 'en')
    {
        return time_elapsed_string($this->timestamp_add); // Menggunakan helper global kamu
    }

    public function getStatusBadge()
    {
        $status = strtolower($this->discussion_status);
        $class = ($status == 'open') ? 'success' : 'danger';
        return '<span class="badge badge-' . $class . '">' . strtoupper($status) . '</span>';
    }

    /**
     * Cek apakah diskusi dibuat dalam 24 jam terakhir
     * @return bool
     */
    public function isNew()
    {
        $createdTime = strtotime($this->timestamp_add);
        $currentTime = time();
        $diffInSeconds = $currentTime - $createdTime;

        // 24 jam = 86400 detik
        return $diffInSeconds <= 86400;
    }

}