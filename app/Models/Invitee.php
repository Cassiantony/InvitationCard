<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitee extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'name', 'email', 'phone', 'company', 'notes',
        'invitation_code', 'qr_code', 'status', 'invited_at',
    ];
    

    protected $casts = [
        'invited_at' => 'datetime',
        'responded_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function attendances()
    {
        return $this->hasMany(EventAttendance::class);
    }

    // Generate unique invitation code
    public static function generateInvitationCode()
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('invitation_code', $code)->exists());

        return $code;
    }

    // Check if invitee has checked in
    public function hasCheckedIn()
    {
        return !is_null($this->checked_in_at);
    }

    // Get QR code URL for this invitee
    public function getQrCodeUrl()
    {
        return route('invitees.verify', $this->invitation_code);
    }

    // Scope for pending invitees
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for confirmed invitees
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    // Scope for checked-in invitees
    public function scopeCheckedIn($query)
    {
        return $query->whereNotNull('checked_in_at');
    }

    
}