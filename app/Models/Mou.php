<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Mou extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'mou_number',
        'title',
        'slug',
        'institution_id',
        'category_id',
        'level',
        'type',
        'cooperation_type',
        'faculty_id',
        'study_program',
        'pic_name',
        'pic_phone',
        'pic_email',
        'start_date',
        'end_date',
        'duration_months',
        'status',
        'visibility',
        'description',
        'public_summary',
        'main_document',
        'show_pdf_public',
        'allow_download',
        'renewal_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'show_pdf_public' => 'boolean',
        'allow_download' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($mou) {
            if (empty($mou->slug)) {
                $mou->slug = Str::slug($mou->title) . '-' . Str::random(5);
            }
        });
    }

    // Relationships
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function renewals()
    {
        return $this->hasMany(MouRenewal::class)->orderBy('renewal_number', 'desc');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function implementations()
    {
        return $this->hasMany(Implementation::class);
    }

    public function publicImplementations()
    {
        return $this->hasMany(Implementation::class)->where('visibility', 'public');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeExpiring($query)
    {
        return $query->where('status', 'akan_expire');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expire');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('mou_number', 'like', "%{$search}%")
                ->orWhereHas('institution', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        });
    }

    // Attributes
    public function getRemainingDaysAttribute(): int
    {
        return (int) now()->diffInDays($this->end_date, false);
    }

    public function getDurationTextAttribute(): string
    {
        $months = $this->start_date->diffInMonths($this->end_date);
        if ($months >= 12) {
            $years = floor($months / 12);
            $remainingMonths = $months % 12;
            return $years . ' tahun' . ($remainingMonths > 0 ? ' ' . $remainingMonths . ' bulan' : '');
        }
        return $months . ' bulan';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'aktif' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Aktif</span>',
            'akan_expire' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Akan Expire</span>',
            'expire' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Expire</span>',
            default => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Unknown</span>',
        };
    }

    public function getLevelBadgeAttribute(): string
    {
        return match ($this->level) {
            'lokal' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Lokal</span>',
            'nasional' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Nasional</span>',
            'internasional' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Internasional</span>',
            default => '',
        };
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'akademik' => 'Akademik',
            'penelitian' => 'Penelitian',
            'mbkm' => 'MBKM',
            'industri' => 'Industri',
            'pengabdian' => 'Pengabdian',
            'pemerintah' => 'Pemerintah',
            'internasional' => 'Internasional',
            default => 'Lainnya',
        };
    }

    // Methods
    public function updateStatus(): void
    {
        $daysRemaining = $this->remaining_days;

        if ($daysRemaining <= 0) {
            $this->status = 'expire';
        } elseif ($daysRemaining <= 90) {
            $this->status = 'akan_expire';
        } else {
            $this->status = 'aktif';
        }

        $this->saveQuietly();
    }

    public function getMainDocumentUrlAttribute(): ?string
    {
        return $this->main_document ? asset('storage/' . $this->main_document) : null;
    }
}
