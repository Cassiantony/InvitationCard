<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invitees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->text('notes')->nullable();
            $table->string('invitation_code')->unique(); // For QR code generation
            $table->enum('status', ['pending', 'sent', 'confirmed', 'declined'])->default('pending');
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['event_id', 'email']);
            $table->index('invitation_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invitees');
    }
};