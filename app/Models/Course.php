<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
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

}
