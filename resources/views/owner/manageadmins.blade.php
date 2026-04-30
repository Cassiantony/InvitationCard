<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Manage Admins - Invito Owner</title>
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

        /* ---- SIDEBAR (exactly matching dashboard style) ---- */
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

        /* topbar */
        .topbar {
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-light);
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        /* cards & admin table */
        .admin-card {
            background: var(--card-bg);
            border: 1px solid var(--border-light);
            border-radius: 28px;
            transition: all 0.2s;
        }
        .admin-table-container {
            background: rgba(255,255,255,0.02);
            border-radius: 28px;
            backdrop-filter: blur(2px);
        }
        .table {
            color: #e2e8f0;
            margin-bottom: 0;
        }
        .table th {
            color: #94a3b8;
            font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 1rem 0.75rem;
        }
        .table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-color: rgba(255,255,255,0.05);
        }
        .badge-role {
            padding: 6px 14px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.75rem;
        }
        .role-super {
            background: linear-gradient(135deg, #f59e0b, #f97316);
            color: #2c0f00;
        }
        .role-admin {
            background: #3b82f6;
            color: white;
        }
        .role-manager {
            background: #8b5cf6;
            color: white;
        }
        .role-viewer {
            background: #475569;
            color: #e2e8f0;
        }
        .status-badge {
            background: #22c55e20;
            border: 1px solid #22c55e40;
            color: #86efac;
        }
        .avatar-placeholder {
            width: 42px;
            height: 42px;
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border-radius: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-outline-glass {
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.02);
            color: #cbd5e1;
            transition: all 0.2s;
        }
        .btn-outline-glass:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        .btn-primary-glass {
            background: rgba(99,102,241,0.2);
            border: 1px solid rgba(99,102,241,0.5);
            color: #c7d2fe;
        }
        .btn-primary-glass:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        .search-input {
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            border-radius: 60px;
            padding: 0.6rem 1.2rem;
        }
        .search-input:focus {
            background: #1e293b;
            border-color: var(--primary);
            box-shadow: none;
        }
        .modal-content-custom {
            background: #0f172a;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 28px;
        }
        .form-control-dark {
            background: #1e293b;
            border: 1px solid #334155;
            color: white;
        }
        .form-control-dark:focus {
            background: #0f172a;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.3);
            color: white;
        }
        @media (max-width: 768px) {
            .table thead { display: none; }
            .table, .table tbody, .table tr, .table td { display: block; width: 100%; }
            .table tr {
                margin-bottom: 1rem;
                border: 1px solid var(--border-light);
                border-radius: 20px;
                padding: 0.5rem;
                background: rgba(255,255,255,0.02);
            }
            .table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 1rem;
                text-align: right;
                border-bottom: 1px dashed rgba(255,255,255,0.05);
            }
            .table td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #94a3b8;
                font-size: 0.8rem;
                flex: 1;
                text-align: left;
            }
            .table td .badge, .table td .btn-group {
                justify-content: flex-end;
            }
            .admin-actions {
                gap: 0.5rem;
                flex-wrap: wrap;
            }
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
        .icon-hover:hover { transform: scale(1.05); }
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
                <li><a href="{{ route('dashboard') }}" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#" class="nav-link active"><i class="fas fa-users-cog"></i> Manage Admins</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-calendar-alt"></i> All Events</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-user-friends"></i> All Invitees</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-credit-card"></i> Payments & Revenue</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-chart-pie"></i> Analytics</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-cog"></i> System Settings</a></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
        <div class="position-absolute bottom-0 w-100 p-4 d-none d-md-block">
            <div class="d-flex align-items-center gap-3">
                <img src="https://ui-avatars.com/api/?background=6366f1&color=fff&name=Cassian+O&size=48&rounded=true" alt="Owner" class="rounded-circle" width="48" height="48">
                <div>
                    <strong class="text-white">{{ Auth::user()->name }} (Owner)</strong><br>
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
                <h3 class="mb-0 text-white fw-semibold" style="font-size: 1.6rem;">Manage Admins</h3>
            </div>
            <div class="d-flex align-items-center gap-3">
                <form method="GET" action="{{ route('manageadmins') }}" class="d-flex align-items-center gap-2">
                    <div class="input-group" style="width: 260px;">
                        <input type="text" name="search" value="{{ $search }}" class="form-control search-input" placeholder="Search admins...">
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill px-3"><i class="fas fa-search"></i></button>
                </form>
                <button class="btn btn-success px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                    <i class="fas fa-user-plus me-2"></i> Add Admin
                </button>
            </div>
        </div>

        <div class="p-3 p-md-4 p-lg-5">
            @if (session('status'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4" role="alert">
                    <strong class="d-block mb-2">Action failed.</strong>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Stats summary -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="admin-card p-3 p-md-4 d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-slate-400 mb-1">Total Admins</p>
                            <h2 class="text-white fw-bold">{{ $totalAdmins }}</h2>
                        </div>
                        <i class="fas fa-user-shield fa-3x text-primary opacity-50"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="admin-card p-3 p-md-4 d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-slate-400 mb-1">Super Admins</p>
                            <h2 class="text-white fw-bold">{{ $superAdminCount }}</h2>
                        </div>
                        <i class="fas fa-crown fa-3x text-warning opacity-50"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="admin-card p-3 p-md-4 d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-slate-400 mb-1">Active Sessions</p>
                            <h2 class="text-white fw-bold">{{ $activeSessionsCount }}</h2>
                        </div>
                        <i class="fas fa-circle-nodes fa-3x text-success opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Admin Table Card -->
            <div class="admin-table-container p-0 admin-card overflow-hidden">
                <div class="p-3 p-md-4 border-bottom border-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="fw-bold text-white mb-0"><i class="fas fa-list-ul me-2 text-primary"></i>All Administrators</h5>
                    <span class="badge bg-primary bg-opacity-25 text-primary px-3 py-2 rounded-pill">
                        Last updated: {{ $lastUpdatedAt ? \Illuminate\Support\Carbon::parse($lastUpdatedAt)->diffForHumans() : 'no data yet' }}
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="adminsTable">
                        <thead>
                            <tr>
                                <th>Admin</th><th>Email</th><th>Role</th><th>Status</th><th>Last Seen</th><th>Joined</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($admins as $admin)
                                @php
                                    $nameParts = preg_split('/\s+/', trim($admin->name)) ?: [];
                                    $firstInitial = strtoupper(substr($nameParts[0] ?? $admin->name, 0, 1));
                                    $secondSource = $nameParts[1] ?? ($admin->name[1] ?? '');
                                    $secondInitial = strtoupper(substr($secondSource, 0, 1));
                                    $initials = trim($firstInitial . $secondInitial);
                                    $roleClass = match ($admin->role) {
                                        'Super Admin' => 'role-super',
                                        'Admin' => 'role-admin',
                                        'Manager' => 'role-manager',
                                        default => 'role-viewer',
                                    };
                                    $isOnline = !is_null($admin->last_seen_at) && $admin->last_seen_at->greaterThanOrEqualTo(now()->subMinutes($onlineWindowMinutes));
                                @endphp
                                <tr>
                                    <td data-label="Admin">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-placeholder">{{ $initials }}</div>
                                            <div><strong class="text-white">{{ $admin->name }}</strong></div>
                                        </div>
                                    </td>
                                    <td data-label="Email">{{ $admin->email }}</td>
                                    <td data-label="Role">
                                        <span class="badge-role {{ $roleClass }}">{{ $admin->role }}</span>
                                    </td>
                                    <td data-label="Status">
                                        <span class="badge status-badge px-3 py-1 rounded-pill {{ $isOnline ? '' : 'bg-secondary border-0 text-light' }}">
                                            {{ $isOnline ? '🟢 Online' : '⚪ Offline' }}
                                        </span>
                                    </td>
                                    <td data-label="Last Seen">
                                        {{ $admin->last_seen_at ? $admin->last_seen_at->diffForHumans() : 'Never' }}
                                    </td>
                                    <td data-label="Joined">{{ optional($admin->created_at)->format('M Y') }}</td>
                                    <td data-label="Actions">
                                        <div class="btn-group btn-group-sm gap-2 admin-actions">
                                            <button href="/owner/user/{{ $admin->id }}/edit" disable class="btn btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button
                                                class="btn btn-outline-danger"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteAdminModal"
                                                data-delete-url="{{ route('owner.admins.destroy', $admin->id) }}"
                                                data-admin-name="{{ $admin->name }}"
                                            >
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-light py-4">
                                        <i class="fas fa-users-slash me-2"></i>No matching admins found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-custom">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white"><i class="fas fa-user-plus me-2 text-primary"></i>Invite New Admin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addAdminForm" method="POST" action="{{ route('owner.admins.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-light" :value="__('Name')">Full Name</label>
                        <input type="text" name="name" class="form-control form-control-dark" id="adminName" required placeholder="e.g., Sarah Johnson" value="{{ old('name') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light" :value="__('Phone Number')">Phone Number</label>
                        <input type="text" name="phone" class="form-control form-control-dark" id="adminPhone" required placeholder="e.g., +2558123456789" value="{{ old('phone') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">Email address</label>
                        <input type="email" name="email" class="form-control form-control-dark" id="adminEmail" required placeholder="admin@invito.com" value="{{ old('email') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Role</label>
                        <select name="role" class="form-select form-control-dark" id="adminRole">
                        <option value="Super Admin" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                            <option value="Super Admin" {{ old('role') === 'Super Admin' ? 'selected' : '' }}>Super Admin (Full Access)</option>
                            <option value="Admin" {{ old('role', 'Admin') === 'Admin' ? 'selected' : '' }}>Admin (Standard)</option>
                            <option value="Manager" {{ old('role') === 'Manager' ? 'selected' : '' }}>Manager (Event Management)</option>
                            <option value="Viewer" {{ old('role') === 'Viewer' ? 'selected' : '' }}>Viewer (Read-only)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">Password</label>
                        <input type="password" name="password" class="form-control form-control-dark" id="adminPassword" required placeholder="***********">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">Confirm Passord</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-dark" id="adminPasswordConfirmation" required placeholder="***********">
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input name="send_invite" class="form-check-input" type="checkbox" id="sendInvite" checked style="background-color: #6366f1;">
                        <label class="form-check-label text-light" for="sendInvite">Send invitation email</label>
                    </div>
                
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="confirmAddAdminBtn"><i class="fas fa-paper-plane me-2"></i>Add Admin</button>
            </div>
            </form>
        </div>
    </div>
</div>






<div class="modal fade" id="deleteAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-custom">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white"><i class="fas fa-user-plus me-2 text-primary"></i>Enter Password to Delete Admin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="deleteAdminForm" method="POST" action="">
                    @csrf
                    @method('DELETE')

                    <p class="text-light mb-3">
                        You are deleting <strong id="deleteAdminName">this admin</strong>. Enter your password to continue.
                    </p>

                    <div class="mb-3">
                        <label class="form-label text-light">Password</label>
                        <input type="password" name="password" class="form-control form-control-dark" id="deleteAdminPassword" required placeholder="***********">
                    </div>

                
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger" id="confirmDeleteAdminBtn"><i class="fas fa-trash-alt me-2"></i>Delete Admin</button>
            </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar responsive logic
    const sidebar = document.getElementById('mainSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const toggleBtn = document.getElementById('menuToggleBtn');
    function closeSidebar() { sidebar.classList.remove('show-sidebar'); backdrop.classList.remove('show'); document.body.style.overflow = ''; }
    function openSidebar() { sidebar.classList.add('show-sidebar'); backdrop.classList.add('show'); document.body.style.overflow = 'hidden'; }
    if (toggleBtn) toggleBtn.addEventListener('click', () => { sidebar.classList.contains('show-sidebar') ? closeSidebar() : openSidebar(); });
    if (backdrop) backdrop.addEventListener('click', closeSidebar);
    window.addEventListener('resize', () => { if (window.innerWidth >= 992) { closeSidebar(); } });

    const deleteModal = document.getElementById('deleteAdminModal');
    const deleteForm = document.getElementById('deleteAdminForm');
    const deleteAdminName = document.getElementById('deleteAdminName');
    const deleteAdminPassword = document.getElementById('deleteAdminPassword');

    if (deleteModal && deleteForm) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            deleteForm.action = button.getAttribute('data-delete-url') || '';
            deleteAdminName.textContent = button.getAttribute('data-admin-name') || 'this admin';
            deleteAdminPassword.value = '';
        });
    }
</script>
</body>
</html>