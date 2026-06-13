<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationDelivery extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'invitee_id',
        'delivery_method',
        'is_resend',
        'fallback_method',
        'status',
        'cost_tsh',
        'recipient',
        'error_message',
        'api_response',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'cost_tsh' => 'integer',
        'is_resend' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function invitee(): BelongsTo
    {
        return $this->belongsTo(Invitee::class);
    }

    public function methodLabel(): string
    {
        if ($this->fallback_method === 'sms' && $this->delivery_method === 'whatsapp') {
            return 'WhatsApp → SMS';
        }

        return match ($this->delivery_method) {
            'whatsapp' => 'WhatsApp',
            'sms' => 'SMS',
            'email' => 'Email',
            default => ucfirst($this->delivery_method),
        };
    }
}
