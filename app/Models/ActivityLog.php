<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function getActionBadgeAttribute(): string
    {
        return match ($this->action) {
            'create' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Tambah</span>',
            'update' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Edit</span>',
            'delete' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Hapus</span>',
            'restore' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Restore</span>',
            'renewal' => '<span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Perpanjang</span>',
            'import' => '<span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">Import</span>',
            'upload' => '<span class="px-2 py-1 text-xs rounded-full bg-cyan-100 text-cyan-800">Upload</span>',
            default => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">' . $this->action . '</span>',
        };
    }
}
