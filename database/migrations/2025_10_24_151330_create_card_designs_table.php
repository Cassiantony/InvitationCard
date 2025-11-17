<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_designs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('design_name');
            $table->enum('design_type', ['template', 'pdf']);
            $table->string('template_name')->nullable(); // for template designs
            $table->string('pdf_file_path')->nullable(); // for PDF designs
            $table->integer('qr_position_x')->default(300);
            $table->integer('qr_position_y')->default(50);
            $table->integer('qr_size')->default(100);
            $table->string('qr_color')->default('#000000');
            $table->string('qr_background_color')->default('#ffffff');
            $table->json('text_content')->nullable(); // for template text content
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index(['event_id', 'is_active']);
            $table->index(['user_id', 'design_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_designs');
    }
};
