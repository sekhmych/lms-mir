<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StepikCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'stepik_id',
        'title',
        'summary',
        'description',
        'cover_url',
        'course_url',
        'price',
        'display_price',
        'currency_code',
        'is_paid',
        'difficulty',
        'language',
        'workload',
        'categories',
        'categories_text',
        'learners_count',
        'is_active',
        'source_updated_at',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'categories' => 'array',
            'is_paid' => 'boolean',
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'source_updated_at' => 'datetime',
            'last_synced_at' => 'datetime',
        ];
    }
}