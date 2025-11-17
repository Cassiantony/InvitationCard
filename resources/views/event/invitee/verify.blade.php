<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Scanner - Invitation System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- QR Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
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
        
        .scanner-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        
        #qr-reader {
            width: 100%;
            border: 2px solid var(--primary-color);
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10;
        }
        
        .scan-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            height: 70%;
            border: 3px solid var(--secondary-color);
            border-radius: 0.5rem;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.4);
        }
        
        .scan-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--secondary-color);
            animation: scan 2s linear infinite;
        }
        
        @keyframes scan {
            0% { top: 0; }
            50% { top: 100%; }
            100% { top: 0; }
        }
        
        .scan-instructions {
            text-align: center;
            margin-top: 1rem;
            color: #6c757d;
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
        
        .badge-rejected {
            background-color: var(--danger-color);
            color: white;
        }
        
        .badge-sms {
            background-color: #6c757d;
            color: white;
        }
        
        .guest-info-card {
            border-left: 4px solid var(--primary-color);
        }
        
        .guest-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e3e6f0;
        }
        
        .stats-card {
            border-left: 0.25rem solid var(--primary-color);
        }
        
        .stats-card.scanned {
            border-left-color: var(--secondary-color);
        }
        
        .stats-card.pending {
            border-left-color: var(--warning-color);
        }
        
        .recent-scans {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .scan-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-bottom: 1px solid #e3e6f0;
            transition: background-color 0.3s;
        }
        
        .scan-item:hover {
            background-color: #f8f9fc;
        }
        
        .scan-item:last-child {
            border-bottom: none;
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
        
        .manual-entry {
            background-color: #f8f9fc;
            border-radius: 0.5rem;
            padding: 1.5rem;
        }
        
        .camera-selector {
            margin-bottom: 1rem;
        }
        
        .sms-code-input {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            letter-spacing: 0.5rem;
            padding: 0.75rem;
        }
        
        .verification-method-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 20;
        }
        
        .method-tabs .nav-link {
            color: #6c757d;
            font-weight: 600;
        }
        
        .method-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
        }
        
        .countdown-timer {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--danger-color);
        }
        
        .resend-code {
            font-size: 0.9rem;
            cursor: pointer;
            color: var(--primary-color);
        }
        
        .resend-code:hover {
            text-decoration: underline;
        }
        
        .resend-code.disabled {
            color: #6c757d;
            cursor: not-allowed;
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
                                Invitees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="fas fa-qrcode"></i>
                                QR Scanner
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
                        <h4 class="mb-0">Guest Verification</h4>
                        <div class="d-flex">
                            <a href="events.html" class="btn btn-outline-secondary me-2">
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
                                        <label class="form-label">Verification Method</label>
                                        <ul class="nav method-tabs">
                                            <li class="nav-item">
                                                <a class="nav-link active" href="#" data-method="qr">
                                                    <i class="fas fa-qrcode me-1"></i> QR Code
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#" data-method="sms">
                                                    <i class="fas fa-sms me-1"></i> SMS Code
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Invitees
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">245</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span>For this event</span>
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
                        <div class="card stats-card scanned h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Verified Today
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">42</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 12.3%</span>
                                            <span>Since morning</span>
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
                        <div class="card stats-card h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            SMS Guests
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">68</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-info mr-2"><i class="fas fa-mobile-alt"></i></span>
                                            <span>Via SMS invitation</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-sms fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card pending h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            No-Shows
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">7</div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-danger mr-2"><i class="fas fa-arrow-up"></i> 2.1%</span>
                                            <span>Confirmed but absent</span>
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
                
                <div class="row">
                    <!-- Scanner Section -->
                    <div class="col-lg-8">
                        <!-- QR Code Scanner -->
                        <div class="card shadow mb-4" id="qr-section">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-qrcode me-2"></i>QR Code Scanner
                                </h6>
                                <div>
                                    <select class="form-select form-select-sm camera-selector" id="camera-select">
                                        <option value="">Select Camera</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="scanner-container">
                                    <div id="qr-reader"></div>
                                    <div class="scanner-overlay">
                                        <div class="scan-frame">
                                            <div class="scan-line"></div>
                                        </div>
                                    </div>
                                    <span class="verification-method-badge verification-badge badge-verified">
                                        <i class="fas fa-qrcode me-1"></i> QR Code
                                    </span>
                                </div>
                                
                                <div class="scan-instructions">
                                    <p class="mb-2">
                                        <i class="fas fa-info-circle me-1 text-primary"></i>
                                        Point your camera at the QR code on the invitation card
                                    </p>
                                    <small class="text-muted">
                                        The scanner will automatically detect and verify the QR code
                                    </small>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <button class="btn btn-outline-secondary me-2" id="stop-scanner-btn">
                                        <i class="fas fa-stop me-1"></i> Stop Scanner
                                    </button>
                                    <button class="btn btn-primary" id="start-scanner-btn">
                                        <i class="fas fa-play me-1"></i> Start Scanner
                                    </button>
                                </div>
                                
                                <div class="mt-4">
                                    <h6 class="mb-3">Manual QR Entry</h6>
                                    <div class="manual-entry">
                                        <div class="mb-3">
                                            <label for="manual-qr-code" class="form-label">Enter QR Code</label>
                                            <input type="text" class="form-control" id="manual-qr-code" placeholder="Paste or type the QR code content">
                                        </div>
                                        <div class="d-grid">
                                            <button class="btn btn-primary" id="verify-qr-manual-btn">
                                                <i class="fas fa-check-circle me-1"></i> Verify QR Code
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SMS Code Verification -->
                        <div class="card shadow mb-4" id="sms-section" style="display: none;">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-sms me-2"></i>SMS Code Verification
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <i class="fas fa-mobile-alt text-primary" style="font-size: 4rem;"></i>
                                    <h4 class="mt-3">SMS Code Verification</h4>
                                    <p class="text-muted">Enter the 6-digit code sent via SMS to the guest</p>
                                </div>
                                
                                <div class="manual-entry">
                                    <div class="mb-3">
                                        <label for="sms-code" class="form-label">6-Digit SMS Code</label>
                                        <input type="text" class="form-control sms-code-input" id="sms-code" 
                                               placeholder="_ _ _ _ _ _" maxlength="6" pattern="[0-9]{6}">
                                        <div class="form-text">Enter the code exactly as received via SMS</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="guest-phone" class="form-label">Guest Phone Number (Optional)</label>
                                        <input type="tel" class="form-control" id="guest-phone" placeholder="+1 (555) 123-4567">
                                        <div class="form-text">Helps locate the guest if code verification fails</div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="countdown-timer" id="countdown-timer">Code expires in: 05:00</div>
                                        <div class="resend-code" id="resend-code">
                                            <i class="fas fa-redo me-1"></i> Resend Code
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button class="btn btn-primary" id="verify-sms-btn">
                                            <i class="fas fa-check-circle me-1"></i> Verify SMS Code
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <h6 class="mb-3">Recent SMS Verifications</h6>
                                    <div id="sms-entries-list">
                                        <p class="text-muted text-center py-3">No SMS verifications yet</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Verifications -->
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Verifications</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="recent-scans" id="recent-scans-list">
                                    <div class="scan-item">
                                        <div class="flex-shrink-0">
                                            <img src="https://via.placeholder.com/50" alt="User" class="rounded-circle user-avatar">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="mb-1">John Smith</h6>
                                                <span class="verification-badge badge-verified">
                                                    <i class="fas fa-qrcode me-1"></i> QR Code
                                                </span>
                                            </div>
                                            <p class="mb-1 text-muted small">Verified 5 minutes ago</p>
                                        </div>
                                    </div>
                                    
                                    <div class="scan-item">
                                        <div class="flex-shrink-0">
                                            <img src="https://via.placeholder.com/50" alt="User" class="rounded-circle user-avatar">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="mb-1">Sarah Johnson</h6>
                                                <span class="verification-badge badge-verified">
                                                    <i class="fas fa-sms me-1"></i> SMS Code
                                                </span>
                                            </div>
                                            <p class="mb-1 text-muted small">Verified 12 minutes ago</p>
                                        </div>
                                    </div>
                                    
                                    <div class="scan-item">
                                        <div class="flex-shrink-0">
                                            <img src="https://via.placeholder.com/50" alt="User" class="rounded-circle user-avatar">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="mb-1">Michael Brown</h6>
                                                <span class="verification-badge badge-pending">
                                                    <i class="fas fa-clock me-1"></i> Pending
                                                </span>
                                            </div>
                                            <p class="mb-1 text-muted small">Verified 25 minutes ago</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Verification Info -->
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Verification Status</h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <i class="fas fa-qrcode text-primary" style="font-size: 4rem;"></i>
                                    <h4 class="mt-3" id="status-title">Ready to Scan</h4>
                                    <p class="text-muted" id="status-description">Scanner is active and waiting for QR codes</p>
                                </div>
                                
                                <div class="guest-info-card p-3 mb-4" id="guest-info" style="display: none;">
                                    <!-- Guest info will be populated here -->
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="mb-3" id="tips-title">Scanning Tips</h6>
                                    <ul class="list-unstyled small" id="tips-list">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Ensure good lighting conditions
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Hold steady for 2-3 seconds
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Position QR code within the frame
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Avoid reflections and glare
                                        </li>
                                    </ul>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary" id="test-scan-btn">
                                        <i class="fas fa-vial me-1"></i> Test with Sample
                                    </button>
                                    <button class="btn btn-outline-secondary" id="clear-scans-btn">
                                        <i class="fas fa-eraser me-1"></i> Clear Recent
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">SMS Code Status</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Active SMS Codes</span>
                                        <span>12/68</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 18%"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Verified via SMS</span>
                                        <span>24</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 35%"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Expired Codes</span>
                                        <span>8</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 12%"></div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <button class="btn btn-sm btn-outline-info" id="send-test-sms-btn">
                                        <i class="fas fa-paper-plane me-1"></i> Send Test SMS
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Modal -->
    <div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verificationModalLabel">
                        <i class="fas fa-user-check me-2"></i>Invitee Verification
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="verification-success" style="display: none;">
                        <div class="text-center mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            <h3 class="text-success mt-3">Verification Successful!</h3>
                            <p class="text-muted" id="verification-method-text">Verified via QR Code</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <img src="https://via.placeholder.com/120" alt="Guest" class="guest-avatar mb-3" id="guest-avatar">
                                <h5 id="guest-name">John Smith</h5>
                                <p class="text-muted" id="guest-email">john.smith@example.com</p>
                                <span class="verification-badge" id="guest-verification-badge">
                                    <i class="fas fa-check me-1"></i> Verified
                                </span>
                            </div>
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Guest Information</h6>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="fw-bold" width="40%">Phone:</td>
                                                <td id="guest-phone">+1 (555) 123-4567</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Company:</td>
                                                <td id="guest-company">ABC Corporation</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Ticket Type:</td>
                                                <td id="guest-ticket">VIP</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Table Number:</td>
                                                <td id="guest-table">12</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Seat Number:</td>
                                                <td id="guest-seat">A5</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Invitation Method:</td>
                                                <td id="guest-invite-method">WhatsApp</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <div class="alert alert-success">
                                        <i class="fas fa-info-circle me-2"></i>
                                        This guest has been successfully verified and checked in.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="verification-failed" style="display: none;">
                        <div class="text-center mb-4">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                            <h3 class="text-warning mt-3">Verification Failed</h3>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-circle me-2"></i>Unable to verify this code</h6>
                            <p class="mb-0" id="failure-reason">The code is invalid or has already been used.</p>
                        </div>
                        
                        <div class="mt-3">
                            <h6>Possible reasons:</h6>
                            <ul>
                                <li>Code has already been used</li>
                                <li>Invalid or expired code</li>
                                <li>Guest is not on the invitee list</li>
                                <li>Event mismatch</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div id="verification-pending" style="display: none;">
                        <div class="text-center mb-4">
                            <i class="fas fa-clock text-info" style="font-size: 4rem;"></i>
                            <h3 class="text-info mt-3">Manual Verification Required</h3>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Additional verification needed</h6>
                            <p class="mb-0">This guest requires manual approval. Please check their identification.</p>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <button class="btn btn-success w-100" id="approve-guest-btn">
                                    <i class="fas fa-check me-1"></i> Approve Guest
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-danger w-100" id="reject-guest-btn">
                                    <i class="fas fa-times me-1"></i> Reject Guest
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="next-guest-btn">
                        <i class="fas fa-arrow-right me-1"></i> Next Guest
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // QR Scanner variables
        let html5QrcodeScanner = null;
        let isScannerActive = false;
        let recentScans = [];
        let currentMethod = 'qr'; // 'qr' or 'sms'
        let countdownTimer = null;
        let countdownSeconds = 300; // 5 minutes
        
        // Sample guest data for demonstration
        const guestDatabase = {
            "EVT001-GST123": {
                id: "GST123",
                name: "John Smith",
                email: "john.smith@example.com",
                phone: "+1 (555) 123-4567",
                company: "ABC Corporation",
                ticketType: "VIP",
                tableNumber: "12",
                seatNumber: "A5",
                status: "verified",
                avatar: "https://via.placeholder.com/120",
                inviteMethod: "whatsapp"
            },
            "EVT001-GST124": {
                id: "GST124",
                name: "Sarah Johnson",
                email: "sarah.j@example.com",
                phone: "+1 (555) 987-6543",
                company: "XYZ Inc",
                ticketType: "Standard",
                tableNumber: "8",
                seatNumber: "B2",
                status: "verified",
                avatar: "https://via.placeholder.com/120",
                inviteMethod: "sms"
            },
            "EVT001-GST125": {
                id: "GST125",
                name: "Michael Brown",
                email: "michael.b@example.com",
                phone: "+1 (555) 456-7890",
                company: "Tech Solutions",
                ticketType: "VIP",
                tableNumber: "5",
                seatNumber: "C1",
                status: "pending",
                avatar: "https://via.placeholder.com/120",
                inviteMethod: "whatsapp"
            },
            "EVT001-GST126": {
                id: "GST126",
                name: "Emily Davis",
                email: "emily.davis@example.com",
                phone: "+1 (555) 234-5678",
                company: "Global Enterprises",
                ticketType: "Standard",
                tableNumber: "15",
                seatNumber: "D4",
                status: "rejected",
                avatar: "https://via.placeholder.com/120",
                inviteMethod: "sms"
            }
        };
        
        // SMS codes database (in a real app, this would be server-side)
        const smsCodes = {
            "829174": { 
                guestId: "GST124", 
                phone: "+1 (555) 987-6543",
                expires: Date.now() + 300000, // 5 minutes from now
                used: false
            },
            "536281": { 
                guestId: "GST126", 
                phone: "+1 (555) 234-5678",
                expires: Date.now() + 300000,
                used: false
            },
            "472913": { 
                guestId: "GST128", 
                phone: "+1 (555) 345-6789",
                expires: Date.now() - 60000, // Expired 1 minute ago
                used: false
            }
        };
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            initializeScanner();
            updateRecentScansList();
            startCountdownTimer();
        });
        
        // Set up event listeners
        function setupEventListeners() {
            // Method selection
            document.querySelectorAll('.method-tabs .nav-link').forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    const method = this.getAttribute('data-method');
                    setActiveMethod(method);
                });
            });
            
            // Scanner controls
            document.getElementById('start-scanner-btn').addEventListener('click', startScanner);
            document.getElementById('stop-scanner-btn').addEventListener('click', stopScanner);
            
            // Manual verification
            document.getElementById('verify-qr-manual-btn').addEventListener('click', verifyManualQRCode);
            document.getElementById('verify-sms-btn').addEventListener('click', verifySMSCode);
            
            // SMS code input formatting
            document.getElementById('sms-code').addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').substring(0, 6);
            });
            
            // Test and utility buttons
            document.getElementById('test-scan-btn').addEventListener('click', simulateScan);
            document.getElementById('clear-scans-btn').addEventListener('click', clearRecentScans);
            document.getElementById('send-test-sms-btn').addEventListener('click', sendTestSMS);
            document.getElementById('resend-code').addEventListener('click', resendSMSCode);
            
            // Modal buttons
            document.getElementById('next-guest-btn').addEventListener('click', function() {
                const modal = bootstrap.Modal.getInstance(document.getElementById('verificationModal'));
                modal.hide();
                // Restart scanner after modal closes
                if (isScannerActive && currentMethod === 'qr') {
                    setTimeout(startScanner, 500);
                }
            });
            
            document.getElementById('approve-guest-btn').addEventListener('click', function() {
                alert('Guest approved manually!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('verificationModal'));
                modal.hide();
            });
            
            document.getElementById('reject-guest-btn').addEventListener('click', function() {
                alert('Guest rejected!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('verificationModal'));
                modal.hide();
            });
        }
        
        // Set active method (QR or SMS)
        function setActiveMethod(method) {
            currentMethod = method;
            
            // Update tab states
            document.querySelectorAll('.method-tabs .nav-link').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelector(`.method-tabs .nav-link[data-method="${method}"]`).classList.add('active');
            
            // Show/hide sections
            document.getElementById('qr-section').style.display = method === 'qr' ? 'block' : 'none';
            document.getElementById('sms-section').style.display = method === 'sms' ? 'block' : 'none';
            
            // Update status display
            if (method === 'qr') {
                document.getElementById('status-title').textContent = 'Ready to Scan';
                document.getElementById('status-description').textContent = 'Scanner is active and waiting for QR codes';
                document.getElementById('tips-title').textContent = 'Scanning Tips';
                document.getElementById('tips-list').innerHTML = `
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Ensure good lighting conditions
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Hold steady for 2-3 seconds
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Position QR code within the frame
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Avoid reflections and glare
                    </li>
                `;
                
                // Stop scanner if switching from SMS
                if (isScannerActive) {
                    stopScanner();
                }
                // Start scanner if switching to QR
                setTimeout(() => {
                    const cameraId = document.getElementById('camera-select').value;
                    if (cameraId) {
                        startScanner();
                    }
                }, 100);
                
            } else {
                document.getElementById('status-title').textContent = 'SMS Code Verification';
                document.getElementById('status-description').textContent = 'Enter the 6-digit code sent to the guest';
                document.getElementById('tips-title').textContent = 'SMS Verification Tips';
                document.getElementById('tips-list').innerHTML = `
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Codes are case-sensitive
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Codes expire after 5 minutes
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Each code can only be used once
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Ask guest to find the code in their SMS app
                    </li>
                `;
                
                // Stop scanner if switching to SMS
                if (isScannerActive) {
                    stopScanner();
                }
                
                // Reset SMS form
                document.getElementById('sms-code').value = '';
                document.getElementById('guest-phone').value = '';
            }
        }
        
        // Initialize QR scanner
        function initializeScanner() {
            // Get available cameras
            Html5Qrcode.getCameras().then(cameras => {
                const cameraSelect = document.getElementById('camera-select');
                cameraSelect.innerHTML = '<option value="">Select Camera</option>';
                
                if (cameras && cameras.length) {
                    cameras.forEach(camera => {
                        const option = document.createElement('option');
                        option.value = camera.id;
                        option.text = camera.label || `Camera ${cameraSelect.length}`;
                        cameraSelect.appendChild(option);
                    });
                    
                    // Auto-select first camera
                    if (cameras.length > 0) {
                        cameraSelect.value = cameras[0].id;
                    }
                } else {
                    console.error('No cameras found');
                }
            }).catch(err => {
                console.error('Error getting cameras:', err);
            });
            
            // Create scanner instance
            html5QrcodeScanner = new Html5Qrcode("qr-reader");
        }
        
        // Start QR scanner
        function startScanner() {
            const cameraId = document.getElementById('camera-select').value;
            
            if (!cameraId) {
                alert('Please select a camera first');
                return;
            }
            
            html5QrcodeScanner.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                onScanSuccess,
                onScanFailure
            ).then(() => {
                isScannerActive = true;
                document.getElementById('start-scanner-btn').disabled = true;
                document.getElementById('stop-scanner-btn').disabled = false;
                console.log('QR Scanner started');
            }).catch(err => {
                console.error('Error starting scanner:', err);
                alert('Error starting scanner: ' + err);
            });
        }
        
        // Stop QR scanner
        function stopScanner() {
            if (html5QrcodeScanner && isScannerActive) {
                html5QrcodeScanner.stop().then(() => {
                    isScannerActive = false;
                    document.getElementById('start-scanner-btn').disabled = false;
                    document.getElementById('stop-scanner-btn').disabled = true;
                    console.log('QR Scanner stopped');
                }).catch(err => {
                    console.error('Error stopping scanner:', err);
                });
            }
        }
        
        // Handle successful scan
        function onScanSuccess(decodedText, decodedResult) {
            console.log(`Scan result: ${decodedText}`, decodedResult);
            
            // Stop scanner temporarily
            stopScanner();
            
            // Process the scanned code
            processScannedCode(decodedText, 'qr');
        }
        
        // Handle scan failure
        function onScanFailure(error) {
            // This is expected to be called often for non-QR codes
            // We don't need to handle it unless we want specific error reporting
        }
        
        // Process scanned QR code
        function processScannedCode(code, method) {
            // Check if code exists in our database
            const guest = guestDatabase[code];
            
            if (guest) {
                // Add to recent scans
                addToRecentScans(guest, method);
                
                // Show verification modal
                showVerificationModal(guest, method);
            } else {
                // Show error modal
                showErrorModal('Invalid code. This code is not recognized in our system.', method);
            }
        }
        
        // Verify manual QR code entry
        function verifyManualQRCode() {
            const manualCode = document.getElementById('manual-qr-code').value.trim();
            
            if (!manualCode) {
                alert('Please enter a QR code to verify');
                return;
            }
            
            processScannedCode(manualCode, 'qr');
            document.getElementById('manual-qr-code').value = '';
        }
        
        // Verify SMS code
        function verifySMSCode() {
            const smsCode = document.getElementById('sms-code').value.trim();
            const guestPhone = document.getElementById('guest-phone').value.trim();
            
            if (!smsCode) {
                alert('Please enter the SMS code');
                return;
            }
            
            if (smsCode.length !== 6) {
                alert('Please enter a valid 6-digit code');
                return;
            }
            
            // Check if code exists and is valid
            const codeData = smsCodes[smsCode];
            
            if (!codeData) {
                showErrorModal('Invalid SMS code. Please check the code and try again.', 'sms');
                return;
            }
            
            if (codeData.used) {
                showErrorModal('This SMS code has already been used.', 'sms');
                return;
            }
            
            if (Date.now() > codeData.expires) {
                showErrorModal('This SMS code has expired. Please request a new code.', 'sms');
                return;
            }
            
            // Find guest by ID
            const guest = Object.values(guestDatabase).find(g => g.id === codeData.guestId);
            
            if (guest) {
                // Mark code as used
                codeData.used = true;
                
                // Add to recent scans
                addToRecentScans(guest, 'sms');
                
                // Show verification modal
                showVerificationModal(guest, 'sms');
                
                // Reset form
                document.getElementById('sms-code').value = '';
                document.getElementById('guest-phone').value = '';
            } else {
                showErrorModal('Unable to find guest associated with this code.', 'sms');
            }
        }
        
        // Show verification modal with guest info
        function showVerificationModal(guest, method) {
            // Hide all verification states first
            document.getElementById('verification-success').style.display = 'none';
            document.getElementById('verification-failed').style.display = 'none';
            document.getElementById('verification-pending').style.display = 'none';
            
            // Show appropriate state based on guest status
            if (guest.status === 'verified') {
                document.getElementById('verification-success').style.display = 'block';
                
                // Set verification method text
                const methodText = method === 'qr' ? 'QR Code' : 'SMS Code';
                document.getElementById('verification-method-text').textContent = `Verified via ${methodText}`;
                
                // Populate guest info
                document.getElementById('guest-name').textContent = guest.name;
                document.getElementById('guest-email').textContent = guest.email;
                document.getElementById('guest-phone').textContent = guest.phone;
                document.getElementById('guest-company').textContent = guest.company;
                document.getElementById('guest-ticket').textContent = guest.ticketType;
                document.getElementById('guest-table').textContent = guest.tableNumber;
                document.getElementById('guest-seat').textContent = guest.seatNumber;
                document.getElementById('guest-invite-method').textContent = 
                    guest.inviteMethod === 'sms' ? 'SMS' : 'WhatsApp';
                
                // Update guest avatar
                const avatar = document.getElementById('guest-avatar');
                if (avatar) avatar.src = guest.avatar;
                
                // Update verification badge
                const badge = document.getElementById('guest-verification-badge');
                badge.className = 'verification-badge badge-verified';
                badge.innerHTML = `<i class="fas fa-check me-1"></i> Verified via ${methodText}`;
                
            } else if (guest.status === 'pending') {
                document.getElementById('verification-pending').style.display = 'block';
            } else {
                document.getElementById('verification-failed').style.display = 'block';
                document.getElementById('failure-reason').textContent = 
                    'This guest has been rejected or is not approved to attend.';
            }
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('verificationModal'));
            modal.show();
        }
        
        // Show error modal
        function showErrorModal(message, method) {
            // Hide all verification states first
            document.getElementById('verification-success').style.display = 'none';
            document.getElementById('verification-failed').style.display = 'none';
            document.getElementById('verification-pending').style.display = 'none';
            
            // Show error state
            document.getElementById('verification-failed').style.display = 'block';
            document.getElementById('failure-reason').textContent = message;
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('verificationModal'));
            modal.show();
        }
        
        // Add scan to recent scans
        function addToRecentScans(guest, method) {
            const scan = {
                guest: guest,
                method: method,
                timestamp: new Date(),
                id: Date.now() // Simple ID generation
            };
            
            recentScans.unshift(scan); // Add to beginning of array
            if (recentScans.length > 10) {
                recentScans.pop(); // Keep only last 10 scans
            }
            
            updateRecentScansList();
        }
        
        // Update recent scans list
        function updateRecentScansList() {
            const container = document.getElementById('recent-scans-list');
            
            if (recentScans.length === 0) {
                container.innerHTML = '<p class="text-muted text-center py-3">No recent verifications</p>';
                return;
            }
            
            container.innerHTML = recentScans.map(scan => {
                const guest = scan.guest;
                const timeAgo = getTimeAgo(scan.timestamp);
                
                let statusBadge = '';
                if (guest.status === 'verified') {
                    if (scan.method === 'qr') {
                        statusBadge = '<span class="verification-badge badge-verified"><i class="fas fa-qrcode me-1"></i> QR Code</span>';
                    } else {
                        statusBadge = '<span class="verification-badge badge-verified"><i class="fas fa-sms me-1"></i> SMS Code</span>';
                    }
                } else if (guest.status === 'pending') {
                    statusBadge = '<span class="verification-badge badge-pending"><i class="fas fa-clock me-1"></i> Pending</span>';
                } else {
                    statusBadge = '<span class="verification-badge badge-rejected"><i class="fas fa-times me-1"></i> Rejected</span>';
                }
                
                return `
                    <div class="scan-item">
                        <div class="flex-shrink-0">
                            <img src="https://via.placeholder.com/50" alt="User" class="rounded-circle user-avatar">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="mb-1">${guest.name}</h6>
                                ${statusBadge}
                            </div>
                            <p class="mb-1 text-muted small">Verified ${timeAgo}</p>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Get time ago string
        function getTimeAgo(timestamp) {
            const now = new Date();
            const diffMs = now - timestamp;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            
            if (diffMins < 1) return 'just now';
            if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
            if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
            
            return timestamp.toLocaleDateString();
        }
        
        // Start countdown timer for SMS codes
        function startCountdownTimer() {
            clearInterval(countdownTimer);
            countdownSeconds = 300; // 5 minutes
            
            countdownTimer = setInterval(() => {
                countdownSeconds--;
                
                if (countdownSeconds <= 0) {
                    clearInterval(countdownTimer);
                    document.getElementById('countdown-timer').textContent = 'Code expired';
                    document.getElementById('countdown-timer').className = 'countdown-timer text-danger';
                    document.getElementById('resend-code').classList.remove('disabled');
                } else {
                    const minutes = Math.floor(countdownSeconds / 60);
                    const seconds = countdownSeconds % 60;
                    document.getElementById('countdown-timer').textContent = 
                        `Code expires in: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                }
            }, 1000);
        }
        
        // Resend SMS code
        function resendSMSCode() {
            const resendBtn = document.getElementById('resend-code');
            
            if (resendBtn.classList.contains('disabled')) {
                return;
            }
            
            // In a real app, this would call an API to resend the code
            alert('New SMS code has been sent to the guest');
            
            // Reset timer and disable resend button
            startCountdownTimer();
            resendBtn.classList.add('disabled');
            document.getElementById('countdown-timer').className = 'countdown-timer';
            
            // Re-enable resend button after 30 seconds
            setTimeout(() => {
                resendBtn.classList.remove('disabled');
            }, 30000);
        }
        
        // Send test SMS
        function sendTestSMS() {
            alert('Test SMS sent! In a real application, this would send an SMS with a verification code.');
        }
        
        // Simulate a scan for testing
        function simulateScan() {
            if (currentMethod === 'qr') {
                // Pick a random guest from the database
                const keys = Object.keys(guestDatabase);
                const randomKey = keys[Math.floor(Math.random() * keys.length)];
                processScannedCode(randomKey, 'qr');
            } else {
                // For SMS mode, use a valid test code
                processScannedCode('EVT001-GST124', 'sms');
            }
        }
        
        // Clear recent scans
        function clearRecentScans() {
            if (recentScans.length === 0) {
                alert('No verifications to clear');
                return;
            }
            
            if (confirm('Are you sure you want to clear all recent verifications?')) {
                recentScans = [];
                updateRecentScansList();
            }
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