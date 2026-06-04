<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OwnerAdminController extends Controller
{
    private const ONLINE_WINDOW_MINUTES = 5;

    public function index(Request $request): View
    {
        return view('owner.manageadmins', $this->manageAdminsViewData($request));
    }

    /**
     * @return array<string, mixed>
     */
    private function manageAdminsViewData(Request $request): array
    {
        $search = trim((string) $request->query('search', ''));

        $admins = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->get();

        $ownerCount = User::where('role', User::ROLE_OWNER)->count();

        return [
            'admins' => $admins,
            'search' => $search,
            'totalAdmins' => User::count(),
            'ownerCount' => $ownerCount,
            'superAdminCount' => $ownerCount,
            'activeSessionsCount' => User::whereNotNull('last_seen_at')
                ->where('last_seen_at', '>=', now()->subMinutes(self::ONLINE_WINDOW_MINUTES))
                ->count(),
            'onlineWindowMinutes' => self::ONLINE_WINDOW_MINUTES,
            'lastUpdatedAt' => User::max('updated_at'),
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', Rule::in(User::ROLES_CREATABLE_BY_OWNER)],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $userData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'role' => $request->role,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'viewer_for_user_id' => null,
        ];

        if ($request->role === User::ROLE_VIEWER) {
            $userData['viewer_for_user_id'] = $request->user()->id;
        }

        User::create($userData);

        return redirect()
            ->route('manageadmins')
            ->with('status', 'Admin account created successfully.');
    }


    public function edit(Request $request, User $user): View
    {
        return view('owner.manageadmins', array_merge(
            $this->manageAdminsViewData($request),
            ['user' => $user]
        ));
    }

      // Delete user with password confirmation
    public function destroy(Request $request, User $user): RedirectResponse
      {
          $authenticatedUser = auth()->user();
          
          // Check if user can be deleted
          if (!$user->canBeDeletedBy($authenticatedUser)) {
              return redirect()->route('manageadmins')
                  ->with('error', 'You cannot delete this user.');
          }
          
          // Validate super admin's password
          $request->validate([
              'password' => ['required', 'string'],
          ]);
          
          // Verify password
          if (!Hash::check($request->password, $authenticatedUser->password)) {
              throw ValidationException::withMessages([
                  'password' => ['The password is incorrect.'],
              ]);
          }
          
          // Store the user info before deletion for logging
          $userInfo = [
              'id' => $user->id,
              'name' => $user->name,
              'email' => $user->email,
              'role' => $user->role
          ];
          
          // Delete the user
          $user->delete();
          
          // Log the action (optional)
          Log::info('User deleted by owner', [
              'deleter_id' => $authenticatedUser->id,
              'deleter_name' => $authenticatedUser->name,
              'deleted_user' => $userInfo,
              'timestamp' => now(),
          ]);
          
          return redirect()->route('manageadmins')
              ->with('status', "User {$userInfo['name']} has been deleted successfully.");
      }
}
