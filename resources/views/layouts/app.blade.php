
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, shrink-to-fit=no">
    <title>EventFlow | Manager Dashboard - Events, Funds & Invitations</title>
    <!-- Bootstrap 5 CSS + Icons + Google Fonts + Flatpickr -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #6c757d;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --dark: #1e293b;
            --light-bg: #f8fafc;
            --card-radius: 1.25rem;
            --fund-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --event-card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        }
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', sans-serif;
        }
        /* modern sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                left: -280px;
                width: 280px;
                top: 0;
                bottom: 0;
                overflow-y: auto;
            }
            .sidebar.show-sidebar { left: 0; }
            .overlay-blur {
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background: rgba(0,0,0,0.6);
                backdrop-filter: blur(3px);
                z-index: 990;
                display: none;
            }
            .overlay-blur.active { display: block; }
        }
        .sidebar .nav-link {
            color: #cbd5e1;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            font-weight: 500;
            transition: 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(67, 97, 238, 0.2);
            color: white;
        }
        .sidebar .nav-link i { width: 1.8rem; font-size: 1.1rem; }
        .topbar {
            background: white;
            border-radius: 1.25rem;
            padding: 0.8rem 1.5rem;
            margin-bottom: 1.8rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.03);
        }
        /* info cards (stats) */
        .info-card {
            background: white;
            border: none;
            border-radius: 1.25rem;
            padding: 1.25rem;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .info-card:hover { transform: translateY(-4px); box-shadow: 0 20px 30px -12px rgba(0,0,0,0.1); }
        .info-icon {
            width: 48px;
            height: 48px;
            background: rgba(67, 97, 238, 0.1);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        .info-number { font-size: 1.8rem; font-weight: 800; margin-bottom: 0.25rem; color: #0f172a; }
        .info-label { font-size: 0.8rem; font-weight: 500; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Fund Card Premium */
        .fund-card-premium {
            background: var(--fund-gradient);
            border-radius: 1.5rem;
            color: white;
            box-shadow: 0 15px 30px -10px rgba(16,185,129,0.4);
        }
        .disbursement-badge-light {
            background: rgba(255,255,255,0.2);
            border-radius: 2rem;
            padding: 0.3rem 0.9rem;
            font-size: 0.7rem;
        }
        /* Event Cards (beautiful grid) */
        .event-card {
            background: white;
            border-radius: 1.25rem;
            border: none;
            transition: all 0.25s ease;
            box-shadow: var(--event-card-shadow);
            overflow: hidden;
        }
        .event-card:hover { transform: translateY(-6px); box-shadow: 0 20px 35px -12px rgba(0,0,0,0.15); }
        .event-category-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.35rem 1rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 700;
            background: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .category-conference { background: #e0e7ff; color: #4338ca; }
        .category-workshop { background: #dcfce7; color: #166534; }
        .category-social { background: #ffe4e6; color: #be123c; }
        .category-webinar { background: #fef3c7; color: #b45309; }
        .event-date, .event-location, .event-organizer { font-size: 0.85rem; color: #475569; }
        .card-footer-custom {
            background: #fafcff;
            border-top: 1px solid #edf2f7;
            padding: 0.9rem 1.25rem;
        }
        .btn-outline-custom {
            border-radius: 2rem;
            padding: 0.35rem 1rem;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .invite-btn-sm {
            background: #f1f5f9;
            border: none;
            border-radius: 2rem;
            padding: 0.3rem 0.9rem;
            font-size: 0.75rem;
        }
        .invite-btn-sm:hover { background: #e2e8f0; }
        .progress-thin { height: 6px; border-radius: 1rem; }
        .avatar-manager {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .toast-custom { z-index: 9999; }
        footer { font-size: 0.7rem; color: #94a3b8; }
        @media (max-width: 768px) {
            .info-number { font-size: 1.4rem; }
            .event-card { margin-bottom: 0.5rem; }
        }
    </style>
</head>
<body>

<div class="overlay-blur" id="mobileOverlay"></div>

<div class="container-fluid px-0">
    <div class="row g-0">
        <!-- SIDEBAR (Inspired by Manager Layout) -->
        <div class="col-md-3 col-lg-2 sidebar" id="mainSidebar">
            <div class="position-sticky pt-4 px-3">
                <div class="d-flex justify-content-between align-items-center d-md-none pb-3 border-bottom border-secondary">
                    <h5 class="text-white mb-0"><i class="fas fa-tasks me-2"></i>EventFlow</h5>
                    <button class="btn btn-sm btn-outline-light rounded-circle" id="closeSidebarMobile"><i class="fas fa-times"></i></button>
                </div>
                <div class="text-center mb-4 d-none d-md-block">
                    <div class="avatar-manager mx-auto mb-2" style="width: 50px; height: 50px; font-size: 1.4rem;"><i class="fas fa-calendar-alt"></i></div>
                    <h5 class="text-white mt-2">Manager Portal</h5>
                    <span class="badge bg-primary bg-opacity-75 mt-1">v3.0</span>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.manage') }}" id="dashboardNav">
                            <i class="fas fa-tachometer-alt"></i> 
                            Dashboard</a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('event.index') }}" id="eventsNav">
                            <i class="fas fa-calendar-alt"></i> 
                            My Events</a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.manage') }}" id="createEventNavBtn">
                            <i class="fas fa-plus-circle"></i> 
                            Create Event</a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('event.create') ? 'active' : '' }}" href="{{ route('design.create') }}" id="createEventNavBtn">
                            <i class="fas fa-palette"></i> 
                            Design Card</a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('event.index') ? 'active' : '' }}" href="{{ route('admin.manage') }}" id="createEventNavBtn">
                            <i class="fas fa-plus-circle"></i> 
                            Create Event</a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('design.*') ? 'active' : '' }}" href="{{ route('admin.manage') }}" id="createEventNavBtn">
                            <i class="fas fa-plus-circle"></i> 
                            Create Event</a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('send.*') ? 'active' : '' }}" href="{{ route('event.invitation.send') }}" id="createEventNavBtn">
                        <i class="fas fa-paper-plane"></i>
                        Send Invitations</a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}" href="{{ route('admin.manage') }}" id="createEventNavBtn">
                            <i class="fas fa-plus-circle"></i> 
                            Create Event</a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.manage') }}" id="fundsNav">
                            <i class="fas fa-coins"></i> Disbursements</a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-chart-line"></i> 
                            Analytics</a>
                        </li>
                    <li class="nav-item mt-4">
                        <hr class="bg-secondary opacity-25"></li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            @method('POST')
                    <li class="nav-item">
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout</button>
                        </li>
                        </form>
                </ul>
            </div>
        </div>

        <!-- MAIN DASHBOARD CONTENT (Blade-like UI but fully interactive) -->
        <div class="col-md-9 col-lg-10 px-3 px-md-4 py-3" id="mainContent">
            <!-- Topbar -->
            <div class="topbar d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex gap-3 align-items-center">
                    <button class="btn btn-link d-md-none text-dark p-0" id="mobileMenuToggle"><i class="fas fa-bars fs-4"></i></button>
                    <div><h5 class="mb-0 fw-bold"><i class="fas fa-chalkboard-user text-primary me-2"></i>My Events <span class="text-muted fs-6 fw-normal">| Manager Dashboard</span></h5></div>
                </div>
                <div class="d-flex gap-3 align-items-center mt-2 mt-sm-0 flex-wrap justify-content-end">
                    @yield('header-actions')
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill position-relative shadow-sm" data-bs-toggle="dropdown"><i class="far fa-bell"></i> <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span></button>
                        
                    </div>
                    <div class="d-flex align-items-center gap-2"><div class="avatar-manager" style="width: 38px; height: 38px;">JD</div><span class="d-none d-sm-inline fw-semibold">{{ Auth::user()->name ?? 'Manager' }}</span></div>
                </div>
            </div>

            <!-- Main Content -->
           

                <!-- Main Content Area -->
                <div class="content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (optional, if needed) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Mobile sidebar toggle
            document.querySelector('[data-bs-toggle="collapse"]').addEventListener('click', function() {
                const sidebar = document.querySelector('.sidebar');
                sidebar.classList.toggle('show');
            });
        });

        // Show alert function for custom alerts
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} custom-alert alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const mainContent = document.querySelector('.main-content');
            mainContent.insertBefore(alertDiv, mainContent.firstChild);
            
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
    
    <!-- Additional JavaScript sections can be added here -->
    @stack('scripts')
</body>
</html>




