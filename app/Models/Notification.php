<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'mou_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function mou()
    {
        return $this->belongsTo(Mou::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
