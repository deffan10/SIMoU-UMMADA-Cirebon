<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $fillable = [
        'admin_id',
        'file_name',
        'file_path',
        'total_rows',
        'success_count',
        'failed_count',
        'duplicate_count',
        'status',
        'errors',
        'summary',
    ];

    protected $casts = [
        'errors' => 'array',
        'summary' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
