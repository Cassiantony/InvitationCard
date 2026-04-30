<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Owner Dashboard - Invito (Responsive)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #22c55e;
            --warning: #eab308;
            --danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e2937 100%);
            color: #e2e8f0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* --- Responsive Sidebar --- */
        .sidebar {
            background: rgba(15, 23, 42, 0.98);
            backdrop-filter: blur(14px);
            border-right: 1px solid rgba(255,255,255,0.08);
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 1050;
            transition: transform 0.3s ease-in-out;
            transform: translateX(0);
            overflow-y: auto;
        }

        /* Sidebar hidden state for mobile */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: 2rem 0 2rem rgba(0,0,0,0.3);
            }
            .sidebar.show-sidebar {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0 !important;
                width: 100%;
            }
            .topbar-mobile-pad {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
        }

        /* Desktop always visible */
        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0) !important;
            }
            .mobile-menu-toggle {
                display: none;
            }
            .main-content {
                margin-left: 260px;
                width: auto;
            }
        }

        /* scroll within sidebar */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #1e2937;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 10px;
        }

        .nav-link {
            color: #94a3b8;
            padding: 12px 20px;
            border-radius: 12px;
            margin: 4px 12px;
            transition: all 0.25s ease;
            font-weight: 500;
        }

        .nav-link i {
            width: 26px;
            margin-right: 6px;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(99, 102, 241, 0.2);
            color: white;
            transform: translateX(5px);
        }

        /* Top Bar */
        .topbar {
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            min-height: 76px;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        /* Cards + Stats */
        .stat-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            transition: transform 0.25s ease, background 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-6px);
            background: rgba(255,255,255,0.08);
        }

        .card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            backdrop-filter: blur(2px);
        }

        .table {
            color: #e2e8f0;
        }

        .table th {
            color: #94a3b8;
            font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 1rem 0.75rem;
        }
        .table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .badge {
            padding: 6px 14px;
            font-weight: 500;
            border-radius: 40px;
            letter-spacing: 0.01em;
        }

        .revenue-badge {
            background: linear-gradient(90deg, #22c55e, #86efac);
            color: #052e16;
            font-weight: 600;
        }

        /* responsive table */
        @media (max-width: 768px) {
            .table thead {
                display: none;
            }
            .table, .table tbody, .table tr, .table td {
                display: block;
                width: 100%;
            }
            .table tr {
                margin-bottom: 1rem;
                border: 1px solid rgba(255,255,255,0.1);
                border-radius: 20px;
                padding: 0.75rem;
                background: rgba(255,255,255,0.03);
            }
            .table td {
                text-align: right;
                padding: 0.65rem 1rem;
                position: relative;
                border-bottom: 1px dashed rgba(255,255,255,0.05);
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 1rem;
            }
            .table td:last-child {
                border-bottom: none;
            }
            .table td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #94a3b8;
                text-align: left;
                flex: 1;
                font-size: 0.85rem;
            }
            .table td span, .table td strong, .table td .badge {
                text-align: right;
                flex: 1;
            }
        }

        /* search input on small screens */
        @media (max-width: 768px) {
            .search-wrapper {
                width: 180px !important;
            }
            .topbar .btn-success {
                padding-left: 12px;
                padding-right: 12px;
                font-size: 0.85rem;
            }
            .topbar .btn-success i {
                margin-right: 4px;
            }
            .stat-card h3 {
                font-size: 1.6rem;
            }
            .p-5 {
                padding: 1.5rem !important;
            }
            .topbar {
                padding: 0.8rem 1rem !important;
            }
            .owner-avatar-placeholder {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .stat-card p.text-sm {
                font-size: 0.8rem;
            }
            .stat-card h3 {
                font-size: 1.4rem;
            }
            .topbar .d-flex.gap-4 {
                gap: 0.8rem !important;
            }
            .btn-success .fa-plus {
                margin-right: 0 !important;
            }
            .btn-success .btn-text {
                display: none;
            }
            .btn-success i {
                margin-right: 0;
            }
            .topbar .input-group {
                width: 160px !important;
            }
        }

        /* overlay for mobile sidebar */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(3px);
            z-index: 1040;
            display: none;
        }
        .sidebar-backdrop.show {
            display: block;
        }

        .text-indigo {
            color: #a5b4fc;
        }
        .bg-indigo-light {
            background: rgba(99,102,241,0.2);
        }
        hr.border-secondary {
            opacity: 0.3;
        }
        .btn-outline-light {
            border-color: rgba(255,255,255,0.2);
            color: #cbd5e1;
        }
        .btn-outline-light:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .form-control.bg-transparent:focus {
            background: rgba(255,255,255,0.05);
            box-shadow: none;
            border-color: #6366f1;
            color: white;
        }
        ::placeholder {
            color: #64748b;
        }
    </style>
