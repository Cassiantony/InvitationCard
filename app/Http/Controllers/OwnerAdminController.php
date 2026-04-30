<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OwnerAdminController extends Controller
{
    private const ONLINE_WINDOW_MINUTES = 5;

    public function index(Request $request): View
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

        return view('owner.manageadmins', [
            'admins' => $admins,
            'search' => $search,
            'totalAdmins' => User::count(),
            'superAdminCount' => User::whereRaw("REPLACE(LOWER(role), ' ', '') = ?", ['superadmin'])->count(),
            'activeSessionsCount' => User::whereNotNull('last_seen_at')
                ->where('last_seen_at', '>=', now()->subMinutes(self::ONLINE_WINDOW_MINUTES))
                ->count(),
            'onlineWindowMinutes' => self::ONLINE_WINDOW_MINUTES,
            'lastUpdatedAt' => User::max('updated_at'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'role' => $request->role,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('manageadmins')
            ->with('status', 'Admin account created successfully.');
    }


    public function edit(User $user){
        $data['user'] = $user;
        return view('owner.manageadmins', $data);
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
          Log::info('User deleted by Super Admin', [
              'super_admin_id' => $authenticatedUser->id,
              'super_admin_name' => $authenticatedUser->name,
              'deleted_user' => $userInfo,
              'timestamp' => now()
          ]);
          
          return redirect()->route('manageadmins')
              ->with('status', "User {$userInfo['name']} has been deleted successfully.");
      }
}
