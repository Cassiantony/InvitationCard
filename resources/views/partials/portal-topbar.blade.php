@php
    $topbarUser = auth()->user();
    $topbarTitle = $topbarTitle ?? 'Admin Control';
    $topbarSubtitle = $topbarSubtitle ?? '';
@endphp
<div class="topbar d-flex flex-wrap justify-content-between align-items-center mb-4 sticky-top">
    <div class="d-flex align-items-center flex-grow-1 min-w-0">
        <button class="btn btn-link d-md-none text-dark me-2 flex-shrink-0" type="button" id="mobileMenuToggle">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <div class="min-w-0">
            @if(!empty($topbarBackUrl))
                <a href="{{ $topbarBackUrl }}" class="btn btn-outline-secondary btn-sm mb-1">
                    <i class="fas fa-arrow-left me-1"></i> {{ $topbarBackLabel ?? 'Back' }}
                </a>
            @endif
            <h5 class="mb-0 fw-semibold text-truncate">
                <i class="fas fa-user-shield me-2 text-primary"></i>
                {{ $topbarTitle }}
                @if($topbarSubtitle)
                    <span class="text-muted fs-6 d-none d-sm-inline">| {{ $topbarSubtitle }}</span>
                @endif
            </h5>
        </div>
    </div>
    <div class="d-flex gap-2 mt-2 mt-sm-0 align-items-center flex-shrink-0">
        @if(isset($topbarActions))
            {!! $topbarActions !!}
        @endif
        <a href="{{ route('wallet.add-funds') }}" class="btn btn-light btn-sm rounded-pill d-none d-md-inline-flex">
            <i class="fas fa-wallet me-1"></i> Tsh {{ number_format((int) $topbarUser->wallet_balance) }}
        </a>
        <div class="d-flex align-items-center">
            <img src="https://ui-avatars.com/api/?background=4e73df&color=fff&name={{ urlencode($topbarUser->name) }}" class="rounded-circle" width="38" height="38" alt="">
            <span class="ms-2 d-none d-md-inline fw-semibold">{{ $topbarUser->name }}</span>
        </div>
    </div>
</div>
