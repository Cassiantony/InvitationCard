<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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

    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);

        $existing = User::query()->where('email', $request->input('email'))->first();

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

        $managerId = (int) auth()->id();

        $viewer = $existing;

        if ($viewer) {
            if (! $viewer->isViewer()) {
                return back()
                    ->withInput()
                    ->with('error', 'That email belongs to a non-viewer account.');
            }

            if ((int) $viewer->viewer_for_user_id !== $managerId) {
                return back()
                    ->withInput()
                    ->with('error', 'That viewer account belongs to another organizer.');
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
            return back()
                ->withInput()
                ->with('error', 'This viewer is already assigned to this event.');
        }

        $event->viewers()->attach($viewer->id);

        return redirect()
            ->route('event.viewers.index', $event)
            ->with('success', "{$viewer->name} can now scan QR codes for this event.");
    }

    public function destroy(Event $event, User $user): RedirectResponse
    {
        $this->authorizeEvent($event);

        if (! $user->isViewer() || (int) $user->viewer_for_user_id !== (int) auth()->id()) {
            abort(403, 'You cannot remove this viewer.');
        }

        if (! $event->viewers()->where('users.id', $user->id)->exists()) {
            return redirect()
                ->route('event.viewers.index', $event)
                ->with('error', 'Viewer is not assigned to this event.');
        }

        $event->viewers()->detach($user->id);

        return redirect()
            ->route('event.viewers.index', $event)
            ->with('success', 'Viewer removed from this event.');
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
