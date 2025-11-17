<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Invitees - Invitation System</title>
    
    <!-- Add CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
        
        .invitee-item.new {
            border-color: var(--secondary-color);
            background-color: rgba(28, 200, 138, 0.05);
        }
        
        .invitee-item.existing {
            border-color: var(--warning-color);
            background-color: rgba(246, 194, 62, 0.05);
        }
        
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background-color: #f8f9fc;
        }
        
        .upload-area:hover {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .upload-area.dragover {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.1);
        }
        
        .upload-icon {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
        
        .tab-content {
            min-height: 400px;
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
        
        .preview-table {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-new {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .badge-existing {
            background-color: var(--warning-color);
            color: white;
        }
        
        .badge-error {
            background-color: var(--danger-color);
            color: white;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e3e6f0;
            z-index: 1;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        
        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e3e6f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .step.active .step-number {
            background-color: var(--primary-color);
            color: white;
        }
        
        .step.completed .step-number {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .step-label {
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .template-table {
            font-size: 0.875rem;
        }
        
        .template-table th {
            background-color: #f8f9fc;
            position: sticky;
            top: 0;
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
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
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
                            <a class="nav-link active" href="{{ route('invitee.create') }}">
                                <i class="fas fa-user-plus"></i>
                                Add Invitees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-user-friends"></i>
                                All Invitees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-paper-plane"></i>
                                Send Invitations
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
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" 
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Sign out
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
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
                        <h4 class="mb-0">Add Invitees</h4>
                        <div class="d-flex">
                            <a href="{{ route('event.index') }}" class="btn btn-outline-secondary me-2">
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
                                <select class="form-select" id="event-select" name="event_id">
                                    <option value="">Select an event</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}">
                                            {{ $event->title }} ({{ \Carbon\Carbon::parse($event->date)->format('d M, Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-end h-100">
                                    <div class="w-100">
                                        <label class="form-label">Add Method</label>
                                        <div class="d-grid gap-2 d-md-flex">
                                            <button class="btn btn-outline-primary active" id="excel-method-btn">
                                                <i class="fas fa-file-excel me-1"></i> Import Excel
                                            </button>
                                            <button class="btn btn-outline-primary" id="manual-method-btn">
                                                <i class="fas fa-keyboard me-1"></i> Manual Entry
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-label">Add Invitees</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-label">Review & Confirm</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-label">Complete</div>
                    </div>
                </div>
                
                <!-- Step 1: Add Invitees -->
                <div class="form-step active" id="step-1">
                    <!-- Excel Import Section -->
                    <div class="card shadow mb-4" id="excel-section">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-file-excel me-2 text-success"></i>Import from Excel
                            </h6>
                            <a href="{{ route('download-template') }}" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="upload-area" id="upload-area">
                                        <div class="upload-icon">
                                            <i class="fas fa-file-excel"></i>
                                        </div>
                                        <h5>Drop your Excel file here</h5>
                                        <p class="text-muted">or click to browse</p>
                                        <input type="file" id="excel-file" accept=".xlsx, .xls, .csv" class="d-none">
                                        <button class="btn btn-primary mt-2" id="browse-btn">
                                            <i class="fas fa-search me-1"></i> Browse Files
                                        </button>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <strong>Supported formats:</strong> .xlsx, .xls, .csv<br>
                                            <strong>Maximum file size:</strong> 10MB<br>
                                            <strong>Template format:</strong> Name, Email, Phone, Company (optional)
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <h6 class="mb-3">Excel Template Example</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered template-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Company</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>John Smith</td>
                                                    <td>john@example.com</td>
                                                    <td>+1234567890</td>
                                                    <td>ABC Corp</td>
                                                </tr>
                                                <tr>
                                                    <td>Sarah Johnson</td>
                                                    <td>sarah@example.com</td>
                                                    <td>+1234567891</td>
                                                    <td>XYZ Inc</td>
                                                </tr>
                                                <tr>
                                                    <td>Michael Brown</td>
                                                    <td>michael@example.com</td>
                                                    <td>+1234567892</td>
                                                    <td>Tech Solutions</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4" id="upload-progress" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Processing file...</span>
                                    <span id="progress-percent">0%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" id="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                            
                            <div class="mt-4" id="upload-results" style="display: none;">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Success!</strong> 
                                    <span id="imported-count">0</span> invitees imported successfully.
                                    <span id="duplicate-count" class="ms-2">0 duplicates found.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Manual Entry Section -->
                    <div class="card shadow mb-4" id="manual-section" style="display: none;">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-keyboard me-2"></i>Manual Entry
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="invitee-name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="invitee-name" placeholder="Enter full name">
                                </div>
                                <div class="col-md-4">
                                    <label for="invitee-email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="invitee-email" placeholder="Enter email">
                                </div>
                                <div class="col-md-4">
                                    <label for="invitee-phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="invitee-phone" placeholder="Enter phone number">
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="invitee-company" class="form-label">Company</label>
                                    <input type="text" class="form-control" id="invitee-company" placeholder="Enter company name">
                                </div>
                                <div class="col-md-6">
                                    <label for="invitee-notes" class="form-label">Notes</label>
                                    <input type="text" class="form-control" id="invitee-notes" placeholder="Additional notes">
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-outline-secondary" id="clear-form-btn">
                                    <i class="fas fa-eraser me-1"></i> Clear Form
                                </button>
                                <button class="btn btn-success" id="add-invitee-btn">
                                    <i class="fas fa-plus me-1"></i> Add Invitee
                                </button>
                            </div>
                            
                            <div class="mt-4">
                                <h6 class="mb-3">Added Invitees</h6>
                                <div id="manual-invitees-list">
                                    <p class="text-muted text-center py-3">No invitees added yet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-primary" id="next-to-step-2" disabled>
                            Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 2: Review & Confirm -->
                <div class="form-step" id="step-2" style="display: none;">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Review Invitees</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Please review the invitees below before adding them to your event.
                            </div>
                            
                            <div class="table-responsive preview-table">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="select-all-review">
                                                </div>
                                            </th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Company</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="review-invitees-list">
                                        <!-- Invitees will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <span id="selected-review-count">0</span> of <span id="total-review-count">0</span> invitees selected
                                </div>
                                <div>
                                    <button class="btn btn-outline-danger btn-sm" id="remove-selected-btn">
                                        <i class="fas fa-trash me-1"></i> Remove Selected
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-outline-secondary" id="back-to-step-1">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </button>
                        <button class="btn btn-success" id="confirm-add-btn">
                            <i class="fas fa-check me-1"></i> Add to Event
                        </button>
                    </div>
                </div>
                
                <!-- Step 3: Complete -->
                <div class="form-step" id="step-3" style="display: none;">
                    <div class="card shadow">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-success mb-3">Invitees Added Successfully!</h3>
                            <p class="text-muted mb-4">
                                You have successfully added <strong id="final-count">0</strong> invitees to your event.
                            </p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i> Send Invitations
                                </a>
                                <a href="#" class="btn btn-outline-secondary">
                                    <i class="fas fa-calendar me-1"></i> Back to Events
                                </a>
                                <button class="btn btn-outline-primary" id="add-more-btn">
                                    <i class="fas fa-plus me-1"></i> Add More Invitees
                                </button>
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
        // Data storage
        let invitees = [];
        let selectedReviewInvitees = new Set();
        let currentMethod = 'excel'; // 'excel' or 'manual'
        
        // Get CSRF token safely
        function getCsrfToken() {
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            return csrfMeta ? csrfMeta.getAttribute('content') : '';
        }
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            updateNextButton();
        });
        
        // Set up event listeners
        function setupEventListeners() {
            // Method selection
            document.getElementById('excel-method-btn').addEventListener('click', function() {
                setActiveMethod('excel');
            });
            
            document.getElementById('manual-method-btn').addEventListener('click', function() {
                setActiveMethod('manual');
            });
            
            // Excel upload
            document.getElementById('browse-btn').addEventListener('click', function() {
                document.getElementById('excel-file').click();
            });
            
            document.getElementById('excel-file').addEventListener('change', handleFileUpload);
            
            // Drag and drop for Excel upload
            const uploadArea = document.getElementById('upload-area');
            if (uploadArea) {
                uploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    uploadArea.classList.add('dragover');
                });
                
                uploadArea.addEventListener('dragleave', function() {
                    uploadArea.classList.remove('dragover');
                });
                
                uploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                    
                    if (e.dataTransfer.files.length) {
                        document.getElementById('excel-file').files = e.dataTransfer.files;
                        handleFileUpload();
                    }
                });
            }
            
            // Manual entry
            document.getElementById('add-invitee-btn').addEventListener('click', addManualInvitee);
            document.getElementById('clear-form-btn').addEventListener('click', clearManualForm);
            
            // Step navigation
            document.getElementById('next-to-step-2').addEventListener('click', function() {
                navigateToStep(2);
                renderReviewList();
            });
            
            document.getElementById('back-to-step-1').addEventListener('click', function() {
                navigateToStep(1);
            });
            
            document.getElementById('confirm-add-btn').addEventListener('click', confirmAddInvitees);
            
            document.getElementById('add-more-btn').addEventListener('click', function() {
                // Reset and go back to step 1
                invitees = [];
                selectedReviewInvitees.clear();
                navigateToStep(1);
                renderManualInviteesList();
                updateNextButton();
            });
            
            // Review list interactions
            const selectAllReview = document.getElementById('select-all-review');
            if (selectAllReview) {
                selectAllReview.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.review-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                        const id = checkbox.value;
                        if (this.checked) {
                            selectedReviewInvitees.add(id);
                        } else {
                            selectedReviewInvitees.delete(id);
                        }
                    });
                    updateReviewSelectionCount();
                });
            }
            
            const removeSelectedBtn = document.getElementById('remove-selected-btn');
            if (removeSelectedBtn) {
                removeSelectedBtn.addEventListener('click', function() {
                    if (selectedReviewInvitees.size === 0) {
                        alert('Please select at least one invitee to remove.');
                        return;
                    }
                    
                    if (confirm(`Are you sure you want to remove ${selectedReviewInvitees.size} invitee(s)?`)) {
                        // Remove selected invitees
                        invitees = invitees.filter(invitee => !selectedReviewInvitees.has(invitee.id));
                        selectedReviewInvitees.clear();
                        renderReviewList();
                        updateNextButton();
                    }
                });
            }
        }
        
        // Set active method (Excel or Manual)
        function setActiveMethod(method) {
            currentMethod = method;
            
            // Update button states
            document.getElementById('excel-method-btn').classList.toggle('active', method === 'excel');
            document.getElementById('manual-method-btn').classList.toggle('active', method === 'manual');
            
            // Show/hide sections
            document.getElementById('excel-section').style.display = method === 'excel' ? 'block' : 'none';
            document.getElementById('manual-section').style.display = method === 'manual' ? 'block' : 'none';
            
            // Update next button state
            updateNextButton();
        }
        
        // Handle Excel file upload
        function handleFileUpload() {
            const fileInput = document.getElementById('excel-file');
            const file = fileInput.files[0];
            
            if (!file) return;
            
            // Validate file type
            const validTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'];
            if (!validTypes.includes(file.type) && !file.name.endsWith('.xlsx') && !file.name.endsWith('.xls') && !file.name.endsWith('.csv')) {
                alert('Please upload a valid Excel or CSV file.');
                return;
            }
            
            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                alert('File size must be less than 10MB.');
                return;
            }
            
            // Show progress
            const progressDiv = document.getElementById('upload-progress');
            const progressBar = document.getElementById('progress-bar');
            const progressPercent = document.getElementById('progress-percent');
            
            if (progressDiv) progressDiv.style.display = 'block';
            if (progressBar) progressBar.style.width = '0%';
            if (progressPercent) progressPercent.textContent = '0%';
            
            // Simulate file processing
            let progress = 0;
            const interval = setInterval(() => {
                progress += 5;
                if (progressBar) progressBar.style.width = `${progress}%`;
                if (progressPercent) progressPercent.textContent = `${progress}%`;
                
                if (progress >= 100) {
                    clearInterval(interval);
                    
                    // Process the file
                    setTimeout(() => {
                        processExcelFile(file);
                    }, 500);
                }
            }, 100);
        }
        
        // Process Excel file
        async function processExcelFile(file) {
            const formData = new FormData();
            formData.append('excel_file', file);
            formData.append('event_id', document.getElementById('event-select').value);

            try {
                const response = await fetch("{{ route('upload-invitees-excel') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                // Hide progress
                const progressDiv = document.getElementById('upload-progress');
                if (progressDiv) progressDiv.style.display = 'none';
                
                if (result.success) {
                    // Add the imported invitees to our list
                    invitees = [...invitees, ...result.results.invitees];
                    
                    // Update UI
                    const importedCount = document.getElementById('imported-count');
                    const duplicateCount = document.getElementById('duplicate-count');
                    const uploadResults = document.getElementById('upload-results');
                    
                    if (importedCount) importedCount.textContent = result.results.successful;
                    if (duplicateCount) duplicateCount.textContent = `${result.results.duplicates} duplicates found.`;
                    if (uploadResults) uploadResults.style.display = 'block';
                    
                    updateNextButton();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                // Hide progress
                const progressDiv = document.getElementById('upload-progress');
                if (progressDiv) progressDiv.style.display = 'none';
                
                console.error('Upload error:', error);
                alert('Error uploading file: ' + error.message);
            }
        }
        
        // Add manual invitee
        async function addManualInvitee() {
            const name = document.getElementById('invitee-name').value.trim();
            const email = document.getElementById('invitee-email').value.trim();
            const phone = document.getElementById('invitee-phone').value.trim();
            const company = document.getElementById('invitee-company').value.trim();
            const notes = document.getElementById('invitee-notes').value.trim();
            const eventId = document.getElementById('event-select').value;

            // Basic validation
            if (!name || !email) {
                alert('Please fill in at least name and email fields.');
                return;
            }

            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address.');
                return;
            }

            if (!eventId) {
                alert('Please select an event first.');
                return;
            }

            const formData = {
                name: name,
                email: email,
                phone: phone,
                company: company,
                notes: notes,
                event_id: eventId
            };

            try {
                const response = await fetch("{{ route('store-manual-invitees') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    invitees.push(result.invitee);
                    renderManualInviteesList();
                    clearManualForm();
                    updateNextButton();
                } else {
                    alert('Error: ' + (result.message || Object.values(result.errors || {}).flat().join(', ')));
                }
            } catch (error) {
                console.error('Add invitee error:', error);
                alert('Error adding invitee: ' + error.message);
            }
        }
        
        // Clear manual form
        function clearManualForm() {
            document.getElementById('invitee-name').value = '';
            document.getElementById('invitee-email').value = '';
            document.getElementById('invitee-phone').value = '';
            document.getElementById('invitee-company').value = '';
            document.getElementById('invitee-notes').value = '';
        }
        
        // Render manual invitees list
        function renderManualInviteesList() {
            const container = document.getElementById('manual-invitees-list');
            if (!container) return;
            
            if (invitees.length === 0) {
                container.innerHTML = '<p class="text-muted text-center py-3">No invitees added yet</p>';
                return;
            }
            
            // Filter only manually added invitees (for this session)
            const manualInvitees = invitees.filter(inv => inv.id && inv.id.startsWith('temp_'));
            
            if (manualInvitees.length === 0) {
                container.innerHTML = '<p class="text-muted text-center py-3">No invitees added yet</p>';
                return;
            }
            
            container.innerHTML = manualInvitees.map(invitee => `
                <div class="invitee-item ${invitee.status === 'new' ? 'new' : 'existing'}">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${invitee.name}</h6>
                                <p class="mb-1 text-muted small">${invitee.email}</p>
                                <p class="mb-0 text-muted small">${invitee.phone || 'No phone'}</p>
                            </div>
                            <div>
                                <span class="status-badge ${invitee.status === 'new' ? 'badge-new' : 'badge-existing'}">
                                    <i class="fas ${invitee.status === 'new' ? 'fa-plus' : 'fa-user'} me-1"></i>
                                    ${invitee.status === 'new' ? 'New' : 'Existing'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        // Render review list
        function renderReviewList() {
            const container = document.getElementById('review-invitees-list');
            const totalCount = document.getElementById('total-review-count');
            
            if (!container) return;
            
            if (invitees.length === 0) {
                container.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">No invitees to review</td></tr>';
                if (totalCount) totalCount.textContent = '0';
                return;
            }
            
            container.innerHTML = invitees.map(invitee => `
                <tr>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input review-checkbox" type="checkbox" value="${invitee.id}" 
                                   ${selectedReviewInvitees.has(invitee.id) ? 'checked' : ''}>
                        </div>
                    </td>
                    <td>${invitee.name}</td>
                    <td>${invitee.email}</td>
                    <td>${invitee.phone || '-'}</td>
                    <td>${invitee.company || '-'}</td>
                    <td>
                        <span class="status-badge ${invitee.status === 'new' ? 'badge-new' : 'badge-existing'}">
                            <i class="fas ${invitee.status === 'new' ? 'fa-plus' : 'fa-user'} me-1"></i>
                            ${invitee.status === 'new' ? 'New' : 'Existing'}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger remove-invitee" data-id="${invitee.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            
            if (totalCount) totalCount.textContent = invitees.length;
            updateReviewSelectionCount();
            
            // Add event listeners to checkboxes and remove buttons
            document.querySelectorAll('.review-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const id = this.value;
                    if (this.checked) {
                        selectedReviewInvitees.add(id);
                    } else {
                        selectedReviewInvitees.delete(id);
                    }
                    updateReviewSelectionCount();
                });
            });
            
            document.querySelectorAll('.remove-invitee').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    if (confirm('Are you sure you want to remove this invitee?')) {
                        invitees = invitees.filter(inv => inv.id !== id);
                        selectedReviewInvitees.delete(id);
                        renderReviewList();
                        updateNextButton();
                    }
                });
            });
        }
        
        // Update review selection count
        function updateReviewSelectionCount() {
            const selectedCount = document.getElementById('selected-review-count');
            if (selectedCount) selectedCount.textContent = selectedReviewInvitees.size;
            
            // Update select all checkbox
            const selectAll = document.getElementById('select-all-review');
            const checkboxes = document.querySelectorAll('.review-checkbox');
            
            if (checkboxes.length === 0) {
                if (selectAll) {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                }
            } else if (selectedReviewInvitees.size === checkboxes.length) {
                if (selectAll) {
                    selectAll.checked = true;
                    selectAll.indeterminate = false;
                }
            } else if (selectedReviewInvitees.size > 0) {
                if (selectAll) {
                    selectAll.checked = false;
                    selectAll.indeterminate = true;
                }
            } else {
                if (selectAll) {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                }
            }
        }
        
        // Navigate between steps
        function navigateToStep(stepNumber) {
            // Hide all steps
            document.querySelectorAll('.form-step').forEach(step => {
                step.style.display = 'none';
            });
            
            // Show the selected step
            const stepElement = document.getElementById(`step-${stepNumber}`);
            if (stepElement) {
                stepElement.style.display = 'block';
            }
            
            // Update step indicator
            document.querySelectorAll('.step').forEach(step => {
                step.classList.remove('active', 'completed');
                
                const stepValue = parseInt(step.getAttribute('data-step'));
                if (stepValue === stepNumber) {
                    step.classList.add('active');
                } else if (stepValue < stepNumber) {
                    step.classList.add('completed');
                }
            });
        }
        
        // Update next button state
        function updateNextButton() {
            const nextButton = document.getElementById('next-to-step-2');
            if (nextButton) {
                nextButton.disabled = invitees.length === 0;
            }
        }
        
        // Final confirmation to save invitees
        async function confirmAddInvitees() {
            // Filter only new invitees for saving
            const newInvitees = invitees.filter(inv => inv.status === 'new');
            
            if (newInvitees.length === 0) {
                alert('No new invitees to add.');
                return;
            }

            const eventId = document.getElementById('event-select').value;
            
            if (!eventId) {
                alert('Please select an event.');
                return;
            }

            try {
                const response = await fetch("{{ route('store-invitees') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        event_id: eventId,
                        invitees: newInvitees
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    navigateToStep(3);
                    const finalCount = document.getElementById('final-count');
                    if (finalCount) finalCount.textContent = result.count;
                    
                    // Show errors if any
                    if (result.errors && result.errors.length > 0) {
                        console.warn('Some invitees failed:', result.errors);
                    }
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Confirm error:', error);
                alert('Error saving invitees: ' + error.message);
            }
        }
    </script>
</body>
</html>