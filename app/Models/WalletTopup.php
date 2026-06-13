<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTopup extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'amount_tsh',
        'payment_method',
        'status',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'external_reference',
        'failure_reason',
        'completed_at',
    ];

    protected $casts = [
        'amount_tsh' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethodLabel(): string
    {
        return config("wallet.payment_methods.{$this->payment_method}.label")
            ?? ucfirst(str_replace('_', ' ', $this->payment_method));
    }
}
