<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'direction_id',
        'type',
        'price',
        'duration',
        'external_link',
        'created_by',
    ];

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }
    public function sessions(): HasMany
    {
        return $this->hasMany(CourseSession::class);
    }
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
