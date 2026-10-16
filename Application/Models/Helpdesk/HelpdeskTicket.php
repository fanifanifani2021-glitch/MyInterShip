<?php

class HelpdeskTicket extends Model
{
    protected $table = 'helpdesk_tickets';
    protected $primaryKey = 'id_ticket';

    protected $schema = [
        'id_ticket'    => ['type' => 'int', 'length' => 11],
        'ticket_code'  => ['type' => 'string', 'length' => 30],
        'id_category'  => ['type' => 'int', 'length' => 11],
        'user_id'      => ['type' => 'string', 'length' => 100],
        'user_type'    => ['type' => 'string', 'length' => 50],
        'user_name'    => ['type' => 'string', 'length' => 150],
        'user_email'   => ['type' => 'string', 'length' => 150],
        'priority'     => ['type' => 'string', 'length' => 20],
        'subject'      => ['type' => 'string', 'length' => 255],
        'description'  => ['type' => 'string', 'length' => null],
        'status'       => ['type' => 'string', 'length' => 20],
        'assigned_to'  => ['type' => 'string', 'length' => 100],
        'created_at'   => ['type' => 'datetime'],
        'updated_at'   => ['type' => 'datetime'],
    ];

    public $fillable = [
        'ticket_code',
        'id_category',
        'user_id',
        'user_type',
        'user_name',
        'user_email',
        'priority',
        'subject',
        'description',
        'status',
        'assigned_to'
    ];

    /**
     * Relasi ke Category
     */
    public function category()
    {
        return $this->belongsTo('HelpdeskCategory', 'id_category', 'id_category');
    }

    /**
     * Relasi ke Replies (Threads)
     */
    public function replies()
    {
        return HelpdeskReply::where('id_ticket', $this->id_ticket)
            ->orderBy('created_at', 'ASC')
            ->get();
    }

    /**
     * Relasi ke Lampiran File Tiket Utama (yang bukan dari reply)
     */
    public function attachments()
    {
        return HelpdeskAttachment::where('id_ticket', $this->id_ticket)
            ->whereNull('id_reply')
            ->get();
    }

    /**
     * Badge Status Tiket HTML
     */
    public function getStatusBadge()
    {
        switch (strtolower($this->status)) {
            case 'open':
                return '<span class="badge badge-warning"><i class="fas fa-folder-open mr-1"></i> Open</span>';
            case 'in progress':
                return '<span class="badge badge-info"><i class="fas fa-spinner fa-spin mr-1"></i> In Progress</span>';
            case 'resolved':
                return '<span class="badge badge-success"><i class="fas fa-check-double mr-1"></i> Resolved</span>';
            case 'closed':
                return '<span class="badge badge-secondary"><i class="fas fa-lock mr-1"></i> Closed</span>';
            default:
                return '<span class="badge badge-dark">' . htmlspecialchars($this->status) . '</span>';
        }
    }

    /**
     * Badge Priority HTML
     */
    public function getPriorityBadge()
    {
        switch (strtolower($this->priority)) {
            case 'urgent':
                return '<span class="badge badge-danger text-uppercase"><i class="fas fa-exclamation-triangle mr-1"></i> Urgent</span>';
            case 'high':
                return '<span class="badge badge-warning text-uppercase"><i class="fas fa-arrow-up mr-1"></i> High</span>';
            case 'medium':
                return '<span class="badge badge-primary text-uppercase">Medium</span>';
            case 'low':
                return '<span class="badge badge-light border text-uppercase">Low</span>';
            default:
                return '<span class="badge badge-secondary text-uppercase">' . htmlspecialchars($this->priority) . '</span>';
        }
    }
}