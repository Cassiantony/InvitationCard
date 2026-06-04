<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_OWNER = 'owner';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_MANAGER = 'manager';

    public const ROLE_VIEWER = 'viewer';

    /** @var list<string> */
    public const ROLES = [
        self::ROLE_OWNER,
        self::ROLE_ADMIN,
        self::ROLE_MANAGER,
        self::ROLE_VIEWER,
    ];

    /** Roles the owner may create from the manage-users UI (no second owner via form). */
    public const ROLES_CREATABLE_BY_OWNER = [
        self::ROLE_ADMIN,
        self::ROLE_MANAGER,
        self::ROLE_VIEWER,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'role',
        'password',
        'viewer_for_user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function normalizedRole(): string
    {
        $slug = strtolower(str_replace(' ', '', (string) $this->role));

        return match ($slug) {
            'superadmin', 'superadministrator' => self::ROLE_OWNER,
            default => $slug,
        };
    }

    public function roleLabel(): string
    {
        return match ($this->normalizedRole()) {
            self::ROLE_OWNER => 'Owner',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_VIEWER => 'Viewer',
            default => ucfirst((string) $this->role),
        };
    }

    public function dashboardUrl(): string
    {
        return match ($this->normalizedRole()) {
            self::ROLE_OWNER => route('dashboard'),
            self::ROLE_ADMIN => route('admin.dashboard'),
            self::ROLE_MANAGER => route('manager.dashboard'),
            self::ROLE_VIEWER => route('viewer.dashboard'),
            default => route('viewer.dashboard'),
        };
    }

    public function isOwner(): bool
    {
        return $this->normalizedRole() === self::ROLE_OWNER;
    }

    public function isAdmin(): bool
    {
        return $this->normalizedRole() === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->normalizedRole() === self::ROLE_MANAGER;
    }

    public function isViewer(): bool
    {
        return $this->normalizedRole() === self::ROLE_VIEWER;
    }

    public function viewerOrganizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewer_for_user_id');
    }

    /**
     * @return HasMany<User, User>
     */
    public function assignedViewers(): HasMany
    {
        return $this->hasMany(User::class, 'viewer_for_user_id');
    }

    /**
     * Events this viewer may scan invitations for.
     */
    public function viewerEvents()
    {
        return $this->belongsToMany(Event::class, 'event_viewer')
            ->withTimestamps();
    }

    /**
     * Whether this user may look up / check in this invitee (QR scan at the door).
     */
    public function canAccessInviteeForScan(Invitee $invitee): bool
    {
        $event = $invitee->event;
        if (! $event) {
            return false;
        }

        if ($this->isViewer()) {
            if ($this->viewer_for_user_id === null
                || (int) $event->user_id !== (int) $this->viewer_for_user_id) {
                return false;
            }

            $assignedEvents = $this->viewerEvents();

            if ($assignedEvents->exists()) {
                return $assignedEvents->where('events.id', $event->id)->exists();
            }

            return true;
        }

        if ($this->isOwner() || $this->isAdmin()) {
            return true;
        }

        return (int) $event->user_id === (int) $this->id;
    }

    public function isSuperAdmin(): bool
    {
        return $this->isOwner();
    }

    public function canManageManagers(): bool
    {
        return $this->isOwner() || $this->isAdmin();
    }

    public function canAccessAdminArea(): bool
    {
        return $this->isOwner() || $this->isAdmin();
    }

    public function canBeDeletedBy(self $actor): bool
    {
        if ($this->id === $actor->id) {
            return false;
        }

        if ($this->isOwner()) {
            return false;
        }

        if ($actor->isOwner()) {
            return true;
        }

        if ($actor->isAdmin()) {
            return $this->isManager() || $this->isViewer();
        }

        if ($actor->isManager()) {
            return $this->isViewer()
                && $this->viewer_for_user_id !== null
                && (int) $this->viewer_for_user_id === (int) $actor->id;
        }

        return false;
    }
}
