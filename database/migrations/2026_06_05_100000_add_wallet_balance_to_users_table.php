<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('wallet_balance')->default(0)->after('viewer_for_user_id');
        });

        DB::table('users')
            ->whereIn('role', ['owner', 'Owner', 'admin', 'Admin', 'manager', 'Manager', 'superadmin', 'superadministrator'])
            ->where('wallet_balance', 0)
            ->update(['wallet_balance' => 100000]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('wallet_balance');
        });
    }
};
