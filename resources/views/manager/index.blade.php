<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, shrink-to-fit=no">
    <title>Admin Dashboard | Responsive Manager Management</title>
    <!-- Bootstrap 5 CSS + Icons + Google Fonts + jQuery (optional for nice effects) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        :root {
            --primary: #4e73df;
            --primary-dark: #2e59d9;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --sidebar-bg-start: #4e73df;
            --sidebar-bg-end: #224abe;
        }
        body {
            background-color: #f8f9fc;
            overflow-x: hidden;
        }
        /* ========== FULLY RESPONSIVE SIDEBAR (Collapsible on mobile) ========== */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg-start) 0%, var(--sidebar-bg-end) 100%);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                left: -280px;
                width: 280px;
                transition: left 0.3s ease;
                z-index: 1050;
                overflow-y: auto;
            }
            .sidebar.show-sidebar {
                left: 0;
            }
            .overlay-blur {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.4);
                z-index: 1040;
                display: none;
            }
            .overlay-blur.active {
                display: block;
            }
            .main-content-full {
                width: 100%;
            }
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 0.85rem 1rem;
            font-weight: 500;
            border-radius: 0.35rem;
            margin-bottom: 0.2rem;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.15);
        }
        .sidebar .nav-link i {
            width: 1.8rem;
            text-align: center;
        }
        .sidebar .brand {
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        /* Topbar responsive */
        .topbar {
            background: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.08);
            padding: 0.75rem 1rem;
            border-radius: 0 0 0.5rem 0.5rem;
            margin-bottom: 1.5rem;
        }
        .notification-badge {
            position: absolute;
            top: -6px;
            right: -8px;
            background: var(--danger);
            border-radius: 50%;
            font-size: 0.65rem;
            padding: 0.2rem 0.45rem;
            font-weight: bold;
            color: white;
        }
        .stat-card {
            border-left: 0.25rem solid var(--primary);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08);
        }
        .manager-table-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.05);
        }
        .avatar-sm {
            width: 34px;
            height: 34px;
            background: #e9ecef;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--primary);
        }
        .table-responsive-stack {
            overflow-x: auto;
        }
        .badge-manager {
            background-color: #e3f2fd;
            color: #0b5ed7;
            padding: 0.35rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        /* responsive table adjustments */
        @media (max-width: 576px) {
            .table thead {
                display: none;
            }
            .table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #e3e6f0;
                border-radius: 0.75rem;
                background: white;
                padding: 0.75rem;
            }
            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                border: none;
                padding: 0.5rem 0;
                border-bottom: 1px solid #f0f0f0;
            }
            .table tbody td:last-child {
                border-bottom: none;
            }
            .table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                float: left;
                color: #5a5c69;
                font-size: 0.85rem;
            }
        }
        .modal-content {
            border-radius: 1rem;
        }
        footer {
            font-size: 0.8rem;
            color: #858796;
        }
        .btn-close-white-custom {
            filter: brightness(0) invert(1);
        }
        .toast-container-fixed {
            z-index: 1100;
        }
    </style>
</head>
<body>

<!-- Mobile Sidebar Overlay -->
<div class="overlay-blur" id="mobileOverlay"></div>

