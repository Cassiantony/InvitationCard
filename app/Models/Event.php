<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'date', // The column in question
        'location',
        'organizer_name',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     * This ensures the 'date' column is always a Carbon object,
     * allowing methods like format() and isToday() to work.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Get the user that owns the event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the invitees for the event.
     */
    public function invitees()
    {
        return $this->hasMany(Invitee::class);
    }

    /**
     * Get the invitation designs for the event.
     */
    public function designs()
    {
        return $this->hasMany(InvitationDesign::class);
    }

    /**
     * Get the active design for the event.
     */
    public function activeDesign()
    {
        return $this->hasOne(InvitationDesign::class)->latest();
    }

    /**
     * Get the attendances for the event.
     */
    public function attendances()
    {
        return $this->hasManyThrough(EventAttendance::class, Invitee::class);
    }

    /**
     * Check if event is upcoming
     */
    public function getIsUpcomingAttribute()
    {
        // The $this->date is now a Carbon object
        return $this->date > now();
    }

    /**
     * Check if event is today
     */
    public function getIsTodayAttribute()
    {
        // The $this->date is now a Carbon object
        return $this->date->isToday();
    }

    /**
     * Check if event is past
     */
    public function getIsPastAttribute()
    {
        // The $this->date is now a Carbon object
        return $this->date < now();
    }

    /**
     * Format date accessor
     */
    public function getFormattedDateAttribute()
    {
        // The $this->date is now a Carbon object
        return $this->date->format('F j, Y g:i A');
    }

    /**
     * Get event status
     */
    public function getStatusAttribute()
    {
        if ($this->is_past) {
            return 'completed';
        } elseif ($this->is_today) {
            return 'ongoing';
        } else {
            return 'upcoming';
        }
    }
}
