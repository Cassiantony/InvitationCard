<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Invitations - Invitation System</title>
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
                            <a class="nav-link" href="{{ route('invitee.create') }}">
                                <i class="fas fa-user-friends"></i>
                                Invitees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="">
                                <i class="fas fa-paper-plane"></i>
                                Send Invitations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-wallet"></i>
                                Billing
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
                
                <!-- Balance Information -->
                <div class="balance-info">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-1">Your Account Balance</h5>
                            <p class="mb-0">Current balance available for sending invitations</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h2 class="mb-0">$245.00</h2>
                            <a href="#" class="text-white-50">Add Funds <i class="fas fa-arrow-right ms-1"></i></a>
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
                                            <option value="1" selected>Annual Company Gala (15 Dec, 2023)</option>
                                            <option value="2">Product Launch Event (10 Jan, 2024)</option>
                                            <option value="3">Client Appreciation Dinner (22 Dec, 2023)</option>
                                            <option value="4">Team Building Workshop (05 Nov, 2023)</option>
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
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Invitation Preview</h6>
                            </div>
                            <div class="card-body">
                                <div class="invitation-preview">
                                    <div class="preview-header">
                                        <h4 id="preview-event-title">Annual Company Gala</h4>
                                        <p class="mb-0" id="preview-event-date">December 15, 2023 at 7:00 PM</p>
                                    </div>
                                    <div class="preview-body">
                                        <p>You are cordially invited to our Annual Company Gala celebrating another successful year.</p>
                                        <p><i class="fas fa-map-marker-alt me-2"></i> <strong>Venue:</strong> Grand Ballroom, City Center</p>
                                        <p><i class="fas fa-user me-2"></i> <strong>Organizer:</strong> ABC Corporation</p>
                                        <p><i class="fas fa-info-circle me-2"></i> <strong>Dress Code:</strong> Formal Attire</p>
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
                                            <span>$10.00</span>
                                        </div>
                                        <div class="cost-item">
                                            <span>Number of Selected Invitees:</span>
                                            <span id="cost-invitee-count">0</span>
                                        </div>
                                        <div class="cost-item">
                                            <span>WhatsApp Delivery (Estimated):</span>
                                            <span id="cost-whatsapp">0</span>
                                        </div>
                                        <div class="cost-item">
                                            <span>SMS Delivery (Estimated):</span>
                                            <span id="cost-sms">0</span>
                                        </div>
                                        <div class="cost-item">
                                            <span>Service Fee (5%):</span>
                                            <span id="cost-service">$0.00</span>
                                        </div>
                                        <div class="total-cost">
                                            <span>Total Cost:</span>
                                            <span id="cost-total">$0.00</span>
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
                                                <p class="mb-0 text-muted">Use your available balance: $245.00</p>
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
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Invitations Sent This Month</span>
                                        <span>45/100</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 45%"></div>
                                    </div>
                                </div>
                                
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h5 class="text-primary mb-0">128</h5>
                                            <small class="text-muted">Total Sent</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="text-success mb-0">87%</h5>
                                        <small class="text-muted">Delivery Rate</small>
                                    </div>
                                </div>
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
                    <p class="mb-0">We've successfully sent <strong id="sent-count">0</strong> invitations to your guests.</p>
                    <p>You will receive a delivery report shortly.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">View Delivery Report</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sample invitees data
        const invitees = [
            { id: 1, name: "John Smith", email: "john.smith@example.com", phone: "+1234567890", hasWhatsApp: true },
            { id: 2, name: "Sarah Johnson", email: "sarah.j@example.com", phone: "+1234567891", hasWhatsApp: true },
            { id: 3, name: "Michael Brown", email: "michael.b@example.com", phone: "+1234567892", hasWhatsApp: false },
            { id: 4, name: "Emily Davis", email: "emily.davis@example.com", phone: "+1234567893", hasWhatsApp: true },
            { id: 5, name: "Robert Wilson", email: "robert.w@example.com", phone: "+1234567894", hasWhatsApp: true },
            { id: 6, name: "Jennifer Lee", email: "jennifer.lee@example.com", phone: "+1234567895", hasWhatsApp: false },
            { id: 7, name: "David Miller", email: "david.m@example.com", phone: "+1234567896", hasWhatsApp: true },
            { id: 8, name: "Amanda Taylor", email: "amanda.t@example.com", phone: "+1234567897", hasWhatsApp: true }
        ];
        
        const selectedInvitees = new Set();
        const invitationCost = 10.00; // $10 per invitation
        const serviceFeeRate = 0.05; // 5% service fee
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            renderInviteesList();
            updateCostSummary();
            setupEventListeners();
        });
        
        // Render the invitees list
        function renderInviteesList() {
            const container = document.getElementById('invitees-list');
            const totalCount = document.getElementById('total-count');
            
            if (invitees.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-user-friends"></i>
                        <h4>No invitees found</h4>
                        <p>There are no invitees for the selected event.</p>
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
            
            // Send invitations button
            document.getElementById('send-invitations-btn').addEventListener('click', function() {
                if (selectedInvitees.size === 0) {
                    alert('Please select at least one invitee to send invitations to.');
                    return;
                }
                
                const paymentMethod = document.querySelector('.payment-method.selected').getAttribute('data-method');
                
                if (paymentMethod === 'wallet') {
                    // Show wallet payment modal
                    document.getElementById('modal-total-cost').textContent = calculateTotalCost().toFixed(2);
                    document.getElementById('modal-remaining-balance').textContent = 
                        (245.00 - calculateTotalCost()).toFixed(2);
                    
                    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
                    paymentModal.show();
                } else {
                    // For other payment methods, we would show different payment flows
                    alert(`Redirecting to ${paymentMethod} payment...`);
                }
            });
            
            // Confirm wallet payment
            document.getElementById('confirm-wallet-payment').addEventListener('click', function() {
                // In a real application, this would process the payment via API
                
                // Close payment modal
                const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                paymentModal.hide();
                
                // Show success modal
                document.getElementById('sent-count').textContent = selectedInvitees.size;
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                
                // Reset form
                selectedInvitees.clear();
                renderInviteesList();
                updateCostSummary();
            });
            
            // Invitation method change
            document.querySelectorAll('input[name="invitationMethod"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    updateCostSummary();
                });
            });
        }
        
        // Update the selected invitees count
        function updateSelectedCount() {
            document.getElementById('selected-count').textContent = selectedInvitees.size;
            
            // Enable/disable send button based on selection
            document.getElementById('send-invitations-btn').disabled = selectedInvitees.size === 0;
        }
        
        // Calculate and update the cost summary
        function updateCostSummary() {
            const selectedCount = selectedInvitees.size;
            const invitationMethod = document.querySelector('input[name="invitationMethod"]:checked').value;
            
            let whatsappCount = 0;
            let smsCount = 0;
            
            if (invitationMethod === 'auto') {
                // Auto-detect: use WhatsApp if available, otherwise SMS
                invitees.forEach(invitee => {
                    if (selectedInvitees.has(invitee.id)) {
                        if (invitee.hasWhatsApp) {
                            whatsappCount++;
                        } else {
                            smsCount++;
                        }
                    }
                });
            } else if (invitationMethod === 'whatsapp') {
                // WhatsApp only
                whatsappCount = selectedCount;
            } else if (invitationMethod === 'sms') {
                // SMS only
                smsCount = selectedCount;
            }
            
            const baseCost = selectedCount * invitationCost;
            const serviceFee = baseCost * serviceFeeRate;
            const totalCost = baseCost + serviceFee;
            
            // Update cost display
            document.getElementById('cost-invitee-count').textContent = selectedCount;
            document.getElementById('cost-whatsapp').textContent = `$${(whatsappCount * invitationCost).toFixed(2)}`;
            document.getElementById('cost-sms').textContent = `$${(smsCount * invitationCost).toFixed(2)}`;
            document.getElementById('cost-service').textContent = `$${serviceFee.toFixed(2)}`;
            document.getElementById('cost-total').textContent = `$${totalCost.toFixed(2)}`;
            
            // Update payment status
            const paymentStatus = document.getElementById('payment-status');
            if (selectedCount === 0) {
                paymentStatus.textContent = 'Pending';
                paymentStatus.className = 'badge bg-secondary';
            } else {
                paymentStatus.textContent = 'Ready';
                paymentStatus.className = 'badge bg-primary';
            }
        }
        
        // Calculate total cost for payment
        function calculateTotalCost() {
            const selectedCount = selectedInvitees.size;
            const baseCost = selectedCount * invitationCost;
            const serviceFee = baseCost * serviceFeeRate;
            return baseCost + serviceFee;
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