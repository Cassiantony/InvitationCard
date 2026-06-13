<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, shrink-to-fit=no">
    <title>EventFlow | Manager Dashboard - Events, Funds & Invitations</title>
    <!-- Bootstrap 5 CSS + Icons + Google Fonts + Flatpickr -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #6c757d;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --dark: #1e293b;
            --light-bg: #f8fafc;
            --card-radius: 1.25rem;
            --fund-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --event-card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        }
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', sans-serif;
        }
        /* modern sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                left: -280px;
                width: 280px;
                top: 0;
                bottom: 0;
                overflow-y: auto;
            }
            .sidebar.show-sidebar { left: 0; }
            .overlay-blur {
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background: rgba(0,0,0,0.6);
                backdrop-filter: blur(3px);
                z-index: 990;
                display: none;
            }
            .overlay-blur.active { display: block; }
        }
        .sidebar .nav-link {
            color: #cbd5e1;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            font-weight: 500;
            transition: 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(67, 97, 238, 0.2);
            color: white;
        }
        .sidebar .nav-link i { width: 1.8rem; font-size: 1.1rem; }
        .topbar {
            background: white;
            border-radius: 1.25rem;
            padding: 0.8rem 1.5rem;
            margin-bottom: 1.8rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.03);
        }
        /* info cards (stats) */
        .info-card {
            background: white;
            border: none;
            border-radius: 1.25rem;
            padding: 1.25rem;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .info-card:hover { transform: translateY(-4px); box-shadow: 0 20px 30px -12px rgba(0,0,0,0.1); }
        .info-icon {
            width: 48px;
            height: 48px;
            background: rgba(67, 97, 238, 0.1);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        .info-number { font-size: 1.8rem; font-weight: 800; margin-bottom: 0.25rem; color: #0f172a; }
        .info-label { font-size: 0.8rem; font-weight: 500; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Fund Card Premium */
        .fund-card-premium {
            background: var(--fund-gradient);
            border-radius: 1.5rem;
            color: white;
            box-shadow: 0 15px 30px -10px rgba(16,185,129,0.4);
        }
        .disbursement-badge-light {
            background: rgba(255,255,255,0.2);
            border-radius: 2rem;
            padding: 0.3rem 0.9rem;
            font-size: 0.7rem;
        }
        /* Event Cards (beautiful grid) */
        .event-card {
            background: white;
            border-radius: 1.25rem;
            border: none;
            transition: all 0.25s ease;
            box-shadow: var(--event-card-shadow);
            overflow: hidden;
        }
        .event-card:hover { transform: translateY(-6px); box-shadow: 0 20px 35px -12px rgba(0,0,0,0.15); }
        .event-category-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.35rem 1rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 700;
            background: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .category-conference { background: #e0e7ff; color: #4338ca; }
        .category-workshop { background: #dcfce7; color: #166534; }
        .category-social { background: #ffe4e6; color: #be123c; }
        .category-webinar { background: #fef3c7; color: #b45309; }
        .event-date, .event-location, .event-organizer { font-size: 0.85rem; color: #475569; }
        .card-footer-custom {
            background: #fafcff;
            border-top: 1px solid #edf2f7;
            padding: 0.9rem 1.25rem;
        }
        .btn-outline-custom {
            border-radius: 2rem;
            padding: 0.35rem 1rem;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .invite-btn-sm {
            background: #f1f5f9;
            border: none;
            border-radius: 2rem;
            padding: 0.3rem 0.9rem;
            font-size: 0.75rem;
        }
        .invite-btn-sm:hover { background: #e2e8f0; }
        .progress-thin { height: 6px; border-radius: 1rem; }
        .avatar-manager {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .toast-custom { z-index: 9999; }
        footer { font-size: 0.7rem; color: #94a3b8; }
        @media (max-width: 768px) {
            .info-number { font-size: 1.4rem; }
            .event-card { margin-bottom: 0.5rem; }
        }
    </style>
</head>
<body>

<div class="overlay-blur" id="mobileOverlay"></div>

<div class="container-fluid px-0">
    <div class="row g-0">
        <!-- SIDEBAR (Inspired by Manager Layout) -->
        <div class="col-md-3 col-lg-2 sidebar" id="mainSidebar">
            <div class="position-sticky pt-4 px-3">
                <div class="d-flex justify-content-between align-items-center d-md-none pb-3 border-bottom border-secondary">
                    <h5 class="text-white mb-0"><i class="fas fa-tasks me-2"></i>EventFlow</h5>
                    <button class="btn btn-sm btn-outline-light rounded-circle" id="closeSidebarMobile"><i class="fas fa-times"></i></button>
                </div>
                <div class="text-center mb-4 d-none d-md-block">
                    <div class="avatar-manager mx-auto mb-2" style="width: 50px; height: 50px; font-size: 1.4rem;"><i class="fas fa-calendar-alt"></i></div>
                    <h5 class="text-white mt-2">Manager Portal</h5>
                    <span class="badge bg-primary bg-opacity-75 mt-1">v3.0</span>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link active" href="#" id="dashboardNav"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" id="eventsNav"><i class="fas fa-calendar-alt"></i> My Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" id="createEventNavBtn"><i class="fas fa-plus-circle"></i> Create Event</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" id="fundsNav"><i class="fas fa-coins"></i> Disbursements</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-chart-line"></i> Analytics</a></li>
                    <li class="nav-item mt-4"><hr class="bg-secondary opacity-25"></li>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        @method('POST')
                        <li class="nav-item"><button type="submit" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt"></i> Logout</button></li>
                    </form>
                </ul>
            </div>
        </div>

        <!-- MAIN DASHBOARD CONTENT (Blade-like UI but fully interactive) -->
        <div class="col-md-9 col-lg-10 px-3 px-md-4 py-3" id="mainContent">
            <!-- Topbar -->
            <div class="topbar d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex gap-3 align-items-center">
                    <button class="btn btn-link d-md-none text-dark p-0" id="mobileMenuToggle"><i class="fas fa-bars fs-4"></i></button>
                    <div><h5 class="mb-0 fw-bold"><i class="fas fa-chalkboard-user text-primary me-2"></i>My Events <span class="text-muted fs-6 fw-normal">| Manager Dashboard</span></h5></div>
                </div>
                <div class="d-flex gap-3 align-items-center mt-2 mt-sm-0">
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill position-relative shadow-sm" data-bs-toggle="dropdown"><i class="far fa-bell"></i> <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-dollar-sign text-success me-2"></i>Funds added</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-envelope text-info me-2"></i>Invitation tips</a></li>
                        </ul>
                    </div>
                    <div class="d-flex align-items-center gap-2"><div class="avatar-manager" style="width: 38px; height: 38px;">JD</div><span class="d-none d-sm-inline fw-semibold">Jessica Diaz</span></div>
                </div>
            </div>

            <!-- STATS CARDS (Total, Upcoming, Completed, Today) -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="info-card h-100">
                        <div class="info-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="info-number" id="statTotal">
                            {{ $totalEvents ?? 0 }}
                        </div>
                        <div class="info-label">
                            Total Events
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="info-card h-100">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="info-number" id="statUpcoming">
                        {{ $upcomingEvents ?? 0 }}
                        </div>
                        <div class="info-label">
                            Upcoming
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="info-card h-100">
                        <div class="info-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="info-number" id="statCompleted">
                        {{ $pastEvents ?? 0 }}
                        </div>
                        <div class="info-label">
                            Completed
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="info-card h-100">
                        <div class="info-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="info-number" id="statToday">
                    <div class="info-number">
                        {{ $todayEvents ?? 0 }}
                    </div>
                        </div>
                        <div class="info-label">
                            Today
                        </div>
                    </div>
                </div>
            </div>


            <!-- EVENTS GRID (Card structure exactly like blade) -->
            <div id="eventsListSection">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap"><h5 class="fw-bold"><i class="fas fa-calendar-alt text-primary me-2"></i> My Events <span class="badge bg-primary rounded-pill ms-1" id="eventsCountBadge">0</span></h5><button class="btn btn-primary btn-sm rounded-pill px-3" id="floatingCreateBtn"><i class="fas fa-plus me-1"></i> Create Event</button></div>
                <div class="row" id="eventsCardsContainer">
                @if($events && count($events) > 0)
                @foreach($events as $event)
                    <div class="col-xl-4 col-lg-6 mb-4">
                        <div class="card event-card h-100">
                            <div class="position-relative">
                                <span class="event-category-badge category-{{ $event->category }}">
                                    {{ ucfirst($event->category) }}
                                </span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-bold">
                                    {{ $event->title }}
                                </h5>
                                <p class="card-text event-description small text-muted">
                                    {{ Str::limit($event->description, 100) }}
                                </p>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-alt text-muted me-2"></i>
                                    <span class="event-date">
                                        {{ \Carbon\Carbon::parse($event->date)->format('M j, Y g:i A') }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                    <span class="event-location">
                                        {{ $event->location }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user text-muted me-2"></i>
                                    <span class="event-organizer">
                                        {{ $event->organizer_name }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <span>
                                        <i class="fas fa-envelope"></i> 
                                        Invites: 
                                    </span>
                                    <span class="badge ${status}">
                                        Complete
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer-custom d-flex justify-content-between gap-2">
                                <a href="{{ route('event.show', $event->id) }}" class="btn btn-sm btn-outline-primary rounded-pill invite-single-btn">
                                    <i class="fas fa-eye me-1"></i> 
                                    View
                                </a>
                                <a href="{{ route('event.viewers.index', $event) }}" class="btn btn-sm btn-outline-info rounded-pill">
                                    <i class="fas fa-user-shield me-1"></i>
                                    Viewers
                                </a>
                                <a href="{{ route('event.edit', $event->id) }}" class="btn btn-sm btn-outline-warning rounded-pill edit-disabled">
                                    <i class="fas fa-edit me-1"></i> 
                                    Edit
                                                    </a>
                                        <form action="{{ route('event.destroy', $event->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger rounded-pill delete-event-btn"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                    @endforeach
                    @else
                    <div class="col-12"><div class="card shadow-sm"><div class="card-body text-center py-5"><i class="fas fa-calendar-times fa-3x text-muted mb-3"></i><h4 class="text-muted">No Events Found</h4><p class="text-muted mb-4">You haven't created any events yet.</p><a href="#" id="emptyStateCreateBtn" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Create Your First Event</a></div></div></div>
                    @endif
                </div>
                <div class="d-flex justify-content-center mt-4" id="paginationWrapper"></div>
            </div>
            <footer class="text-center mt-5 pb-3">&copy; 2025 EventFlow — Smart Manager Disbursement System</footer>
        </div>
    </div>
</div>

<!-- Invitation Modal -->
<div class="modal fade" id="inviteModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4"><div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="fas fa-paper-plane me-2"></i> Send Invitations</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>Sending invitations for: <strong id="inviteEventTitle"></strong></p><label class="form-label">Number of invitations</label><input type="number" class="form-control" id="inviteQuantity" min="1" value="10"><div class="alert alert-info mt-3 small"><i class="fas fa-dollar-sign"></i> Cost per invitation: $0.50</div><p>Your balance: <strong id="modalFundBalance">$0.00</strong></p></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-success" id="confirmSendInviteBtn"><i class="fas fa-paper-plane"></i> Send & Deduct</button></div></div></div></div>

<!-- Fund Request Modal -->
<div class="modal fade" id="fundRequestModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4"><div class="modal-header bg-success text-white"><h5><i class="fas fa-coins"></i> Request Disbursement</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><label>Amount ($)</label><input type="number" id="requestAmount" class="form-control" min="50" step="50" value="200"><small class="text-muted">Approval within 24h, added to your fund.</small></div><div class="modal-footer"><button class="btn btn-primary" id="submitFundRequest">Request Funds</button></div></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // ------------------- DATA & STATE -------------------
    let events = [];
    let managerFund = 520.00;   // starting fund
    let totalInvitationsSent = 38;
    const COST_PER_INVITE = 0.50;

    function saveData() { localStorage.setItem('manager_events_ui', JSON.stringify(events)); localStorage.setItem('manager_fund_ui', managerFund); localStorage.setItem('total_invites_ui', totalInvitationsSent); }
    function loadData() {
        const storedEvents = localStorage.getItem('manager_events_ui');
        if(storedEvents) events = JSON.parse(storedEvents);
        else {
            events = [
                { id: 'ev1', title: 'Tech Leadership Summit', start: '2025-06-10T09:00:00', end: '2025-06-10T18:00:00', location: 'Convention Hall, SF', capacity: 120, description: 'Industry leaders and keynote speeches.', category: 'conference', invitationsSent: 65, rsvpCount: 42, organizer_name: 'Jessica Diaz' },
                { id: 'ev2', title: 'Design Workshop', start: '2025-05-28T14:00:00', end: '2025-05-28T17:30:00', location: 'Online (Zoom)', capacity: 200, description: 'UI/UX intensive with live prototypes', category: 'workshop', invitationsSent: 112, rsvpCount: 78, organizer_name: 'Jessica Diaz' }
            ];
        }
        const storedFund = localStorage.getItem('manager_fund_ui'); if(storedFund !== null) managerFund = parseFloat(storedFund);
        const storedInvites = localStorage.getItem('total_invites_ui'); if(storedInvites !== null) totalInvitationsSent = parseInt(storedInvites);
        renderAllEventsGrid();
        updateStatsAndFundUI();
    }

    function updateStatsAndFundUI() {
        const total = events.length;
        const now = new Date();
        const upcoming = events.filter(ev => new Date(ev.start) > now).length;
        const completed = events.filter(ev => new Date(ev.end) < now).length;
        const todayEvents = events.filter(ev => { const d = new Date(ev.start); return d.toDateString() === now.toDateString(); }).length;
        document.getElementById('statTotal').innerText = total;
        document.getElementById('statUpcoming').innerText = upcoming;
        document.getElementById('statCompleted').innerText = completed;
        document.getElementById('statToday').innerText = todayEvents;
        document.getElementById('eventsCountBadge').innerText = total;
        document.getElementById('managerFundAmount').innerHTML = `$${managerFund.toFixed(2)}`;
        document.getElementById('totalInvitesSent').innerText = totalInvitationsSent;
        const totalSpent = totalInvitationsSent * COST_PER_INVITE;
        document.getElementById('totalSpendInvites').innerText = `$${totalSpent.toFixed(2)}`;
        const percentUtil = Math.min(100, (totalSpent / (managerFund + totalSpent)) * 100 || 0);
        document.getElementById('fundUtilProgress').style.width = `${percentUtil}%`;
    }

    function getStatusClass(start, end) { const now=new Date(); if(now<new Date(start)) return 'badge bg-primary bg-opacity-10 text-primary'; if(now>=new Date(start) && now<=new Date(end)) return 'badge bg-success bg-opacity-10 text-success'; return 'badge bg-secondary bg-opacity-10 text-secondary'; }
    function getStatusText(start,end) { const now=new Date(); if(now<new Date(start)) return 'Upcoming'; if(now>=new Date(start) && now<=new Date(end)) return 'Ongoing'; return 'Completed'; }

    function renderAllEventsGrid() {
        const container = document.getElementById('eventsCardsContainer');
        if(events.length===0) { container.innerHTML=`<div class="col-12"><div class="card shadow-sm"><div class="card-body text-center py-5"><i class="fas fa-calendar-times fa-3x text-muted mb-3"></i><h4 class="text-muted">No Events Found</h4><p class="text-muted mb-4">You haven't created any events yet.</p><a href="#" id="emptyStateCreateBtn" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Create Your First Event</a></div></div></div>`; document.getElementById('emptyStateCreateBtn')?.addEventListener('click',(e)=>{e.preventDefault(); showCreateForm();}); return; }
        let html = '';
        events.forEach(event => {
            const categoryClass = `category-${event.category || 'conference'}`;
            const startFormatted = new Date(event.start).toLocaleString(undefined, { month:'short', day:'numeric', hour:'2-digit', minute:'2-digit' });
            const status = getStatusClass(event.start, event.end);
            const statusText = getStatusText(event.start, event.end);
            html += `<div class="col-xl-4 col-lg-6 mb-4"><div class="card event-card h-100"><div class="position-relative"><span class="event-category-badge ${categoryClass}">${(event.category || 'Conference').toUpperCase()}</span></div><div class="card-body"><h5 class="card-title fw-bold">${escapeHtml(event.title)}</h5><p class="card-text event-description small text-muted">${escapeHtml(event.description?.substring(0,100) || 'No description')}</p><div class="d-flex align-items-center mb-2"><i class="fas fa-calendar-alt text-muted me-2"></i><span class="event-date">${startFormatted}</span></div><div class="d-flex align-items-center mb-2"><i class="fas fa-map-marker-alt text-muted me-2"></i><span class="event-location">${escapeHtml(event.location || 'TBD')}</span></div><div class="d-flex align-items-center mb-2"><i class="fas fa-user text-muted me-2"></i><span class="event-organizer">${escapeHtml(event.organizer_name || 'Manager')}</span></div><div class="d-flex justify-content-between mt-2"><span><i class="fas fa-envelope"></i> Invites: ${event.invitationsSent || 0}</span><span class="badge ${status}">${statusText}</span></div></div><div class="card-footer-custom d-flex justify-content-between gap-2"><button class="btn btn-sm btn-outline-primary rounded-pill invite-single-btn" data-id="${event.id}"><i class="fas fa-paper-plane me-1"></i> Send Invites</button><a href="#" class="btn btn-sm btn-outline-warning rounded-pill edit-disabled" data-id="${event.id}"><i class="fas fa-edit me-1"></i> Edit</a><button class="btn btn-sm btn-outline-danger rounded-pill delete-event-btn" data-id="${event.id}"><i class="fas fa-trash"></i></button></div></div></div>`;
        });
        container.innerHTML = html;
        attachCardActions();
    }

    function attachCardActions() {
        document.querySelectorAll('.delete-event-btn').forEach(btn => btn.addEventListener('click',(e)=>{ const id=btn.getAttribute('data-id'); if(confirm('Delete event?')){ events=events.filter(ev=>ev.id!==id); saveData(); renderAllEventsGrid(); updateStatsAndFundUI(); showToast('Event removed','danger'); } }));
        document.querySelectorAll('.invite-single-btn').forEach(btn => btn.addEventListener('click',()=>{ const id=btn.getAttribute('data-id'); const ev=events.find(e=>e.id===id); if(ev){ document.getElementById('inviteEventTitle').innerText=ev.title; document.getElementById('modalFundBalance').innerText=`$${managerFund.toFixed(2)}`; window.currentInviteId=id; new bootstrap.Modal(document.getElementById('inviteModal')).show(); } }));
        document.querySelectorAll('.edit-disabled').forEach(btn=>btn.addEventListener('click',(e)=>{ e.preventDefault(); showToast('Edit: recreate or upgrade', 'info'); }));
    }

    function sendInvitations(eventId, qty) {
        const event = events.find(ev=>ev.id===eventId);
        if(!event) return false;
        const cost = qty * COST_PER_INVITE;
        if(managerFund < cost) { showToast(`Insufficient funds! Need $${cost.toFixed(2)}. Request disbursement.`, 'danger'); return false; }
        managerFund -= cost;
        totalInvitationsSent += qty;
        event.invitationsSent = (event.invitationsSent || 0) + qty;
        event.rsvpCount = (event.rsvpCount || 0) + Math.floor(qty * 0.3);
        saveData();
        renderAllEventsGrid();
        updateStatsAndFundUI();
        showToast(`${qty} invitation(s) sent for "${event.title}". Cost $${cost.toFixed(2)}`, 'success');
        return true;
    }

    function createNewEvent(data) {
        const newEvent = { id: 'evt_'+Date.now(), title: data.title, start: data.start, end: data.end, location: data.location, capacity: data.capacity, description: data.description, category: data.category, invitationsSent: 0, rsvpCount: 0, organizer_name: 'Jessica Diaz' };
        events.unshift(newEvent);
        saveData();
        renderAllEventsGrid();
        updateStatsAndFundUI();
        showToast(`Event "${data.title}" created!`, 'success');
        hideCreateForm();
    }

    function escapeHtml(s){ if(!s) return ''; return s.replace(/[&<>]/g, function(m){if(m==='&') return '&amp;'; if(m==='<') return '&lt;'; if(m==='>') return '&gt;'; return m;});}
    function showToast(msg,type='success'){ const container=document.createElement('div'); container.className='position-fixed bottom-0 end-0 p-3 toast-custom'; const bg=type==='success'?'bg-success':type==='danger'?'bg-danger':'bg-warning'; container.innerHTML=`<div class="toast align-items-center text-white ${bg} border-0 show" role="alert"><div class="d-flex"><div class="toast-body"><i class="fas ${type==='success'?'fa-check-circle':'fa-exclamation-triangle'} me-2"></i> ${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`; document.body.appendChild(container); const bs=new bootstrap.Toast(container.querySelector('.toast'),{delay:2800}); bs.show(); setTimeout(()=>container.remove(),3000);}
    function hideCreateForm(){ document.getElementById('createEventSection').style.display='none'; }
    function showCreateForm(){ document.getElementById('createEventSection').style.display='block'; window.scrollTo({top:0,behavior:'smooth'}); flatpickr(".datetime-picker",{enableTime:true,dateFormat:"Y-m-d H:i:S",time_24hr:true}); }
    function initFlatpickr(){ flatpickr(".datetime-picker",{enableTime:true,dateFormat:"Y-m-d H:i:S",time_24hr:true}); }

    // Mobile sidebar events
    const sidebar=document.getElementById('mainSidebar'), overlay=document.getElementById('mobileOverlay');
    document.getElementById('mobileMenuToggle')?.addEventListener('click',()=>{ if(window.innerWidth<768){ sidebar.classList.add('show-sidebar'); overlay.classList.add('active'); document.body.style.overflow='hidden'; } });
    document.getElementById('closeSidebarMobile')?.addEventListener('click',()=>{ sidebar.classList.remove('show-sidebar'); overlay.classList.remove('active'); document.body.style.overflow=''; });
    overlay.addEventListener('click',()=>{ sidebar.classList.remove('show-sidebar'); overlay.classList.remove('active'); document.body.style.overflow=''; });
    document.getElementById('floatingCreateBtn')?.addEventListener('click',showCreateForm);
    document.getElementById('createEventNavBtn')?.addEventListener('click',(e)=>{e.preventDefault(); showCreateForm();});
    document.getElementById('cancelCreateBtn')?.addEventListener('click',hideCreateForm);
    document.getElementById('requestDisbursementBtn')?.addEventListener('click',()=>{new bootstrap.Modal(document.getElementById('fundRequestModal')).show();});
    document.getElementById('submitFundRequest')?.addEventListener('click',()=>{let amt=parseFloat(document.getElementById('requestAmount').value); if(amt>0 && amt<=5000){ managerFund+=amt; saveData(); updateStatsAndFundUI(); showToast(`$${amt} added to fund!`,'success'); bootstrap.Modal.getInstance(document.getElementById('fundRequestModal')).hide(); } else showToast('Invalid amount','danger');});
    document.getElementById('confirmSendInviteBtn')?.addEventListener('click',()=>{ let qty=parseInt(document.getElementById('inviteQuantity').value); if(qty>0 && window.currentInviteId){ sendInvitations(window.currentInviteId,qty); bootstrap.Modal.getInstance(document.getElementById('inviteModal')).hide(); } else showToast('Invalid quantity','danger'); });
    document.getElementById('createEventForm')?.addEventListener('submit',(e)=>{ e.preventDefault(); let title=document.getElementById('eventTitle').value.trim(), start=document.getElementById('eventStart').value, end=document.getElementById('eventEnd').value; if(!title||!start||!end){ showToast('Title & dates required','danger'); return; } if(new Date(start)>=new Date(end)){ showToast('End after start','danger'); return; } createNewEvent({ title, start, end, location:document.getElementById('eventLocation').value, capacity:document.getElementById('eventCapacity').value, description:document.getElementById('eventDesc').value, category:document.getElementById('eventCategory').value }); });
    document.getElementById('dashboardNav')?.addEventListener('click',(e)=>{e.preventDefault(); hideCreateForm(); renderAllEventsGrid(); updateStatsAndFundUI();});
    document.getElementById('eventsNav')?.addEventListener('click',(e)=>{e.preventDefault(); hideCreateForm(); document.getElementById('eventsListSection').scrollIntoView({behavior:'smooth'});});
    document.getElementById('fundsNav')?.addEventListener('click',(e)=>{e.preventDefault(); document.querySelector('.fund-card-premium').scrollIntoView({behavior:'smooth'});});
    loadData(); initFlatpickr();
    window.currentInviteId = null;
</script>
</body>
</html>