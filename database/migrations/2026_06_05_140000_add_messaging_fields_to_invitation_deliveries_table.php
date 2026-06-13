<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_deliveries', function (Blueprint $table) {
            $table->boolean('is_resend')->default(false)->after('delivery_method');
            $table->string('fallback_method', 32)->nullable()->after('is_resend');
            $table->text('api_response')->nullable()->after('error_message');
        });
    }

    public function down(): void
    {
        Schema::table('invitation_deliveries', function (Blueprint $table) {
            $table->dropColumn(['is_resend', 'fallback_method', 'api_response']);
        });
    }
};
