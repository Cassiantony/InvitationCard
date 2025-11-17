<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Invitee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InviteesImport;
use App\Exports\InviteesTemplateExport;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class InviteeController extends Controller
{
    public function create()
    {
        $events = Event::where('user_id', Auth::id())
                     ->orderBy('date', 'desc')
                     ->get();

        return view('event.invitee.create', compact('events'));
    }

    public function uploadExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'event_id' => 'required|exists:events,id'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
    
        try {
            $import = new InviteesImport();
            $importedData = Excel::toArray($import, $request->file('excel_file'))[0];
            
            \Log::info('Excel Import Data:', $importedData); // Debug log
    
            $results = [
                'total' => count($importedData),
                'successful' => 0,
                'duplicates' => 0,
                'errors' => 0,
                'invitees' => []
            ];
    
            // Process data without saving to database immediately
            foreach ($importedData as $index => $row) {
                \Log::info('Processing row:', $row); // Debug each row
                
                $inviteeData = [
                    'id' => 'temp_' . ($index + 1),
                    'name' => $row['name'] ?? ($row['Name'] ?? ($row['NAME'] ?? '')),
                    'email' => $row['email'] ?? ($row['Email'] ?? ($row['EMAIL'] ?? '')),
                    'phone' => $row['phone'] ?? ($row['Phone'] ?? ($row['PHONE'] ?? null)),
                    'company' => $row['company'] ?? ($row['Company'] ?? ($row['COMPANY'] ?? null)),
                    'event_id' => $request->event_id
                ];
    
                \Log::info('Processed invitee data:', $inviteeData); // Debug processed data
    
                // Validate required fields
                if (empty($inviteeData['name']) || empty($inviteeData['email'])) {
                    $inviteeData['status'] = 'error';
                    $inviteeData['error'] = 'Missing name or email';
                    $results['errors']++;
                } 
                // Check for duplicates in database
                elseif (Invitee::where('event_id', $request->event_id)
                              ->where('email', $inviteeData['email'])
                              ->exists()) {
                    $inviteeData['status'] = 'existing';
                    $inviteeData['error'] = 'Already exists in event';
                    $results['duplicates']++;
                } else {
                    $inviteeData['status'] = 'new';
                    $results['successful']++;
                }
    
                $results['invitees'][] = $inviteeData;
            }
    
            return response()->json([
                'success' => true,
                'message' => 'File processed successfully',
                'results' => $results
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Excel upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeManual(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'event_id' => 'required|exists:events,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check for duplicates
            $existingInvitee = Invitee::where('event_id', $request->event_id)
                                    ->where('email', $request->email)
                                    ->first();

            $inviteeData = [
                'id' => 'temp_' . time(),
                'event_id' => $request->event_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'notes' => $request->notes,
                'status' => 'new'
            ];

            if ($existingInvitee) {
                $inviteeData['status'] = 'existing';
                $inviteeData['error'] = 'Already exists in event';
                
                return response()->json([
                    'success' => false,
                    'message' => 'An invitee with this email already exists for this event',
                    'invitee' => $inviteeData
                ], 409);
            }

            // Return data without saving to DB immediately
            return response()->json([
                'success' => true,
                'message' => 'Invitee added successfully',
                'invitee' => $inviteeData
            ]);

        } catch (\Exception $e) {
            \Log::error('Manual invitee error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding invitee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'event_id' => 'required|exists:events,id',
        'invitees' => 'required|array|min:1',
        'invitees.*.name' => 'required|string|max:255',
        'invitees.*.email' => 'required|email|max:255',
        'invitees.*.status' => 'sometimes|string' // Add validation for status
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        $eventId = $request->event_id;

        // Get all existing emails for this event in one query
        $existingEmails = Invitee::where('event_id', $eventId)
            ->whereIn('email', array_column($request->invitees, 'email'))
            ->pluck('email')
            ->toArray();

        foreach ($request->invitees as $index => $inviteeData) {
            try {
                // Skip if status is not 'new' (if provided)
                if (($inviteeData['status'] ?? '') !== 'new') {
                    continue;
                }

                // Check for duplicate email
                if (in_array($inviteeData['email'], $existingEmails)) {
                    $errorCount++;
                    $errors[] = "Duplicate email: {$inviteeData['email']}";
                    continue;
                }

                // Generate unique invitation code
                $invitationCode = Invitee::generateInvitationCode();

                // Generate QR code content and image
                $qrContent = route('invitee.show', ['code' => $invitationCode]);
                $qrImage = QrCode::format('png')
                    ->size(250)
                    ->errorCorrection('H')
                    ->generate($qrContent);

                // Ensure directory exists and generate filename
                $directory = 'qrcodes';
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }
                
                $fileName = $directory . '/' . $invitationCode . '_' . time() . '.png';

                // Store QR code
                Storage::disk('public')->put($fileName, $qrImage);

                // Create invitee with all data including QR code
                Invitee::create([
                    'event_id' => $eventId,
                    'name' => $inviteeData['name'],
                    'email' => $inviteeData['email'],
                    'phone' => $inviteeData['phone'] ?? null,
                    'company' => $inviteeData['company'] ?? null,
                    'notes' => $inviteeData['notes'] ?? null,
                    'invitation_code' => $invitationCode,
                    'qr_code' => $fileName, // Set directly in create
                    'status' => 'pending',
                    'invited_at' => now(),
                ]);

                $successCount++;
                $existingEmails[] = $inviteeData['email']; // Add to existing emails to prevent duplicates in same batch

            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "Error with {$inviteeData['email']}: " . $e->getMessage();
                \Log::error("Error creating invitee {$inviteeData['email']}: " . $e->getMessage());
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "{$successCount} invitees successfully added to the event",
            'count' => $successCount,
            'errors' => $errors,
            'error_count' => $errorCount
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Store invitees error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error saving invitees: ' . $e->getMessage()
        ], 500);
    }
}

    public function downloadTemplate()
    {
        return Excel::download(new InviteesTemplateExport, 'invitees-template.xlsx');
    }

    public function show($code)
{
    // Find the invitee by invitation code
    $invitee = \App\Models\Invitee::where('invitation_code', $code)->first();

    if (!$invitee) {
        return response()->json([
            'success' => false,
            'message' => 'Invitee not found.'
        ], 404);
    }

    // You can return a view or JSON data
    return view('event.invitee.show', compact('invitee'));
}

}