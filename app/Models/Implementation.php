<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Implementation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'mou_id',
        'title',
        'description',
        'file_path',
        'original_filename',
        'file_size',
        'visibility',
        'uploaded_by',
    ];

    public function mou()
    {
        return $this->belongsTo(Mou::class);
    }

    public function uploader()
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) return '-';
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        return number_format($bytes / 1024, 2) . ' KB';
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }
}
