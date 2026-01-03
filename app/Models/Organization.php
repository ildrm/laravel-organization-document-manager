<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'is_active',
        'email_verified_at',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'email_verified_at' => 'datetime',
            'settings' => 'array',
        ];
    }

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function admins()
    {
        return $this->hasMany(User::class)->where('is_org_admin', true);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function privateChats()
    {
        return $this->hasMany(PrivateChat::class);
    }
}
