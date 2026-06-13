<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, shrink-to-fit=no">
    <title>Admin Dashboard - Invitation System</title>
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
        body {
            background-color: #f8f9fc;
            overflow-x: hidden;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg-start) 0%, var(--sidebar-bg-end) 100%);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                left: -280px;
                width: 280px;
                transition: left 0.3s ease;
                z-index: 1050;
                overflow-y: auto;
            }
            .sidebar.show-sidebar { left: 0; }
            .overlay-blur {
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background: rgba(0,0,0,0.4);
                z-index: 1040;
                display: none;
            }
            .overlay-blur.active { display: block; }
            .main-content-full { width: 100%; }
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 0.85rem 1rem;
            font-weight: 500;
            border-radius: 0.35rem;
            margin-bottom: 0.2rem;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.15);
        }
        .sidebar .nav-link i {
            width: 1.8rem;
            text-align: center;
        }
        .sidebar .brand {
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .topbar {
            background: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.08);
            padding: 0.75rem 1rem;
            border-radius: 0 0 0.5rem 0.5rem;
            margin-bottom: 1.5rem;
        }
        .notification-badge {
            position: absolute;
            top: -6px;
            right: -8px;
            background: var(--danger-color);
            border-radius: 50%;
            font-size: 0.65rem;
            padding: 0.2rem 0.45rem;
            font-weight: bold;
            color: white;
        }
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 700;
        }
        .stat-card {
            border-left: 0.25rem solid var(--primary);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08);
        }
        .stat-card.event { border-left-color: var(--secondary-color); }
        .stat-card.invitee { border-left-color: var(--warning-color); }
        .stat-card.rsvp { border-left-color: var(--danger-color); }
        .bg-gradient-primary {
            background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .event-card { transition: all 0.3s; }
        .event-card:hover { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); }
        .progress { height: 10px; }
        .text-xs { font-size: 0.75rem; }
        .text-gray-300 { color: #dddfeb; }
        .text-gray-800 { color: #5a5c69; }
    </style>
</head>
<body>

<div class="overlay-blur" id="mobileOverlay"></div>

<div class="container-fluid px-0">
    <div class="row g-0">
        <!-- Sidebar -->
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
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('manage.managers') ? 'active' : '' }}" href="{{ route('manage.managers') }}">
                            <i class="fas fa-users-cog"></i>
                            Managers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('event.create') ? 'active' : '' }}" href="{{ route('event.create') }}">
                            <i class="fas fa-calendar-plus"></i>
                            Create Event
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('event.index') ? 'active' : '' }}" href="{{ route('event.index') }}">
                            <i class="fas fa-calendar-alt"></i>
                            My Events
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('invitee.create', 'event.show') ? 'active' : '' }}" href="{{ route('event.index') }}">
                            <i class="fas fa-user-plus"></i>
                            Add Invitees
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('design.*') ? 'active' : '' }}" href="{{ route('design.create') }}">
                            <i class="fas fa-palette"></i>
                            Design Card
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('send.*') ? 'active' : '' }}" href="{{ route('event.invitation.send') }}">
                            <i class="fas fa-paper-plane"></i>
                            Send Invitations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}" href="#">
                            <i class="fas fa-cog"></i>
                            Settings
                        </a>
                    </li>
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

        <!-- Main content -->
        <div class="col-md-9 col-lg-10 ms-sm-auto px-3 px-md-4 main-content-full" id="mainContent">
            <div class="topbar d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link d-md-none text-dark me-2" id="mobileMenuToggle">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <div>
                        <h5 class="mb-0 fw-semibold"><i class="fas fa-user-shield me-2 text-primary"></i> Admin Control <span class="text-muted fs-6 d-none d-sm-inline">| Dashboard</span></h5>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-2 mt-sm-0">
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill dropdown-toggle px-3" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i> <span class="position-relative">🔔<span class="notification-badge">3</span></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-check-circle text-success"></i> New manager assigned</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-week"></i> Event reminder</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">View all</a></li>
                        </ul>
                    </div>
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?background=4e73df&color=fff&name={{ urlencode(Auth::user()->name) }}" class="rounded-circle" width="38" height="38" alt="admin">
                        <span class="ms-2 d-none d-md-inline fw-semibold">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Stats -->
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Events</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($totalEvents) }}</div>
                                    <div class="mt-2 text-muted text-xs">
                                        @if($newEventsSinceLastMonth > 0)
                                            <span class="text-success"><i class="fas fa-arrow-up"></i> {{ $newEventsSinceLastMonth }} new</span>
                                        @else
                                            <span><i class="fas fa-minus"></i> 0 new</span>
                                        @endif
                                        <span> since last month</span>
                                    </div>
                                </div>
                                <div class="col-auto"><i class="fas fa-calendar-alt fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card event h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs fw-bold text-success text-uppercase mb-1">Active Events</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($activeEvents) }}</div>
                                    <div class="mt-2 text-muted text-xs">
                                        @if($newActiveEventsSinceLastWeek > 0)
                                            <span class="text-success"><i class="fas fa-arrow-up"></i> {{ $newActiveEventsSinceLastWeek }} new</span>
                                        @else
                                            <span><i class="fas fa-minus"></i> 0 new</span>
                                        @endif
                                        <span> since last week</span>
                                    </div>
                                </div>
                                <div class="col-auto"><i class="fas fa-calendar-check fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card invitee h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs fw-bold text-warning text-uppercase mb-1">Total Managers</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($totalManagers) }}</div>
                                    <div class="mt-2 text-muted text-xs">
                                        @if($newManagersSinceLastWeek > 0)
                                            <span class="text-success"><i class="fas fa-arrow-up"></i> {{ $newManagersSinceLastWeek }} new</span>
                                        @else
                                            <span><i class="fas fa-minus"></i> 0 new</span>
                                        @endif
                                        <span> since last week</span>
                                    </div>
                                </div>
                                <div class="col-auto"><i class="fas fa-user-friends fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card rsvp h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs fw-bold text-danger text-uppercase mb-1">Total Viewers</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($totalViewers) }}</div>
                                    <div class="mt-2 text-muted text-xs">
                                        @if($newViewersSinceLastWeek > 0)
                                            <span class="text-success"><i class="fas fa-arrow-up"></i> {{ $newViewersSinceLastWeek }} new</span>
                                        @else
                                            <span><i class="fas fa-minus"></i> 0 new</span>
                                        @endif
                                        <span> since last week</span>
                                    </div>
                                </div>
                                <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recent Events -->
                <div class="col-lg-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 fw-bold text-primary">Recent Events</h6>
                            <a href="{{ route('event.index') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @forelse($recentEvents as $item)
                                    @php
                                        $event = $item['event'];
                                        $eventDate = $event->date ? \Illuminate\Support\Carbon::parse($event->date) : null;
                                    @endphp
                                    <div class="col-md-6 mb-4">
                                        <div class="card event-card h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start gap-2">
                                                    <h5 class="card-title mb-0">{{ $event->title }}</h5>
                                                    <span class="badge {{ $item['status_class'] }}">{{ $item['status_label'] }}</span>
                                                </div>
                                                @if($event->category)
                                                    <span class="badge bg-light text-dark border mt-2">{{ ucfirst($event->category) }}</span>
                                                @endif
                                                <p class="card-text mt-2 mb-1">
                                                    <i class="fas fa-calendar-day text-primary me-2"></i>
                                                    {{ $eventDate ? $eventDate->format('d M, Y | g:i A') : 'Date not set' }}
                                                </p>
                                                <p class="card-text mb-1">
                                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>{{ $event->location ?: '—' }}
                                                </p>
                                                <p class="card-text mb-0 small text-muted">
                                                    <i class="fas fa-user text-primary me-2"></i>
                                                    {{ $event->user?->name ?? $event->organizer_name ?? '—' }}
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <div>
                                                        <span class="text-muted small">Invitees: {{ $event->invitees_count }}</span>
                                                        @if($item['is_completed'])
                                                            <span class="ms-2 text-muted small">Confirmed: {{ $item['confirmed_count'] }}</span>
                                                        @else
                                                            <span class="ms-2 text-muted small">Viewers: {{ $item['viewers_count'] }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="btn-group">
                                                        <a href="{{ route('event.show', $event->id) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center text-muted py-4">
                                        <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                        <p class="mb-0">No events yet. <a href="{{ route('event.create') }}">Create your first event</a>.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RSVP & Activity -->
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 fw-bold text-primary">RSVP Status</h6>
                        </div>
                        <div class="card-body">
                            @forelse($rsvpEvents as $rsvp)
                                <div class="mb-4">
                                    <h6 class="small fw-bold">{{ $rsvp['title'] }} <span class="float-end">{{ $rsvp['percent'] }}%</span></h6>
                                    <div class="progress">
                                        <div class="progress-bar {{ $rsvp['bar_class'] }}" style="width: {{ $rsvp['percent'] }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">No upcoming events with invitees yet.</p>
                            @endforelse

                            <hr>

                            <h6 class="fw-bold mt-4">Recent Activity</h6>
                            <div class="mt-3">
                                @forelse($recentActivity as $activity)
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="fas {{ $activity['icon'] }} {{ $activity['icon_class'] }}"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="mb-0 small">{{ $activity['message'] }}</p>
                                            <small class="text-muted">{{ $activity['time'] }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted small mb-0">No recent invitee activity.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header py-3">
                            <h6 class="m-0 fw-bold text-primary">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <div class="card bg-gradient-primary text-white h-100">
                                        <div class="card-body">
                                            <i class="fas fa-calendar-plus fa-2x mb-3"></i>
                                            <h5>Create Event</h5>
                                            <p class="small">Set up a new event</p>
                                            <a href="{{ route('event.create') }}" class="btn btn-light btn-sm">Create</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <div class="card bg-gradient-primary text-white h-100">
                                        <div class="card-body">
                                            <i class="fas fa-users-cog fa-2x mb-3"></i>
                                            <h5>Manage Managers</h5>
                                            <p class="small">Add or remove managers</p>
                                            <a href="{{ route('manage.managers') }}" class="btn btn-light btn-sm">Manage</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <div class="card bg-gradient-primary text-white h-100">
                                        <div class="card-body">
                                            <i class="fas fa-palette fa-2x mb-3"></i>
                                            <h5>Design Card</h5>
                                            <p class="small">Upload invitation PDF</p>
                                            <a href="{{ route('event.invitation.card-upload') }}" class="btn btn-light btn-sm">Design</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-gradient-primary text-white h-100">
                                        <div class="card-body">
                                            <i class="fas fa-paper-plane fa-2x mb-3"></i>
                                            <h5>Send Invitations</h5>
                                            <p class="small">Deliver cards to invitees</p>
                                            <a href="{{ route('event.invitation.send') }}" class="btn btn-light btn-sm">Send</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 768) closeSidebar();
    });
</script>
</body>
</html>
