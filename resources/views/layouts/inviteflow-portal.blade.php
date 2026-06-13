<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'InviteFlow')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        :root {
            --primary: #4e73df;
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --sidebar-bg-start: #4e73df;
            --sidebar-bg-end: #224abe;
        }
        body { background-color: #f8f9fc; overflow-x: hidden; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg-start) 0%, var(--sidebar-bg-end) 100%);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed; left: -280px; width: 280px;
                transition: left 0.3s ease; z-index: 1050; overflow-y: auto;
            }
            .sidebar.show-sidebar { left: 0; }
            .overlay-blur {
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.4); z-index: 1040; display: none;
            }
            .overlay-blur.active { display: block; }
            .main-content-full { width: 100%; }
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 0.85rem 1rem; font-weight: 500;
            border-radius: 0.35rem; margin-bottom: 0.2rem; transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff; background-color: rgba(255, 255, 255, 0.15);
        }
        .sidebar .nav-link i { width: 1.8rem; text-align: center; }
        .sidebar .brand { border-bottom: 1px solid rgba(255,255,255,0.2); }
        .topbar {
            background: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.08);
            padding: 0.75rem 1rem;
            border-radius: 0 0 0.5rem 0.5rem;
            margin-bottom: 1.5rem;
        }
        .notification-badge {
            position: absolute; top: -6px; right: -8px;
            background: var(--danger-color); border-radius: 50%;
            font-size: 0.65rem; padding: 0.2rem 0.45rem; font-weight: bold; color: white;
        }
        .card {
            border: none; border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        .stat-card { border-left: 0.25rem solid var(--primary); }
        .stat-card.success { border-left-color: var(--secondary-color); }
        .stat-card.danger { border-left-color: var(--danger-color); }
        .stat-card.warning { border-left-color: var(--warning-color); }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        @stack('styles')
    </style>
</head>
<body>

@php
    $portalUser = auth()->user();
    $dashboardRoute = $portalUser->dashboardUrl();
    $activeNav = $activeNav ?? '';
    $pageSubtitle = $pageSubtitle ?? '';
    $walletBalance = (int) ($portalUser->wallet_balance ?? 0);
@endphp

<div class="overlay-blur" id="mobileOverlay"></div>

<div class="container-fluid px-0">
    <div class="row g-0">
        <div class="col-md-3 col-lg-2 sidebar" id="mainSidebar">
            <div class="position-sticky pt-4 px-3">
                <div class="text-center mb-4 brand pb-3 d-flex justify-content-between align-items-center d-md-none">
                    <h4 class="text-white mb-0"><i class="fas fa-envelope-open-text me-2"></i>InviteFlow</h4>
                    <button class="btn btn-sm btn-light rounded-circle" id="closeSidebarMobile"><i class="fas fa-times"></i></button>
                </div>
                <div class="text-center mb-4 brand pb-3 d-none d-md-block">
                    <i class="fas fa-envelope-open-text fa-2x text-white"></i>
                    <h4 class="text-white mt-2 mb-0">InviteFlow</h4>
                    <p class="text-white-50 small">Admin Portal</p>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ $activeNav === 'dashboard' ? 'active' : '' }}" href="{{ $dashboardRoute }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    @if($portalUser->canManageManagers())
                    <li class="nav-item">
                        <a class="nav-link {{ $activeNav === 'managers' ? 'active' : '' }}" href="{{ route('manage.managers') }}">
                            <i class="fas fa-users-cog"></i> Managers
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link {{ $activeNav === 'events' ? 'active' : '' }}" href="{{ route('event.index') }}">
                            <i class="fas fa-calendar-alt"></i> My Events
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeNav === 'send' ? 'active' : '' }}" href="{{ route('event.invitation.send') }}">
                            <i class="fas fa-paper-plane"></i> Send Invitations
                        </a>
                    </li>
                    <li class="nav-item">
                        @php
                            $deliveryReportHref = isset($deliveryReportEvent)
                                ? route('event.invitation.delivery-report', $deliveryReportEvent)
                                : route('event.invitation.send');
                        @endphp
                        <a class="nav-link {{ $activeNav === 'delivery' ? 'active' : '' }}" href="{{ $deliveryReportHref }}">
                            <i class="fas fa-chart-bar"></i> Delivery Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeNav === 'wallet' ? 'active' : '' }}" href="{{ route('wallet.add-funds') }}">
                            <i class="fas fa-wallet"></i> Add Funds
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeNav === 'design' ? 'active' : '' }}" href="{{ route('event.invitation.card-upload') }}">
                            <i class="fas fa-palette"></i> Design Card
                        </a>
                    </li>
                    <li class="nav-item mt-4">
                        <hr class="bg-light opacity-25">
                        <a class="nav-link" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('portal-logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                        <form id="portal-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </li>
                </ul>
                <div class="mt-4 d-none d-md-block text-center text-white-50 small">
                    <i class="fas fa-shield-alt"></i> v2.0 • secure
                </div>
            </div>
        </div>

        <div class="col-md-9 col-lg-10 ms-sm-auto px-3 px-md-4 main-content-full" id="mainContent">
            <div class="topbar d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link d-md-none text-dark me-2" id="mobileMenuToggle">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <div>
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-user-shield me-2 text-primary"></i>
                            Admin Control
                            @if($pageSubtitle)
                                <span class="text-muted fs-6 d-none d-sm-inline">| {{ $pageSubtitle }}</span>
                            @endif
                        </h5>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-2 mt-sm-0 align-items-center">
                    <a href="{{ route('wallet.add-funds') }}" class="btn btn-light btn-sm rounded-pill d-none d-md-inline-flex">
                        <i class="fas fa-wallet me-1"></i> Tsh {{ number_format($walletBalance) }}
                    </a>
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?background=4e73df&color=fff&name={{ urlencode($portalUser->name) }}" class="rounded-circle" width="38" height="38" alt="">
                        <span class="ms-2 d-none d-md-inline fw-semibold">{{ $portalUser->name }}</span>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('mainSidebar');
    const overlay = document.getElementById('mobileOverlay');
    const menuToggle = document.getElementById('mobileMenuToggle');
    const closeSidebarBtn = document.getElementById('closeSidebarMobile');
    function openSidebar() {
        if (window.innerWidth < 768) {
            sidebar.classList.add('show-sidebar');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    function closeSidebar() {
        sidebar.classList.remove('show-sidebar');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    if (menuToggle) menuToggle.addEventListener('click', openSidebar);
    if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
    window.addEventListener('resize', () => { if (window.innerWidth >= 768) closeSidebar(); });
</script>
@stack('scripts')
</body>
</html>
