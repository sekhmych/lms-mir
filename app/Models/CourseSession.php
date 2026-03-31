<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['course_id', 'trainer_id', 'start_date', 'end_date', 'status'])]
class CourseSession extends Model
{
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

}
