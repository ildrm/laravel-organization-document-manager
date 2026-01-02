<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'form_id',
        'form_version_id',
        'title',
        'created_by',
        'data',
        'files',
        'status',
        'notes',
        'reminders', // Temporary field for form handling
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'files' => 'array',
        ];
    }

    // Relationships
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function formVersion(): BelongsTo
    {
        return $this->belongsTo(FormVersion::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }
}