<div class="container-fluid px-0">
    <div class="row g-0">
        <!-- SIDEBAR (Responsive, togglable on mobile) -->
        <div class="col-md-3 col-lg-2 sidebar" id="mainSidebar">
            <div class="position-sticky pt-4 px-3">
                <div class="text-center mb-4 brand pb-3 d-flex justify-content-between align-items-center d-md-none">
                    <h4 class="text-white mb-0"><i class="fas fa-envelope-open-text me-2"></i>InviteFlow</h4>
                    <button class="btn btn-sm btn-light rounded-circle" id="closeSidebarMobile"><i class="fas fa-times"></i></button>
                </div>
                <div class="text-center mb-4 brand pb-3 d-none d-md-block">
                    <i class="fas fa-envelope-open-text fa-2x text-white"></i>
                    <h4 class="text-white mt-2 mb-0">InviteFlow</h4>
                    <p class="text-white-50 small">Admin Portal</p>
                </div>
                <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('manage.managers') ? 'active' : '' }}" href="{{ route('manage.managers') }}">
                                <i class="fas fa-users-cog"></i>
                                Managers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('event.create') ? 'active' : '' }}" href="{{ route('event.create') }}">
                                <i class="fas fa-calendar-plus"></i>
                                Create Event
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('event.index') ? 'active' : '' }}" href="{{ route('event.index') }}">
                                <i class="fas fa-calendar-alt"></i>
                                My Events
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('invitee.create', 'event.show') ? 'active' : '' }}" href="{{ route('event.index') }}">
                                <i class="fas fa-user-plus"></i>
                                Add Invitees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('design.*') ? 'active' : '' }}" href="{{ route('design.create') }}">
                                <i class="fas fa-palette"></i>
                                Design Card
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('send.*') ? 'active' : '' }}" href="{{ route('event.invitation.send') }}">
                                <i class="fas fa-paper-plane"></i>
                                Send Invitations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}" href="#">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                        </li>                   
                    <li class="nav-item mt-4">
                        <hr class="bg-light opacity-25">
                        <a class="nav-link" href="{{ route('logout') }}">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                    </ul>
                <div class="mt-4 d-none d-md-block text-center text-white-50 small">
                    <i class="fas fa-shield-alt"></i> v2.0 • secure
                </div>
            </div>
        </div>


        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 ms-sm-auto px-3 px-md-4 main-content-full" id="mainContent">
            <!-- Topbar with mobile menu toggler -->
            <div class="topbar d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link d-md-none text-dark me-2" id="mobileMenuToggle">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <div>
                        <h5 class="mb-0 fw-semibold"><i class="fas fa-user-shield me-2 text-primary"></i> Admin Control <span class="text-muted fs-6 d-none d-sm-inline">| Manager Hub</span></h5>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-2 mt-sm-0">
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill dropdown-toggle px-3" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i> <span class="position-relative">🔔<span class="notification-badge">3</span></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-check-circle text-success"></i> New manager assigned</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-week"></i> Event reminder</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">View all</a></li>
                        </ul>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?background=4e73df&color=fff&name=Admin" class="rounded-circle" width="38" height="38" alt="admin">
                        <span class="ms-2 d-none d-md-inline fw-semibold">Alex Carter</span>
                    </div>
                </div>
            </div>

            <!-- Stats Row Responsive -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase fw-bold">Managers</div>
                            <div class="h2 fw-bold" id="totalManagersStat">{{ $managers->count() }}</div>
                            <div class="text-success small"><i class="fas fa-arrow-up"></i> +2 new</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase fw-bold">Active Events</div>
                            <div class="h2 fw-bold">12</div>
                            <div class="text-info small"><i class="fas fa-calendar-check"></i> Managed</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase fw-bold">Invitations</div>
                            <div class="h2 fw-bold">1.2k</div>
                            <div class="text-success small"><i class="fas fa-envelope"></i> +56 today</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase fw-bold">RSVP Rate</div>
                            <div class="h2 fw-bold">74%</div>
                            <div class="text-warning small"><i class="fas fa-chart-line"></i> +5%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MANAGERS TABLE (Fully responsive) -->
            <div class="card manager-table-card mb-5">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <span><i class="fas fa-list-ul me-2 text-primary"></i> <strong>All Managers</strong> <span id="managerCountBadge" class="badge bg-primary rounded-pill ms-2">{{ $managers->count() }}</span></span>
                    <button class="btn btn-sm btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addManagerModal">
                        <i class="fas fa-plus-circle"></i> <span class="d-none d-sm-inline">Add Manager</span><span class="d-inline d-sm-none"><i class="fas fa-user-plus"></i></span>
                    </button>
                </div>
                <div class="card-body p-0">
                    @if (session('error'))
                        <div class="alert alert-danger m-3">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger m-3">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="table-responsive-stack">
                        <table class="table table-hover align-middle mb-0" id="managersTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="managersTableBody">
                                @forelse ($managers as $index => $manager)
                                    <tr>
                                        <td data-label="#">{{ $index + 1 }}</td>
                                        <td data-label="Full Name">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2"><i class="fas fa-user-tie"></i></div>
                                                {{ $manager->name }}
                                            </div>
                                        </td>
                                        <td data-label="Email">
                                            <a href="mailto:{{ $manager->email }}" class="text-decoration-none">{{ $manager->email }}</a>
                                        </td>
                                        <td data-label="Role"><span class="badge-manager">Manager</span></td>
                                        <td data-label="Phone">{{ $manager->phone ?: '—' }}</td>
                                        <td data-label="Status"><span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill"><i class="fas fa-check-circle"></i> Active</span></td>
                                        <td data-label="Action">
                                        <div class="btn-group btn-group-sm gap-2 admin-actions">
                                            <button href="/owner/user/{{ $manager->id }}/edit" disable class="btn btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button
                                                class="btn btn-outline-danger"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteAdminModal"
                                                data-delete-url="{{ route('manage.managers.destroy', $manager) }}"
                                                data-admin-name="{{ $manager->name }}"
                                            >
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-user-slash fa-2x mb-2 d-block"></i> No managers yet. Click "Add Manager" to create.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
            <footer class="text-center pb-4">
                &copy; 2025 InviteFlow — Fully responsive admin manager panel
            </footer>
        </div>
    </div>
