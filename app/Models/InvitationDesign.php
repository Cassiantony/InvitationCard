<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationDesign extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'design_type',
        'template_name',
        'custom_image_path',
        'qr_position',
        'qr_size',
        'qr_color',
        'qr_background_color',
        'is_template',
        'apply_to_all',
        'user_id',
    ];

    protected $casts = [
        'qr_position' => 'array',
        'is_template' => 'boolean',
        'apply_to_all' => 'boolean',
    ];

    /**
     * Get the event that owns the design.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user that owns the design.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get custom image URL
     */
    public function getCustomImageUrlAttribute()
    {
        return $this->custom_image_path ? asset('storage/' . $this->custom_image_path) : null;
    }
}