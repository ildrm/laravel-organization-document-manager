<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormVersion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'form_id',
        'version',
        'schema',
        'is_published',
        'is_current',
        'created_by',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'schema' => 'array',
            'is_published' => 'boolean',
            'is_current' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    // Relationships
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
