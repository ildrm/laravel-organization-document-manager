<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(FormVersion::class);
    }

    public function currentVersion()
    {
        return $this->hasOne(FormVersion::class)->where('is_current', true);
    }

    public function publishedVersions()
    {
        return $this->hasMany(FormVersion::class)->where('is_published', true);
    }

    public function latestPublishedVersion()
    {
        return $this->hasOne(FormVersion::class)
            ->where('is_published', true)
            ->orderByDesc('version')
            ->latest();
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
