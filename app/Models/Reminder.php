<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'organization_id',
        'field_key',
        'reminder_at',
        'sent_at',
        'is_sent',
        'email_to',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'reminder_at' => 'datetime',
            'sent_at' => 'datetime',
            'is_sent' => 'boolean',
        ];
    }

    // Relationships
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get all recipient emails for this reminder
     *
     * @return array<string>
     */
    public function getRecipientEmails(): array
    {
        $emails = array_filter(explode(',', $this->email_to ?? ''));

        if ($this->document?->creator) {
            $emails[] = $this->document->creator->email;
        }

        return array_unique(array_map('trim', $emails));
    }
}
