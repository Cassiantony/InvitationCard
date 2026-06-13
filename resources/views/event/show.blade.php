<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $event->title }} - Invitation System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --dark-color: #5a5c69;
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
            padding: 0.85rem 1rem;
            font-weight: 500;
            border-radius: 0.35rem;
            margin-bottom: 0.2rem;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.15);
        }
        
        .sidebar .nav-link i {
            width: 1.8rem;
            text-align: center;
        }
        .sidebar .brand { border-bottom: 1px solid rgba(255,255,255,0.2); }
        
        .topbar {
            background-color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            height: 4.375rem;
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
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-success {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .event-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 0.35rem 0.35rem;
        }
        
        .event-category-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .category-corporate {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .category-wedding {
            background-color: rgba(220, 53, 69, 0.2);
            color: white;
        }
        
        .category-birthday {
            background-color: rgba(255, 193, 7, 0.2);
            color: white;
        }
        
        .category-conference {
            background-color: rgba(40, 167, 69, 0.2);
            color: white;
        }
        
        .category-seminar {
            background-color: rgba(108, 117, 125, 0.2);
            color: white;
        }
        
        .category-other {
            background-color: rgba(111, 66, 193, 0.2);
            color: white;
        }
        
        .event-status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
            margin-left: 0.5rem;
        }
        
        .status-upcoming {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .status-ongoing {
            background-color: var(--warning-color);
            color: white;
        }
        
        .status-completed {
            background-color: var(--dark-color);
            color: white;
        }
        
        .info-card {
            text-align: center;
            padding: 1.5rem;
            transition: transform 0.3s;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
        }
        
        .info-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .info-number {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .info-label {
            font-size: 0.875rem;
            color: var(--dark-color);
        }
        
        .detail-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .detail-icon {
            font-size: 1.25rem;
            color: var(--primary-color);
            margin-right: 1rem;
            min-width: 30px;
        }
        
        .detail-content h5 {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .action-buttons .btn {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .description-box {
            background-color: #f8f9fc;
            border-left: 4px solid var(--primary-color);
            padding: 1.5rem;
            border-radius: 0.25rem;
        }
        
        .countdown-timer {
            background: linear-gradient(135deg, var(--warning-color) 0%, #f8c120 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            text-align: center;
        }
        
        .countdown-number {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .countdown-label {
            font-size: 0.875rem;
            text-transform: uppercase;
        }
        
        .guest-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .guest-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border: 1px solid #e3e6f0;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
            background-color: #fff;
        }
        
        .guest-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 1rem;
        }
        
        .guest-info {
            flex: 1;
        }
        
        .guest-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .guest-email {
            font-size: 0.875rem;
            color: var(--dark-color);
        }
        
        .guest-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-attending {
            background-color: rgba(28, 200, 138, 0.1);
            color: var(--secondary-color);
        }
        
        .status-pending {
            background-color: rgba(246, 194, 62, 0.1);
            color: var(--warning-color);
        }
        
        .status-declined {
            background-color: rgba(231, 74, 59, 0.1);
            color: var(--danger-color);
        }

        .stat-card { border-left: 0.25rem solid var(--primary-color); }
        .stat-card.success { border-left-color: var(--secondary-color); }
        .stat-card.danger { border-left-color: var(--danger-color); }
        .stat-card.warning { border-left-color: var(--warning-color); }

        @media (max-width: 767.98px) {
            .event-header { padding: 2rem 0; }
            .event-header .display-5 { font-size: 1.75rem; }
            .topbar .action-buttons .btn-label { display: none; }
            .topbar .action-buttons .btn { padding: 0.375rem 0.65rem; }
        }
        @media (max-width: 575.98px) {
            .topbar-inner { flex-direction: column; align-items: stretch !important; gap: 0.75rem; }
            .topbar-inner .action-buttons { display: flex; flex-wrap: wrap; gap: 0.5rem; }
            .topbar-inner .action-buttons .btn { flex: 1 1 auto; }
        }
    </style>
</head>
<body>
@php
    $activeNav = 'event';
    $sidebarEvent = $event;
    $eventDate = \Carbon\Carbon::parse($event->date);
    $now = \Carbon\Carbon::now();
    if ($eventDate->isPast()) {
        $status = 'completed';
        $statusText = 'Completed';
    } elseif ($eventDate->isToday()) {
        $status = 'ongoing';
        $statusText = 'Today';
    } else {
        $status = 'upcoming';
        $statusText = 'Upcoming';
    }
    $viewerStoreUrl = route('event.viewers.store', $event);
    $viewerDestroyUrl = str_replace('999999', '__ID__', route('event.viewers.destroy', [$event, 999999]));
@endphp

<div class="overlay-blur" id="mobileOverlay"></div>

<div class="container-fluid px-0">
    <div class="row g-0">
        @include('partials.admin-sidebar')

        <div class="col-md-9 col-lg-10 ms-sm-auto px-3 px-md-4 main-content-full">
                <!-- Topbar -->
                <div class="topbar mb-4 sticky-top">
                    <div class="d-flex justify-content-between align-items-center py-3 topbar-inner flex-wrap gap-2">
                        <div class="d-flex align-items-center min-w-0">
                            <button class="btn btn-link d-md-none text-dark me-2 flex-shrink-0" type="button" id="mobileMenuToggle">
                                <i class="fas fa-bars fa-lg"></i>
                            </button>
                            <div class="min-w-0">
                                <a href="{{ route('event.index') }}" class="btn btn-outline-secondary btn-sm mb-1">
                                    <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-inline">Back to Events</span><span class="d-sm-none">Back</span>
                                </a>
                                <h4 class="mb-0 text-truncate">Event Details</h4>
                            </div>
                        </div>
                        <div class="action-buttons">
                            <a href="{{ route('event.edit', $event->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> <span class="btn-label">Edit Event</span>
                            </a>
                            <form action="{{ route('event.destroy', $event->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this event?')">
                                    <i class="fas fa-trash"></i> <span class="btn-label">Delete Event</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mt-2">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Success Message -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Event Header -->
                <div class="event-header">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <span class="event-category-badge category-{{ $event->category }}">
                                    <i class="fas fa-tag me-1"></i>{{ ucfirst($event->category) }}
                                </span>
                                @php
                                    // status computed at top of page
                                @endphp
                                <span class="event-status-badge status-{{ $status }}">
                                    {{ $statusText }}
                                </span>
                                <h1 class="display-5 fw-bold">{{ $event->title }}</h1>
                                <p class="lead mb-0">Organized by {{ $event->organizer_name }}</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                @if($status === 'upcoming')
                                <div class="countdown-timer">
                                    <h5 class="mb-3">Event Starts In</h5>
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <div class="countdown-number" id="countdown-days">--</div>
                                            <div class="countdown-label">Days</div>
                                        </div>
                                        <div class="col-3">
                                            <div class="countdown-number" id="countdown-hours">--</div>
                                            <div class="countdown-label">Hours</div>
                                        </div>
                                        <div class="col-3">
                                            <div class="countdown-number" id="countdown-minutes">--</div>
                                            <div class="countdown-label">Minutes</div>
                                        </div>
                                        <div class="col-3">
                                            <div class="countdown-number" id="countdown-seconds">--</div>
                                            <div class="countdown-label">Seconds</div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event Stats -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card info-card h-100">
                            <div class="card-body">
                                <div class="info-icon">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                                <div class="info-number">{{ $totalInvites ?? 0 }}</div>
                                <div class="info-label">Total Invited</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card info-card h-100">
                            <div class="card-body">
                                <div class="info-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="info-number">{{ $confirmedAttendees ?? 0 }}</div>
                                <div class="info-label">Confirmed</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card info-card h-100">
                            <div class="card-body">
                                <div class="info-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <div class="info-number">{{ $pendingResponses ?? 0 }}</div>
                                <div class="info-label">Pending</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card info-card h-100">
                            <div class="card-body">
                                <div class="info-icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <div class="info-number">{{ $declinedInvites ?? 0 }}</div>
                                <div class="info-label">Declined</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Report Summary -->
                <div class="card shadow mb-4">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Delivery Report</h5>
                        <a href="{{ route('event.invitation.delivery-report', $event) }}" class="btn btn-sm btn-outline-primary">
                            Full report <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3 col-6">
                                <div class="card stat-card success h-100 mb-0">
                                    <div class="card-body py-3">
                                        <div class="text-muted small">Delivered</div>
                                        <div class="h4 mb-0">{{ $deliveryStats['sent'] }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="card stat-card danger h-100 mb-0">
                                    <div class="card-body py-3">
                                        <div class="text-muted small">Failed</div>
                                        <div class="h4 mb-0">{{ $deliveryStats['failed'] }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="card stat-card warning h-100 mb-0">
                                    <div class="card-body py-3">
                                        <div class="text-muted small">Spent</div>
                                        <div class="h4 mb-0">Tsh {{ number_format($deliveryStats['total_spent_tsh']) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="card stat-card h-100 mb-0">
                                    <div class="card-body py-3">
                                        <div class="text-muted small">Success rate</div>
                                        <div class="h4 mb-0">{{ $deliveryStats['delivery_rate'] }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($recentDeliveries->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Invitee</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                            <th>RSVP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentDeliveries as $delivery)
                                            <tr>
                                                <td>{{ $delivery->invitee?->name ?? '—' }}</td>
                                                <td>{{ $delivery->methodLabel() }}</td>
                                                <td>
                                                    @if($delivery->status === 'sent')
                                                        <span class="badge bg-success">Sent</span>
                                                    @else
                                                        <span class="badge bg-danger">Failed</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($delivery->invitee)
                                                        <span class="badge {{ $delivery->invitee->rsvpBadgeClass() }}">{{ $delivery->invitee->rsvpLabel() }}</span>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">No invitations sent yet. <a href="{{ route('event.invitation.send', ['event_id' => $event->id]) }}">Send invitations</a></p>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <!-- Event Details -->
                    <div class="col-lg-8 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Event Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="detail-content">
                                        <h5>Date & Time</h5>
                                        <p class="mb-0">{{ \Carbon\Carbon::parse($event->date)->format('l, F j, Y') }}</p>
                                        <p class="mb-0 text-muted">{{ \Carbon\Carbon::parse($event->date)->format('g:i A') }}</p>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="detail-content">
                                        <h5>Location</h5>
                                        <p class="mb-0">{{ $event->location }}</p>
                                        <a href="#" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="fas fa-directions me-1"></i> Get Directions
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="detail-content">
                                        <h5>Organizer</h5>
                                        <p class="mb-0">{{ $event->organizer_name }}</p>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-align-left"></i>
                                    </div>
                                    <div class="detail-content">
                                        <h5>Description</h5>
                                        <div class="description-box">
                                            {!! nl2br(e($event->description)) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions & Invitees -->
                    <div class="col-lg-4 mb-4">
                        <!-- Quick Actions -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('invitee.create', $event->id) }}" class="btn btn-primary">
                                        <i class="fas fa-user-plus"></i> Add Invitees
                                    </a>
                                    <a href="{{ route('event.invitation.card-upload', ['event_id' => $event->id]) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-file-pdf me-1"></i> Upload invitation PDF
                                    </a>
                                    <a href="{{ route('event.invitation.send', ['event_id' => $event->id]) }}" class="btn btn-success">
                                        <i class="fas fa-paper-plane"></i> Send Invitations
                                    </a>
                                    <a href="{{ route('event.invitation.delivery-report', $event) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-chart-bar me-1"></i> Delivery Report
                                    </a>
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#manageViewersModal">
                                        <i class="fas fa-eye me-1"></i> Manage Viewers
                                    </button>
                                    <a href="{{ route('event.verify', $event) }}" class="btn btn-warning">
                                        <i class="fas fa-qrcode me-1"></i> Verify Guests
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Event Viewers -->
                        <div class="card shadow">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Event Viewers</h5>
                                <span class="badge bg-primary" id="viewers-count-badge">{{ $viewers->count() }}</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="guest-list p-3" id="viewers-sidebar-list">
                                    @forelse($viewers as $viewer)
                                        <div class="guest-item" data-viewer-id="{{ $viewer->id }}">
                                            <div class="guest-avatar">{{ strtoupper(substr($viewer->name, 0, 2)) }}</div>
                                            <div class="guest-info min-w-0">
                                                <div class="guest-name text-truncate">{{ $viewer->name }}</div>
                                                <div class="guest-email text-truncate">{{ $viewer->email }}</div>
                                            </div>
                                            <span class="guest-status status-attending flex-shrink-0">Viewer</span>
                                        </div>
                                    @empty
                                        <p class="text-muted text-center mb-0 py-3" id="viewers-empty-msg">No viewers assigned yet.</p>
                                    @endforelse
                                </div>
                                <div class="card-footer text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#manageViewersModal">
                                        Manage Viewers
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Manage Viewers Modal --}}
<div class="modal fade" id="manageViewersModal" tabindex="-1" aria-labelledby="manageViewersModalLabel" aria-hidden="true"
     data-viewer-store-url="{{ $viewerStoreUrl }}"
     data-viewer-destroy-url="{{ $viewerDestroyUrl }}">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="manageViewersModalLabel"><i class="fas fa-eye me-2 text-primary"></i>Manage Viewers</h5>
                    <p class="text-muted small mb-0">{{ $event->title }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="viewer-form-alert" class="alert d-none" role="alert"></div>
                <div class="row g-4">
                    <div class="col-md-5">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-user-plus me-2 text-primary"></i>Add viewer</h6>
                        <p class="small text-muted">Creates a viewer login linked to you. They can scan QR codes for this event only.</p>
                        <form id="add-viewer-form">
                            <div class="mb-3">
                                <label for="viewer-name" class="form-label">Full name</label>
                                <input type="text" class="form-control" id="viewer-name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="viewer-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="viewer-email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="viewer-phone" class="form-label">Phone <span class="text-muted">(optional)</span></label>
                                <input type="text" class="form-control" id="viewer-phone" name="phone">
                            </div>
                            <div class="mb-3">
                                <label for="viewer-password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="viewer-password" name="password">
                                <div class="form-text">Required for new accounts. Leave blank when re-adding an existing viewer.</div>
                            </div>
                            <div class="mb-3">
                                <label for="viewer-password-confirm" class="form-label">Confirm password</label>
                                <input type="password" class="form-control" id="viewer-password-confirm" name="password_confirmation">
                            </div>
                            <button type="submit" class="btn btn-primary w-100" id="add-viewer-btn">
                                <i class="fas fa-plus me-1"></i> Add to this event
                            </button>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-semibold mb-0"><i class="fas fa-users me-2 text-primary"></i>Assigned viewers</h6>
                            <span class="badge bg-primary" id="viewers-modal-count">{{ $viewers->count() }}</span>
                        </div>
                        <div id="viewers-modal-list">
                            @forelse($viewers as $viewer)
                                <div class="d-flex align-items-center justify-content-between px-2 py-3 border-bottom gap-2" data-viewer-id="{{ $viewer->id }}">
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-truncate">{{ $viewer->name }}</div>
                                        <div class="small text-muted text-truncate">{{ $viewer->email }}</div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0 remove-viewer-btn" data-viewer-id="{{ $viewer->id }}" title="Remove from event">
                                        <i class="fas fa-user-minus"></i>
                                    </button>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4" id="viewers-modal-empty">
                                    <i class="fas fa-eye fa-2x mb-2 opacity-50"></i>
                                    <p class="mb-0">No viewers yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('mainSidebar');
        const overlay = document.getElementById('mobileOverlay');
        const menuToggle = document.getElementById('mobileMenuToggle');
        const closeSidebarBtn = document.getElementById('closeSidebarMobile');
        function openSidebar() {
            if (window.innerWidth < 768 && sidebar) {
                sidebar.classList.add('show-sidebar');
                overlay?.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }
        function closeSidebar() {
            sidebar?.classList.remove('show-sidebar');
            overlay?.classList.remove('active');
            document.body.style.overflow = '';
        }
        menuToggle?.addEventListener('click', openSidebar);
        closeSidebarBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);
        window.addEventListener('resize', () => { if (window.innerWidth >= 768) closeSidebar(); });

        // Manage viewers modal (AJAX)
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const viewersModalEl = document.getElementById('manageViewersModal');
        const VIEWER_STORE_URL = viewersModalEl.dataset.viewerStoreUrl;
        const VIEWER_DESTROY_URL = viewersModalEl.dataset.viewerDestroyUrl;

        function updateViewerCounts(count) {
            document.getElementById('viewers-count-badge').textContent = count;
            document.getElementById('viewers-modal-count').textContent = count;
        }

        function renderSidebarViewer(viewer) {
            const empty = document.getElementById('viewers-empty-msg');
            if (empty) empty.remove();
            const list = document.getElementById('viewers-sidebar-list');
            const item = document.createElement('div');
            item.className = 'guest-item';
            item.dataset.viewerId = viewer.id;
            item.innerHTML = `
                <div class="guest-avatar">${viewer.initials}</div>
                <div class="guest-info min-w-0">
                    <div class="guest-name text-truncate">${escapeHtml(viewer.name)}</div>
                    <div class="guest-email text-truncate">${escapeHtml(viewer.email)}</div>
                </div>
                <span class="guest-status status-attending flex-shrink-0">Viewer</span>`;
            list.appendChild(item);
        }

        function renderModalViewer(viewer) {
            const empty = document.getElementById('viewers-modal-empty');
            if (empty) empty.remove();
            const list = document.getElementById('viewers-modal-list');
            const row = document.createElement('div');
            row.className = 'd-flex align-items-center justify-content-between px-2 py-3 border-bottom gap-2';
            row.dataset.viewerId = viewer.id;
            row.innerHTML = `
                <div class="min-w-0">
                    <div class="fw-semibold text-truncate">${escapeHtml(viewer.name)}</div>
                    <div class="small text-muted text-truncate">${escapeHtml(viewer.email)}</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0 remove-viewer-btn" data-viewer-id="${viewer.id}" title="Remove from event">
                    <i class="fas fa-user-minus"></i>
                </button>`;
            list.appendChild(row);
        }

        function removeViewerFromDom(viewerId) {
            document.querySelectorAll(`[data-viewer-id="${viewerId}"]`).forEach(el => el.remove());
            const sidebarList = document.getElementById('viewers-sidebar-list');
            const modalList = document.getElementById('viewers-modal-list');
            const count = modalList.querySelectorAll('[data-viewer-id]').length;
            updateViewerCounts(count);
            if (count === 0) {
                sidebarList.innerHTML = '<p class="text-muted text-center mb-0 py-3" id="viewers-empty-msg">No viewers assigned yet.</p>';
                modalList.innerHTML = '<div class="text-center text-muted py-4" id="viewers-modal-empty"><i class="fas fa-eye fa-2x mb-2 opacity-50"></i><p class="mb-0">No viewers yet.</p></div>';
            }
        }

        function escapeHtml(str) {
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }

        function showViewerAlert(type, message) {
            const el = document.getElementById('viewer-form-alert');
            el.className = 'alert alert-' + type;
            el.textContent = message;
            el.classList.remove('d-none');
        }

        document.getElementById('add-viewer-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('add-viewer-btn');
            btn.disabled = true;
            document.getElementById('viewer-form-alert').classList.add('d-none');

            const form = e.target;
            const body = {
                name: form.name.value.trim(),
                email: form.email.value.trim(),
                phone: form.phone.value.trim(),
                password: form.password.value,
                password_confirmation: form.password_confirmation.value,
            };

            try {
                const res = await fetch(VIEWER_STORE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(body),
                });
                const data = await res.json();
                if (!res.ok || !data.success) {
                    showViewerAlert('danger', data.message || 'Could not add viewer.');
                    btn.disabled = false;
                    return;
                }
                showViewerAlert('success', data.message);
                if (!document.querySelector(`#viewers-modal-list [data-viewer-id="${data.viewer.id}"]`)) {
                    renderModalViewer(data.viewer);
                    renderSidebarViewer(data.viewer);
                    updateViewerCounts(document.querySelectorAll('#viewers-modal-list [data-viewer-id]').length);
                }
                form.reset();
            } catch (err) {
                showViewerAlert('danger', 'Network error. Try again.');
            }
            btn.disabled = false;
        });

        document.getElementById('viewers-modal-list').addEventListener('click', async (e) => {
            const btn = e.target.closest('.remove-viewer-btn');
            if (!btn) return;
            if (!confirm('Remove this viewer from the event?')) return;

            const viewerId = btn.dataset.viewerId;
            btn.disabled = true;
            const url = VIEWER_DESTROY_URL.replace('__ID__', viewerId);

            try {
                const res = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await res.json();
                if (!res.ok || !data.success) {
                    alert(data.message || 'Could not remove viewer.');
                    btn.disabled = false;
                    return;
                }
                removeViewerFromDom(viewerId);
            } catch (err) {
                alert('Network error. Try again.');
                btn.disabled = false;
            }
        });
    </script>
    
    @if($status === 'upcoming')
    <script>
        // Countdown timer for upcoming events
        function updateCountdown() {
            const eventDate = new Date("{{ $event->date }}").getTime();
            const now = new Date().getTime();
            const distance = eventDate - now;
            
            if (distance < 0) {
                document.getElementById("countdown-days").innerHTML = "00";
                document.getElementById("countdown-hours").innerHTML = "00";
                document.getElementById("countdown-minutes").innerHTML = "00";
                document.getElementById("countdown-seconds").innerHTML = "00";
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById("countdown-days").innerHTML = days.toString().padStart(2, '0');
            document.getElementById("countdown-hours").innerHTML = hours.toString().padStart(2, '0');
            document.getElementById("countdown-minutes").innerHTML = minutes.toString().padStart(2, '0');
            document.getElementById("countdown-seconds").innerHTML = seconds.toString().padStart(2, '0');
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
    </script>
    @endif
</body>
</html>