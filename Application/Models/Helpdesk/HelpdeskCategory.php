<?php

class HelpdeskCategory extends Model
{
    protected $table = 'helpdesk_categories';
    protected $primaryKey = 'id_category';

    protected $schema = [
        'id_category'          => ['type' => 'int', 'length' => 11],
        'category_name'        => ['type' => 'string', 'length' => 100],
        'category_description' => ['type' => 'string', 'length' => 255],
        'is_active'            => ['type' => 'int', 'length' => 1], // Ubah ke int agar auto-cast ke integer
        'created_at'           => ['type' => 'datetime'],
        'updated_at'           => ['type' => 'datetime'],
    ];

    public $fillable = [
        'category_name',
        'category_description',
        'is_active'
    ];

    /**
     * Relasi One-to-Many ke HelpdeskTicket
     */
    public function tickets()
    {
        return $this->hasMany('HelpdeskTicket', 'id_category', 'id_category');
    }

    /**
     * Badge Status Aktif HTML
     */
    public function getStatusBadge()
    {
        if ($this->is_active) {
            return '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Active</span>';
        }
        return '<span class="badge badge-secondary"><i class="fas fa-minus-circle mr-1"></i> Inactive</span>';
    }
}