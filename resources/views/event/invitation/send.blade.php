<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Invitations - Invitation System</title>
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
        
        .invitee-item.selected {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .payment-summary {
            background-color: #f8f9fc;
            border-radius: 0.35rem;
            padding: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .cost-breakdown {
            font-size: 0.9rem;
        }
        
        .cost-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .total-cost {
            font-weight: bold;
            font-size: 1.2rem;
            border-top: 1px solid #dee2e6;
            padding-top: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .invitation-preview {
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 1.5rem;
            background-color: white;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .preview-header {
            background: linear-gradient(135deg, var(--primary-color), #224abe);
            color: white;
            padding: 1.5rem;
            text-align: center;
            border-radius: 0.5rem 0.5rem 0 0;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        }
        
        .preview-body {
            padding: 0.5rem 0;
        }
        
        .preview-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e3e6f0;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .method-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-whatsapp {
            background-color: #25D366;
            color: white;
        }
        
        .badge-sms {
            background-color: #6c757d;
            color: white;
        }
        
        .balance-info {
            background: linear-gradient(135deg, var(--primary-color), #224abe);
            color: white;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .progress {
            height: 8px;
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
        
        .payment-method {
            border: 2px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .payment-method:hover {
            border-color: var(--primary-color);
        }
        
        .payment-method.selected {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .payment-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
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
                            <a class="nav-link" href="{{ route('event.index') }}">
                                <i class="fas fa-calendar-alt"></i>
                                My Events
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link active" href="">
                                <i class="fas fa-paper-plane"></i>
                                Send Invitations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('wallet.add-funds') }}">
                                <i class="fas fa-wallet"></i>
                                Add Funds
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
                        <h4 class="mb-0">Send Invitations</h4>
                        <div class="d-flex">
                            <a href="{{ route('event.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-arrow-left me-1"></i> Back to Events
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info d-flex align-items-start mb-4">
                    <i class="fab fa-whatsapp me-2 mt-1"></i>
                    <div>
                        <strong>WhatsApp first:</strong> Each invitee receives their card as a <strong>PNG image</strong> on WhatsApp via the <strong>Meta Cloud API</strong>.
                        If the number is not on WhatsApp, the system sends the <strong>invitation code via NextSMS</strong> automatically.
                        Cost: Tsh {{ number_format($invitationCostTsh) }} per successful delivery.
                        @if(config('invitation.demo_messaging') && app()->environment('local') && empty(config('services.whatsapp.access_token')))
                            <span class="d-block mt-1 small">Demo mode: configure <code>WHATSAPP_*</code> and <code>NEXTSMS_*</code> in <code>.env</code> for live sending. Until then, phone numbers ending in an <strong>even</strong> digit simulate WhatsApp; odd digits fall back to SMS.</span>
                        @endif
                    </div>
                </div>

                <!-- Balance Information -->
                <div class="balance-info">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-1">Your Account Balance</h5>
                            <p class="mb-0">Current balance available for sending invitations</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h2 class="mb-0" id="wallet-balance-display">Tsh {{ number_format($walletBalance) }}</h2>
                            <a href="{{ route('wallet.add-funds', ['return' => url()->current()]) }}" class="text-white me-3">
                                <i class="fas fa-plus-circle me-1"></i>Add funds
                            </a>
                            <a href="{{ $selectedEvent ? route('event.invitation.delivery-report', $selectedEvent) : '#' }}" class="text-white-50" id="delivery-report-link">
                                View delivery report <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Select Event & Invitees</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="event-select" class="form-label">Select Event</label>
                                        <select class="form-select" id="event-select">
                                            <option value="">Choose an event</option>
                                            @foreach($events as $event)
                                                <option value="{{ $event->id }}" {{ $selectedEvent && $selectedEvent->id === $event->id ? 'selected' : '' }}>
                                                    {{ $event->title }} ({{ $event->date->format('M j, Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Invitation Method</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="invitationMethod" id="autoMethod" value="auto" checked>
                                                <label class="form-check-label" for="autoMethod">
                                                    Auto-detect (WhatsApp preferred)
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="invitationMethod" id="whatsappOnly" value="whatsapp">
                                                <label class="form-check-label" for="whatsappOnly">
                                                    WhatsApp Only
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="invitationMethod" id="smsOnly" value="sms">
                                                <label class="form-check-label" for="smsOnly">
                                                    SMS Only
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>Select Invitees</h6>
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary me-1" id="select-all-btn">
                                            <i class="fas fa-check-square me-1"></i> Select All
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" id="deselect-all-btn">
                                            <i class="fas fa-square me-1"></i> Deselect All
                                        </button>
                                    </div>
                                </div>
                                
                                <div id="invitees-list" style="max-height: 400px; overflow-y: auto;">
                                    <!-- Invitee items will be populated by JavaScript -->
                                </div>
                                
                                <div class="mt-3 text-end">
                                    <span id="selected-count">0</span> of <span id="total-count">0</span> invitees selected
                                </div>
                            </div>
                        </div>

                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-file-pdf me-2 text-danger"></i>Invitation card (PDF)
                                </h6>
                                @if($selectedEvent)
                                    <span class="badge {{ $cardDesign ? 'bg-success' : 'bg-warning text-dark' }}" id="card-design-status">
                                        {{ $cardDesign ? 'Card configured' : 'Not configured' }}
                                    </span>
                                @endif
                            </div>
                            <div class="card-body">
                                @if(!$selectedEvent)
                                    <p class="text-muted mb-0">Select an event above to upload your invitation PDF and choose where each invitee’s QR code appears on page 1.</p>
                                @else
                                    @if($cardDesign)
                                        <div class="alert alert-success mb-3">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>{{ $cardDesign->design_name }}</strong> is saved for this event.
                                            Each invitee gets their own QR on your card when invitations are sent as a <strong>PNG image</strong>.
                                        </div>
                                        @if(!$cardDesign->template_image_path)
                                            <div class="alert alert-warning mb-3">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                Re-save your design once so your PDF is converted to an image for WhatsApp delivery.
                                                <a href="{{ route('event.invitation.card-upload', ['event_id' => $selectedEvent->id, 'return' => 'send']) }}" class="alert-link">Open Design Card</a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-warning mb-3">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            No invitation PDF yet. Upload your card and place the QR before sending.
                                        </div>
                                    @endif
                                    <p class="text-muted small mb-3">
                                        Upload your design as PDF. When you save, page 1 is converted to a high-quality PNG automatically.
                                        Invitations are sent as that image (with each invitee’s unique QR) via WhatsApp or SMS.
                                    </p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('event.invitation.card-upload', ['event_id' => $selectedEvent->id, 'return' => 'send']) }}"
                                           class="btn btn-primary" id="configure-card-btn">
                                            <i class="fas fa-upload me-1"></i>
                                            {{ $cardDesign ? 'Edit PDF & QR position' : 'Upload PDF & place QR' }}
                                        </a>
                                        @if($cardDesign && $cardDesign->pdf_file_path)
                                            <a href="{{ asset('storage/' . $cardDesign->pdf_file_path) }}" target="_blank" rel="noopener"
                                               class="btn btn-outline-secondary">
                                                <i class="fas fa-eye me-1"></i> View template PDF
                                            </a>
                                        @endif
                                        <a href="{{ route('invitee.create', $selectedEvent->id) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-user-plus me-1"></i> Add invitees
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Invitation Preview</h6>
                            </div>
                            <div class="card-body">
                                <div class="invitation-preview">
                                    <div class="preview-header">
                                        <h4 id="preview-event-title">{{ $selectedEvent?->title ?? 'Select an event' }}</h4>
                                        <p class="mb-0" id="preview-event-date">
                                            @if($selectedEvent)
                                                {{ $selectedEvent->date->format('F j, Y \a\t g:i A') }}
                                            @else
                                                Event date will appear here
                                            @endif
                                        </p>
                                    </div>
                                    <div class="preview-body">
                                        @if($selectedEvent)
                                            <p>{{ Str::limit($selectedEvent->description, 200) ?: 'Your uploaded PDF is the visual invitation; this summary is for reference only.' }}</p>
                                            <p><i class="fas fa-map-marker-alt me-2"></i> <strong>Venue:</strong> {{ $selectedEvent->location }}</p>
                                            <p><i class="fas fa-user me-2"></i> <strong>Organizer:</strong> {{ $selectedEvent->organizer_name }}</p>
                                        @else
                                            <p class="text-muted">Select an event to see details. The actual invitation is your uploaded PDF with a personalized QR code.</p>
                                        @endif
                                    </div>
                                    <div class="preview-footer">
                                        <p class="mb-1">We look forward to celebrating with you!</p>
                                        <p class="mb-0">Please RSVP by December 10, 2023</p>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <label for="custom-message" class="form-label">Custom Message (Optional)</label>
                                    <textarea class="form-control" id="custom-message" rows="3" placeholder="Add a personal message to your invitation..."></textarea>
                                    <div class="form-text">This message will be included with your invitation.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Payment Summary</h6>
                                <span class="badge bg-primary" id="payment-status">Pending</span>
                            </div>
                            <div class="card-body">
                                <div class="payment-summary mb-4">
                                    <div class="cost-breakdown">
                                        <div class="cost-item">
                                            <span>Invitation Cost per Card:</span>
                                            <span id="cost-per-card">Tsh {{ number_format($invitationCostTsh) }}</span>
                                        </div>
                                        <div class="cost-item">
                                            <span>Number of Selected Invitees:</span>
                                            <span id="cost-invitee-count">0</span>
                                        </div>
                                        <div class="cost-item">
                                            <span>Delivery method:</span>
                                            <span id="cost-delivery-method">Auto (WhatsApp → SMS)</span>
                                        </div>
                                        <div class="total-cost">
                                            <span>Total Cost:</span>
                                            <span id="cost-total">Tsh 0</span>
                                        </div>
                                        <div class="cost-item mt-2">
                                            <span>Balance after send:</span>
                                            <span id="cost-remaining">Tsh {{ number_format($walletBalance) }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="mb-3">Payment Method</h6>
                                    <div class="payment-method selected" data-method="wallet">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-wallet text-primary payment-icon"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1">Account Wallet</h6>
                                                <p class="mb-0 text-muted">Use your wallet: <span id="wallet-balance-inline">Tsh {{ number_format($walletBalance) }}</span></p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-check text-primary"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="payment-method" data-method="card">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-credit-card text-primary payment-icon"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1">Credit/Debit Card</h6>
                                                <p class="mb-0 text-muted">Pay with your card</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="payment-method" data-method="paypal">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fab fa-paypal text-primary payment-icon"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1">PayPal</h6>
                                                <p class="mb-0 text-muted">Pay with your PayPal account</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button class="btn btn-success btn-lg" id="send-invitations-btn" disabled>
                                        <i class="fas fa-paper-plane me-2"></i> Send Invitations
                                    </button>
                                </div>
                                
                                <div class="mt-3 text-center">
                                    <small class="text-muted">
                                        By sending invitations, you agree to our 
                                        <a href="#">Terms of Service</a> and confirm you have permission to contact these individuals.
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Sending Statistics</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h5 class="text-primary mb-0" id="stat-total-sent">{{ $deliveryStats['sent'] ?? 0 }}</h5>
                                            <small class="text-muted">Sent (this event)</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="text-success mb-0" id="stat-delivery-rate">{{ ($deliveryStats['rate'] ?? 0) > 0 ? ($deliveryStats['rate'].'%') : '—' }}</h5>
                                        <small class="text-muted">Success rate</small>
                                    </div>
                                </div>
                                @if($selectedEvent)
                                    <div class="mt-3 text-center">
                                        <a href="{{ route('event.invitation.delivery-report', $selectedEvent) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-chart-bar me-1"></i> Full delivery report
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Complete Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="wallet-payment" class="payment-tab">
                        <div class="text-center mb-4">
                            <i class="fas fa-wallet text-primary" style="font-size: 3rem;"></i>
                            <h4 class="mt-3">Pay from Wallet</h4>
                        </div>
                        
                        <div class="payment-summary mb-4">
                            <h6 class="mb-3">Order Summary</h6>
                            <div class="cost-breakdown">
                                <div class="cost-item">
                                    <span>Total Cost:</span>
                                    <span id="modal-total-cost">$0.00</span>
                                </div>
                                <div class="cost-item">
                                    <span>Current Balance:</span>
                                    <span>$245.00</span>
                                </div>
                                <div class="total-cost">
                                    <span>Remaining Balance:</span>
                                    <span id="modal-remaining-balance">$245.00</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button class="btn btn-success btn-lg" id="confirm-wallet-payment">
                                <i class="fas fa-check me-2"></i> Confirm Payment
                            </button>
                        </div>
                    </div>
                    
                    <div id="card-payment" class="payment-tab" style="display: none;">
                        <!-- Card payment form would go here -->
                        <p>Card payment form would be implemented here.</p>
                    </div>
                    
                    <div id="paypal-payment" class="payment-tab" style="display: none;">
                        <!-- PayPal payment would go here -->
                        <p>PayPal payment integration would be implemented here.</p>
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
                    <h5 class="modal-title" id="successModalLabel"><i class="fas fa-check-circle me-2"></i> Invitations Sent Successfully!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-paper-plane text-success mb-3" style="font-size: 4rem;"></i>
                    <h4 class="mb-3">Your invitations are on their way!</h4>
                    <p class="mb-0">We've emailed <strong id="sent-count">0</strong> invitation card(s) to your guests.</p>
                    <p id="send-failures" class="text-danger small mb-0 d-none"></p>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-success" id="view-delivery-report-btn">View Delivery Report</a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const cardUploadBaseUrl = @json(route('event.invitation.card-upload'));
        const inviteesJsonBase = @json(url('/event'));
        const inviteeCreateBase = @json(url('event/invitees/create'));
        const hasCardDesign = @json((bool) $cardDesign && (bool) ($cardDesign->template_image_path ?? false));
        let walletBalance = @json($walletBalance);
        const invitationCost = @json($invitationCostTsh);
        let invitees = [];
        const selectedInvitees = new Set();
        let currentEventId = @json($selectedEvent?->id);
        let lastDeliveryReportUrl = @json($selectedEvent ? route('event.invitation.delivery-report', $selectedEvent) : null);

        function formatTsh(amount) {
            return 'Tsh ' + Number(amount).toLocaleString('en-TZ');
        }

        function updateWalletDisplay() {
            const text = formatTsh(walletBalance);
            document.getElementById('wallet-balance-display').textContent = text;
            const inline = document.getElementById('wallet-balance-inline');
            if (inline) inline.textContent = text;
        }

        function phoneOnWhatsApp(phone) {
            const digits = String(phone || '').replace(/\D/g, '');
            if (digits.length < 9) return false;
            return (parseInt(digits.slice(-1), 10) % 2) === 0;
        }

        function selectedDeliveryMode() {
            return document.querySelector('input[name="invitationMethod"]:checked')?.value || 'auto';
        }

        function deliveryModeLabel(mode) {
            if (mode === 'whatsapp') return 'WhatsApp only';
            if (mode === 'sms') return 'SMS only (code)';
            return 'Auto (WhatsApp → SMS)';
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            if (currentEventId) {
                loadInviteesForEvent(currentEventId);
            } else {
                renderInviteesList();
            }
            updateCostSummary();
        });

        async function loadInviteesForEvent(eventId) {
            const container = document.getElementById('invitees-list');
            container.innerHTML = '<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Loading invitees…</div>';
            selectedInvitees.clear();

            try {
                const response = await fetch(`${inviteesJsonBase}/${eventId}/invitees.json`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                invitees = (data.invitees || []).map(function (inv) {
                    return {
                        id: inv.id,
                        name: inv.name,
                        email: inv.email,
                        phone: inv.phone || '',
                        hasWhatsApp: phoneOnWhatsApp(inv.phone)
                    };
                });
            } catch (e) {
                invitees = [];
            }

            renderInviteesList();
            updateCostSummary();
        }
        
        // Render the invitees list
        function renderInviteesList() {
            const container = document.getElementById('invitees-list');
            const totalCount = document.getElementById('total-count');
            
            if (invitees.length === 0) {
                const addHint = currentEventId
                    ? `<a href="${inviteeCreateBase}/${currentEventId}" class="btn btn-sm btn-primary mt-2">Add invitees</a>`
                    : '';
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-user-friends"></i>
                        <h4>No invitees found</h4>
                        <p>There are no invitees for the selected event.</p>
                        ${addHint}
                    </div>
                `;
                totalCount.textContent = '0';
                return;
            }
            
            container.innerHTML = invitees.map(invitee => `
                <div class="invitee-item ${selectedInvitees.has(invitee.id) ? 'selected' : ''}" data-id="${invitee.id}">
                    <div class="form-check me-3">
                        <input class="form-check-input invitee-checkbox" type="checkbox" value="${invitee.id}" 
                               ${selectedInvitees.has(invitee.id) ? 'checked' : ''}>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${invitee.name}</h6>
                                <p class="mb-1 text-muted small">${invitee.email}</p>
                                <p class="mb-0 text-muted small">${invitee.phone}</p>
                            </div>
                            <div>
                                <span class="method-badge ${invitee.hasWhatsApp ? 'badge-whatsapp' : 'badge-sms'}">
                                    <i class="fab ${invitee.hasWhatsApp ? 'fa-whatsapp' : 'fa-sms'} me-1"></i>
                                    ${invitee.hasWhatsApp ? 'WhatsApp' : 'SMS'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            totalCount.textContent = invitees.length;
            updateSelectedCount();
        }
        
        // Set up event listeners
        function setupEventListeners() {
            document.getElementById('event-select').addEventListener('change', function() {
                const eventId = this.value;
                if (eventId) {
                    window.location.href = @json(route('event.invitation.send')) + '?event_id=' + encodeURIComponent(eventId);
                } else {
                    window.location.href = @json(route('event.invitation.send'));
                }
            });

            // Invitee selection
            document.getElementById('invitees-list').addEventListener('click', function(e) {
                const inviteeItem = e.target.closest('.invitee-item');
                if (inviteeItem) {
                    const id = parseInt(inviteeItem.getAttribute('data-id'));
                    const checkbox = inviteeItem.querySelector('.invitee-checkbox');
                    
                    if (selectedInvitees.has(id)) {
                        selectedInvitees.delete(id);
                        checkbox.checked = false;
                        inviteeItem.classList.remove('selected');
                    } else {
                        selectedInvitees.add(id);
                        checkbox.checked = true;
                        inviteeItem.classList.add('selected');
                    }
                    
                    updateSelectedCount();
                    updateCostSummary();
                }
            });
            
            // Select all / deselect all
            document.getElementById('select-all-btn').addEventListener('click', function() {
                invitees.forEach(invitee => {
                    selectedInvitees.add(invitee.id);
                });
                renderInviteesList();
                updateCostSummary();
            });
            
            document.getElementById('deselect-all-btn').addEventListener('click', function() {
                selectedInvitees.clear();
                renderInviteesList();
                updateCostSummary();
            });
            
            // Payment method selection
            document.querySelectorAll('.payment-method').forEach(method => {
                method.addEventListener('click', function() {
                    document.querySelectorAll('.payment-method').forEach(m => {
                        m.classList.remove('selected');
                        m.querySelector('.fa-check')?.classList.add('d-none');
                    });
                    
                    this.classList.add('selected');
                    const checkIcon = this.querySelector('.fa-check');
                    if (checkIcon) checkIcon.classList.remove('d-none');
                });
            });
            
            // Send invitations by email (testing)
            document.getElementById('send-invitations-btn').addEventListener('click', async function() {
                if (!currentEventId) {
                    alert('Please select an event first.');
                    return;
                }
                if (!hasCardDesign) {
                    alert('Upload and save a PDF card design for this event before sending.');
                    window.location.href = cardUploadBaseUrl + '?event_id=' + encodeURIComponent(currentEventId) + '&return=send';
                    return;
                }
                if (selectedInvitees.size === 0) {
                    alert('Please select at least one invitee to send invitations to.');
                    return;
                }

                const btn = this;
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending…';

                try {
                    const response = await fetch(sendInvitationsUrl(currentEventId), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            invitee_ids: Array.from(selectedInvitees),
                            delivery_mode: selectedDeliveryMode(),
                        }),
                    });

                    const data = await response.json();
                    const failuresEl = document.getElementById('send-failures');

                    if (data.success) {
                        document.getElementById('sent-count').textContent = data.sent_count;
                        if (typeof data.wallet_balance === 'number') {
                            walletBalance = data.wallet_balance;
                            updateWalletDisplay();
                            updateCostSummary();
                        }
                        if (data.delivery_report_url) {
                            lastDeliveryReportUrl = data.delivery_report_url;
                            document.getElementById('view-delivery-report-btn').href = data.delivery_report_url;
                            const reportLink = document.getElementById('delivery-report-link');
                            if (reportLink) reportLink.href = data.delivery_report_url;
                        }
                        if (data.failed_count > 0 && failuresEl) {
                            failuresEl.textContent = data.failed_count + ' could not be sent. Check logs for details.';
                            failuresEl.classList.remove('d-none');
                        } else if (failuresEl) {
                            failuresEl.classList.add('d-none');
                        }
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();
                        selectedInvitees.clear();
                        await loadInviteesForEvent(currentEventId);
                    } else {
                        let msg = data.message || 'Failed to send invitations.';
                        if (data.failed && data.failed.length) {
                            msg += '\n\n' + data.failed.map(f => (f.email || f.id) + ': ' + f.message).join('\n');
                        }
                        alert(msg);
                    }
                } catch (e) {
                    alert('Network error while sending invitations.');
                } finally {
                    btn.innerHTML = originalHtml;
                    updateSelectedCount();
                }
            });
            
            if (lastDeliveryReportUrl) {
                document.getElementById('view-delivery-report-btn').href = lastDeliveryReportUrl;
            }

            // Invitation method change
            document.querySelectorAll('input[name="invitationMethod"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    updateCostSummary();
                });
            });
        }
        
        function sendInvitationsUrl(eventId) {
            return `${inviteesJsonBase}/${eventId}/invitation/send`;
        }
        
        // Update the selected invitees count
        function updateSelectedCount() {
            document.getElementById('selected-count').textContent = selectedInvitees.size;
            updateCostSummary();
        }
        
        // Calculate and update the cost summary
        function updateCostSummary() {
            const selectedCount = selectedInvitees.size;
            const totalCost = selectedCount * invitationCost;
            const remaining = walletBalance - totalCost;

            document.getElementById('cost-invitee-count').textContent = selectedCount;
            document.getElementById('cost-total').textContent = formatTsh(totalCost);
            document.getElementById('cost-remaining').textContent = formatTsh(Math.max(0, remaining));
            const modeEl = document.getElementById('cost-delivery-method');
            if (modeEl) modeEl.textContent = deliveryModeLabel(selectedDeliveryMode());

            const paymentStatus = document.getElementById('payment-status');
            const sendBtn = document.getElementById('send-invitations-btn');

            if (selectedCount === 0) {
                paymentStatus.textContent = 'Pending';
                paymentStatus.className = 'badge bg-secondary';
                sendBtn.disabled = true;
            } else if (remaining < 0) {
                paymentStatus.textContent = 'Insufficient';
                paymentStatus.className = 'badge bg-danger';
                sendBtn.disabled = true;
            } else {
                paymentStatus.textContent = 'Ready';
                paymentStatus.className = 'badge bg-primary';
                sendBtn.disabled = false;
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