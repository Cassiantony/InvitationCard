<?php

namespace App\Http\Controllers;

use App\Models\CardDesign;
use App\Models\Event;
use App\Models\InvitationDelivery;
use App\Models\Invitee;
use App\Services\InvitationCardPdfComposer;
use App\Services\InvitationDeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function index()
{
    // Get events with optional query parameters
    $query = \App\Models\Event::query();
    
    // Add sorting
    $sort = request('sort', 'date_desc');
    switch ($sort) {
        case 'date_asc':
            $query->orderBy('date', 'asc');
            break;
        case 'title':
            $query->orderBy('title', 'asc');
            break;
        case 'category':
            $query->orderBy('category', 'asc');
            break;
        default:
            $query->orderBy('date', 'desc');
    }
    
    // Add filtering by category if requested
    if (request()->has('category') && request('category') != 'all') {
        $query->where('category', request('category'));
    }
    
    // Add filtering by status if requested
    if (request()->has('status')) {
        $status = request('status');
        if ($status === 'upcoming') {
            $query->where('date', '>', now());
        } elseif ($status === 'past') {
            $query->where('date', '<', now());
        } elseif ($status === 'today') {
            $query->whereDate('date', today());
        }
    }
    
    // Add search functionality
    if (request()->has('search')) {
        $search = request('search');
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%")
              ->orWhere('organizer_name', 'like', "%{$search}%");
        });
    }
    
    $events = $query->paginate(9);
    
    // Calculate stats
    $totalEvents = \App\Models\Event::count();
    $upcomingEvents = \App\Models\Event::where('date', '>', now())->count();
    $pastEvents = \App\Models\Event::where('date', '<', now())->count();
    $todayEvents = \App\Models\Event::whereDate('date', today())->count();
    
    return view('event.index', compact('events', 'totalEvents', 'upcomingEvents', 'pastEvents', 'todayEvents'));
}

