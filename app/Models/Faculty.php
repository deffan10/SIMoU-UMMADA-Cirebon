<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Faculty extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($faculty) {
            if (empty($faculty->slug)) {
                $faculty->slug = Str::slug($faculty->name);
            }
        });
    }

    public function studyPrograms()
    {
        return $this->hasMany(StudyProgram::class);
    }

    public function mous()
    {
        return $this->hasMany(Mou::class);
    }
}
