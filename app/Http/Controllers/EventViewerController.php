<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class EventViewerController extends Controller
{
    public function index(Event $event): View
    {
        $this->authorizeEvent($event);

        $event->load(['viewers' => fn ($q) => $q->orderBy('name')]);

        return view('event.viewers.index', [
            'event' => $event,
            'viewers' => $event->viewers,
        ]);
    }

    public function store(Request $request, Event $event): RedirectResponse|JsonResponse
    {
        $this->authorizeEvent($event);

        $existing = User::query()->where('email', $request->input('email'))->first();

        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:255'],
                'password' => [
                    $existing ? 'nullable' : 'required',
                    'confirmed',
                    Password::defaults(),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->validator->errors()->first(),
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }

        $managerId = (int) auth()->id();
        $viewer = $existing;

        if ($viewer) {
            if (! $viewer->isViewer()) {
                return $this->respond($request, false, 'That email belongs to a non-viewer account.', 422);
            }

            if ((int) $viewer->viewer_for_user_id !== $managerId) {
                return $this->respond($request, false, 'That viewer account belongs to another organizer.', 422);
            }
        } else {
            $viewer = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?: '-',
                'role' => User::ROLE_VIEWER,
                'password' => Hash::make($validated['password']),
                'viewer_for_user_id' => $managerId,
            ]);
        }

        if ($event->viewers()->where('users.id', $viewer->id)->exists()) {
            return $this->respond($request, false, 'This viewer is already assigned to this event.', 422);
        }

        $event->viewers()->attach($viewer->id);

        $payload = [
            'success' => true,
            'message' => "{$viewer->name} can now scan QR codes for this event.",
            'viewer' => $this->serializeViewer($viewer),
        ];

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return redirect()
            ->route('event.viewers.index', $event)
            ->with('success', $payload['message']);
    }

    public function destroy(Request $request, Event $event, User $user): RedirectResponse|JsonResponse
    {
        $this->authorizeEvent($event);

        if (! $user->isViewer() || (int) $user->viewer_for_user_id !== (int) auth()->id()) {
            abort(403, 'You cannot remove this viewer.');
        }

        if (! $event->viewers()->where('users.id', $user->id)->exists()) {
            return $this->respond($request, false, 'Viewer is not assigned to this event.', 422);
        }

        $event->viewers()->detach($user->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Viewer removed from this event.',
            ]);
        }

        return redirect()
            ->route('event.viewers.index', $event)
            ->with('success', 'Viewer removed from this event.');
    }

    private function serializeViewer(User $viewer): array
    {
        return [
            'id' => $viewer->id,
            'name' => $viewer->name,
            'email' => $viewer->email,
            'phone' => ($viewer->phone && $viewer->phone !== '-') ? $viewer->phone : null,
            'initials' => strtoupper(substr($viewer->name, 0, 2)),
        ];
    }

    private function respond(Request $request, bool $success, string $message, int $status = 200): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => $success, 'message' => $message], $status);
        }

        return back()->withInput()->with($success ? 'success' : 'error', $message);
    }

    private function authorizeEvent(Event $event): void
    {
        $user = auth()->user();

        if ($user->canAccessAdminArea()) {
            return;
        }

        if ((int) $event->user_id !== (int) $user->id) {
            abort(403, 'You do not have permission to manage viewers for this event.');
        }
    }
}
