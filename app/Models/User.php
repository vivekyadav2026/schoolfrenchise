<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'role',
        'centre_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'centre_id' => 'integer',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin') || $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin']) || in_array($this->role, ['super_admin', 'admin']);
    }

    public function isCentre(): bool
    {
        return $this->hasRole('centre') || $this->role === 'centre';
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff') || $this->role === 'staff';
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student') || $this->role === 'student';
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasRole(string $role): bool
    {
        if ($this->role === $role) {
            return true;
        }
        return $this->roles->contains('name', $role);
    }

    public function hasAnyRole(array $roles): bool
    {
        if (in_array($this->role, $roles)) {
            return true;
        }
        return $this->roles->whereIn('name', $roles)->isNotEmpty();
    }

    public function assignRole(string|Role $role): void
    {
        if (is_string($role)) {
            $roleModel = Role::where('name', $role)->first();
            if ($roleModel) {
                $this->roles()->syncWithoutDetaching([$roleModel->id]);
            }
        } elseif ($role instanceof Role) {
            $this->roles()->syncWithoutDetaching([$role->id]);
        }
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permission)) {
                return true;
            }
        }
        return false;
    }

    public function centre()
    {
        if ($this->centre_id) {
            return $this->belongsTo(Centre::class, 'centre_id');
        }
        return $this->hasOne(Centre::class, 'user_id');
    }

    public function assignedCentre()
    {
        return $this->belongsTo(Centre::class, 'centre_id');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function systemNotifications()
    {
        return $this->hasMany(SystemNotification::class, 'user_id')->latest();
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class, 'user_id')->latest();
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id')->latest();
    }
}
