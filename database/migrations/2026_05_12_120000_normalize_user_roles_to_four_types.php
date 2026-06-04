<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Collapse legacy roles into four canonical values: owner, admin, manager, viewer.
     */
    public function up(): void
    {
        User::query()->orderBy('id')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $slug = strtolower(str_replace(' ', '', (string) $user->role));

                $canonical = match ($slug) {
                    'superadmin', 'superadministrator' => User::ROLE_OWNER,
                    'owner' => User::ROLE_OWNER,
                    'admin' => User::ROLE_ADMIN,
                    'manager' => User::ROLE_MANAGER,
                    'viewer' => User::ROLE_VIEWER,
                    default => User::ROLE_VIEWER,
                };

                if ($user->role !== $canonical) {
                    $user->forceFill(['role' => $canonical])->saveQuietly();
                }
            }
        });
    }

    public function down(): void
    {
        // Non-reversible; roles stay normalized.
    }
};
