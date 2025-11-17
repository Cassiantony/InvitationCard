<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('invitees', function (Blueprint $table) {
            $table->string('qr_code')->nullable()->after('invitation_code');
        });
    }

    
    public function down(): void
    {
        Schema::table('invitees', function (Blueprint $table) {
            $table->dropColumn('qr_code');
        });
    }
};