<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Add Admin - Invito Owner Portal</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-glow: rgba(99, 102, 241, 0.2);
            --success: #22c55e;
            --warning: #eab308;
            --danger: #ef4444;
            --dark-bg: #0f172a;
            --card-bg: rgba(255, 255, 255, 0.05);
            --border-light: rgba(255, 255, 255, 0.08);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0b1120 0%, #111827 100%);
            color: #e2e8f0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styles - Matching Dashboard */
        .sidebar {
            background: rgba(15, 23, 42, 0.96);
            backdrop-filter: blur(14px);
            border-right: 1px solid var(--border-light);
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 1050;
            transition: transform 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            transform: translateX(0);
            overflow-y: auto;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show-sidebar {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0 !important;
                width: 100%;
            }
            .mobile-menu-toggle {
                display: inline-flex !important;
            }
        }

        @media (min-width: 992px) {
            .mobile-menu-toggle {
                display: none;
            }
            .main-content {
                margin-left: 260px;
            }
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: #1e2937; }
        .sidebar::-webkit-scrollbar-thumb { background: #475569; border-radius: 8px; }

        .nav-link {
            color: #94a3b8;
            padding: 12px 20px;
            border-radius: 12px;
            margin: 4px 12px;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        .nav-link i { width: 26px; margin-right: 10px; }
        .nav-link:hover, .nav-link.active {
            background: rgba(99, 102, 241, 0.2);
            color: white;
            transform: translateX(5px);
        }
        .nav-link.active {
            background: linear-gradient(90deg, rgba(99,102,241,0.25), transparent);
            border-left: 3px solid var(--primary);
        }

        /* Topbar */
        .topbar {
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-light);
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        /* Form Container */
        .form-container {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-light);
            border-radius: 32px;
            backdrop-filter: blur(2px);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            color: #cbd5e1;
        }

        .form-control, .form-select {
            background: #1e293b;
            border: 1px solid #334155;
            color: #f1f5f9;
            padding: 0.75rem 1rem;
            border-radius: 14px;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            background: #0f172a;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
            color: white;
        }

        .form-control::placeholder {
            color: #64748b;
        }

        .input-group-text {
            background: #1e293b;
            border: 1px solid #334155;
            color: #94a3b8;
            border-radius: 14px;
        }

        .btn-primary-custom {
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
            border: none;
            padding: 12px 28px;
            border-radius: 40px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
        }

        .btn-outline-secondary-custom {
            border: 1px solid #475569;
            color: #cbd5e1;
            border-radius: 40px;
            padding: 12px 28px;
            font-weight: 500;
        }

        .btn-outline-secondary-custom:hover {
            background: rgba(255,255,255,0.05);
            border-color: #6366f1;
            color: white;
        }

        .role-badge-demo {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .role-super { background: linear-gradient(135deg, #f59e0b, #f97316); color: #2c0f00; }
        .role-admin { background: #3b82f6; color: white; }
        .role-manager { background: #8b5cf6; color: white; }
        .role-viewer { background: #475569; color: #e2e8f0; }

        .info-box {
            background: rgba(99, 102, 241, 0.1);
            border-left: 3px solid var(--primary);
            border-radius: 16px;
            padding: 1rem;
        }

        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            z-index: 1040;
            display: none;
        }
        .sidebar-backdrop.show { display: block; }

        /* Toast Notification */
        .toast-custom {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1100;
            min-width: 280px;
            background: #1e293b;
            border: 1px solid rgba(99,102,241,0.4);
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem !important;
            }
            .btn-primary-custom, .btn-outline-secondary-custom {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar" id="mainSidebar">
        <div class="p-4 pb-2">
            <div class="d-flex align-items-center gap-3 mb-5">
                <div class="bg-white rounded-3 p-2 shadow-sm" style="border-radius: 18px !important;">
                    <i class="fas fa-envelope-open-text fa-2x text-primary"></i>
                </div>
                <div>
                    <h4 class="text-white mb-0 fw-bold">Invito</h4>
                    <small class="text-indigo-300">Owner Portal</small>
                </div>
            </div>
            <ul class="nav flex-column">
                <li><a href="#" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#" class="nav-link active"><i class="fas fa-users-cog"></i> Manage Admins</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-calendar-alt"></i> All Events</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-user-friends"></i> All Invitees</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-credit-card"></i> Payments & Revenue</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-chart-pie"></i> Analytics</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-cog"></i> System Settings</a></li>
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

    <!-- Main Content -->
    <div class="flex-grow-1 main-content" id="mainContent">
        <!-- Topbar -->
        <div class="topbar px-4 px-lg-5 py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-light mobile-menu-toggle" id="menuToggleBtn" style="border-radius: 12px;">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                <h3 class="mb-0 text-white fw-semibold" style="font-size: 1.6rem;">Add New Admin</h3>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="manage-admins.html" class="btn btn-outline-light rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i> Back to Admins
                </a>
            </div>
        </div>

        <div class="p-3 p-md-4 p-lg-5">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <!-- Main Form Card -->
                    <div class="form-container p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-opacity-10 d-inline-flex p-3 rounded-4 mb-3">
                                <i class="fas fa-user-shield fa-3x text-primary"></i>
                            </div>
                            <h2 class="text-white fw-bold">Invite Administrator</h2>
                            <p class="text-slate-400">Grant secure access to team members with granular permissions</p>
                        </div>

                        <!-- Laravel Form: action will be handled by your controller -->
                        <form id="addAdminForm" method="POST" action="" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Full Name -->
                            <div class="mb-4">
                                <label class="form-label required-field">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" name="name" id="name" 
                                           placeholder="e.g., Sarah Johnson" required autocomplete="off">
                                </div>
                                <small class="text-slate-500 mt-1 d-block">Enter the admin's legal full name</small>
                                <!-- Error handling - laravel will populate -->
                                <div class="invalid-feedback d-none" id="nameError"></div>
                            </div>

                            <!-- Email Address -->
                            <div class="mb-4">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" id="email" 
                                           placeholder="admin@invito.com" required autocomplete="off">
                                </div>
                                <small class="text-slate-500">Invitation will be sent to this email</small>
                                <div class="invalid-feedback d-none" id="emailError"></div>
                            </div>

                            <!-- Role Selection -->
                            <div class="mb-4">
                                <label class="form-label">Admin Role <span class="text-danger">*</span></label>
                                <select class="form-select" name="role" id="role" required>
                                    <option value="">Select permission level</option>
                                    <option value="Super Admin">🔱 Super Admin (Full System Access)</option>
                                    <option value="Admin">🛡️ Admin (Standard management)</option>
                                    <option value="Manager">📋 Manager (Event management only)</option>
                                    <option value="Viewer">👁️ Viewer (Read-only reports)</option>
                                </select>
                                <div class="mt-2" id="roleDescription">
                                    <small class="text-slate-400"><i class="fas fa-info-circle me-1"></i> Super Admins have full system control including billing.</small>
                                </div>
                            </div>

                            <!-- Temporary Password (optional but helpful) -->
                            <div class="mb-4">
                                <label class="form-label">Temporary Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input type="text" class="form-control" name="password" id="password" 
                                           placeholder="Leave blank to auto-generate">
                                    <button class="btn btn-outline-secondary" type="button" id="generatePasswordBtn">
                                        <i class="fas fa-sync-alt"></i> Generate
                                    </button>
                                </div>
                                <small class="text-slate-500">Auto-generated password will be sent via email</small>
                            </div>

                            <!-- Additional Permissions (Custom for Laravel) -->
                            <div class="mb-4 border-top border-secondary pt-3">
                                <label class="form-label fw-semibold">Additional Permissions</label>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="can_manage_events" id="canManageEvents" value="1" checked style="background-color: #6366f1;">
                                            <label class="form-check-label text-light" for="canManageEvents">
                                                <i class="fas fa-calendar-alt me-1"></i> Manage Events
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="can_view_revenue" id="canViewRevenue" value="1" style="background-color: #6366f1;">
                                            <label class="form-check-label text-light" for="canViewRevenue">
                                                <i class="fas fa-chart-line me-1"></i> View Revenue
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="can_invite_users" id="canInviteUsers" value="1" checked style="background-color: #6366f1;">
                                            <label class="form-check-label text-light" for="canInviteUsers">
                                                <i class="fas fa-user-plus me-1"></i> Invite Users
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="can_manage_admins" id="canManageAdmins" value="1" style="background-color: #6366f1;">
                                            <label class="form-check-label text-light" for="canManageAdmins">
                                                <i class="fas fa-users-cog me-1"></i> Manage Admins
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Send Invitation Checkbox -->
                            <div class="mb-4 p-3 rounded-3 bg-dark bg-opacity-25">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sendInvitationEmail" name="send_invitation" value="1" checked style="background-color: #6366f1;">
                                    <label class="form-check-label text-light fw-medium" for="sendInvitationEmail">
                                        <i class="fas fa-paper-plane me-1"></i> Send invitation email immediately
                                    </label>
                                </div>
                                <p class="small text-slate-400 mt-2 mb-0 ms-4">Admin will receive login credentials and setup instructions</p>
                            </div>

                            <!-- Info Box: Laravel integration hint -->
                            <div class="info-box mb-4">
                                <i class="fas fa-code-branch me-2 text-primary"></i>
                                <strong class="text-white">Laravel Backend Ready</strong>
                                <p class="small text-slate-300 mt-2 mb-0">
                                    This form uses @csrf and follows Laravel conventions. Controller should validate and store admin user.<br>
                                    <code class="text-primary">POST /admin/store</code> | Model: <code>Admin/User</code> with role assignment.
                                </p>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex flex-wrap gap-3 justify-content-end mt-4 pt-2">
                                <a href="" class="btn btn-outline-secondary-custom">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary-custom" id="submitBtn">
                                    <i class="fas fa-save me-2"></i> Create Admin & Send Invite
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Role Quick Reference -->
                    <div class="row mt-4 g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-dark bg-opacity-25 text-center">
                                <span class="role-badge-demo role-super mb-2">Super Admin</span>
                                <p class="small text-slate-400 mt-2 mb-0">Full control: users, events, billing, settings</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-dark bg-opacity-25 text-center">
                                <span class="role-badge-demo role-admin mb-2">Admin</span>
                                <p class="small text-slate-400 mt-2 mb-0">Manage events & invitees + limited settings</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification (Laravel session / JS simulation) -->
<div class="toast-custom toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true" id="liveToast" style="display: none;">
    <div class="d-flex">
        <div class="toast-body" id="toastMessage">
            <i class="fas fa-check-circle me-2 text-success"></i> Admin created successfully!
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar Responsive Logic (same as dashboard)
    const sidebar = document.getElementById('mainSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const toggleBtn = document.getElementById('menuToggleBtn');
    
    function closeSidebar() { 
        sidebar.classList.remove('show-sidebar'); 
        backdrop.classList.remove('show'); 
        document.body.style.overflow = ''; 
    }
    function openSidebar() { 
        sidebar.classList.add('show-sidebar'); 
        backdrop.classList.add('show'); 
        document.body.style.overflow = 'hidden'; 
    }
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => { 
            sidebar.classList.contains('show-sidebar') ? closeSidebar() : openSidebar(); 
        });
    }
    if (backdrop) backdrop.addEventListener('click', closeSidebar);
    window.addEventListener('resize', () => { 
        if (window.innerWidth >= 992) closeSidebar(); 
    });

    // Role description dynamic changer
    const roleSelect = document.getElementById('role');
    const roleDescDiv = document.getElementById('roleDescription');
    function updateRoleDescription() {
        const role = roleSelect.value;
        let desc = '';
        switch(role) {
            case 'Super Admin': desc = '🔱 <strong>Super Admin</strong>: Complete system ownership — can manage other admins, billing, global settings.'; break;
            case 'Admin': desc = '🛡️ <strong>Admin</strong>: Full event management, invitee handling, and reporting, but no admin management.'; break;
            case 'Manager': desc = '📋 <strong>Manager</strong>: Create & manage events, view invitee lists, no financial access.'; break;
            case 'Viewer': desc = '👁️ <strong>Viewer</strong>: Read-only access to dashboards and event analytics.'; break;
            default: desc = '<i class="fas fa-info-circle me-1"></i> Select a role to see permissions breakdown.';
        }
        roleDescDiv.innerHTML = `<small class="text-slate-300">${desc}</small>`;
    }
    roleSelect.addEventListener('change', updateRoleDescription);
    updateRoleDescription();

    // Generate random password helper (optional)
    const generateBtn = document.getElementById('generatePasswordBtn');
    const passwordField = document.getElementById('password');
    if (generateBtn) {
        generateBtn.addEventListener('click', () => {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
            let password = '';
            for (let i = 0; i < 12; i++) {
                password += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            passwordField.value = password;
        });
    }

    // --- Laravel Integration Note: Form Submission ---
    // The form action uses Laravel route. We'll handle client-side validation and optionally show spinner.
    const form = document.getElementById('addAdminForm');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', function(e) {
        // Let Laravel handle validation, but we can do basic required check
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        if (!name || !email) {
            e.preventDefault();
            alert('Please fill in name and email fields');
            return;
        }
        // Show loading state (optional)
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Creating...';
        // The form proceeds to POST to Laravel route
        // In case of validation errors, Laravel will redirect back with errors (you handle blade)
        // This page is ready to receive $errors from Laravel's validation.
    });

    // Display Toast when returns from successful store via session (flash message simulation)
    // Check URL parameters or Laravel session (we add a small JS to catch success flag)
    function showToast(message, isError = false) {
        const toastEl = document.getElementById('liveToast');
        const toastMsg = document.getElementById('toastMessage');
        if (isError) {
            toastMsg.innerHTML = `<i class="fas fa-exclamation-triangle me-2 text-warning"></i> ${message}`;
        } else {
            toastMsg.innerHTML = `<i class="fas fa-check-circle me-2 text-success"></i> ${message}`;
        }
        toastEl.style.display = 'flex';
        const bsToast = new bootstrap.Toast(toastEl, { delay: 4000, autohide: true });
        bsToast.show();
        setTimeout(() => { toastEl.style.display = 'none'; }, 4000);
    }

    // Check for Laravel session success message via a meta or injected variable.
    // For demo, but you can hook: if (session('success')) showToast.
    // This page also supports url hash trigger for demonstration.
    window.addEventListener('load', () => {
        // Example: if the URL contains ?created=1, simulate success (for testing without backend)
        if (window.location.search.includes('created=1')) {
            showToast('Admin was successfully created! Invitation email sent.');
        }
        // Reset button state in case of back navigation
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i> Create Admin & Send Invite';
    });

    // Client-side email validation highlight
    const emailInput = document.getElementById('email');
    emailInput.addEventListener('blur', function() {
        const emailRegex = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/;
        if (emailInput.value && !emailRegex.test(emailInput.value)) {
            emailInput.classList.add('is-invalid');
        } else {
            emailInput.classList.remove('is-invalid');
        }
    });
</script>
</body>
</html>