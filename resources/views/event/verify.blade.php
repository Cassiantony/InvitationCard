<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Guests — {{ $event->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --sidebar-bg-start: #4e73df;
            --sidebar-bg-end: #224abe;
        }
        body { background: #f8f9fc; overflow-x: hidden; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg-start) 0%, var(--sidebar-bg-end) 100%);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            z-index: 1000;
        }
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed; left: -280px; width: 280px;
                transition: left 0.3s ease; z-index: 1050; overflow-y: auto;
            }
            .sidebar.show-sidebar { left: 0; }
            .overlay-blur {
                position: fixed; inset: 0; background: rgba(0,0,0,0.4);
                z-index: 1040; display: none;
            }
            .overlay-blur.active { display: block; }
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.85); padding: 0.85rem 1rem;
            font-weight: 500; border-radius: 0.35rem; margin-bottom: 0.2rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff; background: rgba(255,255,255,0.15);
        }
        .sidebar .nav-link i { width: 1.8rem; text-align: center; }
        .sidebar .brand { border-bottom: 1px solid rgba(255,255,255,0.2); }
        .topbar {
            background: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.08);
            padding: 0.75rem 1rem;
            border-radius: 0 0 0.5rem 0.5rem;
        }
        .card { border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); border-radius: 0.35rem; }
        #qr-reader {
            width: 100%;
            max-width: 100%;
            min-height: 260px;
            border-radius: 0.5rem;
            overflow: hidden;
            background: #111;
        }
        #qr-reader video { border-radius: 0.5rem; width: 100% !important; height: auto !important; }
        #qr-reader img { display: none; }
        .result-card { display: none; }
        .result-card.show { display: block; }
        .verify-modal-icon { font-size: 3.5rem; }
        @media (max-width: 575.98px) {
            .action-row .btn { width: 100%; }
        }
    </style>
</head>
<body>
@php
    $activeNav = 'verify';
    $sidebarEvent = $event;
    $topbarTitle = 'Verify Guests';
    $topbarSubtitle = $event->title;
    $topbarBackUrl = route('event.show', $event->id);
    $topbarBackLabel = 'Back to Event';
@endphp

<div class="overlay-blur" id="mobileOverlay"></div>

<div class="container-fluid px-0">
    <div class="row g-0">
        @include('partials.admin-sidebar')

        <div class="col-md-9 col-lg-10 ms-sm-auto px-3 px-md-4 main-content-full pb-4">
            @include('partials.portal-topbar')

            <div class="row g-3 g-lg-4">
                <div class="col-lg-6">
                    <div class="card mb-3">
                        <div class="card-header bg-white">
                            <strong><i class="fas fa-camera me-2 text-primary"></i>Scan QR code</strong>
                        </div>
                        <div class="card-body">
                            <div id="qr-reader" class="mb-3"></div>
                            <p id="camera-status" class="small text-muted text-center mb-3">Tap start to allow camera access.</p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center action-row">
                                <button type="button" class="btn btn-primary" id="start-scanner-btn">
                                    <i class="fas fa-play me-1"></i> Start camera
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="stop-scanner-btn" disabled>
                                    <i class="fas fa-stop me-1"></i> Stop
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-white">
                            <strong><i class="fas fa-keyboard me-2 text-primary"></i>Enter invitation code</strong>
                        </div>
                        <div class="card-body">
                            <div class="input-group input-group-lg flex-column flex-sm-row">
                                <input type="text" class="form-control text-uppercase mb-2 mb-sm-0" id="manual-code"
                                       placeholder="e.g. A1B2C3D4" autocomplete="off">
                                <button class="btn btn-primary px-4" type="button" id="lookup-btn">Verify</button>
                            </div>
                            <p class="text-muted small mt-3 mb-0">Only invitees for <strong>{{ $event->title }}</strong> are accepted.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card result-card" id="result-card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <strong>Guest details</strong>
                            <span class="badge" id="result-rsvp-badge">—</span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img id="result-avatar" src="" alt="" class="rounded-circle me-3 flex-shrink-0" width="56" height="56">
                                <div class="min-w-0">
                                    <h5 class="mb-0 text-truncate" id="result-name">—</h5>
                                    <div class="text-muted small" id="result-code">—</div>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-3 small">
                                <li class="mb-2 text-break"><i class="fas fa-envelope me-2 text-muted"></i><span id="result-email">—</span></li>
                                <li class="mb-2"><i class="fas fa-phone me-2 text-muted"></i><span id="result-phone">—</span></li>
                                <li class="mb-2"><i class="fas fa-building me-2 text-muted"></i><span id="result-company">—</span></li>
                                <li><i class="fas fa-user-check me-2 text-muted"></i><span id="result-checkin">Not checked in</span></li>
                            </ul>
                            <button type="button" class="btn btn-success w-100" id="checkin-btn" disabled>
                                <i class="fas fa-check me-1"></i> Check in guest
                            </button>
                        </div>
                    </div>

                    <div class="card" id="result-placeholder">
                        <div class="card-body text-center text-muted py-5 px-3">
                            <i class="fas fa-qrcode fa-3x mb-3 d-block opacity-50"></i>
                            Scan a QR code or enter an invitation code. A popup will show whether the guest is approved for this event.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Verification result modal --}}
