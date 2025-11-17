<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation Status - Invitation System</title>
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
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .badge-delivered {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .badge-read {
            background-color: var(--primary-color);
            color: white;
        }
        
        .badge-confirmed {
            background-color: #17a2b8;
            color: white;
        }
        
        .badge-pending {
            background-color: var(--warning-color);
            color: white;
        }
        
        .badge-failed {
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
        
        .stats-card.delivered {
            border-left-color: var(--secondary-color);
        }
        
        .stats-card.read {
            border-left-color: var(--primary-color);
        }
        
        .stats-card.confirmed {
            border-left-color: #17a2b8;
        }
        
        .stats-card.pending {
            border-left-color: var(--warning-color);
        }
        
        .stats-card.failed {
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
        
        .timeline {
            position: relative;
            padding-left: 2rem;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 0.7rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e3e6f0;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -2rem;
            top: 0.25rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: var(--primary-color);
            border: 2px solid white;
        }
        
        .timeline-item.success::before {
            background-color: var(--secondary-color);
        }
        
        .timeline-item.warning::before {
            background-color: var(--warning-color);
        }
        
        .timeline-item.danger::before {
            background-color: var(--danger-color);
        }
        
        .time-badge {
            font-size: 0.75rem;
            background-color: #e9ecef;
            color: #6c757d;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        
        .status-indicator {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
        
        .dot-delivered {
            background-color: var(--secondary-color);
        }
        
        .dot-read {
            background-color: var(--primary-color);
        }
        
        .dot-confirmed {
            background-color: #17a2b8;
        }
        
        .dot-pending {
            background-color: var(--warning-color);
        }
        
        .dot-failed {
            background-color: var(--danger-color);
        }
        
        .method-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
        }
        
        .icon-whatsapp {
            background-color: #25D366;
            color: white;
        }
        
        .icon-sms {
            background-color: #6c757d;
            color: white;
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
                            <a class="nav-link" href="verification-status.html">
                                <i class="fas fa-clipboard-check"></i>
                                Verification Status
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="fas fa-paper-plane"></i>
                                Invitation Status
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
                        <h4 class="mb-0">Invitation Delivery Status</h4>
                        <div class="d-flex">
                            <a href="send-invitations.html" class="btn btn-primary me-2">
                                <i class="fas fa-paper-plane me-1"></i> Send More
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
                                            <button class="btn btn-outline-primary" id="resend-btn">
                                                <i class="fas fa-redo me-1"></i> Resend Failed
                                            </button>
                                            <button class="btn btn-outline-success" id="export-btn">
                                                <i class="fas fa-file-export me-1"></i> Export Report
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
                                            Total Sent
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">245</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span>100% of invitees</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card delivered h-100" data-status="delivered">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Delivered
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">218</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-success mr-2"><i class="fas fa-check"></i> 89%</span>
                                            <span>success rate</span>
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
                        <div class="card stats-card read h-100" data-status="read">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Read
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">189</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-primary mr-2"><i class="fas fa-eye"></i> 77%</span>
                                            <span>of delivered</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-eye fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card confirmed h-100" data-status="confirmed">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Confirmed
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">142</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-info mr-2"><i class="fas fa-user-check"></i> 58%</span>
                                            <span>attending</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Delivery Progress -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">Delivery Progress</h6>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Overall Delivery Success</span>
                            <span>218/245 (89%)</span>
                        </div>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 89%"></div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 8%"></div>
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 3%"></div>
                        </div>
                        
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h5 class="text-success mb-0">89%</h5>
                                    <small class="text-muted">Delivered</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h5 class="text-warning mb-0">8%</h5>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h5 class="text-danger mb-0">3%</h5>
                                    <small class="text-muted">Failed</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-primary mb-0">77%</h5>
                                <small class="text-muted">Read Rate</small>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="status-indicator">
                                <span class="status-dot dot-delivered"></span>
                                <small>Delivered - Invitation successfully sent to recipient</small>
                            </div>
                            <div class="status-indicator">
                                <span class="status-dot dot-read"></span>
                                <small>Read - Recipient has opened the invitation</small>
                            </div>
                            <div class="status-indicator">
                                <span class="status-dot dot-confirmed"></span>
                                <small>Confirmed - Recipient has accepted the invitation</small>
                            </div>
                            <div class="status-indicator">
                                <span class="status-dot dot-pending"></span>
                                <small>Pending - Invitation is being processed</small>
                            </div>
                            <div class="status-indicator">
                                <span class="status-dot dot-failed"></span>
                                <small>Failed - Invitation could not be delivered</small>
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
                                    <label for="status-filter" class="form-label">Delivery Status</label>
                                    <select class="form-select" id="status-filter">
                                        <option value="all">All Statuses</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="read">Read</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="pending">Pending</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="method-filter" class="form-label">Delivery Method</label>
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
                                    All Invitations
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="whatsapp-tab" data-bs-toggle="tab" data-bs-target="#whatsapp" type="button" role="tab">
                                    WhatsApp <span class="badge bg-success ms-1">187</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="sms-tab" data-bs-toggle="tab" data-bs-target="#sms" type="button" role="tab">
                                    SMS <span class="badge bg-secondary ms-1">58</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="failed-tab" data-bs-toggle="tab" data-bs-target="#failed" type="button" role="tab">
                                    Failed <span class="badge bg-danger ms-1">7</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="inviteeTabsContent">
                            <!-- All Invitations Tab -->
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
                            
                            <!-- WhatsApp Tab -->
                            <div class="tab-pane fade" id="whatsapp" role="tabpanel">
                                <div class="row" id="whatsapp-invitees-grid">
                                    <!-- WhatsApp invitees will be populated here -->
                                </div>
                                
                                <div class="card d-none" id="whatsapp-invitees-list">
                                    <div class="card-body p-0">
                                        <div class="list-view" id="whatsapp-invitees-list-content">
                                            <!-- List view content will be populated here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- SMS Tab -->
                            <div class="tab-pane fade" id="sms" role="tabpanel">
                                <div class="row" id="sms-invitees-grid">
                                    <!-- SMS invitees will be populated here -->
                                </div>
                                
                                <div class="card d-none" id="sms-invitees-list">
                                    <div class="card-body p-0">
                                        <div class="list-view" id="sms-invitees-list-content">
                                            <!-- List view content will be populated here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Failed Tab -->
                            <div class="tab-pane fade" id="failed" role="tabpanel">
                                <div class="row" id="failed-invitees-grid">
                                    <!-- Failed invitees will be populated here -->
                                </div>
                                
                                <div class="card d-none" id="failed-invitees-list">
                                    <div class="card-body p-0">
                                        <div class="list-view" id="failed-invitees-list-content">
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

    <!-- Invitation Detail Modal -->
    <div class="modal fade" id="invitationDetailModal" tabindex="-1" aria-labelledby="invitationDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invitationDetailModalLabel">
                        <i class="fas fa-paper-plane me-2"></i>Invitation Delivery Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="https://via.placeholder.com/120" alt="Guest" class="guest-avatar mb-3">
                            <h5 id="modal-guest-name">John Smith</h5>
                            <p class="text-muted" id="modal-guest-email">john.smith@example.com</p>
                            <span class="status-badge" id="modal-delivery-status">
                                <i class="fas fa-check me-1"></i> Delivered
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
                                            <td class="fw-bold">Invitation Method:</td>
                                            <td id="modal-delivery-method">WhatsApp</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Sent On:</td>
                                            <td id="modal-sent-time">Dec 10, 2023 14:30</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Delivery Timeline</h6>
                                    <div class="timeline">
                                        <div class="timeline-item success">
                                            <h6 class="mb-1">Invitation Sent</h6>
                                            <p class="mb-1 text-muted">Invitation was successfully sent to recipient</p>
                                            <small class="time-badge">Dec 10, 2023 14:30</small>
                                        </div>
                                        
                                        <div class="timeline-item success">
                                            <h6 class="mb-1">Delivered</h6>
                                            <p class="mb-1 text-muted">Invitation was delivered to recipient's device</p>
                                            <small class="time-badge">Dec 10, 2023 14:32</small>
                                        </div>
                                        
                                        <div class="timeline-item success">
                                            <h6 class="mb-1">Read</h6>
                                            <p class="mb-1 text-muted">Recipient opened and read the invitation</p>
                                            <small class="time-badge">Dec 10, 2023 15:45</small>
                                        </div>
                                        
                                        <div class="timeline-item success">
                                            <h6 class="mb-1">Confirmed</h6>
                                            <p class="mb-1 text-muted">Recipient accepted the invitation</p>
                                            <small class="time-badge">Dec 10, 2023 16:20</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="resend-invitation-btn">
                        <i class="fas fa-redo me-1"></i> Resend Invitation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sample invitee data with delivery status
        const invitees = [
            {
                id: 1,
                name: "John Smith",
                email: "john.smith@example.com",
                phone: "+1 (555) 123-4567",
                company: "ABC Corporation",
                deliveryMethod: "whatsapp",
                status: "confirmed",
                sentTime: new Date(Date.now() - 48 * 60 * 60000), // 2 days ago
                deliveredTime: new Date(Date.now() - 47 * 60 * 60000),
                readTime: new Date(Date.now() - 46 * 60 * 60000),
                confirmedTime: new Date(Date.now() - 45 * 60 * 60000),
                avatar: "https://via.placeholder.com/120"
            },
            {
                id: 2,
                name: "Sarah Johnson",
                email: "sarah.j@example.com",
                phone: "+1 (555) 987-6543",
                company: "XYZ Inc",
                deliveryMethod: "sms",
                status: "delivered",
                sentTime: new Date(Date.now() - 24 * 60 * 60000), // 1 day ago
                deliveredTime: new Date(Date.now() - 23 * 60 * 60000),
                avatar: "https://via.placeholder.com/120"
            },
            {
                id: 3,
                name: "Michael Brown",
                email: "michael.b@example.com",
                phone: "+1 (555) 456-7890",
                company: "Tech Solutions",
                deliveryMethod: "whatsapp",
                status: "read",
                sentTime: new Date(Date.now() - 12 * 60 * 60000), // 12 hours ago
                deliveredTime: new Date(Date.now() - 11 * 60 * 60000),
                readTime: new Date(Date.now() - 10 * 60 * 60000),
                avatar: "https://via.placeholder.com/120"
            },
            {
                id: 4,
                name: "Emily Davis",
                email: "emily.davis@example.com",
                phone: "+1 (555) 234-5678",
                company: "Global Enterprises",
                deliveryMethod: "sms",
                status: "pending",
                sentTime: new Date(Date.now() - 2 * 60 * 60000), // 2 hours ago
                avatar: "https://via.placeholder.com/120"
            },
            {
                id: 5,
                name: "Robert Wilson",
                email: "robert.w@example.com",
                phone: "+1 (555) 345-6789",
                company: "Innovation Labs",
                deliveryMethod: "whatsapp",
                status: "failed",
                sentTime: new Date(Date.now() - 6 * 60 * 60000), // 6 hours ago
                failureReason: "Invalid phone number",
                avatar: "https://via.placeholder.com/120"
            },
            {
                id: 6,
                name: "Jennifer Lee",
                email: "jennifer.lee@example.com",
                phone: "+1 (555) 567-8901",
                company: "Creative Minds",
                deliveryMethod: "whatsapp",
                status: "confirmed",
                sentTime: new Date(Date.now() - 36 * 60 * 60000), // 36 hours ago
                deliveredTime: new Date(Date.now() - 35 * 60 * 60000),
                readTime: new Date(Date.now() - 34 * 60 * 60000),
                confirmedTime: new Date(Date.now() - 33 * 60 * 60000),
                avatar: "https://via.placeholder.com/120"
            },
            {
                id: 7,
                name: "David Miller",
                email: "david.m@example.com",
                phone: "+1 (555) 678-9012",
                company: "Future Tech",
                deliveryMethod: "sms",
                status: "delivered",
                sentTime: new Date(Date.now() - 18 * 60 * 60000), // 18 hours ago
                deliveredTime: new Date(Date.now() - 17 * 60 * 60000),
                avatar: "https://via.placeholder.com/120"
            },
            {
                id: 8,
                name: "Amanda Taylor",
                email: "amanda.t@example.com",
                phone: "+1 (555) 789-0123",
                company: "Next Gen Solutions",
                deliveryMethod: "whatsapp",
                status: "read",
                sentTime: new Date(Date.now() - 8 * 60 * 60000), // 8 hours ago
                deliveredTime: new Date(Date.now() - 7 * 60 * 60000),
                readTime: new Date(Date.now() - 6 * 60 * 60000),
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
            document.getElementById('resend-btn').addEventListener('click', function() {
                alert('Resending failed invitations...');
            });
            
            document.getElementById('export-btn').addEventListener('click', function() {
                alert('Exporting delivery report...');
            });
            
            // Stats card clicks
            document.querySelectorAll('.stats-card').forEach(card => {
                card.addEventListener('click', function() {
                    const status = this.getAttribute('data-status');
                    filterByStatus(status);
                });
            });
            
            // Modal button
            document.getElementById('resend-invitation-btn').addEventListener('click', function() {
                alert('Invitation resent successfully!');
            });
        }
        
        // Set active view (grid or list)
        function setActiveView(view) {
            // Update button states
            document.getElementById('grid-view').classList.toggle('active', view === 'grid');
            document.getElementById('list-view').classList.toggle('active', view === 'list');
            
            // Update all tab views
            const tabs = ['all', 'whatsapp', 'sms', 'failed'];
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
            renderInviteesByTab('all', invitees);
            renderInviteesByTab('whatsapp', invitees.filter(i => i.deliveryMethod === 'whatsapp'));
            renderInviteesByTab('sms', invitees.filter(i => i.deliveryMethod === 'sms'));
            renderInviteesByTab('failed', invitees.filter(i => i.status === 'failed'));
        }
        
        // Render invitees by tab
        function renderInviteesByTab(tab, inviteeList) {
            const gridContainer = document.getElementById(`${tab}-invitees-grid`);
            const listContainer = document.getElementById(`${tab}-invitees-list-content`);
            
            if (inviteeList.length === 0) {
                gridContainer.innerHTML = `
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-user-friends"></i>
                            <h4>No invitations found</h4>
                            <p>There are no invitations matching your criteria.</p>
                        </div>
                    </div>
                `;
                listContainer.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-user-friends"></i>
                        <h4>No invitations found</h4>
                        <p>There are no invitations matching your criteria.</p>
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
                                ${getStatusBadge(invitee.status)}
                            </div>
                            <p class="mb-1 text-muted small">${invitee.email}</p>
                            <p class="mb-1 text-muted small">${invitee.phone}</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="method-icon ${invitee.deliveryMethod === 'whatsapp' ? 'icon-whatsapp' : 'icon-sms'}">
                                    <i class="fab ${invitee.deliveryMethod === 'whatsapp' ? 'fa-whatsapp' : 'fa-sms'}"></i>
                                </span>
                                <span class="time-badge">${getTimeAgo(invitee.sentTime)}</span>
                            </div>
                            <div class="invitee-actions mt-2">
                                <button class="btn btn-sm btn-outline-primary view-details" data-id="${invitee.id}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${invitee.status === 'failed' ? 
                                    `<button class="btn btn-sm btn-outline-success ms-1 resend-invite" data-id="${invitee.id}">
                                        <i class="fas fa-redo"></i>
                                    </button>` : 
                                    ''
                                }
                                <button class="btn btn-sm btn-outline-danger ms-1 remove-invite" data-id="${invitee.id}">
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
                                    ${invitee.company} • Sent ${getTimeAgo(invitee.sentTime)}
                                </p>
                            </div>
                            <div class="text-end">
                                ${getStatusBadge(invitee.status)}
                                <div class="mt-2">
                                    <span class="method-icon ${invitee.deliveryMethod === 'whatsapp' ? 'icon-whatsapp' : 'icon-sms'}">
                                        <i class="fab ${invitee.deliveryMethod === 'whatsapp' ? 'fa-whatsapp' : 'fa-sms'}"></i>
                                    </span>
                                    ${invitee.deliveredTime ? 
                                        `<span class="time-badge ms-1">Delivered ${getTimeAgo(invitee.deliveredTime)}</span>` : 
                                        ''
                                    }
                                </div>
                            </div>
                        </div>
                        <div class="invitee-actions mt-2">
                            <button class="btn btn-sm btn-outline-primary view-details" data-id="${invitee.id}">
                                <i class="fas fa-eye me-1"></i> Details
                            </button>
                            ${invitee.status === 'failed' ? 
                                `<button class="btn btn-sm btn-outline-success ms-1 resend-invite" data-id="${invitee.id}">
                                    <i class="fas fa-redo me-1"></i> Resend
                                </button>` : 
                                ''
                            }
                            <button class="btn btn-sm btn-outline-danger ms-1 remove-invite" data-id="${invitee.id}">
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
                    showInvitationDetails(id);
                });
            });
            
            document.querySelectorAll('.resend-invite').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    resendInvitation(id);
                });
            });
            
            document.querySelectorAll('.remove-invite').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    removeInvitation(id);
                });
            });
        }
        
        // Get status badge HTML
        function getStatusBadge(status) {
            let badgeClass = '';
            let badgeIcon = '';
            let badgeText = '';
            
            switch(status) {
                case 'delivered':
                    badgeClass = 'badge-delivered';
                    badgeIcon = 'fa-check';
                    badgeText = 'Delivered';
                    break;
                case 'read':
                    badgeClass = 'badge-read';
                    badgeIcon = 'fa-eye';
                    badgeText = 'Read';
                    break;
                case 'confirmed':
                    badgeClass = 'badge-confirmed';
                    badgeIcon = 'fa-user-check';
                    badgeText = 'Confirmed';
                    break;
                case 'pending':
                    badgeClass = 'badge-pending';
                    badgeIcon = 'fa-clock';
                    badgeText = 'Pending';
                    break;
                case 'failed':
                    badgeClass = 'badge-failed';
                    badgeIcon = 'fa-times';
                    badgeText = 'Failed';
                    break;
            }
            
            return `<span class="status-badge ${badgeClass}">
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
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 1) return 'just now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;
            
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
                filteredInvitees = filteredInvitees.filter(i => i.deliveryMethod === methodFilter);
            }
            
            // Apply search filter
            if (searchTerm) {
                filteredInvitees = filteredInvitees.filter(i => 
                    i.name.toLowerCase().includes(searchTerm) ||
                    i.email.toLowerCase().includes(searchTerm) ||
                    i.phone.includes(searchTerm)
                );
            }
            
            renderInviteesByTab('all', filteredInvitees);
            renderInviteesByTab('whatsapp', filteredInvitees.filter(i => i.deliveryMethod === 'whatsapp'));
            renderInviteesByTab('sms', filteredInvitees.filter(i => i.deliveryMethod === 'sms'));
            renderInviteesByTab('failed', filteredInvitees.filter(i => i.status === 'failed'));
        }
        
        // Filter by status (from stats cards)
        function filterByStatus(status) {
            if (status === 'all') {
                document.getElementById('status-filter').value = 'all';
            } else {
                document.getElementById('status-filter').value = status;
            }
            
            applyFilters();
        }
        
        // Show invitation details in modal
        function showInvitationDetails(id) {
            const invitee = invitees.find(i => i.id === id);
            if (!invitee) return;
            
            // Populate modal with invitee data
            document.getElementById('modal-guest-name').textContent = invitee.name;
            document.getElementById('modal-guest-email').textContent = invitee.email;
            document.getElementById('modal-guest-phone').textContent = invitee.phone;
            document.getElementById('modal-guest-company').textContent = invitee.company;
            document.getElementById('modal-delivery-method').textContent = 
                invitee.deliveryMethod === 'whatsapp' ? 'WhatsApp' : 'SMS';
            document.getElementById('modal-sent-time').textContent = 
                invitee.sentTime.toLocaleString();
            
            // Update delivery status badge in modal
            const badge = document.getElementById('modal-delivery-status');
            badge.className = 'status-badge ' + 
                (invitee.status === 'delivered' ? 'badge-delivered' : 
                 invitee.status === 'read' ? 'badge-read' : 
                 invitee.status === 'confirmed' ? 'badge-confirmed' : 
                 invitee.status === 'pending' ? 'badge-pending' : 'badge-failed');
            badge.innerHTML = `<i class="fas ${invitee.status === 'delivered' ? 'fa-check' : 
                invitee.status === 'read' ? 'fa-eye' : 
                invitee.status === 'confirmed' ? 'fa-user-check' : 
                invitee.status === 'pending' ? 'fa-clock' : 'fa-times'} me-1"></i> ${badge.textContent}`;
            
            // Update timeline based on status
            const timeline = document.querySelector('.timeline');
            timeline.innerHTML = '';
            
            // Always show sent event
            timeline.innerHTML += `
                <div class="timeline-item success">
                    <h6 class="mb-1">Invitation Sent</h6>
                    <p class="mb-1 text-muted">Invitation was successfully sent to recipient</p>
                    <small class="time-badge">${invitee.sentTime.toLocaleString()}</small>
                </div>
            `;
            
            // Show delivered if available
            if (invitee.deliveredTime) {
                timeline.innerHTML += `
                    <div class="timeline-item ${invitee.status !== 'failed' ? 'success' : 'danger'}">
                        <h6 class="mb-1">${invitee.status === 'failed' ? 'Delivery Failed' : 'Delivered'}</h6>
                        <p class="mb-1 text-muted">${invitee.status === 'failed' ? 
                            (invitee.failureReason || 'Invitation could not be delivered') : 
                            'Invitation was delivered to recipient\'s device'}</p>
                        <small class="time-badge">${invitee.deliveredTime.toLocaleString()}</small>
                    </div>
                `;
            }
            
            // Show read if available (WhatsApp only)
            if (invitee.readTime && invitee.deliveryMethod === 'whatsapp') {
                timeline.innerHTML += `
                    <div class="timeline-item success">
                        <h6 class="mb-1">Read</h6>
                        <p class="mb-1 text-muted">Recipient opened and read the invitation</p>
                        <small class="time-badge">${invitee.readTime.toLocaleString()}</small>
                    </div>
                `;
            }
            
            // Show confirmed if available
            if (invitee.confirmedTime) {
                timeline.innerHTML += `
                    <div class="timeline-item success">
                        <h6 class="mb-1">Confirmed</h6>
                        <p class="mb-1 text-muted">Recipient accepted the invitation</p>
                        <small class="time-badge">${invitee.confirmedTime.toLocaleString()}</small>
                    </div>
                `;
            }
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('invitationDetailModal'));
            modal.show();
        }
        
        // Resend an invitation
        function resendInvitation(id) {
            if (confirm('Resend this invitation?')) {
                // In a real app, this would call an API to resend
                const invitee = invitees.find(i => i.id === id);
                if (invitee) {
                    invitee.status = 'pending';
                    invitee.sentTime = new Date();
                    
                    renderAllInvitees();
                    updateStatistics();
                    alert('Invitation has been resent!');
                }
            }
        }
        
        // Remove an invitation
        function removeInvitation(id) {
            if (confirm('Are you sure you want to remove this invitation?')) {
                // In a real app, this would update the database
                const index = invitees.findIndex(i => i.id === id);
                if (index !== -1) {
                    invitees.splice(index, 1);
                    renderAllInvitees();
                    updateStatistics();
                    alert('Invitation removed!');
                }
            }
        }
        
        // Update statistics
        function updateStatistics() {
            const total = invitees.length;
            const delivered = invitees.filter(i => i.status === 'delivered' || i.status === 'read' || i.status === 'confirmed').length;
            const read = invitees.filter(i => i.status === 'read' || i.status === 'confirmed').length;
            const confirmed = invitees.filter(i => i.status === 'confirmed').length;
            const pending = invitees.filter(i => i.status === 'pending').length;
            const failed = invitees.filter(i => i.status === 'failed').length;
            
            // Update stats cards
            document.querySelector('.stats-card[data-status="all"] .h5').textContent = total;
            document.querySelector('.stats-card[data-status="delivered"] .h5').textContent = delivered;
            document.querySelector('.stats-card[data-status="read"] .h5').textContent = read;
            document.querySelector('.stats-card[data-status="confirmed"] .h5').textContent = confirmed;
            
            // Update progress bar
            const deliveredPercent = Math.round((delivered / total) * 100);
            const pendingPercent = Math.round((pending / total) * 100);
            const failedPercent = Math.round((failed / total) * 100);
            
            document.querySelector('.progress-bar.bg-success').style.width = `${deliveredPercent}%`;
            document.querySelector('.progress-bar.bg-warning').style.width = `${pendingPercent}%`;
            document.querySelector('.progress-bar.bg-danger').style.width = `${failedPercent}%`;
            
            // Update progress text
            document.querySelector('.progress').previousElementSibling.innerHTML = 
                `<span>Overall Delivery Success</span><span>${delivered}/${total} (${deliveredPercent}%)</span>`;
            
            // Update tab badges
            const whatsappCount = invitees.filter(i => i.deliveryMethod === 'whatsapp').length;
            const smsCount = invitees.filter(i => i.deliveryMethod === 'sms').length;
            
            document.querySelector('#whatsapp-tab .badge').textContent = whatsappCount;
            document.querySelector('#sms-tab .badge').textContent = smsCount;
            document.querySelector('#failed-tab .badge').textContent = failed;
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