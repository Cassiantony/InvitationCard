<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminManagerController extends Controller
{
    public function index(): View
    {
        $managers = User::query()
            ->where('role', User::ROLE_MANAGER)
            ->latest()
            ->get();

        return view('manager.index', [
            'managers' => $managers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?: '-',
            'role' => User::ROLE_MANAGER,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('manage.managers')->with('status', 'Manager created successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (! $user->isManager()) {
            return redirect()->route('manage.managers')->with('error', 'Only manager accounts can be removed from this list.');
        }

        if (! auth()->user()->canManageManagers()) {
            abort(403);
        }

        $user->delete();

        return redirect()->route('manage.managers')->with('status', 'Manager removed successfully.');
    }
}