<div class="modal fade" id="verifyResultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center border-0 shadow">
            <div class="modal-body py-4 px-4">
                <div id="verify-modal-icon-wrap" class="mb-3">
                    <i id="verify-modal-icon" class="fas fa-check-circle text-success verify-modal-icon"></i>
                </div>
                <h4 class="mb-2" id="verify-modal-title">Guest verified</h4>
                <p class="text-muted mb-1" id="verify-modal-guest"></p>
                <p class="mb-3" id="verify-modal-message"></p>
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                    <button type="button" class="btn btn-primary" id="verify-modal-checkin-btn" style="display:none;">
                        <i class="fas fa-check me-1"></i> Check in
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
    const LOOKUP_URL = @json($lookupUrl);
    const CHECKIN_URL = @json($checkInUrl);
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    let html5QrCode = null;
    let scannerRunning = false;
    let currentCode = '';
    let lastInvitee = null;
    let scanCooldown = false;

    const verifyModal = new bootstrap.Modal(document.getElementById('verifyResultModal'));
    const statusEl = document.getElementById('camera-status');

    const statusConfig = {
        confirmed: {
            icon: 'fa-check-circle', color: 'text-success',
            title: 'Approved — Attending',
            message: 'This guest confirmed they will attend.',
        },
        declined: {
            icon: 'fa-times-circle', color: 'text-danger',
            title: 'Declined — Not attending',
            message: 'This guest declined the invitation.',
        },
        sent: {
            icon: 'fa-clock', color: 'text-warning',
            title: 'Invited — No RSVP yet',
            message: 'Invitation was sent but the guest has not responded yet.',
        },
        pending: {
            icon: 'fa-question-circle', color: 'text-secondary',
            title: 'Registered — No RSVP',
            message: 'Guest is on the list but has not confirmed attendance.',
        },
    };

    function extractCode(raw) {
        const s = String(raw).trim();
        const m = s.match(/\/invitee\/([A-Za-z0-9_-]+)/i);
        return m ? m[1].toUpperCase() : s.toUpperCase();
    }

    async function apiPost(url, code) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ code: extractCode(code) }),
        });
        return { ok: res.ok, status: res.status, data: await res.json().catch(() => ({})) };
    }

    function showVerifyModal(type, invitee, customMessage) {
        const icon = document.getElementById('verify-modal-icon');
        const title = document.getElementById('verify-modal-title');
        const guest = document.getElementById('verify-modal-guest');
        const message = document.getElementById('verify-modal-message');
        const checkinBtn = document.getElementById('verify-modal-checkin-btn');

        if (type === 'not_found') {
            icon.className = 'fas fa-user-slash text-danger verify-modal-icon';
            title.textContent = 'Not invited';
            guest.textContent = '';
            message.textContent = customMessage || 'This code is not registered for this event.';
            checkinBtn.style.display = 'none';
        } else if (type === 'error') {
            icon.className = 'fas fa-exclamation-triangle text-danger verify-modal-icon';
            title.textContent = 'Verification failed';
            guest.textContent = '';
            message.textContent = customMessage || 'Could not verify this code.';
            checkinBtn.style.display = 'none';
        } else {
            const cfg = statusConfig[type] || statusConfig.pending;
            icon.className = 'fas ' + cfg.icon + ' ' + cfg.color + ' verify-modal-icon';
            title.textContent = cfg.title;
            guest.textContent = invitee ? invitee.name + ' · ' + invitee.invitation_code : '';
            message.textContent = customMessage || cfg.message;
            if (invitee && !invitee.checked_in && type === 'confirmed') {
                checkinBtn.style.display = 'inline-block';
            } else if (invitee && !invitee.checked_in && (type === 'sent' || type === 'pending')) {
                checkinBtn.style.display = 'inline-block';
            } else {
                checkinBtn.style.display = 'none';
            }
        }
        verifyModal.show();
    }

    function updateResultPanel(invitee) {
        document.getElementById('result-placeholder').classList.add('d-none');
        const card = document.getElementById('result-card');
        card.classList.add('show');

        const type = invitee.status || 'pending';
        const cfg = statusConfig[type] || statusConfig.pending;
        const badge = document.getElementById('result-rsvp-badge');
        badge.textContent = cfg.title.split('—')[0].trim();
        badge.className = 'badge ' + (type === 'confirmed' ? 'bg-success' : (type === 'declined' ? 'bg-danger' : 'bg-secondary'));

        document.getElementById('result-name').textContent = invitee.name;
        document.getElementById('result-code').textContent = 'Code: ' + invitee.invitation_code;
        document.getElementById('result-email').textContent = invitee.email || '—';
        document.getElementById('result-phone').textContent = invitee.phone || '—';
        document.getElementById('result-company').textContent = invitee.company || '—';
        document.getElementById('result-avatar').src = 'https://ui-avatars.com/api/?background=4e73df&color=fff&name=' + encodeURIComponent(invitee.name);
        document.getElementById('result-checkin').textContent = invitee.checked_in
            ? 'Checked in' + (invitee.checked_in_at ? ' · ' + new Date(invitee.checked_in_at).toLocaleString() : '')
            : 'Not checked in';

        const checkinBtn = document.getElementById('checkin-btn');
        checkinBtn.disabled = !!invitee.checked_in;
        checkinBtn.innerHTML = invitee.checked_in
            ? '<i class="fas fa-check-double me-1"></i> Already checked in'
            : '<i class="fas fa-check me-1"></i> Check in guest';
    }

    async function lookup(code) {
        currentCode = extractCode(code);
        try {
            const { ok, data } = await apiPost(LOOKUP_URL, currentCode);
            if (!ok || !data.success) {
                showVerifyModal('not_found', null, data.message);
                document.getElementById('result-placeholder').classList.remove('d-none');
                document.getElementById('result-card').classList.remove('show');
                return;
            }
            lastInvitee = data.invitee;
            updateResultPanel(lastInvitee);
            showVerifyModal(lastInvitee.status || 'pending', lastInvitee);
        } catch (e) {
            showVerifyModal('error', null, 'Network error. Try again.');
        }
    }

    async function checkIn(fromModal) {
        if (!currentCode) return;
        const btn = fromModal ? document.getElementById('verify-modal-checkin-btn') : document.getElementById('checkin-btn');
        btn.disabled = true;
        try {
            const { ok, data } = await apiPost(CHECKIN_URL, currentCode);
            if (!ok || !data.success) {
                alert(data.message || 'Check-in failed.');
                btn.disabled = false;
                return;
            }
            lastInvitee = data.invitee;
            updateResultPanel(lastInvitee);
            verifyModal.hide();
            showVerifyModal('confirmed', lastInvitee, data.already_checked_in ? 'Guest was already checked in.' : 'Guest checked in successfully.');
        } catch (e) {
            alert('Check-in failed.');
            btn.disabled = false;
        }
    }

    document.getElementById('lookup-btn').addEventListener('click', () => {
        const code = document.getElementById('manual-code').value.trim();
        if (!code) return;
        lookup(code);
    });
    document.getElementById('manual-code').addEventListener('keydown', e => {
        if (e.key === 'Enter') document.getElementById('lookup-btn').click();
    });
    document.getElementById('checkin-btn').addEventListener('click', () => checkIn(false));
    document.getElementById('verify-modal-checkin-btn').addEventListener('click', () => checkIn(true));

    async function pickCameraId() {
        const cameras = await Html5Qrcode.getCameras();
        if (!cameras || !cameras.length) throw new Error('No camera found on this device.');
        const back = cameras.find(c => /back|rear|environment/i.test(c.label));
        return (back || cameras[cameras.length - 1]).id;
    }

    async function startScanner() {
        if (scannerRunning) return;
        if (typeof Html5Qrcode === 'undefined') {
            statusEl.textContent = 'Scanner library failed to load. Refresh the page.';
            return;
        }

        statusEl.textContent = 'Starting camera…';
        document.getElementById('start-scanner-btn').disabled = true;

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode('qr-reader', { verbose: false });
        }

        const config = {
            fps: 10,
            qrbox: (viewfinderWidth, viewfinderHeight) => {
                const edge = Math.min(viewfinderWidth, viewfinderHeight);
                const size = Math.max(150, Math.floor(edge * 0.7));
                return { width: size, height: size };
            },
            aspectRatio: 1.777,
        };

        const onScan = (decodedText) => {
            if (scanCooldown) return;
            scanCooldown = true;
            stopScanner().finally(() => {
                lookup(decodedText);
                setTimeout(() => { scanCooldown = false; }, 2000);
            });
        };

        try {
            const cameraId = await pickCameraId();
            await html5QrCode.start(cameraId, config, onScan, () => {});
        } catch (e) {
            try {
                await html5QrCode.start({ facingMode: 'environment' }, config, onScan, () => {});
            } catch (e2) {
                statusEl.textContent = 'Camera error: allow permission or use HTTPS/localhost. ' + (e2.message || '');
                document.getElementById('start-scanner-btn').disabled = false;
                return;
            }
        }

        scannerRunning = true;
        statusEl.textContent = 'Camera active — point at QR code';
        document.getElementById('stop-scanner-btn').disabled = false;
    }

    async function stopScanner() {
        if (!html5QrCode || !scannerRunning) return;
        try {
            await html5QrCode.stop();
            await html5QrCode.clear();
        } catch (e) { /* ignore */ }
        scannerRunning = false;
        document.getElementById('start-scanner-btn').disabled = false;
        document.getElementById('stop-scanner-btn').disabled = true;
        statusEl.textContent = 'Camera stopped. Tap start to scan again.';
    }

    document.getElementById('start-scanner-btn').addEventListener('click', startScanner);
    document.getElementById('stop-scanner-btn').addEventListener('click', stopScanner);

    // Sidebar mobile
    const sidebar = document.getElementById('mainSidebar');
    const overlay = document.getElementById('mobileOverlay');
    const menuToggle = document.getElementById('mobileMenuToggle');
    const closeSidebarBtn = document.getElementById('closeSidebarMobile');
    function openSidebar() {
        if (window.innerWidth < 768 && sidebar) {
            sidebar.classList.add('show-sidebar');
            overlay?.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    function closeSidebar() {
        sidebar?.classList.remove('show-sidebar');
        overlay?.classList.remove('active');
        document.body.style.overflow = '';
    }
    menuToggle?.addEventListener('click', openSidebar);
    closeSidebarBtn?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);
    window.addEventListener('resize', () => { if (window.innerWidth >= 768) closeSidebar(); });
</script>
</body>
</html>
