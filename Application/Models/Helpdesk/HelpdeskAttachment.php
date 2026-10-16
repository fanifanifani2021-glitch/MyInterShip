<?php

class HelpdeskAttachment extends Model
{
    protected $table = 'helpdesk_attachments';
    protected $primaryKey = 'id_attachment';
    protected $useTimestamps = false;

    protected $schema = [
        'id_attachment' => ['type' => 'int', 'length' => 11],
        'id_ticket'     => ['type' => 'int', 'length' => 11],
        'id_reply'      => ['type' => 'int', 'length' => 11],
        'file_name'     => ['type' => 'string', 'length' => 255],
        'file_path'     => ['type' => 'string', 'length' => 255],
        'file_size'     => ['type' => 'int', 'length' => 11],
        'created_at'    => ['type' => 'datetime'],
    ];

    public $fillable = [
        'id_ticket',
        'id_reply',
        'file_name',
        'file_path',
        'file_size',
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
     * Relasi Kembali ke Reply (opsional)
     */
    public function reply()
    {
        return $this->belongsTo('HelpdeskReply', 'id_reply', 'id_reply');
    }
}