<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\CardDesign;
use App\Models\Invitee;
use App\Services\InvitationCardPdfComposer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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

        return view('event.invitation.send', compact('events', 'selectedEvent', 'cardDesign'));
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
                ->get(['id', 'name', 'email', 'phone', 'invitation_code'])
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

    public function sendInvitationDetails()
    {
        return view('event.invitation.send-details');
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
}
