@php
    $activeNav = 'wallet';
    $pageSubtitle = 'Payment Pending';
@endphp
@extends('layouts.inviteflow-portal')

@section('title', 'Payment Pending')

@section('content')
    <div class="card mx-auto" style="max-width: 560px;">
        <div class="card-body text-center py-5">
            <i class="fas fa-hourglass-half fa-3x text-warning mb-3"></i>
            <h1 class="h4">Payment pending</h1>
            <p class="text-muted">
                Top-up of <strong>Tsh {{ number_format($topup->amount_tsh) }}</strong> via
                <strong>{{ $topup->paymentMethodLabel() }}</strong> is awaiting payment.
            </p>
            <p class="small text-muted">Reference: {{ $topup->external_reference }}</p>
            <p class="small">
                When Stripe is connected, you will be redirected to complete payment with your mobile money or card.
            </p>
            <a href="{{ route('wallet.add-funds') }}" class="btn btn-primary mt-3">Back to Add Funds</a>
        </div>
    </div>
@endsection