</head>
<body>

<!-- Mobile Sidebar Backdrop -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="d-flex position-relative">
    <!-- Responsive Sidebar -->
    <div class="sidebar" id="mainSidebar">
        <div class="p-4 pb-2">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-white text-indigo rounded-3 p-2 shadow-sm" style="border-radius: 18px !important;">
                    <i class="fas fa-envelope-open-text fa-2x text-primary"></i>
                </div>
                <div>
                    <h4 class="text-white mb-0 fw-bold">Invito</h4>
                    <small class="text-indigo" style="color: #a5b4fc;">Owner Portal</small>
                </div>
            </div>

            <ul class="nav flex-column mt-3">
                <li><a href="#" class="nav-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="{{ route('manageadmins') }}" class="nav-link"><i class="fas fa-users-cog"></i> Manage Admins</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-calendar-alt"></i> All Events</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-user-friends"></i> All Invitees</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-credit-card"></i> Payments & Revenue</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-chart-pie"></i> Analytics</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-cog"></i> System Settings</a></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>

        <div class="position-absolute bottom-0 w-100 p-4 d-none d-md-block">
            <div class="d-flex align-items-center gap-3">
                <img src="https://ui-avatars.com/api/?background=6366f1&color=fff&name=Cassian+O&size=48&rounded=true" alt="Owner" class="rounded-circle" width="48" height="48">
                <div>
                    <strong class="text-white">Cassian (Owner)</strong><br>
                    <small class="text-success">● Online</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-grow-1 main-content" id="mainContent" style="transition: margin 0.3s;">
        <!-- Topbar -->
        <div class="topbar px-4 px-lg-5 py-3 d-flex flex-wrap justify-content-between align-items-center gap-3 topbar-mobile-pad">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-light mobile-menu-toggle" id="menuToggleBtn" style="border-radius: 12px; padding: 6px 12px;">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                <h3 class="mb-0 text-white fw-semibold" style="font-size: 1.5rem;">Owner Dashboard</h3>
            </div>
            
            <div class="d-flex align-items-center gap-2 gap-md-4 flex-wrap">
                <div class="input-group search-wrapper" style="width: 260px;">
                    <input type="text" class="form-control bg-transparent border-0 text-white" placeholder="Search events, users..." style="background: rgba(0,0,0,0.3) !important; border-radius: 40px 0 0 40px; border: 1px solid rgba(255,255,255,0.1);">
                    <button class="btn btn-primary" style="border-radius: 0 40px 40px 0;"><i class="fas fa-search"></i></button>
                </div>

                <button class="btn btn-success px-3 px-md-4" data-bs-toggle="modal" data-bs-target="#newEventModal" style="border-radius: 40px; white-space: nowrap;">
                    <i class="fas fa-plus me-1 me-md-2"></i><span class="btn-text">New Event</span>
                </button>

                <div class="dropdown">
                    <button class="btn btn-link text-white position-relative" data-bs-toggle="dropdown" style="text-decoration: none;">
                        <i class="fas fa-bell fa-lg"></i>
                        <span class="position-absolute translate-middle badge rounded-pill bg-danger" style="top: 5px; right: -8px;">7</span>
                    </button>
                </div>
                <!-- tiny avatar on extra small -->
                <div class="d-md-none rounded-circle bg-primary p-1" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-user text-white"></i>
                </div>
            </div>
        </div>

        <div class="p-3 p-md-4 p-lg-5">
            <!-- Stats Row - Fully Responsive Grid -->
            <div class="row g-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-slate-400 mb-1" style="color:#94a3b8;">Total Revenue</p>
                                <h3 class="text-white fw-bold mb-1">$48,920</h3>
                                <p class="text-success mb-0 small"><i class="fas fa-arrow-up"></i> +18.2% this month</p>
                            </div>
                            <i class="fas fa-dollar-sign fa-3x text-success opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-4 h-100">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-slate-400 mb-1">Total Events</p>
                                <h3 class="text-white fw-bold">142</h3>
                                <p class="text-indigo mb-0 small"><i class="fas fa-arrow-up"></i> +9 new this week</p>
                            </div>
                            <i class="fas fa-calendar-alt fa-3x text-indigo opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-4 h-100">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-slate-400 mb-1">Total Invitees</p>
                                <h3 class="text-white fw-bold">8,947</h3>
                                <p class="text-warning mb-0 small"><i class="fas fa-arrow-up"></i> +521 this month</p>
                            </div>
                            <i class="fas fa-users fa-3x text-warning opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-4 h-100">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-slate-400 mb-1">Pending Payments</p>
                                <h3 class="text-white fw-bold">23</h3>
                                <p class="text-danger mb-0 small"><i class="fas fa-clock"></i> $2,340 to collect</p>
                            </div>
                            <i class="fas fa-credit-card fa-3x text-danger opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5 g-4">
                <!-- Recent Events - Fully responsive table with data-label fallback -->
                <div class="col-lg-7">
                    <div class="card p-3 p-md-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                            <h5 class="fw-semibold text-white mb-0">Recent Events</h5>
                            <a href="#" class="btn btn-sm btn-outline-light rounded-pill px-3">View All Events</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle">
                                <thead>
                                    <tr>
                                        <th>Event Name</th><th>Organizer</th><th>Date</th><th>Invitees</th><th>Revenue</th><th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td data-label="Event Name"><strong>Wedding of Aisha & Khalid</strong></td>
                                        <td data-label="Organizer">Zanzibar Events Co.</td>
                                        <td data-label="Date">12 May 2026</td>
                                        <td data-label="Invitees">180</td>
                                        <td data-label="Revenue"><span class="badge revenue-badge">$4,320</span></td>
                                        <td data-label="Status"><span class="badge bg-success">Active</span></td>
                                    </tr>
                                    <tr>
                                        <td data-label="Event Name"><strong>Corporate Product Launch</strong></td>
                                        <td data-label="Organizer">TechVision Ltd</td>
                                        <td data-label="Date">28 Apr 2026</td>
                                        <td data-label="Invitees">95</td>
                                        <td data-label="Revenue"><span class="badge revenue-badge">$2,850</span></td>
                                        <td data-label="Status"><span class="badge bg-warning text-dark">Upcoming</span></td>
                                    </tr>
                                    <tr>
                                        <td data-label="Event Name"><strong>Family Reunion 2026</strong></td>
                                        <td data-label="Organizer">Mohammed Family</td>
                                        <td data-label="Date">15 Jun 2026</td>
                                        <td data-label="Invitees">245</td>
                                        <td data-label="Revenue"><span class="badge revenue-badge">$5,880</span></td>
                                        <td data-label="Status"><span class="badge bg-success">Active</span></td>
                                    </tr>
                                    <tr>
                                        <td data-label="Event Name"><strong>Startup Summit</strong></td>
                                        <td data-label="Organizer">Innovate Hub</td>
                                        <td data-label="Date">03 Jul 2026</td>
                                        <td data-label="Invitees">312</td>
                                        <td data-label="Revenue"><span class="badge revenue-badge">$7,250</span></td>
                                        <td data-label="Status"><span class="badge bg-secondary">Draft</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Top Performing Events -->
                <div class="col-lg-5">
                    <div class="card p-3 p-md-4 h-100">
                        <h5 class="fw-semibold text-white mb-4">🏆 Top Revenue Events</h5>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <strong class="text-white">Wedding of Aisha & Khalid</strong><br>
                                    <small class="text-slate-400">180 invitees • 92% paid</small>
                                </div>
                                <div class="text-end">
                                    <h5 class="text-success mb-0">$4,320</h5>
                                </div>
                            </div>
                            <hr class="border-secondary my-1">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <strong class="text-white">Family Reunion 2026</strong><br>
                                    <small class="text-slate-400">245 invitees • 78% paid</small>
                                </div>
                                <div class="text-end">
                                    <h5 class="text-success mb-0">$5,880</h5>
                                </div>
                            </div>
                            <hr class="border-secondary my-1">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <strong class="text-white">Startup Summit</strong><br>
                                    <small class="text-slate-400">312 invitees • 68% paid</small>
                                </div>
                                <div class="text-end">
                                    <h5 class="text-success mb-0">$7,250</h5>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-2 text-center">
                            <span class="badge bg-primary bg-opacity-25 text-primary">+15% avg. growth</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Analytics friendly section (optional extra) -->
            <div class="row mt-4 g-4">
                <div class="col-12">
                    <div class="card p-4 text-center text-md-start d-flex flex-md-row justify-content-between align-items-center gap-3">
                        <div>
                            <i class="fas fa-chart-line fa-2x text-primary me-2"></i>
                            <span class="fw-semibold">Monthly snapshot: July trending +23% vs last month</span>
                        </div>
                        <a href="#" class="btn btn-sm btn-primary rounded-pill px-4">View Analytics</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal (new event dummy) -->
