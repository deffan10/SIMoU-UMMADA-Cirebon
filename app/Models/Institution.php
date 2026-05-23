<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Institution extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'type',
        'country',
        'city',
        'website',
        'email',
        'phone',
        'address',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($institution) {
            if (empty($institution->slug)) {
                $institution->slug = Str::slug($institution->name);
            }
        });
    }

    public function mous()
    {
        return $this->hasMany(Mou::class);
    }

    public function activeMous()
    {
        return $this->hasMany(Mou::class)->where('status', 'aktif');
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6366F1&color=fff&size=128';
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'universitas' => 'Universitas',
            'pemerintah' => 'Pemerintah',
            'industri' => 'Industri',
            'sekolah' => 'Sekolah',
            'ngo' => 'NGO',
            'organisasi' => 'Organisasi',
            default => 'Lainnya',
        };
    }
}
