@php
    $portalUser = auth()->user();
    $dashboardRoute = $portalUser->dashboardUrl();
@endphp
<div class="col-md-3 col-lg-2 sidebar" id="mainSidebar">
    <div class="position-sticky pt-4 px-3">
        <div class="text-center mb-4 brand pb-3 d-flex justify-content-between align-items-center d-md-none">
            <h4 class="text-white mb-0"><i class="fas fa-envelope-open-text me-2"></i>InviteFlow</h4>
            <button class="btn btn-sm btn-light rounded-circle" id="closeSidebarMobile" type="button"><i class="fas fa-times"></i></button>
        </div>
        <div class="text-center mb-4 brand pb-3 d-none d-md-block">
            <i class="fas fa-envelope-open-text fa-2x text-white"></i>
            <h4 class="text-white mt-2 mb-0">InviteFlow</h4>
            <p class="text-white-50 small">Admin Portal</p>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ ($activeNav ?? '') === 'dashboard' ? 'active' : '' }}" href="{{ $dashboardRoute }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            @if($portalUser->canManageManagers())
            <li class="nav-item">
                <a class="nav-link {{ ($activeNav ?? '') === 'managers' ? 'active' : '' }}" href="{{ route('manage.managers') }}">
                    <i class="fas fa-users-cog"></i> Managers
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ ($activeNav ?? '') === 'create' ? 'active' : '' }}" href="{{ route('event.create') }}">
                    <i class="fas fa-calendar-plus"></i> Create Event
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ in_array($activeNav ?? '', ['events', 'event']) ? 'active' : '' }}" href="{{ route('event.index') }}">
                    <i class="fas fa-calendar-alt"></i> My Events
                </a>
            </li>
            @if(isset($sidebarEvent))
            <li class="nav-item">
                <a class="nav-link {{ ($activeNav ?? '') === 'invitees' ? 'active' : '' }}" href="{{ route('invitee.create', $sidebarEvent->id) }}">
                    <i class="fas fa-user-plus"></i> Add Invitees
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ ($activeNav ?? '') === 'design' ? 'active' : '' }}" href="{{ route('event.invitation.card-upload', isset($sidebarEvent) ? ['event_id' => $sidebarEvent->id] : []) }}">
                    <i class="fas fa-palette"></i> Design Card
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($activeNav ?? '') === 'send' ? 'active' : '' }}" href="{{ route('event.invitation.send', isset($sidebarEvent) ? ['event_id' => $sidebarEvent->id] : []) }}">
                    <i class="fas fa-paper-plane"></i> Send Invitations
                </a>
            </li>
            <li class="nav-item">
                @php
                    $deliveryHref = isset($sidebarEvent)
                        ? route('event.invitation.delivery-report', $sidebarEvent)
                        : route('event.invitation.send');
                @endphp
                <a class="nav-link {{ ($activeNav ?? '') === 'delivery' ? 'active' : '' }}" href="{{ $deliveryHref }}">
                    <i class="fas fa-chart-bar"></i> Delivery Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($activeNav ?? '') === 'wallet' ? 'active' : '' }}" href="{{ route('wallet.add-funds') }}">
                    <i class="fas fa-wallet"></i> Add Funds
                </a>
            </li>
            @if(isset($sidebarEvent))
            <li class="nav-item">
                <a class="nav-link {{ ($activeNav ?? '') === 'verify' ? 'active' : '' }}" href="{{ route('event.verify', $sidebarEvent) }}">
                    <i class="fas fa-qrcode"></i> Verify Guests
                </a>
            </li>
            @endif
            <li class="nav-item mt-4">
                <hr class="bg-light opacity-25">
                <a class="nav-link" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
        </ul>
        <div class="mt-4 d-none d-md-block text-center text-white-50 small">
            <i class="fas fa-shield-alt"></i> v2.0 • secure
        </div>
    </div>
</div>
