<?php

class DiscussionComment extends Model
{
    protected $table = 'discussion_comment';
    protected $primaryKey = 'id_comment';

    protected $fillable = [
        'id_discussion', 'user_type', 'user_id', 
        'user_name', 'comment', 'timestamp_update'
    ];

    protected $schema = [
        'id_comment'    => ['type' => 'int'],
        'id_discussion' => ['type' => 'int'],
        'timestamp_add' => ['type' => 'datetime']
    ];

    protected $useTimestamps = false;

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Relasi balik ke Topik Diskusi
     */
    public function discussion()
    {
        return $this->belongsTo(Discussion::class, 'id_discussion', 'id_discussion');
    }
}