</div>

<!-- BOOTSTRAP MODAL: ADD MANAGER (Fully responsive) -->
<div class="modal fade" id="addManagerModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4" style="background: linear-gradient(135deg, #4e73df, #224abe);">
                <h5 class="modal-title fw-bold" id="addManagerModalLabel"><i class="fas fa-user-plus me-2"></i> Create New Manager Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addManagerModalForm" method="POST" action="{{ route('manage.managers.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold"><i class="fas fa-user-circle me-1 text-primary"></i> Full Name *</label>
                            <input type="text" class="form-control" id="modalManagerName" name="name" placeholder="e.g., Sophia Rodriguez" required>
                            <div class="invalid-feedback">Name is required.</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold"><i class="fas fa-envelope me-1 text-primary"></i> Email Address *</label>
                            <input type="email" class="form-control" id="modalManagerEmail" name="email" placeholder="manager@example.com" required>
                            <div class="invalid-feedback">Valid email required.</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold"><i class="fas fa-phone-alt me-1 text-primary"></i> Phone (Optional)</label>
                            <input type="tel" class="form-control" id="modalManagerPhone" name="phone" placeholder="+1 234 567 8900">
                        </div>
                        <input type="hidden" id="modalManagerPasswordField" name="password">
                        <div class="col-12">
                            <label class="form-label fw-semibold"><i class="fas fa-key me-1 text-primary"></i> Temporary Password</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="modalTempPass" value="Manager@123" readonly>
                                <button class="btn btn-outline-secondary" type="button" id="modalGenPassBtn"><i class="fas fa-sync-alt"></i> Generate</button>
                            </div>
                            <small class="text-muted">Will be sent via welcome email.</small>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="modalSendEmail" checked>
                                <label class="form-check-label" for="modalSendEmail">
                                    <i class="fas fa-paper-plane text-primary"></i> Send welcome email with credentials
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light d-flex justify-content-between flex-wrap">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
                <button type="submit" class="btn btn-primary px-4" id="submitManagerBtn"><i class="fas fa-save me-1"></i> Create Manager</button>
            </div>
                </form>
            </div>
            
        </div>
    </div>
</div>


<div class="modal fade" id="deleteAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-custom">
            <div class="modal-header border-secondary">
                <h5 class="modal-title"><i class="fas fa-user-times me-2 text-danger"></i>Remove manager</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="deleteAdminForm" method="POST" action="">
                    @csrf
                    @method('DELETE')

                    <p class="mb-3">
                        You are removing <strong id="deleteAdminName">this manager</strong>. This cannot be undone.
                    </p>

                
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger" id="confirmDeleteAdminBtn"><i class="fas fa-trash-alt me-2"></i>Remove manager</button>
            </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function isValidEmail(email) {
        return /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/.test(email);
    }

    function showToastMessage(msg, type = 'success') {
        const toastContainer = document.createElement('div');
        toastContainer.className = 'position-fixed bottom-0 end-0 p-3 toast-container-fixed';
        toastContainer.style.zIndex = '9999';
        const toastDiv = document.createElement('div');
        toastDiv.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : (type === 'danger' ? 'danger' : 'warning')} border-0 show`;
        toastDiv.setAttribute('role', 'alert');
        toastDiv.innerHTML = `
            <div class="d-flex">
                <div class="toast-body"><i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i> ${msg}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        toastContainer.appendChild(toastDiv);
        document.body.appendChild(toastContainer);
        const bsToast = new bootstrap.Toast(toastDiv, { delay: 3000 });
        bsToast.show();
        setTimeout(() => {
            toastDiv.classList.remove('show');
            setTimeout(() => toastContainer.remove(), 300);
        }, 3200);
    }

    function generateRandomPassword() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%';
        let pass = '';
        for(let i=0;i<10;i++) pass += chars.charAt(Math.random() * chars.length);
        return pass;
    }

    function resetModalForm() {
        document.getElementById('modalManagerName').value = '';
        document.getElementById('modalManagerEmail').value = '';
        document.getElementById('modalManagerPhone').value = '';
        const password = generateRandomPassword();
        document.getElementById('modalTempPass').value = password;
        document.getElementById('modalManagerPasswordField').value = password;
        document.getElementById('modalSendEmail').checked = true;
        document.getElementById('modalManagerName').classList.remove('is-invalid');
        document.getElementById('modalManagerEmail').classList.remove('is-invalid');
    }

    // Mobile sidebar logic
    const sidebar = document.getElementById('mainSidebar');
    const overlay = document.getElementById('mobileOverlay');
    const menuToggle = document.getElementById('mobileMenuToggle');
    const closeSidebarBtn = document.getElementById('closeSidebarMobile');

    function openSidebar() {
        if(window.innerWidth < 768) {
            sidebar.classList.add('show-sidebar');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    function closeSidebar() {
        sidebar.classList.remove('show-sidebar');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    if(menuToggle) menuToggle.addEventListener('click', openSidebar);
    if(closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);
    window.addEventListener('resize', function() {
        if(window.innerWidth >= 768) {
            closeSidebar();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('addManagerModalForm');
        
        form.addEventListener('submit', (e) => {
            const name = document.getElementById('modalManagerName').value;
            const email = document.getElementById('modalManagerEmail').value;
            let valid = true;
            if(!name.trim()) { document.getElementById('modalManagerName').classList.add('is-invalid'); valid=false; }
            else { document.getElementById('modalManagerName').classList.remove('is-invalid'); }
            if(!email.trim() || !isValidEmail(email)) { document.getElementById('modalManagerEmail').classList.add('is-invalid'); valid=false; }
            else { document.getElementById('modalManagerEmail').classList.remove('is-invalid'); }
            if(!valid) e.preventDefault();
        });
        
        document.getElementById('modalGenPassBtn').addEventListener('click', () => {
            const password = generateRandomPassword();
            document.getElementById('modalTempPass').value = password;
            document.getElementById('modalManagerPasswordField').value = password;
            showToastMessage('New password generated', 'info');
        });

        document.getElementById('addManagerModal').addEventListener('show.bs.modal', () => {
            resetModalForm();
        });

        document.querySelectorAll('[data-bs-target="#deleteAdminModal"]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const form = document.getElementById('deleteAdminForm');
                const url = btn.getAttribute('data-delete-url');
                if (form && url) {
                    form.action = url;
                }
                const name = btn.getAttribute('data-admin-name') || 'this manager';
                const nameEl = document.getElementById('deleteAdminName');
                if (nameEl) {
                    nameEl.textContent = name;
                }
            });
        });
    });
</script>
</body>
</html>