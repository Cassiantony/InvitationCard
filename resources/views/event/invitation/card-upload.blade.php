<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload invitation card - Invitation System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.inviteCardRoutes = {
            inviteesJson: (eid) => `{{ url('/event') }}/${eid}/invitees.json`,
            downloadCard: (eid, iid) => `{{ url('/event') }}/${eid}/invitation-card/${iid}`
        };
    </script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
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

        .custom-alert {
        position: fixed;
        top: 100px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
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
        
        .design-area {
            border: 2px dashed #dee2e6;
            border-radius: 0.5rem;
            background-color: #f8f9fc;
            min-height: 500px;
            position: relative;
            overflow: hidden;
        }
        
        .template-card {
            border: 2px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            height: 100%;
        }
        
        .template-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-5px);
        }
        
        .template-card.selected {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .template-preview {
            height: 200px;
            background-size: cover;
            background-position: center;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
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
        
        .preview-card {
            width: 400px;
            height: 600px;
            margin: 0 auto;
            position: relative;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .card-canvas {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .qr-code-placeholder {
            position: absolute;
            width: 100px;
            height: 100px;
            background-color: rgba(255, 255, 255, 0.8);
            border: 2px dashed var(--primary-color);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: move;
            z-index: 10;
        }
        
        .qr-code-placeholder i {
            font-size: 2rem;
            color: var(--primary-color);
        }
        
        .position-option {
            border: 2px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        
        .position-option:hover {
            border-color: var(--primary-color);
        }
        
        .position-option.selected {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .position-preview {
            width: 120px;
            height: 160px;
            margin: 0 auto 0.5rem;
            background-color: #f8f9fc;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            position: relative;
        }
        
        .qr-indicator {
            position: absolute;
            width: 30px;
            height: 30px;
            background-color: var(--primary-color);
            border-radius: 0.25rem;
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
        
        .tab-content {
            min-height: 400px;
        }
        
        .customization-panel {
            background-color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .color-option {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .color-option.selected {
            border-color: #333;
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
        
        .form-step {
            display: none;
        }
        
        .form-step.active {
            display: block;
        }
        
        .text-customization-section {
            transition: all 0.3s ease;
        }
        
        .text-customization-section.collapsed {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
        }
        
        .text-customization-toggle {
            cursor: pointer;
            user-select: none;
        }
        
        .text-customization-toggle:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .preview-text {
            font-family: 'Georgia', 'Times New Roman', serif;
            line-height: 1.4;
        }
        
        .qr-code-placeholder {
            transition: all 0.2s ease;
        }
        
        .qr-code-placeholder:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
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
                                <i class="fas fa-user-plus"></i>
                                Add Invitees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="fas fa-palette"></i>
                                Design Card
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
                        <h4 class="mb-0">Upload invitation card (PDF)</h4>
                        <div class="d-flex">
                            @if(!empty($returnUrl))
                                <a href="{{ $returnUrl }}" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-arrow-left me-1"></i> Back to Send Invitations
                                </a>
                            @endif
                            <a href="{{ route('event.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-arrow-left me-1"></i> Back to Events
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Event Selection -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Select event</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <label for="event-select" class="form-label">Event</label>
                                <select class="form-select" id="event-select">
                                    <option value="">Select an event</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}">{{ $event->title }} — {{ $event->date->format('M j, Y g:i A') }}</option>
                                    @endforeach
                                </select>
                                @if($events->isEmpty())
                                    <p class="text-muted small mt-2 mb-0">You have no events yet. <a href="{{ route('event.create') }}">Create an event</a> first, then return here to upload your invitation PDF.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-label">Upload PDF</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-label">Position QR Code</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-label">Preview & Save</div>
                    </div>
                </div>
                
                <!-- Step 1: Upload your invitation PDF -->
                <div class="form-step active" id="step-1">
                    <div class="card shadow mb-4" id="pdf-upload-section">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Your invitation card (PDF)</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Create your invitation in Word, Canva, or any design tool, export as PDF, then upload it here. All artwork and text come from your file—nothing is generated from built-in templates.</p>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="upload-area" id="pdf-upload-area">
                                        <div class="upload-icon">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <h5>Drop your PDF here</h5>
                                        <p class="text-muted">or click to browse</p>
                                        <input type="file" id="pdf-file" accept=".pdf,application/pdf" class="d-none">
                                        <button type="button" class="btn btn-primary mt-2" id="pdf-browse-btn">
                                            <i class="fas fa-search me-1"></i> Browse PDF files
                                        </button>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <strong>Format:</strong> PDF only<br>
                                            <strong>Recommended:</strong> A4 or Letter<br>
                                            <strong>Maximum size:</strong> 20MB<br>
                                            <strong>QR placement:</strong> first page (step 2)
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <h6 class="mb-3">Preview</h6>
                                    <div id="pdf-preview" class="preview-card">
                                        <div class="card-canvas" id="pdf-canvas">
                                            <div class="empty-state">
                                                <i class="fas fa-file-pdf"></i>
                                                <p>No PDF uploaded yet</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div id="form-status" class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            <span id="status-text">Select an event and upload your PDF</span>
                        </div>
                        <button type="button" class="btn btn-primary" id="next-to-step-2">
                            Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 2: Position QR Code -->
                <div class="form-step" id="step-2" style="display: none;">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Position QR Code</h6>
                                    <div>
                                        <button class="btn btn-sm btn-outline-secondary" id="reset-position-btn">
                                            <i class="fas fa-redo me-1"></i> Reset Position
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="preview-card">
                                        <div class="card-canvas" id="design-canvas">
                                            <!-- QR code placeholder will be added here -->
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <label for="qr-size" class="form-label">QR Code Size</label>
                                        <input type="range" class="form-range" id="qr-size" min="50" max="150" value="100">
                                        <div class="d-flex justify-content-between">
                                            <small>Small</small>
                                            <small>Medium</small>
                                            <small>Large</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Quick Positions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <div class="position-option" data-position="top-left">
                                                <div class="position-preview">
                                                    <div class="qr-indicator" style="top: 10px; left: 10px;"></div>
                                                </div>
                                                <small>Top Left</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="position-option" data-position="top-right">
                                                <div class="position-preview">
                                                    <div class="qr-indicator" style="top: 10px; right: 10px;"></div>
                                                </div>
                                                <small>Top Right</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="position-option" data-position="bottom-left">
                                                <div class="position-preview">
                                                    <div class="qr-indicator" style="bottom: 10px; left: 10px;"></div>
                                                </div>
                                                <small>Bottom Left</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="position-option" data-position="bottom-right">
                                                <div class="position-preview">
                                                    <div class="qr-indicator" style="bottom: 10px; right: 10px;"></div>
                                                </div>
                                                <small>Bottom Right</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="position-option" data-position="center">
                                                <div class="position-preview">
                                                    <div class="qr-indicator" style="top: 50%; left: 50%; transform: translate(-50%, -50%);"></div>
                                                </div>
                                                <small>Center</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="position-option" data-position="custom">
                                                <div class="position-preview">
                                                    <div class="qr-indicator" style="top: 30px; right: 30px;"></div>
                                                </div>
                                                <small>Custom</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <h6 class="mb-3">QR Code Customization</h6>
                                        <div class="mb-3">
                                            <label class="form-label">Color</label>
                                            <div>
                                                <span class="color-option selected" style="background-color: #000000;" data-color="#000000"></span>
                                                <span class="color-option" style="background-color: #4e73df;" data-color="#4e73df"></span>
                                                <span class="color-option" style="background-color: #1cc88a;" data-color="#1cc88a"></span>
                                                <span class="color-option" style="background-color: #e74a3b;" data-color="#e74a3b"></span>
                                                <span class="color-option" style="background-color: #ffffff; border: 1px solid #dee2e6;" data-color="#ffffff"></span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Background</label>
                                            <div>
                                                <span class="color-option selected" style="background-color: #ffffff; border: 1px solid #dee2e6;" data-bgcolor="#ffffff"></span>
                                                <span class="color-option" style="background-color: #f8f9fc; border: 1px solid #dee2e6;" data-bgcolor="#f8f9fc"></span>
                                                <span class="color-option" style="background-color: #4e73df;" data-bgcolor="#4e73df"></span>
                                                <span class="color-option" style="background-color: transparent; border: 1px solid #dee2e6;" data-bgcolor="transparent">
                                                    <i class="fas fa-times text-muted"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-outline-secondary" id="back-to-step-1">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </button>
                        <button class="btn btn-primary" id="next-to-step-3">
                            Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 3: Preview & Save -->
                <div class="form-step" id="step-3" style="display: none;">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Final Preview</h6>
                                </div>
                                <div class="card-body">
                                    <div class="preview-card">
                                        <div class="card-canvas" id="final-canvas">
                                            <!-- Final design will be displayed here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Save Design</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="download-invitee-select" class="form-label">Invitee (unique QR)</label>
                                        <select class="form-select" id="download-invitee-select" disabled>
                                            <option value="">Select event in step 1, then choose invitee</option>
                                        </select>
                                        <small class="text-muted d-block mt-1">Save the design first. The download is your PDF with this guest’s invitation QR on page 1.</small>
                                    </div>
                                    <div class="mb-4">
                                        <label for="design-name" class="form-label">Design name</label>
                                        <input type="text" class="form-control" id="design-name" placeholder="e.g. Spring gala invitation 2026">
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-success" id="save-design-btn">
                                            <i class="fas fa-save me-1"></i> Save Design
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" id="download-design-btn">
                                            <i class="fas fa-download me-1"></i> Download PDF (with invitee QR)
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Design Details</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Your PDF:</td>
                                            <td id="detail-template">—</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">QR Position:</td>
                                            <td id="detail-position">Top Right</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">QR Size:</td>
                                            <td id="detail-size">Medium</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">QR Color:</td>
                                            <td id="detail-color">Black</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-outline-secondary" id="back-to-step-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </button>
                        <a href="{{ route('event.invitation.send') }}" class="btn btn-success">
                            <i class="fas fa-paper-plane me-1"></i> Send Invitations
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel"><i class="fas fa-check-circle me-2"></i> Design Saved Successfully!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-palette text-success mb-3" style="font-size: 4rem;"></i>
                    <h4 class="mb-3">Your invitation card is ready!</h4>
                    <p class="mb-0">Your design has been saved and applied to your event.</p>
                </div>
                <div class="modal-footer">
                    @if(!empty($returnUrl))
                        <a href="{{ $returnUrl }}" class="btn btn-success">Back to Send Invitations</a>
                    @else
                        <a href="{{ route('event.index') }}" class="btn btn-success">My events</a>
                    @endif
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
  <script>// Design configuration — user-uploaded PDF only (no built-in templates)
let designConfig = {
    method: 'pdf',
    pdfFile: null,
    pdfPreview: null,
    qrPosition: { x: 300, y: 50 },
    qrSize: 100,
    qrColor: '#000000',
    qrBgColor: '#ffffff',
    eventId: null,
    qrLayoutNorm: { nx: 0.72, ny: 0.05, nw: 0.18 }
};

function getQrPlacementParent() {
    return document.getElementById('pdf-qr-layer') || document.getElementById('design-canvas');
}

function syncQrLayoutNorm() {
    const layer = document.getElementById('pdf-qr-layer');
    const qr = layer ? layer.querySelector('.qr-code-placeholder') : null;
    if (!layer || !qr) return;
    const lw = layer.clientWidth;
    const lh = layer.clientHeight;
    if (lw < 1 || lh < 1) return;
    designConfig.qrLayoutNorm = {
        nx: Math.min(1, Math.max(0, qr.offsetLeft / lw)),
        ny: Math.min(1, Math.max(0, qr.offsetTop / lh)),
        nw: Math.min(1, Math.max(0.02, qr.offsetWidth / lw))
    };
}

async function loadInviteesForEvent(eventId) {
    const sel = document.getElementById('download-invitee-select');
    if (!sel) return;
    if (!eventId) {
        sel.innerHTML = '<option value="">Select an event first</option>';
        sel.disabled = true;
        return;
    }
    sel.disabled = true;
    sel.innerHTML = '<option value="">Loading invitees…</option>';
    try {
        const r = await fetch(window.inviteCardRoutes.inviteesJson(eventId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const d = await r.json();
        sel.innerHTML = '<option value="">Choose invitee for download</option>';
        (d.invitees || []).forEach(function (inv) {
            const o = document.createElement('option');
            o.value = inv.id;
            o.textContent = inv.name + ' — ' + inv.email;
            sel.appendChild(o);
        });
        if (!(d.invitees || []).length) {
            sel.innerHTML = '<option value="">No invitees yet (add invitees, then download)</option>';
        }
        sel.disabled = false;
    } catch (e) {
        sel.innerHTML = '<option value="">Could not load invitees</option>';
        sel.disabled = false;
    }
}

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const preEventId = params.get('event_id');
    const eventSelect = document.getElementById('event-select');
    if (preEventId && eventSelect) {
        eventSelect.value = preEventId;
        designConfig.eventId = preEventId;
    } else if (eventSelect && eventSelect.value) {
        designConfig.eventId = eventSelect.value;
    }

    setupEventListeners();
    updateDesignCanvas();
    updateNextButtonState();
    if (designConfig.eventId) {
        loadInviteesForEvent(designConfig.eventId);
    }
});

// Set up event listeners
function setupEventListeners() {
    // Event selection
    document.getElementById('event-select').addEventListener('change', function() {
        designConfig.eventId = this.value;
        updateNextButtonState();
        loadInviteesForEvent(this.value);
    });

    // PDF file upload
    document.getElementById('pdf-browse-btn').addEventListener('click', function() {
        document.getElementById('pdf-file').click();
    });
    
    document.getElementById('pdf-file').addEventListener('change', handlePDFUpload);
    
    // Drag and drop for PDF upload
    const pdfUploadArea = document.getElementById('pdf-upload-area');
    pdfUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        pdfUploadArea.classList.add('dragover');
    });
    
    pdfUploadArea.addEventListener('dragleave', function() {
        pdfUploadArea.classList.remove('dragover');
    });
    
    pdfUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        pdfUploadArea.classList.remove('dragover');
        
        if (e.dataTransfer.files.length) {
            document.getElementById('pdf-file').files = e.dataTransfer.files;
            handlePDFUpload();
        }
    });
    
    // Step navigation
    document.getElementById('next-to-step-2').addEventListener('click', function() {
        if (!validateStep1()) return;
        navigateToStep(2);
        setupQRCodePositioning();
    });
    
    document.getElementById('next-to-step-3').addEventListener('click', function() {
        navigateToStep(3);
        loadInviteesForEvent(designConfig.eventId);
        updateFinalPreview();
        updateDesignDetails();
    });
    
    document.getElementById('back-to-step-1').addEventListener('click', function() {
        navigateToStep(1);
    });
    
    document.getElementById('back-to-step-2').addEventListener('click', function() {
        navigateToStep(2);
    });
    
    // QR code positioning
    document.getElementById('qr-size').addEventListener('input', function() {
        designConfig.qrSize = parseInt(this.value);
        updateQRCodePosition();
        syncQrLayoutNorm();
    });
    
    document.querySelectorAll('.position-option').forEach(option => {
        option.addEventListener('click', function() {
            const position = this.getAttribute('data-position');
            setQRCodePosition(position);
        });
    });
    
    // QR code customization
    document.querySelectorAll('.color-option[data-color]').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.color-option[data-color]').forEach(o => {
                o.classList.remove('selected');
            });
            this.classList.add('selected');
            designConfig.qrColor = this.getAttribute('data-color');
            updateQRCodePosition();
        });
    });
    
    document.querySelectorAll('.color-option[data-bgcolor]').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.color-option[data-bgcolor]').forEach(o => {
                o.classList.remove('selected');
            });
            this.classList.add('selected');
            designConfig.qrBgColor = this.getAttribute('data-bgcolor');
            updateQRCodePosition();
        });
    });
    
    // Reset position
    document.getElementById('reset-position-btn').addEventListener('click', function() {
        designConfig.qrSize = 100;
        document.getElementById('qr-size').value = 100;
        setQRCodePosition('top-right');
    });
    
    // Save design
    document.getElementById('save-design-btn').addEventListener('click', function() {
        saveDesign();
    });
    
    // Download design
    document.getElementById('download-design-btn').addEventListener('click', function() {
        downloadDesign();
    });
}

