@php
    $activeNav = 'wallet';
    $pageSubtitle = 'Payment Successful';
@endphp
@extends('layouts.inviteflow-portal')

@section('title', 'Funds Added')

@section('content')
    <div class="card mx-auto" style="max-width: 560px;">
        <div class="card-body text-center py-5">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <h1 class="h4">Funds added</h1>
            <p class="text-muted">
                <strong>Tsh {{ number_format($topup->amount_tsh) }}</strong> was added via {{ $topup->paymentMethodLabel() }}.
            </p>
            <p class="h5 text-primary mb-4">New balance: Tsh {{ number_format($walletBalance) }}</p>
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a href="{{ route('event.invitation.send') }}" class="btn btn-primary">Send invitations</a>
                <a href="{{ route('wallet.add-funds') }}" class="btn btn-outline-secondary">Add more funds</a>
            </div>
        </div>
    </div>
@endsection
