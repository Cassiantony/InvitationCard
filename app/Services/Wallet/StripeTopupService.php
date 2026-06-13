<?php

namespace App\Services\Wallet;

use App\Models\WalletTopup;
use RuntimeException;

/**
 * Stripe Checkout / PaymentIntent integration for wallet top-ups.
 * Wire STRIPE_KEY and STRIPE_SECRET in .env when ready.
 */
class StripeTopupService
{
    public function isConfigured(): bool
    {
        return ! empty(config('services.stripe.secret'));
    }

    /**
     * @return array{checkout_url: string, session_id: string}|null
     */
    public function createCheckoutSession(WalletTopup $topup): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        // Placeholder for Stripe SDK integration:
        // \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        // $session = \Stripe\Checkout\Session::create([...]);
        throw new RuntimeException(
            'Stripe SDK not installed yet. Run: composer require stripe/stripe-php, then implement checkout in StripeTopupService.'
        );
    }

    public function handleWebhookPayload(string $payload, ?string $signature): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Stripe is not configured.');
        }

        // Verify webhook signature and complete top-up on checkout.session.completed
    }
}