// Get CSRF token safely
function getCsrfToken() {
    // Try to get from meta tag
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken) {
        return metaToken.getAttribute('content');
    }
    
    // Try to get from input field (common in Laravel)
    const inputToken = document.querySelector('input[name="_token"]');
    if (inputToken) {
        return inputToken.value;
    }
    
    // Return empty string if not found (for demo purposes)
    console.warn('CSRF token not found');
    return '';
}

// Validate step 1
function validateStep1() {
    if (!designConfig.eventId) {
        showAlert('Please select an event.', 'danger');
        return false;
    }

    if (!designConfig.pdfFile) {
        showAlert('Please upload your invitation as a PDF file.', 'danger');
        return false;
    }

    return true;
}

// Handle PDF upload
function handlePDFUpload() {
    const fileInput = document.getElementById('pdf-file');
    const file = fileInput.files[0];
    
    if (!file) return;
    
    // Validate file type (some browsers omit MIME for PDF)
    const nameOk = file.name && file.name.toLowerCase().endsWith('.pdf');
    if (file.type !== 'application/pdf' && !nameOk) {
        showAlert('Please upload a valid PDF file.', 'danger');
        return;
    }
    
    // Validate file size (20MB max)
    if (file.size > 20 * 1024 * 1024) {
        showAlert('File size must be less than 20MB.', 'danger');
        return;
    }
    
    // Show loading state
    const browseBtn = document.getElementById('pdf-browse-btn');
    const originalText = browseBtn.innerHTML;
    browseBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
    browseBtn.disabled = true;
    
    // Store the PDF file
    designConfig.pdfFile = file;
    
    // Create a preview (simplified for demo - in real app you'd use PDF.js)
    const reader = new FileReader();
    reader.onload = function(e) {
        designConfig.pdfPreview = e.target.result;
        updatePDFPreview();
        updateDesignCanvas();
        
        // Reset button
        browseBtn.innerHTML = originalText;
        browseBtn.disabled = false;
        
        updateNextButtonState();
        showAlert('PDF uploaded successfully!', 'success');
    };
    
    reader.onerror = function() {
        showAlert('Failed to read the PDF file. Please try again.', 'danger');
        browseBtn.innerHTML = originalText;
        browseBtn.disabled = false;
    };
    
    reader.readAsDataURL(file);
}

