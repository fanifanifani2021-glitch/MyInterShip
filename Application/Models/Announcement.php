<?php

class Announcement extends Model
{
    protected $table = 'announcement';
    protected $primaryKey = 'id_announcement';

    // Model menggunakan kolom varchar/timestamp bawaan tabel
    protected $schema = [
        'id_announcement'     => ['type' => 'int', 'length' => 11],
        'status'              => ['type' => 'string', 'length' => 100],
        'title'               => ['type' => 'string', 'length' => 150],
        'picture'             => ['type' => 'string', 'length' => 150],
        'category'            => ['type' => 'string', 'length' => 100],
        'content'             => ['type' => 'string', 'length' => null],
        'have_attachment'     => ['type' => 'int', 'length' => 11],
        'attachment'          => ['type' => 'string', 'length' => 150],
        'visitor_count'       => ['type' => 'int', 'length' => 11],
        'created_by'          => ['type' => 'string', 'length' => 150],
        'created_by_username' => ['type' => 'string', 'length' => 100],
        'created_by_role'     => ['type' => 'string', 'length' => 100],
        'created_at'          => ['type' => 'string', 'length' => 150],
        'updated_at'          => ['type' => 'string', 'length' => 150],
        'id_kampus'           => ['type' => 'string', 'length' => 10],
    ];

    public $fillable = [
        'status',
        'title',
        'picture',
        'category',
        'content',
        'have_attachment',
        'attachment',
        'visitor_count',
        'created_by',
        'created_by_username',
        'created_by_role',
        'created_at',
        'updated_at',
        'id_kampus'
    ];
}