<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Verification Status - Invitation System</title>
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
        
        .invitee-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border: 1px solid #e3e6f0;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
            background-color: #fff;
            transition: all 0.3s;
        }
        
        .invitee-item:hover {
            background-color: #f8f9fc;
        }
        
        .verification-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .badge-verified {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .badge-pending {
            background-color: var(--warning-color);
            color: white;
        }
        
        .badge-not-verified {
            background-color: #6c757d;
            color: white;
        }
        
        .badge-rejected {
            background-color: var(--danger-color);
            color: white;
        }
        
        .badge-sms {
            background-color: #6c757d;
            color: white;
        }
        
        .badge-whatsapp {
            background-color: #25D366;
            color: white;
        }
        
        .stats-card {
            border-left: 0.25rem solid var(--primary-color);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-card.verified {
            border-left-color: var(--secondary-color);
        }
        
        .stats-card.pending {
            border-left-color: var(--warning-color);
        }
        
        .stats-card.not-verified {
            border-left-color: #6c757d;
        }
        
        .stats-card.rejected {
            border-left-color: var(--danger-color);
        }
        
        .filter-section {
            background-color: white;
            border-radius: 0.35rem;
            padding: 1.25rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .progress {
            height: 8px;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }
        
        .invitee-actions {
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .invitee-item:hover .invitee-actions {
            opacity: 1;
        }
        
        .view-toggle {
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.25rem;
        }
        
        .view-toggle.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .list-view .invitee-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e3e6f0;
            transition: background-color 0.3s;
        }
        
        .list-view .invitee-item:hover {
            background-color: #f8f9fc;
        }
        
        .list-view .invitee-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .tabs-container .nav-link {
            color: #6c757d;
            font-weight: 600;
            border: none;
            padding: 0.75rem 1.5rem;
        }
        
        .tabs-container .nav-link.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
            background-color: transparent;
        }
        
        .export-btn {
            background-color: white;
            border: 1px solid #e3e6f0;
        }
        
        .verification-chart {
            height: 200px;
            position: relative;
        }
        
        .chart-legend {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin: 0 0.5rem;
        }
        
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
        
        .time-badge {
            font-size: 0.75rem;
            background-color: #e9ecef;
            color: #6c757d;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
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
                        <p class="text-white-50 small" id="user-role-display">Admin Dashboard</p>
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
                                <i class="fas fa-calendar-alt"></i>
                                My Events
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-user-friends"></i>
                                All Invitees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="qr-scanner.html">
                                <i class="fas fa-qrcode"></i>
                                QR Scanner
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="fas fa-clipboard-check"></i>
                                Verification Status
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-chart-bar"></i>
                                Analytics
                            </a>
                        </li>
                    </ul>
                    
                    <div class="mt-5 text-center">
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="https://via.placeholder.com/40" alt="Admin" class="rounded-circle me-2 user-avatar">
                                <strong id="username-display">Admin User</strong>
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
                        <h4 class="mb-0">Verification Status</h4>
                        <div class="d-flex">
                            <a href="qr-scanner.html" class="btn btn-primary me-2">
                                <i class="fas fa-qrcode me-1"></i> Open Scanner
                            </a>
                            <a href="events.html" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Events
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Event Selection -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Select Event</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="event-select" class="form-label">Choose Event</label>
                                <select class="form-select" id="event-select">
                                    <option value="">Select an event</option>
                                    <option value="1" selected>Annual Company Gala (15 Dec, 2023)</option>
                                    <option value="2">Product Launch Event (10 Jan, 2024)</option>
                                    <option value="3">Client Appreciation Dinner (22 Dec, 2023)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-end h-100">
                                    <div class="w-100">
                                        <label class="form-label">Quick Actions</label>
                                        <div class="d-grid gap-2 d-md-flex">
                                            <button class="btn btn-outline-primary" id="send-reminders-btn">
                                                <i class="fas fa-bell me-1"></i> Send Reminders
                                            </button>
                                            <button class="btn btn-outline-success" id="export-btn">
                                                <i class="fas fa-file-export me-1"></i> Export List
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card h-100" data-status="all">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Invitees
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">245</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span>100% of event capacity</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card verified h-100" data-status="verified">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Verified
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">142</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 58%</span>
                                            <span>of total invitees</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card not-verified h-100" data-status="not-verified">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                            Not Verified
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">89</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-danger mr-2"><i class="fas fa-clock"></i> 36%</span>
                                            <span>awaiting arrival</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card rejected h-100" data-status="rejected">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            Rejected
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">14</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-danger mr-2"><i class="fas fa-times"></i> 6%</span>
                                            <span>not admitted</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-times fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">Verification Progress</h6>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Overall Verification Rate</span>
                            <span>142/245 (58%)</span>
                        </div>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 58%"></div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 36%"></div>
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 6%"></div>
                        </div>
                        
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h5 class="text-success mb-0">58%</h5>
                                    <small class="text-muted">Verified</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h5 class="text-warning mb-0">36%</h5>
                                    <small class="text-muted">Not Verified</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h5 class="text-danger mb-0">6%</h5>
                                    <small class="text-muted">Rejected</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-primary mb-0">42</h5>
                                <small class="text-muted">Verified Today</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filters and Controls -->
                <div class="filter-section">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label for="status-filter" class="form-label">Verification Status</label>
                                    <select class="form-select" id="status-filter">
                                        <option value="all">All Statuses</option>
                                        <option value="verified">Verified</option>
                                        <option value="not-verified">Not Verified</option>
                                        <option value="pending">Pending Approval</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="method-filter" class="form-label">Invitation Method</label>
                                    <select class="form-select" id="method-filter">
                                        <option value="all">All Methods</option>
                                        <option value="whatsapp">WhatsApp</option>
                                        <option value="sms">SMS</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="search-invitees" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="search-invitees" placeholder="Name, email, phone...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group" role="group">
                                <span class="me-2 align-middle">View:</span>
                                <button type="button" class="btn btn-outline-primary view-toggle active" id="grid-view">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary view-toggle" id="list-view">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabs for different views -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <ul class="nav nav-tabs card-header-tabs tabs-container" id="inviteeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                                    All Invitees
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="verified-tab" data-bs-toggle="tab" data-bs-target="#verified" type="button" role="tab">
                                    Verified <span class="badge bg-success ms-1">142</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="not-verified-tab" data-bs-toggle="tab" data-bs-target="#not-verified" type="button" role="tab">
                                    Not Verified <span class="badge bg-warning ms-1">89</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                                    Rejected <span class="badge bg-danger ms-1">14</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="inviteeTabsContent">
                            <!-- All Invitees Tab -->
                            <div class="tab-pane fade show active" id="all" role="tabpanel">
                                <div class="row" id="all-invitees-grid">
                                    <!-- Invitees will be populated here -->
                                </div>
                                
                                <div class="card d-none" id="all-invitees-list">
                                    <div class="card-body p-0">
                                        <div class="list-view" id="all-invitees-list-content">
                                            <!-- List view content will be populated here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Verified Tab -->
                            <div class="tab-pane fade" id="verified" role="tabpanel">
                                <div class="row" id="verified-invitees-grid">
                                    <!-- Verified invitees will be populated here -->
                                </div>
                                
                                <div class="card d-none" id="verified-invitees-list">
                                    <div class="card-body p-0">
                                        <div class="list-view" id="verified-invitees-list-content">
                                            <!-- List view content will be populated here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Not Verified Tab -->
                            <div class="tab-pane fade" id="not-verified" role="tabpanel">
                                <div class="row" id="not-verified-invitees-grid">
                                    <!-- Not verified invitees will be populated here -->
                                </div>
                                
                                <div class="card d-none" id="not-verified-invitees-list">
                                    <div class="card-body p-0">
                                        <div class="list-view" id="not-verified-invitees-list-content">
                                            <!-- List view content will be populated here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Rejected Tab -->
                            <div class="tab-pane fade" id="rejected" role="tabpanel">
                                <div class="row" id="rejected-invitees-grid">
                                    <!-- Rejected invitees will be populated here -->
                                </div>
                                
                                <div class="card d-none" id="rejected-invitees-list">
                                    <div class="card-body p-0">
                                        <div class="list-view" id="rejected-invitees-list-content">
                                            <!-- List view content will be populated here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Invitee pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Invitee Detail Modal -->
    <div class="modal fade" id="inviteeDetailModal" tabindex="-1" aria-labelledby="inviteeDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inviteeDetailModalLabel">
                        <i class="fas fa-user me-2"></i>Invitee Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="https://via.placeholder.com/120" alt="Guest" class="guest-avatar mb-3">
                            <h5 id="modal-guest-name">John Smith</h5>
                            <p class="text-muted" id="modal-guest-email">john.smith@example.com</p>
                            <span class="verification-badge" id="modal-verification-status">
                                <i class="fas fa-check me-1"></i> Verified
                            </span>
                        </div>
                        <div class="col-md-8">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">Guest Information</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" width="40%">Phone:</td>
                                            <td id="modal-guest-phone">+1 (555) 123-4567</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Company:</td>
                                            <td id="modal-guest-company">ABC Corporation</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Ticket Type:</td>
                                            <td id="modal-guest-ticket">VIP</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Table Number:</td>
                                            <td id="modal-guest-table">12</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Seat Number:</td>
                                            <td id="modal-guest-seat">A5</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Invitation Method:</td>
                                            <td id="modal-guest-invite-method">WhatsApp</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Verification Details</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" width="40%">Verification Status:</td>
                                            <td id="modal-verification-detail">Verified</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Verification Method:</td>
                                            <td id="modal-verification-method">QR Code</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Verification Time:</td>
                                            <td id="modal-verification-time">Today, 14:32</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Verified By:</td>
                                            <td id="modal-verified-by">Admin User</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="send-reminder-btn">
                        <i class="fas fa-bell me-1"></i> Send Reminder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sample invitee data
        const invitees = [
            {
                id: 1,
                name: "John Smith",
                email: "john.smith@example.com",
                phone: "+1 (555) 123-4567",
                company: "ABC Corporation",
                ticketType: "VIP",
                tableNumber: "12",
                seatNumber: "A5",
                status: "verified",
                inviteMethod: "whatsapp",
                verificationMethod: "qr",
                verificationTime: new Date(Date.now() - 30 * 60000), // 30 minutes ago
                verifiedBy: "Admin User",
                avatar: "https://via.placeholder.com/120"
            },
            {
                id: 2,
                name: "Sarah Johnson",
                email: "sarah.j@example.com",
                phone: "+1 (555) 987-6543",
                company: "XYZ Inc",
                ticketType: "Standard",
                tableNumber: "8",
                seatNumber: "B2",
                status: "verified",
                inviteMethod: "sms",
                verificationMethod: "sms",
                verificationTime: new Date(Date.now() - 45 * 60000), // 45 minutes ago
                verifiedBy: "Admin User",
                avatar: "https://via.placeholder.com/120"
            },
            {
                id: 3,
                name: "Michael Brown",
                email: "michael.b@example.com",
                phone: "+1 (555) 456-7890",
                company: "Tech Solutions",
                ticketType: "VIP",
                tableNumber: "5",
                seatNumber: "C1",
                status: "not-verified",
                inviteMethod: "whatsapp",
                avatar: "https://via.placeholder.com/120"
            },
            {
                id: 4,
                name: "Emily Davis",
                email: "emily.davis@example.com",
                phone: "+1 (555) 234-5678",
                company: "Global Enterprises",
                ticketType: "Standard",
                tableNumber: "15",
                seatNumber: "D4",
                status: "rejected",
                inviteMethod: "sms",
                rejectionReason: "Invalid invitation",
                avatar: "https://via.placeholder.com/120"
            },
            {
                id: 5,
                name: "Robert Wilson",
                email: "robert.w@example.com",
                phone: "+1 (555) 345-6789",
                company: "Innovation Labs",
                ticketType: "VIP",
                tableNumber: "3",
                seatNumber: "A1",
                status: "verified",
                inviteMethod: "whatsapp",
                verificationMethod: "qr",
                verificationTime: new Date(Date.now() - 60 * 60000), // 1 hour ago
                verifiedBy: "Admin User",
                avatar: "https://via.placeholder.com/120"
            },
            {
                id: 6,
                name: "Jennifer Lee",
                email: "jennifer.lee@example.com",
                phone: "+1 (555) 567-8901",
                company: "Creative Minds",
                ticketType: "Standard",
                tableNumber: "10",
                seatNumber: "B5",
                status: "not-verified",
                inviteMethod: "sms",
                avatar: "https://via.placeholder.com/120"
            },
            {
                id: 7,
                name: "David Miller",
                email: "david.m@example.com",
                phone: "+1 (555) 678-9012",
                company: "Future Tech",
                ticketType: "VIP",
                tableNumber: "7",
                seatNumber: "C3",
                status: "verified",
                inviteMethod: "whatsapp",
                verificationMethod: "qr",
                verificationTime: new Date(Date.now() - 15 * 60000), // 15 minutes ago
                verifiedBy: "Admin User",
                avatar: "https://via.placeholder.com/120"
            },
            {
                id: 8,
                name: "Amanda Taylor",
                email: "amanda.t@example.com",
                phone: "+1 (555) 789-0123",
                company: "Next Gen Solutions",
                ticketType: "Standard",
                tableNumber: "18",
                seatNumber: "D2",
                status: "not-verified",
                inviteMethod: "sms",
                avatar: "https://via.placeholder.com/120"
            }
        ];
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            renderAllInvitees();
            updateStatistics();
        });
        
        // Set up event listeners
        function setupEventListeners() {
            // View toggle
            document.getElementById('grid-view').addEventListener('click', function() {
                setActiveView('grid');
            });
            
            document.getElementById('list-view').addEventListener('click', function() {
                setActiveView('list');
            });
            
            // Filter changes
            document.getElementById('status-filter').addEventListener('change', applyFilters);
            document.getElementById('method-filter').addEventListener('change', applyFilters);
            document.getElementById('search-invitees').addEventListener('input', applyFilters);
            
            // Quick action buttons
            document.getElementById('send-reminders-btn').addEventListener('click', function() {
                alert('Reminders sent to all not-verified invitees!');
            });
            
            document.getElementById('export-btn').addEventListener('click', function() {
                alert('Exporting invitee list...');
            });
            
            // Stats card clicks
            document.querySelectorAll('.stats-card').forEach(card => {
                card.addEventListener('click', function() {
                    const status = this.getAttribute('data-status');
                    filterByStatus(status);
                });
            });
            
            // Modal button
            document.getElementById('send-reminder-btn').addEventListener('click', function() {
                alert('Reminder sent to this invitee!');
            });
        }
        
        // Set active view (grid or list)
        function setActiveView(view) {
            // Update button states
            document.getElementById('grid-view').classList.toggle('active', view === 'grid');
            document.getElementById('list-view').classList.toggle('active', view === 'list');
            
            // Update all tab views
            const tabs = ['all', 'verified', 'not-verified', 'rejected'];
            tabs.forEach(tab => {
                const gridElement = document.getElementById(`${tab}-invitees-grid`);
                const listElement = document.getElementById(`${tab}-invitees-list`);
                
                if (view === 'grid') {
                    gridElement.classList.remove('d-none');
                    listElement.classList.add('d-none');
                } else {
                    gridElement.classList.add('d-none');
                    listElement.classList.remove('d-none');
                }
            });
        }
        
        // Render all invitees
        function renderAllInvitees() {
            renderInviteesByStatus('all', invitees);
            renderInviteesByStatus('verified', invitees.filter(i => i.status === 'verified'));
            renderInviteesByStatus('not-verified', invitees.filter(i => i.status === 'not-verified'));
            renderInviteesByStatus('rejected', invitees.filter(i => i.status === 'rejected'));
        }
        
        // Render invitees by status
        function renderInviteesByStatus(status, inviteeList) {
            const gridContainer = document.getElementById(`${status}-invitees-grid`);
            const listContainer = document.getElementById(`${status}-invitees-list-content`);
            
            if (inviteeList.length === 0) {
                gridContainer.innerHTML = `
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-user-friends"></i>
                            <h4>No invitees found</h4>
                            <p>There are no invitees matching your criteria.</p>
                        </div>
                    </div>
                `;
                listContainer.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-user-friends"></i>
                        <h4>No invitees found</h4>
                        <p>There are no invitees matching your criteria.</p>
                    </div>
                `;
                return;
            }
            
            // Render grid view
            gridContainer.innerHTML = inviteeList.map(invitee => `
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="invitee-item">
                        <div class="flex-shrink-0">
                            <img src="${invitee.avatar}" alt="${invitee.name}" class="user-avatar">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">${invitee.name}</h6>
                                ${getVerificationBadge(invitee.status, invitee.verificationMethod)}
                            </div>
                            <p class="mb-1 text-muted small">${invitee.email}</p>
                            <p class="mb-1 text-muted small">${invitee.phone}</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="badge ${invitee.inviteMethod === 'whatsapp' ? 'badge-whatsapp' : 'badge-sms'}">
                                    <i class="fab ${invitee.inviteMethod === 'whatsapp' ? 'fa-whatsapp' : 'fa-sms'} me-1"></i>
                                    ${invitee.inviteMethod === 'whatsapp' ? 'WhatsApp' : 'SMS'}
                                </span>
                                ${invitee.status === 'verified' ? 
                                    `<span class="time-badge">${getTimeAgo(invitee.verificationTime)}</span>` : 
                                    ''
                                }
                            </div>
                            <div class="invitee-actions mt-2">
                                <button class="btn btn-sm btn-outline-primary view-details" data-id="${invitee.id}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${invitee.status === 'not-verified' ? 
                                    `<button class="btn btn-sm btn-outline-success ms-1 verify-now" data-id="${invitee.id}">
                                        <i class="fas fa-check"></i>
                                    </button>` : 
                                    ''
                                }
                                <button class="btn btn-sm btn-outline-danger ms-1 remove-invitee" data-id="${invitee.id}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            // Render list view
            listContainer.innerHTML = inviteeList.map(invitee => `
                <div class="invitee-item">
                    <div class="flex-shrink-0">
                        <img src="${invitee.avatar}" alt="${invitee.name}" class="invitee-avatar">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${invitee.name}</h6>
                                <p class="mb-1 text-muted">${invitee.email} | ${invitee.phone}</p>
                                <p class="mb-0 text-muted small">
                                    ${invitee.company} • Table ${invitee.tableNumber}, Seat ${invitee.seatNumber}
                                </p>
                            </div>
                            <div class="text-end">
                                ${getVerificationBadge(invitee.status, invitee.verificationMethod)}
                                <div class="mt-2">
                                    <span class="badge ${invitee.inviteMethod === 'whatsapp' ? 'badge-whatsapp' : 'badge-sms'}">
                                        <i class="fab ${invitee.inviteMethod === 'whatsapp' ? 'fa-whatsapp' : 'fa-sms'} me-1"></i>
                                        ${invitee.inviteMethod === 'whatsapp' ? 'WhatsApp' : 'SMS'}
                                    </span>
                                    ${invitee.status === 'verified' ? 
                                        `<span class="time-badge ms-1">${getTimeAgo(invitee.verificationTime)}</span>` : 
                                        ''
                                    }
                                </div>
                            </div>
                        </div>
                        <div class="invitee-actions mt-2">
                            <button class="btn btn-sm btn-outline-primary view-details" data-id="${invitee.id}">
                                <i class="fas fa-eye me-1"></i> Details
                            </button>
                            ${invitee.status === 'not-verified' ? 
                                `<button class="btn btn-sm btn-outline-success ms-1 verify-now" data-id="${invitee.id}">
                                    <i class="fas fa-check me-1"></i> Verify Now
                                </button>` : 
                                ''
                            }
                            <button class="btn btn-sm btn-outline-danger ms-1 remove-invitee" data-id="${invitee.id}">
                                <i class="fas fa-times me-1"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
            
            // Add event listeners to action buttons
            document.querySelectorAll('.view-details').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    showInviteeDetails(id);
                });
            });
            
            document.querySelectorAll('.verify-now').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    verifyInvitee(id);
                });
            });
            
            document.querySelectorAll('.remove-invitee').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    removeInvitee(id);
                });
            });
        }
        
        // Get verification badge HTML
        function getVerificationBadge(status, verificationMethod) {
            let badgeClass = '';
            let badgeIcon = '';
            let badgeText = '';
            
            switch(status) {
                case 'verified':
                    badgeClass = 'badge-verified';
                    badgeIcon = verificationMethod === 'qr' ? 'fa-qrcode' : 'fa-sms';
                    badgeText = 'Verified';
                    break;
                case 'not-verified':
                    badgeClass = 'badge-not-verified';
                    badgeIcon = 'fa-clock';
                    badgeText = 'Not Verified';
                    break;
                case 'rejected':
                    badgeClass = 'badge-rejected';
                    badgeIcon = 'fa-times';
                    badgeText = 'Rejected';
                    break;
            }
            
            return `<span class="verification-badge ${badgeClass}">
                <i class="fas ${badgeIcon} me-1"></i> ${badgeText}
            </span>`;
        }
        
        // Get time ago string
        function getTimeAgo(timestamp) {
            if (!timestamp) return '';
            
            const now = new Date();
            const diffMs = now - timestamp;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            
            if (diffMins < 1) return 'just now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            
            return timestamp.toLocaleDateString();
        }
        
        // Apply filters
        function applyFilters() {
            const statusFilter = document.getElementById('status-filter').value;
            const methodFilter = document.getElementById('method-filter').value;
            const searchTerm = document.getElementById('search-invitees').value.toLowerCase();
            
            let filteredInvitees = invitees;
            
            // Apply status filter
            if (statusFilter !== 'all') {
                filteredInvitees = filteredInvitees.filter(i => i.status === statusFilter);
            }
            
            // Apply method filter
            if (methodFilter !== 'all') {
                filteredInvitees = filteredInvitees.filter(i => i.inviteMethod === methodFilter);
            }
            
            // Apply search filter
            if (searchTerm) {
                filteredInvitees = filteredInvitees.filter(i => 
                    i.name.toLowerCase().includes(searchTerm) ||
                    i.email.toLowerCase().includes(searchTerm) ||
                    i.phone.includes(searchTerm)
                );
            }
            
            renderInviteesByStatus('all', filteredInvitees);
            renderInviteesByStatus('verified', filteredInvitees.filter(i => i.status === 'verified'));
            renderInviteesByStatus('not-verified', filteredInvitees.filter(i => i.status === 'not-verified'));
            renderInviteesByStatus('rejected', filteredInvitees.filter(i => i.status === 'rejected'));
        }
        
        // Filter by status (from stats cards)
        function filterByStatus(status) {
            if (status === 'all') {
                document.getElementById('status-filter').value = 'all';
            } else {
                document.getElementById('status-filter').value = status;
            }
            
            // Switch to the appropriate tab
            const tabMap = {
                'all': 'all-tab',
                'verified': 'verified-tab',
                'not-verified': 'not-verified-tab',
                'rejected': 'rejected-tab'
            };
            
            const tabButton = document.getElementById(tabMap[status]);
            if (tabButton) {
                tabButton.click();
            }
            
            applyFilters();
        }
        
        // Show invitee details in modal
        function showInviteeDetails(id) {
            const invitee = invitees.find(i => i.id === id);
            if (!invitee) return;
            
            // Populate modal with invitee data
            document.getElementById('modal-guest-name').textContent = invitee.name;
            document.getElementById('modal-guest-email').textContent = invitee.email;
            document.getElementById('modal-guest-phone').textContent = invitee.phone;
            document.getElementById('modal-guest-company').textContent = invitee.company;
            document.getElementById('modal-guest-ticket').textContent = invitee.ticketType;
            document.getElementById('modal-guest-table').textContent = invitee.tableNumber;
            document.getElementById('modal-guest-seat').textContent = invitee.seatNumber;
            document.getElementById('modal-guest-invite-method').textContent = 
                invitee.inviteMethod === 'whatsapp' ? 'WhatsApp' : 'SMS';
            
            // Set verification details
            document.getElementById('modal-verification-detail').textContent = 
                invitee.status === 'verified' ? 'Verified' : 
                invitee.status === 'not-verified' ? 'Not Verified' : 'Rejected';
            
            document.getElementById('modal-verification-method').textContent = 
                invitee.verificationMethod === 'qr' ? 'QR Code' : 
                invitee.verificationMethod === 'sms' ? 'SMS Code' : 'N/A';
            
            document.getElementById('modal-verification-time').textContent = 
                invitee.verificationTime ? getTimeAgo(invitee.verificationTime) : 'N/A';
            
            document.getElementById('modal-verified-by').textContent = 
                invitee.verifiedBy || 'N/A';
            
            // Update verification badge in modal
            const badge = document.getElementById('modal-verification-status');
            badge.className = 'verification-badge ' + 
                (invitee.status === 'verified' ? 'badge-verified' : 
                 invitee.status === 'not-verified' ? 'badge-not-verified' : 'badge-rejected');
            badge.innerHTML = `<i class="fas ${invitee.status === 'verified' ? 'fa-check' : 
                invitee.status === 'not-verified' ? 'fa-clock' : 'fa-times'} me-1"></i> ${badge.textContent}`;
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('inviteeDetailModal'));
            modal.show();
        }
        
        // Verify an invitee
        function verifyInvitee(id) {
            if (confirm('Mark this invitee as verified?')) {
                // In a real app, this would update the database
                const invitee = invitees.find(i => i.id === id);
                if (invitee) {
                    invitee.status = 'verified';
                    invitee.verificationTime = new Date();
                    invitee.verifiedBy = 'Admin User';
                    invitee.verificationMethod = 'manual';
                    
                    renderAllInvitees();
                    updateStatistics();
                    alert('Invitee marked as verified!');
                }
            }
        }
        
        // Remove an invitee
        function removeInvitee(id) {
            if (confirm('Are you sure you want to remove this invitee?')) {
                // In a real app, this would update the database
                const index = invitees.findIndex(i => i.id === id);
                if (index !== -1) {
                    invitees.splice(index, 1);
                    renderAllInvitees();
                    updateStatistics();
                    alert('Invitee removed!');
                }
            }
        }
        
        // Update statistics
        function updateStatistics() {
            const total = invitees.length;
            const verified = invitees.filter(i => i.status === 'verified').length;
            const notVerified = invitees.filter(i => i.status === 'not-verified').length;
            const rejected = invitees.filter(i => i.status === 'rejected').length;
            
            // Update stats cards
            document.querySelector('.stats-card[data-status="all"] .h5').textContent = total;
            document.querySelector('.stats-card[data-status="verified"] .h5').textContent = verified;
            document.querySelector('.stats-card[data-status="not-verified"] .h5').textContent = notVerified;
            document.querySelector('.stats-card[data-status="rejected"] .h5').textContent = rejected;
            
            // Update progress bar
            const verifiedPercent = Math.round((verified / total) * 100);
            const notVerifiedPercent = Math.round((notVerified / total) * 100);
            const rejectedPercent = Math.round((rejected / total) * 100);
            
            document.querySelector('.progress-bar.bg-success').style.width = `${verifiedPercent}%`;
            document.querySelector('.progress-bar.bg-warning').style.width = `${notVerifiedPercent}%`;
            document.querySelector('.progress-bar.bg-danger').style.width = `${rejectedPercent}%`;
            
            // Update progress text
            document.querySelector('.progress').previousElementSibling.innerHTML = 
                `<span>Overall Verification Rate</span><span>${verified}/${total} (${verifiedPercent}%)</span>`;
            
            // Update tab badges
            document.querySelector('#verified-tab .badge').textContent = verified;
            document.querySelector('#not-verified-tab .badge').textContent = notVerified;
            document.querySelector('#rejected-tab .badge').textContent = rejected;
        }
        
        // User role detection (for demo purposes)
        const urlParams = new URLSearchParams(window.location.search);
        const userRole = urlParams.get('role') || 'admin';
        
        if (userRole === 'superadmin') {
            document.getElementById('user-role-display').textContent = 'Super Admin Dashboard';
            document.getElementById('username-display').textContent = 'Super Admin';
        }
    </script>
</body>
</html>