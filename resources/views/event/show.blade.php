<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} - Invitation System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --dark-color: #5a5c69;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem;
            font-weight: 600;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white"><i class="fas fa-envelope-open-text me-2"></i>Invitation System</h4>
                        <p class="text-white-50 small" id="user-role-display">Super Admin Dashboard</p>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-users-cog"></i>
                                Manage Admins
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('event.create') }}">
                                <i class="fas fa-calendar-plus"></i>
                                Create Event
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('event.index') }}">
                                <i class="fas fa-calendar-alt"></i>
                                My Events
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-user-friends"></i>
                                Invitees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                        </li>
                    </ul>
                    
                    <div class="mt-5 text-center">
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="https://via.placeholder.com/40" alt="User" class="rounded-circle me-2 user-avatar">
                                <strong id="username-display">Super Admin</strong>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Sign out</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 ml-sm-auto px-4">
                <!-- Topbar -->
                <div class="topbar mb-4 sticky-top">
                    <div class="d-flex justify-content-between align-items-center py-3">
                        <button class="btn btn-link d-md-none" type="button">
                            <i class="fas fa-bars fa-lg"></i>
                        </button>
                        <div>
                            <a href="{{ route('event.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                                <i class="fas fa-arrow-left me-1"></i> Back to Events
                            </a>
                            <h4 class="d-inline mb-0">Event Details</h4>
                        </div>
                        <div class="action-buttons">
                            <a href="{{ route('event.edit', $event->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit Event
                            </a>
                            <form action="{{ route('event.destroy', $event->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this event?')">
                                    <i class="fas fa-trash me-1"></i> Delete Event
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

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
                                    <a href="{{ route('invitee.create') }}" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Add Invintees
                                    </a>
                                    <a href="{{ route('event.invitation.send') }}" class="btn btn-success">
                                    <i class="fas fa-paper-plane"></i> Send Invitations
                                    </a>
                                    <a href="#" class="btn btn-info">
                                        <i class="fas fa-qrcode me-1"></i> Generate QR Code
                                    </a>
                                    <a href="#" class="btn btn-warning">
                                        <i class="fas fa-envelope me-1"></i> Send Reminders
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Invitees -->
                        <div class="card shadow">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Recent Invitees</h5>
                                <span class="badge bg-primary">{{ $totalInvites ?? 0 }}</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="guest-list p-3">
                                    <!-- Sample guest data - replace with actual data from your database -->
                                    <div class="guest-item">
                                        <div class="guest-avatar">JD</div>
                                        <div class="guest-info">
                                            <div class="guest-name">John Doe</div>
                                            <div class="guest-email">john.doe@example.com</div>
                                        </div>
                                        <span class="guest-status status-attending">Attending</span>
                                    </div>
                                    
                                    <div class="guest-item">
                                        <div class="guest-avatar">JS</div>
                                        <div class="guest-info">
                                            <div class="guest-name">Jane Smith</div>
                                            <div class="guest-email">jane.smith@example.com</div>
                                        </div>
                                        <span class="guest-status status-pending">Pending</span>
                                    </div>
                                    
                                    <div class="guest-item">
                                        <div class="guest-avatar">RJ</div>
                                        <div class="guest-info">
                                            <div class="guest-name">Robert Johnson</div>
                                            <div class="guest-email">robert.j@example.com</div>
                                        </div>
                                        <span class="guest-status status-declined">Declined</span>
                                    </div>
                                    
                                    <div class="guest-item">
                                        <div class="guest-avatar">SW</div>
                                        <div class="guest-info">
                                            <div class="guest-name">Sarah Wilson</div>
                                            <div class="guest-email">sarah.wilson@example.com</div>
                                        </div>
                                        <span class="guest-status status-attending">Attending</span>
                                    </div>
                                    
                                    <div class="guest-item">
                                        <div class="guest-avatar">MB</div>
                                        <div class="guest-info">
                                            <div class="guest-name">Michael Brown</div>
                                            <div class="guest-email">michael.b@example.com</div>
                                        </div>
                                        <span class="guest-status status-pending">Pending</span>
                                    </div>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="#" class="btn btn-sm btn-outline-primary">View All Invitees</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
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
    
    <script>
        // Share event functionality
        document.querySelector('.btn-primary[href="#"]').addEventListener('click', function(e) {
            e.preventDefault();
            if (navigator.share) {
                navigator.share({
                    title: '{{ $event->title }}',
                    text: '{{ $event->description }}',
                    url: window.location.href,
                })
                .then(() => console.log('Successful share'))
                .catch((error) => console.log('Error sharing:', error));
            } else {
                // Fallback for browsers that don't support Web Share API
                navigator.clipboard.writeText(window.location.href).then(function() {
                    alert('Event link copied to clipboard!');
                }, function() {
                    // Fallback if clipboard API is not supported
                    prompt('Copy this link to share:', window.location.href);
                });
            }
        });
    </script>
</body>
</html>