public function show($id)
{
    try {
        \Log::info('Show method called with ID: ' . $id);
        \Log::info('Authenticated user ID: ' . auth()->id());

        // Get the event
        $event = Event::where('user_id', auth()->id())->find($id);
        
        if (!$event) {
            \Log::warning('Event not found. ID: ' . $id . ', User ID: ' . auth()->id());
            return back()->with('error', 'Event not found or you do not have permission to view it.');
        }

        \Log::info('Event found: ' . $event->title);

        // Initialize statistics with default values
        $totalInvites = 0;
        $confirmedAttendees = 0;
        $pendingResponses = 0;
        $declinedInvites = 0;

        return view('event.show', compact(
            'event',
            'totalInvites',
            'confirmedAttendees',
            'pendingResponses',
            'declinedInvites'
        ));

    } catch (\Exception $e) {
        \Log::error('Event show error: ' . $e->getMessage());
        return back()->with('error', 'An error occurred while loading the event.');
    }
}
    public function create()
    {
        return view('event.create');
    }

    public function store(Request $request)
    { 
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'organizer_name' => ['required', 'string', 'max:255'],
        ]);
       
        Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'date' => $request->date,
            'location' => $request->location,
            'organizer_name' => $request->organizer_name,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Event Created Successfully!');
    }

    public function destroyEvent($id)
    {
        $event = Event::where('user_id', auth()->id())->find($id);

        if (! $event) {
            return redirect()->route('event.index')
                ->with('error', 'Event not found or you do not have permission to delete it.');
        }

        $event->delete();

        return redirect()->route('event.index')->with('success', 'Event deleted successfully.');
    }

    public function sendInvitation(Request $request)
    {
        $events = Event::where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->get();

        $selectedEvent = null;
        $cardDesign = null;

        if ($request->filled('event_id')) {
            $selectedEvent = $events->firstWhere('id', (int) $request->query('event_id'));

            if ($selectedEvent) {
                $cardDesign = CardDesign::where('event_id', $selectedEvent->id)
                    ->where('is_active', true)
                    ->where('design_type', 'pdf')
                    ->orderByDesc('id')
                    ->first();
            }
        }

        $deliveryStats = ['sent' => 0, 'failed' => 0, 'rate' => 0];

        if ($selectedEvent) {
            $attempts = InvitationDelivery::query()
                ->where('event_id', $selectedEvent->id)
                ->where('user_id', auth()->id())
                ->get();
            $sentDeliveries = $attempts->where('status', 'sent')->count();

            $deliveryStats = [
                'sent' => $sentDeliveries,
                'failed' => $attempts->where('status', 'failed')->count(),
                'rate' => $attempts->count() > 0
                    ? (int) round(($sentDeliveries / $attempts->count()) * 100)
                    : 0,
            ];
        }

        return view('event.invitation.send', [
            'events' => $events,
            'selectedEvent' => $selectedEvent,
            'cardDesign' => $cardDesign,
            'walletBalance' => (int) auth()->user()->wallet_balance,
            'invitationCostTsh' => (int) config('invitation.cost_per_card_tsh', 500),
            'deliveryStats' => $deliveryStats,
        ]);
    }


    public function eventCardUpload(Request $request)
    {
        $events = Event::where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->get();

        $returnUrl = null;
        if ($request->filled('return') && $request->query('return') === 'send' && $request->filled('event_id')) {
            $returnUrl = route('event.invitation.send', ['event_id' => $request->query('event_id')]);
        }

        return view('event.invitation.card-upload', compact('events', 'returnUrl'));
    }

    public function inviteesJson(Event $event)
    {
        if ((int) $event->user_id !== (int) auth()->id()) {
            abort(403);
        }

        return response()->json([
            'invitees' => $event->invitees()
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'phone', 'invitation_code', 'status'])
                ->values(),
        ]);
    }

    public function downloadInvitationCard(Event $event, Invitee $invitee, InvitationCardPdfComposer $composer)
    {
        if ((int) $event->user_id !== (int) auth()->id()) {
            abort(403);
        }

        if ((int) $invitee->event_id !== (int) $event->id) {
            abort(404);
        }

        $design = CardDesign::where('event_id', $event->id)
            ->where('is_active', true)
            ->where('design_type', 'pdf')
            ->orderByDesc('id')
            ->first();

        if (! $design || ! $design->pdf_file_path) {
            abort(404, 'No active PDF invitation design for this event. Upload and save a card design first.');
        }

        try {
            $path = $composer->compose($design, $invitee);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $safe = preg_replace('/[^a-zA-Z0-9_-]+/', '-', (string) $invitee->name) ?: 'invitee';

        return response()->download(
            $path,
            "invitation-{$safe}-{$invitee->invitation_code}.pdf",
            ['Content-Type' => 'application/pdf']
        )->deleteFileAfterSend(true);
    }

    public function sendInvitationCards(Request $request, Event $event, InvitationDeliveryService $deliveryService): JsonResponse
    {
        if ((int) $event->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'invitee_ids' => ['required', 'array', 'min:1'],
            'invitee_ids.*' => ['integer', 'exists:invitees,id'],
            'delivery_mode' => ['nullable', 'string', 'in:auto,whatsapp,sms'],
        ]);

        $deliveryMode = $validated['delivery_mode'] ?? 'auto';
        $costPerCard = (int) config('invitation.cost_per_card_tsh', 500);

        $design = CardDesign::where('event_id', $event->id)
            ->where('is_active', true)
            ->where('design_type', 'pdf')
            ->orderByDesc('id')
            ->first();

        if (! $design || ! $design->pdf_file_path) {
            return response()->json([
                'success' => false,
                'message' => 'Upload and save a PDF card design for this event before sending.',
            ], 422);
        }

        if (! $design->template_image_path) {
            return response()->json([
                'success' => false,
                'message' => 'Re-save your card design so the PDF can be converted to an image for WhatsApp. Open Design Card → save again.',
            ], 422);
        }

        $invitees = Invitee::query()
            ->where('event_id', $event->id)
            ->whereIn('id', $validated['invitee_ids'])
            ->get();

        if ($invitees->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No valid invitees selected for this event.',
            ], 422);
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->refresh();

        $totalCost = $invitees->count() * $costPerCard;
        if ((int) $user->wallet_balance < $totalCost) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance. You need Tsh '.number_format($totalCost)
                    .' but have Tsh '.number_format((int) $user->wallet_balance).'.',
                'wallet_balance' => (int) $user->wallet_balance,
                'required_tsh' => $totalCost,
            ], 422);
        }

        $sent = [];
        $failed = [];
        $chargedTsh = 0;

        DB::transaction(function () use ($invitees, $design, $event, $deliveryService, $user, $deliveryMode, &$sent, &$failed, &$chargedTsh) {
            $user->refresh();

            foreach ($invitees as $invitee) {
                $result = $deliveryService->sendToInvitee(
                    $event,
                    $invitee,
                    $design,
                    $user,
                    $deliveryMode,
                    isResend: false,
                );

                if ($result['success']) {
                    InvitationDelivery::create([
                        'user_id' => $user->id,
                        'event_id' => $event->id,
                        'invitee_id' => $invitee->id,
                        'delivery_method' => $result['delivery_method'],
                        'is_resend' => false,
                        'fallback_method' => $result['fallback_method'],
                        'status' => 'sent',
                        'cost_tsh' => $result['charged_tsh'],
                        'recipient' => $result['recipient'],
                        'api_response' => $result['api_response'],
                        'sent_at' => now(),
                    ]);

                    $sent[] = [
                        'id' => $invitee->id,
                        'phone' => $invitee->phone,
                        'method' => $result['delivery_method'],
                    ];
                    $chargedTsh += $result['charged_tsh'];
                } else {
                    InvitationDelivery::create([
                        'user_id' => $user->id,
                        'event_id' => $event->id,
                        'invitee_id' => $invitee->id,
                        'delivery_method' => $result['delivery_method'],
                        'is_resend' => false,
                        'fallback_method' => $result['fallback_method'],
                        'status' => 'failed',
                        'cost_tsh' => 0,
                        'recipient' => $result['recipient'],
                        'error_message' => $result['error_message'],
                        'api_response' => $result['api_response'],
                    ]);

                    $failed[] = [
                        'id' => $invitee->id,
                        'phone' => $invitee->phone,
                        'message' => $result['error_message'],
                    ];
                }
            }
        });

        $user->refresh();
        $sentCount = count($sent);
        $failedCount = count($failed);

        return response()->json([
            'success' => $sentCount > 0,
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'charged_tsh' => $chargedTsh,
            'wallet_balance' => (int) $user->wallet_balance,
            'sent' => $sent,
            'failed' => $failed,
            'delivery_report_url' => route('event.invitation.delivery-report', ['event' => $event->id]),
            'message' => $sentCount > 0
                ? "Sent {$sentCount} invitation(s) via WhatsApp/SMS. Tsh ".number_format($chargedTsh).' deducted from your wallet.'
                : 'No invitations were sent. Check phone numbers and try again.',
        ], $sentCount > 0 ? 200 : 422);
    }

    public function resendQuote(Request $request, Event $event, Invitee $invitee, InvitationDeliveryService $deliveryService): JsonResponse
    {
        if ((int) $event->user_id !== (int) auth()->id() || (int) $invitee->event_id !== (int) $event->id) {
            abort(403);
        }

        $validated = $request->validate([
            'method' => ['required', 'string', 'in:email,sms,whatsapp'],
        ]);

        $cost = $deliveryService->calculateResendCost($invitee, $validated['method']);

        return response()->json([
            'method' => $validated['method'],
            'cost_tsh' => $cost,
            'free' => $cost === 0,
            'wallet_balance' => (int) auth()->user()->wallet_balance,
        ]);
    }

    public function resendInvitation(Request $request, Event $event, Invitee $invitee, InvitationDeliveryService $deliveryService): JsonResponse
    {
        if ((int) $event->user_id !== (int) auth()->id() || (int) $invitee->event_id !== (int) $event->id) {
            abort(403);
        }

        $validated = $request->validate([
            'method' => ['required', 'string', 'in:email,sms,whatsapp'],
        ]);

        $design = CardDesign::where('event_id', $event->id)
            ->where('is_active', true)
            ->where('design_type', 'pdf')
            ->orderByDesc('id')
            ->first();

        if (! $design || ! $design->pdf_file_path) {
            return response()->json([
                'success' => false,
                'message' => 'No card design configured for this event.',
            ], 422);
        }

        if (! $design->template_image_path) {
            return response()->json([
                'success' => false,
                'message' => 'Re-save your card design so the PDF is converted to an image.',
            ], 422);
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->refresh();

        $result = DB::transaction(function () use ($deliveryService, $event, $invitee, $design, $user, $validated) {
            return $deliveryService->sendToInvitee(
                $event,
                $invitee,
                $design,
                $user,
                mode: $validated['method'],
                isResend: true,
                forcedMethod: $validated['method'],
            );
        });

        InvitationDelivery::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'invitee_id' => $invitee->id,
            'delivery_method' => $result['delivery_method'],
            'is_resend' => true,
            'fallback_method' => $result['fallback_method'],
            'status' => $result['success'] ? 'sent' : 'failed',
            'cost_tsh' => $result['charged_tsh'],
            'recipient' => $result['recipient'],
            'error_message' => $result['error_message'],
            'api_response' => $result['api_response'],
            'sent_at' => $result['success'] ? now() : null,
        ]);

        $user->refresh();

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error_message'],
                'wallet_balance' => (int) $user->wallet_balance,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Invitation resent via '.$validated['method'].'.'
                .($result['charged_tsh'] > 0 ? ' Tsh '.number_format($result['charged_tsh']).' deducted.' : ' No charge.'),
            'wallet_balance' => (int) $user->wallet_balance,
            'charged_tsh' => $result['charged_tsh'],
        ]);
    }

    public function verifyInvitation()
    {
        $user = Auth::user();
        if ($user->isViewer()) {
            if (! $user->viewer_for_user_id) {
                return redirect()
                    ->route('viewer.dashboard')
                    ->with('error', 'Your viewer account is not linked to an organizer yet. Ask your organizer to recreate your login.');
            }
        }

        return view('event.invitee.verify', [
            'scanLookupUrl' => route('event.invitation.verify-lookup'),
            'scanCheckInUrl' => route('event.invitation.check-in'),
        ]);
    }

    public function currentInvitation()
    {
        return view('event.invitee.current-invitation');
    }

    public function sendInvitationDetails(Request $request)
    {
        if (! $request->filled('event_id')) {
            return redirect()->route('event.invitation.send');
        }

        return redirect()->route('event.invitation.delivery-report', [
            'event' => (int) $request->query('event_id'),
        ]);
    }

    public function deliveryReport(Request $request, Event $event)
    {
        if ((int) $event->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $events = Event::where('user_id', auth()->id())
            ->orderByDesc('date')
            ->get(['id', 'title', 'date']);

        $deliveries = InvitationDelivery::query()
            ->with(['invitee:id,name,email,phone,status,responded_at'])
            ->where('event_id', $event->id)
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        $sentCount = $deliveries->where('status', 'sent')->count();
        $failedCount = $deliveries->where('status', 'failed')->count();
        $totalSpent = $deliveries->where('status', 'sent')->sum('cost_tsh');
        $totalAttempts = $deliveries->count();
        $deliveryRate = $totalAttempts > 0 ? (int) round(($sentCount / $totalAttempts) * 100) : 0;

        return view('event.invitation.delivery-report', [
            'events' => $events,
            'selectedEvent' => $event,
            'deliveries' => $deliveries,
            'walletBalance' => (int) auth()->user()->wallet_balance,
            'invitationCostTsh' => (int) config('invitation.cost_per_card_tsh', 500),
            'smsCostTsh' => (int) config('invitation.cost_sms_tsh', 500),
            'whatsappResendCostTsh' => (int) config('invitation.cost_whatsapp_resend_tsh', 500),
            'whatsappFreeHours' => (int) config('invitation.whatsapp_free_resend_hours', 20),
            'stats' => [
                'sent' => $sentCount,
                'failed' => $failedCount,
                'total_spent_tsh' => $totalSpent,
                'delivery_rate' => $deliveryRate,
            ],
        ]);
    }

    /**
     * Save card design
     */
    public function saveCardDesign(Request $request)
    {
        $validated = $request->validate([
            'event_id' => [
                'required',
                Rule::exists('events', 'id')->where('user_id', auth()->id()),
            ],
            'design_name' => 'required|string|max:255',
            'design_type' => 'required|in:pdf',
            'qr_position_x' => 'required|integer|min:0',
            'qr_position_y' => 'required|integer|min:0',
            'qr_size' => 'required|integer|min:50|max:200',
            'qr_color' => 'required|string|max:7',
            'qr_background_color' => 'required|string|max:7',
            'qr_layout' => 'required|array',
            'qr_layout.nx' => 'required|numeric|between:0,1',
            'qr_layout.ny' => 'required|numeric|between:0,1',
            'qr_layout.nw' => 'required|numeric|between:0.02,1',
            'pdf_file' => 'required|file|mimes:pdf|max:20480',
            'template_image' => 'required|image|mimes:png,jpeg,jpg|max:15360',
        ]);

        DB::beginTransaction();

        try {
            // Deactivate existing designs for this event
            CardDesign::where('event_id', $validated['event_id'])
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $cardDesign = new CardDesign();
            $cardDesign->event_id = $validated['event_id'];
            $cardDesign->user_id = auth()->id();
            $cardDesign->design_name = $validated['design_name'];
            $cardDesign->design_type = 'pdf';
            $cardDesign->qr_position_x = $validated['qr_position_x'];
            $cardDesign->qr_position_y = $validated['qr_position_y'];
            $cardDesign->qr_size = $validated['qr_size'];
            $cardDesign->qr_color = $validated['qr_color'];
            $cardDesign->qr_background_color = $validated['qr_background_color'];
            $cardDesign->qr_layout = [
                'nx' => (float) $validated['qr_layout']['nx'],
                'ny' => (float) $validated['qr_layout']['ny'],
                'nw' => (float) $validated['qr_layout']['nw'],
            ];
            $cardDesign->is_active = true;

            $pdfFile = $request->file('pdf_file');
            $fileName = 'card-designs/'.Str::uuid().'.'.$pdfFile->getClientOriginalExtension();
            $pdfFile->storeAs('public', $fileName);
            $cardDesign->pdf_file_path = $fileName;

            $imageFile = $request->file('template_image');
            $imageName = 'card-designs/'.Str::uuid().'.png';
            $imageFile->storeAs('public', $imageName);
            $cardDesign->template_image_path = $imageName;

            $cardDesign->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Card design saved successfully!',
                'design_id' => $cardDesign->id
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save card design: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save invitees with individual QR codes
     */
    public function saveInvitees(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'invitees' => 'required|array|min:1',
            'invitees.*.name' => 'required|string|max:255',
            'invitees.*.email' => 'required|email|max:255',
            'invitees.*.phone' => 'nullable|string|max:20',
            'invitees.*.company' => 'nullable|string|max:255',
            'invitees.*.notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $addedCount = 0;
            $duplicateCount = 0;

            foreach ($validated['invitees'] as $inviteeData) {
                // Check if invitee already exists for this event
                $existingInvitee = Invitee::where('event_id', $validated['event_id'])
                    ->where('email', $inviteeData['email'])
                    ->first();

                if ($existingInvitee) {
                    $duplicateCount++;
                    continue;
                }

                // Create new invitee with unique QR code
                $invitee = new Invitee();
                $invitee->event_id = $validated['event_id'];
                $invitee->name = $inviteeData['name'];
                $invitee->email = $inviteeData['email'];
                $invitee->phone = $inviteeData['phone'] ?? null;
                $invitee->company = $inviteeData['company'] ?? null;
                $invitee->notes = $inviteeData['notes'] ?? null;
                $invitee->invitation_code = Invitee::generateInvitationCode();
                $invitee->status = 'pending';
                $invitee->invited_at = now();
                $invitee->save();

                $addedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully added {$addedCount} invitees" . 
                           ($duplicateCount > 0 ? " ({$duplicateCount} duplicates skipped)" : ""),
                'added_count' => $addedCount,
                'duplicate_count' => $duplicateCount
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save invitees: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invitee verification page
     */
    public function verifyInvitee($invitationCode)
    {
        $invitee = Invitee::where('invitation_code', $invitationCode)->first();
        
        if (!$invitee) {
            return view('event.invitee.not-found');
        }

        return view('event.invitee.verify', array_merge(
            compact('invitee'),
            [
                'scanLookupUrl' => route('event.invitation.verify-lookup'),
                'scanCheckInUrl' => route('event.invitation.check-in'),
            ]
        ));
    }

    /**
     * Process invitee verification
     */
    public function processVerification(Request $request, $invitationCode)
    {
        $invitee = Invitee::where('invitation_code', $invitationCode)->first();
        
        if (!$invitee) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid invitation code'
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:confirmed,declined',
            'notes' => 'nullable|string|max:500'
        ]);

        $invitee->status = $validated['status'];
        $invitee->responded_at = now();
        if ($validated['notes']) {
            $invitee->notes = ($invitee->notes ? $invitee->notes . "\n" : '') . $validated['notes'];
        }
        $invitee->save();

        return response()->json([
            'success' => true,
            'message' => 'Response recorded successfully',
            'status' => $validated['status']
        ]);
    }

    public function verifyScanLookup(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:512',
        ]);

        $code = $this->normalizeInvitationCode($validated['code']);
        if ($code === '') {
            return response()->json(['success' => false, 'message' => 'Invalid code'], 422);
        }

        $invitee = Invitee::with('event')
            ->where(function ($q) use ($code) {
                $q->where('invitation_code', $code);
                $q->orWhere('qr_code', $code);
            })
            ->first();

        if (! $invitee) {
            return response()->json(['success' => false, 'message' => 'Invitation not found'], 404);
        }

        if (! Auth::user()->canAccessInviteeForScan($invitee)) {
            return response()->json(['success' => false, 'message' => 'Not allowed to verify this invitation'], 403);
        }

        return response()->json([
            'success' => true,
            'invitee' => $this->serializeInviteeForScan($invitee),
        ]);
    }

    public function verifyScanCheckIn(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:512',
        ]);

        $code = $this->normalizeInvitationCode($validated['code']);
        if ($code === '') {
            return response()->json(['success' => false, 'message' => 'Invalid code'], 422);
        }

        $invitee = Invitee::with('event')
            ->where(function ($q) use ($code) {
                $q->where('invitation_code', $code);
                $q->orWhere('qr_code', $code);
            })
            ->first();

        if (! $invitee) {
            return response()->json(['success' => false, 'message' => 'Invitation not found'], 404);
        }

        if (! Auth::user()->canAccessInviteeForScan($invitee)) {
            return response()->json(['success' => false, 'message' => 'Not allowed to check in this guest'], 403);
        }

        if ($invitee->hasCheckedIn()) {
            return response()->json([
                'success' => true,
                'already_checked_in' => true,
                'invitee' => $this->serializeInviteeForScan($invitee),
            ]);
        }

        $invitee->forceFill([
            'checked_in_at' => now(),
            'checked_in_by' => Auth::id(),
        ])->save();

        return response()->json([
            'success' => true,
            'already_checked_in' => false,
            'invitee' => $this->serializeInviteeForScan($invitee->fresh()),
        ]);
    }

    private function normalizeInvitationCode(string $raw): string
    {
        $raw = trim($raw);
        if (preg_match('#/invitee/([A-Za-z0-9_-]+)#', $raw, $matches)) {
            return $matches[1];
        }

        return $raw;
    }

    private function serializeInviteeForScan(Invitee $invitee): array
    {
        $invitee->loadMissing('event');

        return [
            'id' => $invitee->id,
            'name' => $invitee->name,
            'email' => $invitee->email,
            'phone' => $invitee->phone,
            'company' => $invitee->company,
            'status' => $invitee->status,
            'invitation_code' => $invitee->invitation_code,
            'event_title' => $invitee->event?->title,
            'checked_in' => $invitee->hasCheckedIn(),
            'checked_in_at' => optional($invitee->checked_in_at)?->toIso8601String(),
        ];
    }

    private function friendlyMailErrorMessage(\Throwable $e): string
    {
        $msg = $e->getMessage();

        if (str_contains($msg, '535') || str_contains(strtolower($msg), 'authenticate')) {
            return 'Gmail rejected the SMTP login. You must use a Google App Password (not your normal Gmail password). '
                .'Enable 2-Step Verification, then create an App Password at myaccount.google.com/apppasswords, '
                .'put it in MAIL_PASSWORD in .env, and run: php artisan config:clear';
        }

        return strlen($msg) > 280 ? substr($msg, 0, 277).'…' : $msg;
    }
}
