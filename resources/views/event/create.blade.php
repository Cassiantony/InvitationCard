<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Invitation System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Flatpickr for date/time -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
        
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .template-preview {
            border: 2px dashed #dee2e6;
            border-radius: 0.5rem;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .template-preview:hover {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .template-preview.selected {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.1);
        }
        
        .template-thumbnail {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 0.25rem;
        }
        
        .guest-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            border: 1px solid #e3e6f0;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
            background-color: #fff;
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
        
        .preview-card {
            max-width: 400px;
            margin: 0 auto;
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        
        .preview-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        
        .preview-body {
            padding: 1.5rem;
        }
        
        .preview-footer {
            background-color: #f8f9fa;
            padding: 1rem;
            text-align: center;
            border-top: 1px solid #e3e6f0;
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
                            <a class="nav-link active" href="#">
                                <i class="fas fa-calendar-plus"></i>
                                Create Event
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
                        <h4 class="mb-0">Create New Event</h4>
                        <div class="d-flex">
                            <button class="btn btn-outline-secondary me-2" id="save-draft-btn">
                                <i class="fas fa-save me-1"></i> Save Draft
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-plus me-1"></i> Create New
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-plus me-2"></i>New Event</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-users me-2"></i>Add Invitees</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-palette me-2"></i>Design Template</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                                {{-- Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                        <strong>Whoops!</strong> Please correct the following errors:
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                
                <!-- Event Creation Form -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Event Creation Wizard</h6>
                            </div>
                            <div class="card-body">
                              
                                
                                <!-- Step 1: Event Details -->
                                <div class="form-step active" id="step-1">
                                    <h5 class="mb-4">Event Information</h5>
                                    <form action="{{ route('event.store') }}" method="POST">
                                        @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="event-title" class="form-label">Event Title *</label>
                                                <input type="text" name="title" class="form-control" id="event-title" placeholder="e.g., Annual Company Gala" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="event-description" class="form-label">Event Description</label>
                                                <textarea class="form-control" name="description" id="event-description" rows="3" placeholder="Describe your event..." required></textarea>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="event-category" class="form-label">Event Category</label>
                                                <select class="form-select" id="event-category" name="category" required>
                                                    <option value="">Select a category</option>
                                                    <option value="corporate">Corporate Event</option>
                                                    <option value="wedding">Wedding</option>
                                                    <option value="birthday">Birthday Party</option>
                                                    <option value="conference">Conference</option>
                                                    <option value="seminar">Seminar</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="event-date" class="form-label">Event Date and Time *</label>
                                                <input type="text" name="date" class="form-control" id="event-date" placeholder="Select date and time" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="event-location" class="form-label">Event Location *</label>
                                                <input type="text" name="location" class="form-control" id="event-location" placeholder="e.g., Grand Ballroom, City Center" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="event-organizer" class="form-label">Organizer Name *</label>
                                                <input type="text" name="organizer_name" class="form-control" id="event-organizer" placeholder="Your name or organization" required>
                                            </div>
                                        </div>
                                    </div>
                                   
                                    
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-warning" id="create-event-btn">Create Event</button>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
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
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <script>
        // Initialize date picker
        flatpickr("#event-date", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today"
        });
    
        
     
        
        // Save draft
        document.getElementById('save-draft-btn').addEventListener('click', function() {
            alert('Draft saved successfully!');
        });
        
     
    </script>
</body>
</html>