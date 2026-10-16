<?php

class HelpdeskReply extends Model
{
    protected $table = 'helpdesk_replies';
    protected $primaryKey = 'id_reply';
    protected $useTimestamps = false; // Hanya menggunakan created_at bawaan tabel SQL

    protected $schema = [
        'id_reply'   => ['type' => 'int', 'length' => 11],
        'id_ticket'  => ['type' => 'int', 'length' => 11],
        'user_id'    => ['type' => 'string', 'length' => 100],
        'user_type'  => ['type' => 'string', 'length' => 50],
        'user_name'  => ['type' => 'string', 'length' => 150],
        'message'    => ['type' => 'string', 'length' => null],
        'created_at' => ['type' => 'datetime'],
    ];

    public $fillable = [
        'id_ticket',
        'user_id',
        'user_type',
        'user_name',
        'message',
        'created_at'
    ];

    /**
     * Relasi Kembali ke Ticket Utama
     */
    public function ticket()
    {
        return $this->belongsTo('HelpdeskTicket', 'id_ticket', 'id_ticket');
    }

    /**
     * Relasi ke Lampiran File dari Balasan/Reply
     */
    public function attachments()
    {
        return $this->hasMany('HelpdeskAttachment', 'id_reply', 'id_reply');
    }
}