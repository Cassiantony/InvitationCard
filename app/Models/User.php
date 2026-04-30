<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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

    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

     // Add the missing canBeDeletedBy method
     public function canBeDeletedBy($admin)
     {
         // Cannot delete own account
         if ($this->id === $admin->id) {
             return false;
         }
         
         // Only super admin can delete another super admin
         if ($this->isSuperAdmin() && !$admin->isSuperAdmin()) {
             return false;
         }
         
         // Admin can delete regular users but not super admins or other admins
         if ($admin->isAdmin() && !$this->isUser()) {
             return false;
         }
         
         return true;
     }
    
}
