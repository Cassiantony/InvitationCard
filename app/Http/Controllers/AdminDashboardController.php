<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Invitee;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $now = now();
        $lastMonth = $now->copy()->subMonth();
        $lastWeek = $now->copy()->subWeek();

        $totalEvents = Event::count();
        $newEventsSinceLastMonth = Event::where('created_at', '>=', $lastMonth)->count();

        $activeEvents = Event::where('date', '>=', $now)->count();
        $newActiveEventsSinceLastWeek = Event::where('date', '>=', $now)
            ->where('created_at', '>=', $lastWeek)
            ->count();

        $totalManagers = $this->countUsersByRole(User::ROLE_MANAGER);
        $newManagersSinceLastWeek = $this->countUsersByRole(User::ROLE_MANAGER, $lastWeek);

        $totalViewers = $this->countUsersByRole(User::ROLE_VIEWER);
        $newViewersSinceLastWeek = $this->countUsersByRole(User::ROLE_VIEWER, $lastWeek);

        $recentEvents = Event::query()
            ->with('user:id,name')
            ->withCount([
                'invitees',
                'invitees as confirmed_count' => fn ($q) => $q->where('status', 'confirmed'),
            ])
            ->orderByDesc('created_at')
            ->limit(4)
            ->get()
            ->map(function (Event $event) {
                $viewersCount = $event->viewers()->count();

                $isCompleted = $event->status === 'completed';

                return [
                    'event' => $event,
                    'viewers_count' => $viewersCount,
                    'confirmed_count' => (int) $event->confirmed_count,
                    'is_completed' => $isCompleted,
                    'status_label' => $this->eventStatusLabel($event),
                    'status_class' => $this->eventStatusBadgeClass($event),
                ];
            });

        $rsvpEvents = Event::query()
            ->where('date', '>=', $now)
            ->whereHas('invitees')
            ->withCount([
                'invitees',
                'invitees as confirmed_count' => fn ($q) => $q->where('status', 'confirmed'),
            ])
            ->orderByDesc('date')
            ->limit(3)
            ->get()
            ->map(function (Event $event) {
                $total = (int) $event->invitees_count;
                $confirmed = (int) $event->confirmed_count;

                return [
                    'title' => $event->title,
                    'percent' => $total > 0 ? (int) round(($confirmed / $total) * 100) : 0,
                    'bar_class' => $confirmed >= $total * 0.7 ? 'bg-success' : ($confirmed >= $total * 0.4 ? 'bg-warning' : 'bg-info'),
                ];
            });

        $recentActivity = Invitee::query()
            ->with('event:id,title')
            ->whereIn('status', ['confirmed', 'declined', 'sent'])
            ->where(function ($q) {
                $q->whereNotNull('responded_at')
                    ->orWhereNotNull('invited_at');
            })
            ->orderByRaw('COALESCE(responded_at, invited_at, updated_at) DESC')
            ->limit(5)
            ->get()
            ->map(fn (Invitee $invitee) => $this->formatActivity($invitee));

        return view('admin.dashboard', [
            'totalEvents' => $totalEvents,
            'newEventsSinceLastMonth' => $newEventsSinceLastMonth,
            'activeEvents' => $activeEvents,
            'newActiveEventsSinceLastWeek' => $newActiveEventsSinceLastWeek,
            'totalManagers' => $totalManagers,
            'newManagersSinceLastWeek' => $newManagersSinceLastWeek,
            'totalViewers' => $totalViewers,
            'newViewersSinceLastWeek' => $newViewersSinceLastWeek,
            'recentEvents' => $recentEvents,
            'rsvpEvents' => $rsvpEvents,
            'recentActivity' => $recentActivity,
        ]);
    }

    private function countUsersByRole(string $role, ?Carbon $since = null): int
    {
        $query = User::query()->whereIn('role', $this->roleAliases($role));

        if ($since) {
            $query->where('created_at', '>=', $since);
        }

        return $query->count();
    }

    /**
     * @return list<string>
     */
    private function roleAliases(string $role): array
    {
        return match ($role) {
            User::ROLE_OWNER => ['owner', 'Owner', 'superadmin', 'superadministrator'],
            User::ROLE_ADMIN => ['admin', 'Admin'],
            User::ROLE_MANAGER => ['manager', 'Manager'],
            User::ROLE_VIEWER => ['viewer', 'Viewer'],
            default => [$role],
        };
    }

    private function eventStatusLabel(Event $event): string
    {
        return match ($event->status) {
            'completed' => 'Completed',
            'ongoing' => 'Today',
            default => 'Active',
        };
    }

    private function eventStatusBadgeClass(Event $event): string
    {
        return match ($event->status) {
            'completed' => 'bg-danger',
            'ongoing' => 'bg-primary',
            default => 'bg-success',
        };
    }

    /**
     * @return array{icon: string, icon_class: string, message: string, time: string}
     */
    private function formatActivity(Invitee $invitee): array
    {
        $eventTitle = $invitee->event?->title ?? 'an event';
        $when = $invitee->responded_at ?? $invitee->invited_at ?? $invitee->updated_at;

        if ($invitee->status === 'confirmed') {
            return [
                'icon' => 'fa-user-check',
                'icon_class' => 'text-success',
                'message' => "{$invitee->name} confirmed for {$eventTitle}",
                'time' => $when?->diffForHumans() ?? 'Recently',
            ];
        }

        if ($invitee->status === 'declined') {
            return [
                'icon' => 'fa-user-times',
                'icon_class' => 'text-danger',
                'message' => "{$invitee->name} declined {$eventTitle}",
                'time' => $when?->diffForHumans() ?? 'Recently',
            ];
        }

        return [
            'icon' => 'fa-envelope',
            'icon_class' => 'text-primary',
            'message' => "Invitation sent to {$invitee->name} for {$eventTitle}",
            'time' => $when?->diffForHumans() ?? 'Recently',
        ];
    }
}
