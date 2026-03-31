<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    protected $fillable = [
        'external_request_id',
        'approver_id',
        'status',
        'comment',
        'approved_at'
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(ExternalRequest::class, 'external_request_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
