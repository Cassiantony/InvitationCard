<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Invitation System</title>
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
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 700;
        }
        
        .stat-card {
            border-left: 0.25rem solid var(--primary-color);
        }
        
        .stat-card.event {
            border-left-color: var(--secondary-color);
        }
        
        .stat-card.invitee {
            border-left-color: var(--warning-color);
        }
        
        .stat-card.rsvp {
            border-left-color: var(--danger-color);
        }
        
        .bg-gradient-primary {
            background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
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
        
        .table th {
            border-top: none;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .badge-success {
            background-color: var(--secondary-color);
        }
        
        .badge-warning {
            background-color: var(--warning-color);
        }
        
        .badge-danger {
            background-color: var(--danger-color);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .event-card {
            transition: all 0.3s;
        }
        
        .event-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .progress {
            height: 10px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            padding: 5px 10px;
            border-radius: 50%;
            background: var(--danger-color);
            color: white;
            font-size: 12px;
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
                        <p class="text-white-50 small">Admin Dashboard</p>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
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
                                <i class="fas fa-palette"></i>
                                Templates
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-chart-bar"></i>
                                Analytics
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
                                <img src="https://via.placeholder.com/40" alt="Admin" class="rounded-circle me-2 user-avatar">
                                <strong>Admin User</strong>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt me-2"></i>Sign out</a></li>
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
                        <h4 class="mb-0">Dashboard</h4>
                        <div class="d-flex">
                            <div class="input-group me-3">
                                <input type="text" class="form-control" placeholder="Search events, invitees...">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div class="dropdown me-3 position-relative">
                                <button class="btn btn-primary position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bell"></i>
                                    <span class="notification-badge">3</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                                    <li><h6 class="dropdown-header">Notifications</h6></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user-plus text-success me-2"></i>5 new RSVPs for Company Gala</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Event starting in 2 days</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-envelope text-primary me-2"></i>Invitations sent to 25 people</a></li>
                                </ul>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-plus me-1"></i> Create New
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li><a class="dropdown-item" href="{{ route('event.create') }}"><i class="fas fa-calendar-plus me-2"></i>New Event</a></li>
                                    <li><a class="dropdown-item" href="{{ route('invitee.create') }}"><i class="fas fa-users me-2"></i>Add Invitees</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-palette me-2"></i>Design Template</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Page Content -->
                <div class="row">
                    <!-- Statistics Cards -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Events
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 2 new</span>
                                            <span>Since last month</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card event h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Active Events
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">8</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 1 new</span>
                                            <span>Since last week</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card invitee h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Total Invitees
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">684</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 42 new</span>
                                            <span>Since last week</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card rsvp h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            Pending RSVPs
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">124</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-danger mr-2"><i class="fas fa-arrow-down"></i> 12%</span>
                                            <span>Response rate</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Recent Events -->
                    <div class="col-lg-8 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Events</h6>
                                <a href="#" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="card event-card h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <h5 class="card-title">Annual Company Gala</h5>
                                                    <span class="badge bg-success">Active</span>
                                                </div>
                                                <p class="card-text"><i class="fas fa-calendar-day text-primary me-2"></i>15 Dec, 2023 | 7:00 PM</p>
                                                <p class="card-text"><i class="fas fa-map-marker-alt text-primary me-2"></i>Grand Ballroom, City Center</p>
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <div>
                                                        <span class="text-muted">Invitees: 245</span>
                                                        <span class="ms-3 text-muted">RSVPs: 189</span>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="card event-card h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <h5 class="card-title">Product Launch</h5>
                                                    <span class="badge bg-warning">Upcoming</span>
                                                </div>
                                                <p class="card-text"><i class="fas fa-calendar-day text-primary me-2"></i>10 Jan, 2024 | 2:00 PM</p>
                                                <p class="card-text"><i class="fas fa-map-marker-alt text-primary me-2"></i>Convention Center</p>
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <div>
                                                        <span class="text-muted">Invitees: 180</span>
                                                        <span class="ms-3 text-muted">RSVPs: 92</span>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="card event-card h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <h5 class="card-title">Team Building Workshop</h5>
                                                    <span class="badge bg-danger">Completed</span>
                                                </div>
                                                <p class="card-text"><i class="fas fa-calendar-day text-primary me-2"></i>05 Nov, 2023 | 9:00 AM</p>
                                                <p class="card-text"><i class="fas fa-map-marker-alt text-primary me-2"></i>Corporate Office</p>
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <div>
                                                        <span class="text-muted">Invitees: 75</span>
                                                        <span class="ms-3 text-muted">Attended: 68</span>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-chart-bar"></i></button>
                                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="card event-card h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <h5 class="card-title">Client Appreciation Dinner</h5>
                                                    <span class="badge bg-success">Active</span>
                                                </div>
                                                <p class="card-text"><i class="fas fa-calendar-day text-primary me-2"></i>22 Dec, 2023 | 6:30 PM</p>
                                                <p class="card-text"><i class="fas fa-map-marker-alt text-primary me-2"></i>Luxury Restaurant</p>
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <div>
                                                        <span class="text-muted">Invitees: 120</span>
                                                        <span class="ms-3 text-muted">RSVPs: 85</span>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity & RSVP Status -->
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">RSVP Status</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <h6 class="small font-weight-bold">Annual Company Gala <span class="float-end">75%</span></h6>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 75%"></div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <h6 class="small font-weight-bold">Product Launch <span class="float-end">50%</span></h6>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 50%"></div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <h6 class="small font-weight-bold">Client Dinner <span class="float-end">65%</span></h6>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 65%"></div>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <h6 class="font-weight-bold mt-4">Recent Activity</h6>
                                <div class="mt-3">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-user-check text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="mb-0">John Smith accepted invitation to Company Gala</p>
                                            <small class="text-muted">2 hours ago</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-user-times text-danger"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="mb-0">Sarah Johnson declined Product Launch invitation</p>
                                            <small class="text-muted">5 hours ago</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-envelope text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="mb-0">Invitations sent to 25 new contacts</p>
                                            <small class="text-muted">Yesterday</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Quick Actions -->
                    <div class="col-lg-12 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3 mb-3">
                                        <div class="card bg-gradient-primary text-white h-100">
                                            <div class="card-body">
                                                <i class="fas fa-calendar-plus fa-2x mb-3"></i>
                                                <h5>Create Event</h5>
                                                <p>Set up a new event</p>
                                                <button class="btn btn-light">Create</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card bg-gradient-primary text-white h-100">
                                            <div class="card-body">
                                                <i class="fas fa-user-plus fa-2x mb-3"></i>
                                                <h5>Add Invitees</h5>
                                                <p>Import or add guests</p>
                                                <button class="btn btn-light">Add</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card bg-gradient-primary text-white h-100">
                                            <div class="card-body">
                                                <i class="fas fa-palette fa-2x mb-3"></i>
                                                <h5>Design Card</h5>
                                                <p>Customize invitation</p>
                                                <button class="btn btn-light">Design</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card bg-gradient-primary text-white h-100">
                                            <div class="card-body">
                                                <i class="fas fa-file-export fa-2x mb-3"></i>
                                                <h5>Export Data</h5>
                                                <p>Download reports</p>
                                                <button class="btn btn-light">Export</button>
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

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>