<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'form_version_id',
        'user_id',
        'data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function formVersion(): BelongsTo
    {
        return $this->belongsTo(FormVersion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
