<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MouRenewal extends Model
{
    protected $fillable = [
        'mou_id',
        'renewal_number',
        'old_start_date',
        'old_end_date',
        'new_start_date',
        'new_end_date',
        'duration_months',
        'renewal_note',
        'old_file',
        'new_file',
        'renewed_by',
    ];

    protected $casts = [
        'old_start_date' => 'date',
        'old_end_date' => 'date',
        'new_start_date' => 'date',
        'new_end_date' => 'date',
    ];

    public function mou()
    {
        return $this->belongsTo(Mou::class);
    }

    public function renewedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'renewed_by');
    }
}
