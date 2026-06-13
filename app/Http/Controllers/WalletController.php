<?php

namespace App\Http\Controllers;

use App\Models\WalletTopup;
use App\Services\Wallet\StripeTopupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function create(Request $request): View
    {
        $user = $request->user();

        $recentTopups = WalletTopup::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('wallet.add-funds', [
            'walletBalance' => (int) $user->wallet_balance,
            'paymentMethods' => config('wallet.payment_methods', []),
            'presetAmounts' => config('wallet.preset_amounts_tsh', []),
            'minTopup' => (int) config('wallet.min_topup_tsh', 1000),
            'recentTopups' => $recentTopups,
            'stripeConfigured' => app(StripeTopupService::class)->isConfigured(),
            'demoInstantTopup' => config('wallet.demo_instant_topup') && app()->environment('local'),
            'returnUrl' => $request->query('return'),
        ]);
    }

    public function store(Request $request, StripeTopupService $stripe): RedirectResponse
    {
        $methods = array_keys(config('wallet.payment_methods', []));

        $validated = $request->validate([
            'amount_tsh' => ['required', 'integer', 'min:'.(int) config('wallet.min_topup_tsh', 1000)],
            'payment_method' => ['required', 'string', 'in:'.implode(',', $methods)],
        ]);

        $topup = WalletTopup::create([
            'user_id' => $request->user()->id,
            'amount_tsh' => $validated['amount_tsh'],
            'payment_method' => $validated['payment_method'],
            'status' => WalletTopup::STATUS_PENDING,
            'external_reference' => 'TOPUP-'.strtoupper(uniqid()),
        ]);

        if (config('wallet.demo_instant_topup') && app()->environment('local')) {
            $this->completeTopup($topup);

            return redirect()
                ->route('wallet.topup.success', $topup)
                ->with('success', 'Tsh '.number_format($topup->amount_tsh).' added to your wallet (demo mode).');
        }

        if ($stripe->isConfigured()) {
            try {
                $checkout = $stripe->createCheckoutSession($topup);
                if ($checkout) {
                    $topup->update(['stripe_session_id' => $checkout['session_id']]);

                    return redirect()->away($checkout['checkout_url']);
                }
            } catch (\Throwable $e) {
                $topup->update([
                    'status' => WalletTopup::STATUS_FAILED,
                    'failure_reason' => $e->getMessage(),
                ]);

                return back()->with('error', 'Could not start payment: '.$e->getMessage());
            }
        }

        return redirect()
            ->route('wallet.topup.pending', $topup)
            ->with('info', 'Top-up recorded. Complete payment when Stripe mobile-money integration is connected.');
    }

    public function pending(WalletTopup $topup): View|RedirectResponse
    {
        $this->authorizeTopup($topup);

        if ($topup->status === WalletTopup::STATUS_COMPLETED) {
            return redirect()->route('wallet.topup.success', $topup);
        }

        return view('wallet.topup-pending', [
            'topup' => $topup,
            'walletBalance' => (int) auth()->user()->wallet_balance,
        ]);
    }

    public function success(WalletTopup $topup): View|RedirectResponse
    {
        $this->authorizeTopup($topup);

        return view('wallet.topup-success', [
            'topup' => $topup,
            'walletBalance' => (int) auth()->user()->fresh()->wallet_balance,
        ]);
    }

    public function stripeWebhook(Request $request, StripeTopupService $stripe)
    {
        $stripe->handleWebhookPayload(
            $request->getContent(),
            $request->header('Stripe-Signature')
        );

        return response()->json(['received' => true]);
    }

    public function completeTopup(WalletTopup $topup): void
    {
        if ($topup->status === WalletTopup::STATUS_COMPLETED) {
            return;
        }

        DB::transaction(function () use ($topup) {
            $topup->refresh();

            if ($topup->status === WalletTopup::STATUS_COMPLETED) {
                return;
            }

            $topup->user()->lockForUpdate()->first();
            $topup->user->increment('wallet_balance', $topup->amount_tsh);

            $topup->update([
                'status' => WalletTopup::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);
        });
    }

    private function authorizeTopup(WalletTopup $topup): void
    {
        if ((int) $topup->user_id !== (int) auth()->id()) {
            abort(403);
        }
    }
}
