@php
    $activeNav = 'delivery';
    $pageSubtitle = 'Delivery Report';
    $deliveryReportEvent = $selectedEvent;
@endphp
@extends('layouts.inviteflow-portal')

@section('title', 'Delivery Report — '.$selectedEvent->title)

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <a href="{{ route('event.invitation.send', ['event_id' => $selectedEvent->id]) }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Send
            </a>
            <h1 class="h4 mb-0">Delivery Report</h1>
            <p class="text-muted mb-0">{{ $selectedEvent->title }}</p>
        </div>
        <div class="text-end">
            <div class="text-muted small">Wallet balance</div>
            <div class="h5 mb-0 text-primary">Tsh {{ number_format($walletBalance) }}</div>
            <a href="{{ route('wallet.add-funds', ['return' => url()->current()]) }}" class="small">Add funds</a>
        </div>
    </div>

    @if($events->count() > 1)
        <div class="mb-4">
            <label class="form-label">Switch event</label>
            <select class="form-select" id="event-report-select">
                @foreach($events as $ev)
                    <option value="{{ $ev->id }}" {{ $ev->id === $selectedEvent->id ? 'selected' : '' }}>{{ $ev->title }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card stat-card success h-100">
                <div class="card-body">
                    <div class="text-muted small">Delivered</div>
                    <div class="h3 mb-0">{{ $stats['sent'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card danger h-100">
                <div class="card-body">
                    <div class="text-muted small">Failed</div>
                    <div class="h3 mb-0">{{ $stats['failed'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card warning h-100">
                <div class="card-body">
                    <div class="text-muted small">Total spent</div>
                    <div class="h3 mb-0">Tsh {{ number_format($stats['total_spent_tsh']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="text-muted small">Success rate</div>
                    <div class="h3 mb-0">{{ $stats['delivery_rate'] }}%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong><i class="fas fa-list me-2 text-primary"></i>All delivery attempts</strong>
            <span class="badge bg-secondary">{{ $deliveries->count() }} records</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Invitee</th>
                        <th>RSVP</th>
                        <th>Recipient</th>
                        <th>Method</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th>Response / Details</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $delivery)
                        @php $invitee = $delivery->invitee; @endphp
                        <tr>
                            <td class="small">
                                {{ $delivery->sent_at?->format('d M Y, H:i') ?? $delivery->created_at->format('d M Y, H:i') }}
                                @if($delivery->is_resend)
                                    <span class="badge bg-info text-dark">Resend</span>
                                @endif
                            </td>
                            <td>{{ $invitee?->name ?? '—' }}</td>
                            <td>
                                @if($invitee)
                                    <span class="badge {{ $invitee->rsvpBadgeClass() }}">{{ $invitee->rsvpLabel() }}</span>
                                    @if($invitee->responded_at)
                                        <div class="small text-muted">{{ $invitee->responded_at->format('d M Y') }}</div>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $delivery->recipient }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $delivery->methodLabel() }}</span></td>
                            <td>
                                @if($delivery->status === 'sent')
                                    {{ $delivery->cost_tsh > 0 ? 'Tsh '.number_format($delivery->cost_tsh) : 'Free' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($delivery->status === 'sent')
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Sent</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Failed</span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                @if($delivery->api_response)
                                    <div>{{ $delivery->api_response }}</div>
                                @endif
                                @if($delivery->error_message)
                                    <div class="text-danger">{{ $delivery->error_message }}</div>
                                @endif
                                @if(!$delivery->api_response && !$delivery->error_message)
                                    —
                                @endif
                            </td>
                            <td class="text-end">
                                @if($invitee)
                                    <button type="button" class="btn btn-sm btn-outline-primary resend-btn"
                                            data-invitee-id="{{ $invitee->id }}"
                                            data-invitee-name="{{ $invitee->name }}">
                                        <i class="fas fa-redo me-1"></i>Resend
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No deliveries yet for this event.
                                <div class="mt-2">
                                    <a href="{{ route('event.invitation.send', ['event_id' => $selectedEvent->id]) }}" class="btn btn-sm btn-primary">Send invitations</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="text-muted small mt-3 mb-0">
        Initial send: Tsh {{ number_format($invitationCostTsh) }} (WhatsApp image, or SMS with code if not on WhatsApp).
        Resend: Email free · SMS Tsh {{ number_format($smsCostTsh) }} · WhatsApp free within {{ $whatsappFreeHours }}hrs, then Tsh {{ number_format($whatsappResendCostTsh) }}.
    </p>

    <div class="modal fade" id="resendModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Resend invitation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Resend to <strong id="resend-invitee-name"></strong></p>
                    <label class="form-label">Delivery method</label>
                    <div class="list-group mb-3">
                        <label class="list-group-item list-group-item-action">
                            <input class="form-check-input me-2" type="radio" name="resend_method" value="email" checked>
                            <strong>Email</strong> — Free (image attachment)
                        </label>
                        <label class="list-group-item list-group-item-action">
                            <input class="form-check-input me-2" type="radio" name="resend_method" value="sms">
                            <strong>SMS</strong> — Tsh {{ number_format($smsCostTsh) }} (invitation code)
                        </label>
                        <label class="list-group-item list-group-item-action">
                            <input class="form-check-input me-2" type="radio" name="resend_method" value="whatsapp">
                            <strong>WhatsApp</strong> — <span id="whatsapp-cost-label">Free within {{ $whatsappFreeHours }}hrs of last WhatsApp send</span>
                        </label>
                    </div>
                    <div class="alert alert-light border mb-0">
                        <div class="d-flex justify-content-between">
                            <span>Cost for this resend:</span>
                            <strong id="resend-cost-display">Free</strong>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mt-1">
                            <span>Wallet balance:</span>
                            <span id="resend-wallet-balance">Tsh {{ number_format($walletBalance) }}</span>
                        </div>
                    </div>
                    <div id="resend-error" class="alert alert-danger mt-3 d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirm-resend-btn">
                        <i class="fas fa-paper-plane me-1"></i> Send
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const eventId = @json($selectedEvent->id);
    const quoteUrl = (inviteeId, method) => @json(url('/event')) + '/' + eventId + '/invitation/resend/' + inviteeId + '/quote?method=' + method;
    const resendUrl = (inviteeId) => @json(url('/event')) + '/' + eventId + '/invitation/resend/' + inviteeId;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    let activeInviteeId = null;
    const resendModal = new bootstrap.Modal(document.getElementById('resendModal'));

    document.getElementById('event-report-select')?.addEventListener('change', function () {
        window.location.href = @json(url('/event')) + '/' + this.value + '/invitation/delivery-report';
    });

    async function updateResendCost() {
        if (!activeInviteeId) return;
        const method = document.querySelector('input[name="resend_method"]:checked')?.value || 'email';
        try {
            const res = await fetch(quoteUrl(activeInviteeId, method), { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            document.getElementById('resend-cost-display').textContent = data.cost_tsh > 0
                ? 'Tsh ' + data.cost_tsh.toLocaleString()
                : 'Free';
            document.getElementById('resend-wallet-balance').textContent = 'Tsh ' + (data.wallet_balance || 0).toLocaleString();
        } catch (e) {
            document.getElementById('resend-cost-display').textContent = '—';
        }
    }

    document.querySelectorAll('.resend-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            activeInviteeId = btn.dataset.inviteeId;
            document.getElementById('resend-invitee-name').textContent = btn.dataset.inviteeName;
            document.getElementById('resend-error').classList.add('d-none');
            document.querySelector('input[name="resend_method"][value="email"]').checked = true;
            updateResendCost();
            resendModal.show();
        });
    });

    document.querySelectorAll('input[name="resend_method"]').forEach(r => {
        r.addEventListener('change', updateResendCost);
    });

    document.getElementById('confirm-resend-btn').addEventListener('click', async () => {
        if (!activeInviteeId) return;
        const method = document.querySelector('input[name="resend_method"]:checked')?.value;
        const btn = document.getElementById('confirm-resend-btn');
        const errEl = document.getElementById('resend-error');
        btn.disabled = true;
        errEl.classList.add('d-none');

        try {
            const res = await fetch(resendUrl(activeInviteeId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ method }),
            });
            const data = await res.json();
            if (data.success) {
                window.location.reload();
            } else {
                errEl.textContent = data.message || 'Resend failed.';
                errEl.classList.remove('d-none');
            }
        } catch (e) {
            errEl.textContent = 'Network error while resending.';
            errEl.classList.remove('d-none');
        } finally {
            btn.disabled = false;
        }
    });
</script>
@endpush