// Update PDF preview
function updatePDFPreview() {
    const canvas = document.getElementById('pdf-canvas');
    
    if (designConfig.pdfFile) {
        // Show loading state
        canvas.style.background = '#f8f9fc';
        canvas.innerHTML = `
            <div class="text-center p-4">
                <i class="fas fa-spinner fa-spin text-primary mb-3" style="font-size: 2rem;"></i>
                <h6 class="mb-2">Loading PDF...</h6>
                <p class="text-muted small">Rendering PDF preview</p>
            </div>
        `;
        
        // Render PDF using PDF.js
        renderPDFPreview(designConfig.pdfFile);
    } else {
        canvas.style.background = '#f8f9fc';
        canvas.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-file-pdf"></i>
                <p>No PDF uploaded</p>
            </div>
        `;
    }
}

// Render PDF preview using PDF.js
function renderPDFPreview(pdfFile) {
    const canvas = document.getElementById('pdf-canvas');
    
    // Configure PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    
    const fileReader = new FileReader();
    fileReader.onload = function(e) {
        const typedarray = new Uint8Array(e.target.result);
        
        pdfjsLib.getDocument(typedarray).promise.then(function(pdf) {
            // Get the first page
            pdf.getPage(1).then(function(page) {
                const scale = 0.8; // Adjust scale to fit the preview
                const viewport = page.getViewport({ scale: scale });
                
                // Create canvas element for PDF rendering
                const pdfCanvas = document.createElement('canvas');
                const context = pdfCanvas.getContext('2d');
                pdfCanvas.height = viewport.height;
                pdfCanvas.width = viewport.width;
                
                // Clear the canvas and set background
                canvas.style.background = '#ffffff';
                canvas.innerHTML = '';
                canvas.appendChild(pdfCanvas);
                
                // Render the PDF page
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                
                page.render(renderContext).promise.then(function() {
                    console.log('PDF rendered successfully');
                }).catch(function(error) {
                    console.error('Error rendering PDF:', error);
                    showPDFError(canvas, 'Error rendering PDF');
                });
            }).catch(function(error) {
                console.error('Error getting PDF page:', error);
                showPDFError(canvas, 'Error loading PDF page');
            });
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            showPDFError(canvas, 'Error loading PDF file');
        });
    };
    
    fileReader.onerror = function() {
        showPDFError(canvas, 'Error reading PDF file');
    };
    
    fileReader.readAsArrayBuffer(pdfFile);
}

// Show PDF error
function showPDFError(canvas, message) {
    canvas.style.background = '#f8f9fc';
    canvas.innerHTML = `
        <div class="text-center p-4">
            <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 2rem;"></i>
            <h6 class="mb-2">PDF Preview Error</h6>
            <p class="text-muted small">${message}</p>
            <p class="text-muted small">QR code positioning will still work</p>
        </div>
    `;
}

// Update design canvas (step 1 summary — PDF only)
function updateDesignCanvas() {
    const canvas = document.getElementById('design-canvas');
    if (!canvas) return;

    if (designConfig.pdfFile) {
        canvas.style.background = '#f8f9fc';
        canvas.innerHTML = `
            <div style="height: 100%; position: relative; display: flex; align-items: center; justify-content: center;">
                <div class="text-center p-4">
                    <i class="fas fa-file-pdf text-danger mb-3" style="font-size: 4rem;"></i>
                    <h4 class="mb-2">${designConfig.pdfFile.name}</h4>
                    <p class="text-muted mb-3">Your uploaded PDF — place the QR on the next step.</p>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>All layout and text are taken from your file.</small>
                    </div>
                </div>
            </div>
        `;
        return;
    }

    canvas.style.background = '#f8f9fc';
    canvas.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-file-pdf"></i>
            <p>Upload a PDF to continue</p>
        </div>
    `;
}

// Update next button state
function updateNextButtonState() {
    const nextButton = document.getElementById('next-to-step-2');
    const statusText = document.getElementById('status-text');
    const isValid = Boolean(designConfig.eventId && designConfig.pdfFile);

    nextButton.disabled = !isValid;

    if (!designConfig.eventId) {
        statusText.textContent = 'Select an event';
    } else if (!designConfig.pdfFile) {
        statusText.textContent = 'Upload your invitation PDF';
    } else {
        statusText.textContent = 'Ready — continue to place the QR code';
    }

    if (!isValid) {
        nextButton.classList.add('btn-secondary');
        nextButton.classList.remove('btn-primary');
    } else {
        nextButton.classList.remove('btn-secondary');
        nextButton.classList.add('btn-primary');
    }
}

// Set up QR code positioning
function setupQRCodePositioning() {
    if (designConfig.pdfFile) {
        renderPDFInDesignCanvas();
    } else {
        updateDesignCanvas();
    }
    updateQRCodePosition();
}

// Render PDF in the design canvas for QR positioning
function renderPDFInDesignCanvas() {
    const canvas = document.getElementById('design-canvas');
    
    if (!designConfig.pdfFile) return;
    
    // Show loading state
    canvas.style.background = '#f8f9fc';
    canvas.innerHTML = `
        <div class="text-center p-4">
            <i class="fas fa-spinner fa-spin text-primary mb-3" style="font-size: 2rem;"></i>
            <h6 class="mb-2">Loading PDF for QR positioning...</h6>
            <p class="text-muted small">Rendering PDF content</p>
        </div>
    `;
    
    // Configure PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    
    const fileReader = new FileReader();
    fileReader.onload = function(e) {
        const typedarray = new Uint8Array(e.target.result);
        
        pdfjsLib.getDocument(typedarray).promise.then(function(pdf) {
            // Get the first page
            pdf.getPage(1).then(function(page) {
                const scale = 0.6; // Scale to fit the design canvas
                const viewport = page.getViewport({ scale: scale });
                
                // Create canvas element for PDF rendering
                const pdfCanvas = document.createElement('canvas');
                const context = pdfCanvas.getContext('2d');
                pdfCanvas.height = viewport.height;
                pdfCanvas.width = viewport.width;
                pdfCanvas.style.maxWidth = '100%';
                pdfCanvas.style.height = 'auto';
                
                // Clear the canvas and set background
                canvas.style.background = '#ffffff';
                canvas.innerHTML = '';
                canvas.appendChild(pdfCanvas);
                
                // Render the PDF page
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                
                page.render(renderContext).promise.then(function() {
                    canvas.style.background = '#ffffff';
                    canvas.innerHTML = '';
                    const layer = document.createElement('div');
                    layer.id = 'pdf-qr-layer';
                    layer.style.position = 'relative';
                    layer.style.display = 'inline-block';
                    layer.style.width = pdfCanvas.width + 'px';
                    layer.style.height = pdfCanvas.height + 'px';
                    pdfCanvas.style.display = 'block';
                    pdfCanvas.style.maxWidth = 'none';
                    pdfCanvas.style.width = pdfCanvas.width + 'px';
                    pdfCanvas.style.height = pdfCanvas.height + 'px';
                    layer.appendChild(pdfCanvas);
                    canvas.appendChild(layer);
                    designConfig.pdfCanvas = pdfCanvas;
                    designConfig.pdfViewport = viewport;
                    updateQRCodePosition();
                    syncQrLayoutNorm();
                }).catch(function(error) {
                    console.error('Error rendering PDF:', error);
                    showPDFErrorInDesignCanvas(canvas, 'Error rendering PDF');
                });
            }).catch(function(error) {
                console.error('Error getting PDF page:', error);
                showPDFErrorInDesignCanvas(canvas, 'Error loading PDF page');
            });
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            showPDFErrorInDesignCanvas(canvas, 'Error loading PDF file');
        });
    };
    
    fileReader.onerror = function() {
        showPDFErrorInDesignCanvas(canvas, 'Error reading PDF file');
    };
    
    fileReader.readAsArrayBuffer(designConfig.pdfFile);
}

// Show PDF error in design canvas
function showPDFErrorInDesignCanvas(canvas, message) {
    canvas.style.background = '#f8f9fc';
    canvas.innerHTML = `
        <div class="text-center p-4">
            <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 2rem;"></i>
            <h6 class="mb-2">PDF Preview Error</h6>
            <p class="text-muted small">${message}</p>
            <p class="text-muted small">QR code positioning will still work</p>
        </div>
    `;
}

// Update QR code position
function updateQRCodePosition() {
    const parent = getQrPlacementParent();
    if (!parent) return;

    const existingQR = parent.querySelector('.qr-code-placeholder');
    if (existingQR) {
        existingQR.remove();
    }

    const qrCode = document.createElement('div');
    qrCode.className = 'qr-code-placeholder';
    qrCode.style.width = `${designConfig.qrSize}px`;
    qrCode.style.height = `${designConfig.qrSize}px`;
    qrCode.style.left = `${designConfig.qrPosition.x}px`;
    qrCode.style.top = `${designConfig.qrPosition.y}px`;
    qrCode.style.backgroundColor = designConfig.qrBgColor;
    qrCode.style.borderColor = designConfig.qrColor;
    qrCode.style.position = 'absolute';
    qrCode.style.zIndex = '10';

    qrCode.innerHTML = `<i class="fas fa-qrcode" style="color: ${designConfig.qrColor}"></i>`;

    makeElementDraggable(qrCode);

    parent.appendChild(qrCode);
    syncQrLayoutNorm();
}

// Make element draggable
function makeElementDraggable(element) {
    let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    let isDragging = false;
    
    element.onmousedown = dragMouseDown;
    element.style.cursor = 'move';
    
    function dragMouseDown(e) {
        e.preventDefault();
        e.stopPropagation();
        isDragging = true;
        
        // Get the mouse cursor position at startup
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        document.onmousemove = elementDrag;
        
        // Add visual feedback
        element.style.opacity = '0.8';
    }
    
    function elementDrag(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (!isDragging) return;
        
        // Calculate the new cursor position
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        
        // Calculate new position
        const newTop = element.offsetTop - pos2;
        const newLeft = element.offsetLeft - pos1;
        
        // Constrain within placement layer (same size as rendered PDF page)
        const box = element.parentElement;
        if (!box) return;

        const maxX = box.clientWidth - element.offsetWidth;
        const maxY = box.clientHeight - element.offsetHeight;

        // Set the element's new position
        const constrainedTop = Math.max(0, Math.min(newTop, maxY));
        const constrainedLeft = Math.max(0, Math.min(newLeft, maxX));

        element.style.top = constrainedTop + "px";
        element.style.left = constrainedLeft + "px";

        // Update position in config
        designConfig.qrPosition.x = constrainedLeft;
        designConfig.qrPosition.y = constrainedTop;
    }

    function closeDragElement() {
        isDragging = false;
        element.style.opacity = '1';
        element.style.cursor = 'move';

        // Stop moving when mouse button is released
        document.onmouseup = null;
        document.onmousemove = null;
        syncQrLayoutNorm();
    }
}

// Set QR code to predefined position
function setQRCodePosition(position) {
    const box = document.getElementById('pdf-qr-layer') || document.getElementById('design-canvas');
    if (!box) return;
    const cw = box.clientWidth;
    const ch = box.clientHeight;

    switch(position) {
        case 'top-left':
            designConfig.qrPosition = { x: 20, y: 20 };
            break;
        case 'top-right':
            designConfig.qrPosition = { x: cw - designConfig.qrSize - 20, y: 20 };
            break;
        case 'bottom-left':
            designConfig.qrPosition = { x: 20, y: ch - designConfig.qrSize - 20 };
            break;
        case 'bottom-right':
            designConfig.qrPosition = {
                x: cw - designConfig.qrSize - 20,
                y: ch - designConfig.qrSize - 20
            };
            break;
        case 'center':
            designConfig.qrPosition = {
                x: (cw - designConfig.qrSize) / 2,
                y: (ch - designConfig.qrSize) / 2
            };
            break;
        case 'custom':
            // Keep current position
            break;
    }

    updateQRCodePosition();

    // Update position option selection
    document.querySelectorAll('.position-option').forEach(option => {
        option.classList.remove('selected');
    });
    const marker = document.querySelector(`.position-option[data-position="${position}"]`);
    if (marker) marker.classList.add('selected');
}

// Update final preview
function updateFinalPreview() {
    const canvas = document.getElementById('final-canvas');
    if (!canvas) return;

    if (!designConfig.pdfFile) {
        canvas.style.background = '#f8f9fc';
        canvas.innerHTML = '<div class="empty-state"><p>No PDF loaded</p></div>';
        return;
    }

    canvas.style.background = '#f8f9fc';
    canvas.innerHTML = `
        <div class="text-center p-4" style="height: 100%; display: flex; flex-direction: column; justify-content: center;">
            <i class="fas fa-file-pdf text-danger mb-3" style="font-size: 4rem;"></i>
            <h4 class="mb-2">${designConfig.pdfFile.name}</h4>
            <p class="text-muted mb-3">Summary only. <strong>Save</strong> stores your PDF and QR position. Then pick an invitee and use <strong>Download PDF</strong> for their personalized file (your artwork + their QR on page 1).</p>
            <div class="alert alert-success mb-0">
                <i class="fas fa-check-circle me-2"></i>
                <small>Normalized layout is used so the QR lines up with your PDF on download.</small>
            </div>
        </div>
    `;
}

// Update design details
function updateDesignDetails() {
    const nameCell = document.getElementById('detail-template');
    if (nameCell) {
        nameCell.textContent = designConfig.pdfFile ? designConfig.pdfFile.name : '—';
    }
    
    // Determine position name
    let positionName = 'Custom';
    if (designConfig.qrPosition.x < 100 && designConfig.qrPosition.y < 100) positionName = 'Top Left';
    else if (designConfig.qrPosition.x > 250 && designConfig.qrPosition.y < 100) positionName = 'Top Right';
    else if (designConfig.qrPosition.x < 100 && designConfig.qrPosition.y > 450) positionName = 'Bottom Left';
    else if (designConfig.qrPosition.x > 250 && designConfig.qrPosition.y > 450) positionName = 'Bottom Right';
    else if (designConfig.qrPosition.x > 150 && designConfig.qrPosition.x < 250 && 
             designConfig.qrPosition.y > 250 && designConfig.qrPosition.y < 350) positionName = 'Center';
    
    document.getElementById('detail-position').textContent = positionName;
    
    // Determine size
    let sizeName = 'Small';
    if (designConfig.qrSize > 80 && designConfig.qrSize < 120) sizeName = 'Medium';
    else if (designConfig.qrSize >= 120) sizeName = 'Large';
    
    document.getElementById('detail-size').textContent = sizeName;
    
    // Determine color
    const colorNames = {
        '#000000': 'Black',
        '#4e73df': 'Blue',
        '#1cc88a': 'Green',
        '#e74a3b': 'Red',
        '#ffffff': 'White'
    };
    document.getElementById('detail-color').textContent = colorNames[designConfig.qrColor] || 'Custom';
}

// Navigate between steps
function navigateToStep(stepNumber) {
    // Hide all steps
    document.querySelectorAll('.form-step').forEach(step => {
        step.style.display = 'none';
    });
    
    // Show the selected step
    document.getElementById(`step-${stepNumber}`).style.display = 'block';
    
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

// Save design to backend
async function saveDesign() {
    const designName = document.getElementById('design-name').value.trim();

    if (!designName) {
        showAlert('Please enter a name for your design.', 'danger');
        return;
    }

    if (!designConfig.eventId) {
        showAlert('Please select an event.', 'danger');
        return;
    }

    if (!designConfig.pdfFile) {
        showAlert('Your invitation PDF is missing. Go back to step 1 and upload it.', 'danger');
        return;
    }

    syncQrLayoutNorm();

    const saveBtn = document.getElementById('save-design-btn');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
    saveBtn.disabled = true;

    try {
        const formData = new FormData();
        formData.append('event_id', designConfig.eventId);
        formData.append('design_name', designName);
        formData.append('design_type', 'pdf');
        formData.append('qr_position_x', designConfig.qrPosition.x);
        formData.append('qr_position_y', designConfig.qrPosition.y);
        formData.append('qr_size', designConfig.qrSize);
        formData.append('qr_color', designConfig.qrColor);
        formData.append('qr_background_color', designConfig.qrBgColor);
        formData.append('qr_layout[nx]', designConfig.qrLayoutNorm.nx);
        formData.append('qr_layout[ny]', designConfig.qrLayoutNorm.ny);
        formData.append('qr_layout[nw]', designConfig.qrLayoutNorm.nw);
        formData.append('pdf_file', designConfig.pdfFile);
        formData.append('_token', getCsrfToken());
        
        // Make API call to save design
        const response = await fetch('{{ route("design.create") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const result = await response.json();

        if (!response.ok || !result.success) {
            const msg = result.message
                || (result.errors ? Object.values(result.errors).flat().join(' ') : 'Save failed');
            throw new Error(msg);
        }

        showAlert('Design saved successfully!', 'success');
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();

        designConfig.designId = result.design_id;
        
    } catch (error) {
        console.error('Save error:', error);
        showAlert('Failed to save design: ' + error.message, 'danger');
    } finally {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    }
}

// Download merged PDF (your file + this invitee's QR) from the server
async function downloadDesign() {
    const inviteeId = document.getElementById('download-invitee-select')?.value;
    const eventId = designConfig.eventId;

    if (!eventId) {
        showAlert('Select an event first.', 'danger');
        return;
    }
    if (!inviteeId) {
        showAlert('Choose an invitee. Each download uses that person’s unique invitation QR.', 'danger');
        return;
    }

    const downloadBtn = document.getElementById('download-design-btn');
    const originalText = downloadBtn.innerHTML;
    downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Building PDF…';
    downloadBtn.disabled = true;

    try {
        const url = window.inviteCardRoutes.downloadCard(eventId, inviteeId);
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/pdf, application/json'
            }
        });

        if (response.status === 422) {
            const err = await response.json().catch(() => ({}));
            showAlert(err.message || 'Could not build PDF (e.g. unsupported PDF or missing GD).', 'danger');
            return;
        }

        if (!response.ok) {
            showAlert('Download failed (' + response.status + '). Save your design first, then try again.', 'danger');
            return;
        }

        const blob = await response.blob();
        const disposition = response.headers.get('Content-Disposition');
        let filename = 'invitation.pdf';
        if (disposition && disposition.includes('filename=')) {
            const m = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
            if (m && m[1]) {
                filename = m[1].replace(/['"]/g, '').trim();
            }
        }

        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);

        showAlert('Invitation PDF downloaded.', 'success');
    } catch (error) {
        console.error('Download error:', error);
        showAlert('Failed to download: ' + error.message, 'danger');
    } finally {
        downloadBtn.innerHTML = originalText;
        downloadBtn.disabled = false;
    }
}

// Show alert message
function showAlert(message, type) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} custom-alert alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the main content
    const mainContent = document.querySelector('.col-md-9.col-lg-10');
    mainContent.insertBefore(alertDiv, mainContent.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>

</body>
</html>