<div class="modal fade" id="newEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Create New Event</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Event creation flow would be here. Fully responsive design ready.</p>
                <div class="mb-3">
                    <label class="form-label">Event title</label>
                    <input type="text" class="form-control bg-transparent text-white border-secondary" placeholder="e.g., Summer Gala">
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Create Draft</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function() {
        // Toggle sidebar on mobile/tablet
        const sidebar = document.getElementById('mainSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const menuToggle = document.getElementById('menuToggleBtn');
        const mainContent = document.getElementById('mainContent');

        function closeSidebar() {
            if (sidebar) sidebar.classList.remove('show-sidebar');
            if (backdrop) backdrop.classList.remove('show');
            document.body.style.overflow = '';
        }

        function openSidebar() {
            if (sidebar) sidebar.classList.add('show-sidebar');
            if (backdrop) backdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        if (menuToggle) {
            menuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                if (sidebar.classList.contains('show-sidebar')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', function() {
                closeSidebar();
            });
        }

        // Ensure on window resize above 992px, if sidebar forced hidden, reset transform and remove overlay
        function handleResize() {
            if (window.innerWidth >= 992) {
                if (sidebar) sidebar.classList.remove('show-sidebar');
                if (backdrop) backdrop.classList.remove('show');
                document.body.style.overflow = '';
            } else {
                // if sidebar is shown manually and window goes large, remove overlay anyway
                if (sidebar && sidebar.classList.contains('show-sidebar') && window.innerWidth >= 992) {
                    sidebar.classList.remove('show-sidebar');
                    backdrop.classList.remove('show');
                    document.body.style.overflow = '';
                }
            }
        }
        window.addEventListener('resize', handleResize);
        handleResize();

        // Also fix table data-label for td elements (add missing data-labels from existing rows)
        const allRows = document.querySelectorAll('.table tbody tr');
        const headers = Array.from(document.querySelectorAll('.table thead th')).map(th => th.innerText.trim());
        allRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, idx) => {
                if (headers[idx] && !cell.getAttribute('data-label')) {
                    cell.setAttribute('data-label', headers[idx]);
                }
            });
        });
    })();
</script>
</body>
</html>