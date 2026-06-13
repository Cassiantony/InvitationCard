@php
    $activeNav = 'wallet';
    $pageSubtitle = 'Add Funds';
@endphp
@extends('layouts.inviteflow-portal')

@section('title', 'Add Funds — InviteFlow')

@section('content')
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <strong><i class="fas fa-wallet me-2 text-primary"></i>Top up wallet</strong>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Current balance: <strong class="text-primary">Tsh {{ number_format($walletBalance) }}</strong>
                    </p>

                    @if($demoInstantTopup)
                        <div class="alert alert-warning small">
                            <i class="fas fa-flask me-1"></i>
                            <strong>Local demo mode:</strong> funds are credited instantly without real payment.
                            Set <code>WALLET_DEMO_INSTANT_TOPUP=false</code> when Stripe is connected.
                        </div>
                    @elseif(!$stripeConfigured)
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-1"></i>
                            Stripe is not configured yet. Add <code>STRIPE_KEY</code> and <code>STRIPE_SECRET</code> to
                            <code>.env</code>, then install <code>stripe/stripe-php</code> to enable Mixx, Airtel Money, M-Pesa, Halopesa, and card payments.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('wallet.add-funds.store') }}">
                        @csrf
                        @if($returnUrl)
                            <input type="hidden" name="return" value="{{ $returnUrl }}">
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Amount (Tsh)</label>
                            <input type="number" name="amount_tsh" id="amount_tsh" class="form-control form-control-lg @error('amount_tsh') is-invalid @enderror"
                                   min="{{ $minTopup }}" step="500" value="{{ old('amount_tsh', 10000) }}" required>
                            @error('amount_tsh')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Minimum Tsh {{ number_format($minTopup) }}</div>
                        </div>

                        <div class="mb-3 d-flex flex-wrap gap-2">
                            @foreach($presetAmounts as $preset)
                                <button type="button" class="btn btn-outline-primary btn-sm preset-amount" data-amount="{{ $preset }}">
                                    Tsh {{ number_format($preset) }}
                                </button>
                            @endforeach
                        </div>

                        <label class="form-label">Payment method</label>
                        <div class="row g-2 mb-4">
                            @foreach($paymentMethods as $key => $method)
                                <div class="col-md-6">
                                    <label class="payment-option card h-100 p-3 mb-0 {{ old('payment_method', 'mpesa') === $key ? 'border-primary' : '' }}">
                                        <input type="radio" name="payment_method" value="{{ $key }}" class="form-check-input me-2"
                                               {{ old('payment_method', 'mpesa') === $key ? 'checked' : '' }} required>
                                        <i class="fas {{ $method['icon'] }} me-2 text-primary"></i>
                                        <strong>{{ $method['label'] }}</strong>
                                        <div class="small text-muted mt-1">via Stripe</div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('payment_method')<div class="text-danger small mb-3">{{ $message }}</div>@enderror

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-plus-circle me-2"></i> Continue to payment
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <strong>Recent top-ups</strong>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recentTopups as $topup)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span>Tsh {{ number_format($topup->amount_tsh) }}</span>
                                <span class="badge {{ $topup->status === 'completed' ? 'bg-success' : ($topup->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ ucfirst($topup->status) }}
                                </span>
                            </div>
                            <div class="small text-muted">{{ $topup->paymentMethodLabel() }} · {{ $topup->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="list-group-item text-muted small">No top-ups yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .payment-option { cursor: pointer; transition: box-shadow .2s; }
    .payment-option:has(input:checked) { border-color: var(--primary) !important; box-shadow: 0 0 0 2px rgba(78,115,223,.25); }
    .payment-option input { vertical-align: middle; }
</style>
@endpush

@push('scripts')
<script>
    document.querySelectorAll('.preset-amount').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('amount_tsh').value = btn.dataset.amount;
        });
    });
</script>
@endpush
