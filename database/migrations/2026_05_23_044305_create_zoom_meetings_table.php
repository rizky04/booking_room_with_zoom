<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zoom_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('zoom_meeting_id');
            $table->string('zoom_uuid')->nullable();
            $table->string('host_id')->nullable();
            $table->string('topic');
            $table->string('join_url');
            $table->string('start_url')->nullable();
            $table->string('password')->nullable();
            $table->string('host_email')->nullable();
            $table->integer('duration')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->enum('status', ['waiting', 'started', 'finished'])->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_meetings');
    }
};
