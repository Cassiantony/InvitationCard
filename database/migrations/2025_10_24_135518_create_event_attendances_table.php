<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('event_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitee_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->timestamp('checked_in_at');
            $table->foreignId('checked_in_by')->constrained('users');
            $table->string('check_in_method')->default('qr_code'); // qr_code, manual, nfc, etc.
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['event_id', 'checked_in_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_attendances');
    }
};