<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\CardDesign;
use App\Models\Invitee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function sendInvitation()
    {
        return view('event.invitation.send');
    }


    public function eventCardUpload()
    {
        return view('event.invitation.card-upload');
    }

    public function verifyInvitation()
    {
        return view('event.invitee.verify');
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
            'event_id' => 'required|exists:events,id',
            'design_name' => 'required|string|max:255',
            'design_type' => 'required|in:template,pdf',
            'template_name' => 'nullable|string|max:255',
            'qr_position_x' => 'required|integer|min:0',
            'qr_position_y' => 'required|integer|min:0',
            'qr_size' => 'required|integer|min:50|max:200',
            'qr_color' => 'required|string|max:7',
            'qr_background_color' => 'required|string|max:7',
            'text_content' => 'nullable|array',
            'pdf_file' => 'nullable|file|mimes:pdf|max:20480', // 20MB max
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
            $cardDesign->design_type = $validated['design_type'];
            $cardDesign->qr_position_x = $validated['qr_position_x'];
            $cardDesign->qr_position_y = $validated['qr_position_y'];
            $cardDesign->qr_size = $validated['qr_size'];
            $cardDesign->qr_color = $validated['qr_color'];
            $cardDesign->qr_background_color = $validated['qr_background_color'];
            $cardDesign->is_active = true;

            if ($validated['design_type'] === 'template') {
                $cardDesign->template_name = $validated['template_name'];
                $cardDesign->text_content = $validated['text_content'] ?? [];
            } elseif ($validated['design_type'] === 'pdf' && $request->hasFile('pdf_file')) {
                $pdfFile = $request->file('pdf_file');
                $fileName = 'card-designs/' . Str::uuid() . '.' . $pdfFile->getClientOriginalExtension();
                $pdfFile->storeAs('public', $fileName);
                $cardDesign->pdf_file_path = $fileName;
            }

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

        return view('event.invitee.verify', compact('invitee'));
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
}
