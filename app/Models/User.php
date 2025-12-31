<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'organization_id',
        'is_general_manager',
        'is_org_admin',
        'language_preference',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_general_manager' => 'boolean',
            'is_org_admin' => 'boolean',
        ];
    }

    // Relationships
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'created_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // Helper methods
    public function isGeneralManager(): bool
    {
        return $this->is_general_manager === true;
    }

    public function isOrgAdmin(): bool
    {
        return $this->is_org_admin === true || $this->isGeneralManager();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isGeneralManager()) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->exists();
    }

    public function hasRole(string $roleSlug): bool
    {
        if ($this->isGeneralManager()) {
            return true;
        }

        return $this->roles()
            ->where('slug', $roleSlug)
            ->exists();
    }
}
